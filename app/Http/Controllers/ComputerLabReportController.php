<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\ComputerLabHistory;
use App\Models\LabManagement;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class ComputerLabReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = User::find(auth()->id());
        $announcements = Announcement::where('publish_status', 1)->get();

        $currentYear = $request->input('year', date('Y')); // Get the selected year
        $currentMonth = $request->input('month', date('n')); // Get the selected month

        // OwnersWithLabsQuery
        $ownersWithLabsQuery = ComputerLab::with(['pemilik', 'campus'])
            ->select('id', 'name', 'pemilik_id', 'campus_id')
            ->where('publish_status', 1);

        // LabManagement query
        $labManagementQuery = LabManagement::query()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear);

        // Filter based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin and Superadmin see all lab management data
            $computerLabList = ComputerLab::where('publish_status', 1)->get();
            $campusList = Campus::with('computerLab')->get();
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $userCampusIds = $user->campus->pluck('id')->toArray();
            
            // Pegawai Penyemak only sees lab management data for labs in their campuses
            $labManagementQuery->whereHas('computerLab', function ($query) use ($userCampusIds) {
                $query->whereIn('campus_id', $userCampusIds);
            });
            
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->whereIn('campus_id', $userCampusIds)
                ->get();
            
            // Owner with lab
            $ownersWithLabsQuery->whereIn('campus_id', $userCampusIds);
            
            $campusList = Campus::with('computerLab')->whereIn('id', $userCampusIds)->get();        
        } else {
            // Regular Pemilik only sees lab management data for their own labs
            $labManagementQuery->whereHas('computerLab', function ($query) use ($user) {
                $query->where('pemilik_id', $user->id);
            });
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->where('pemilik_id', $user->id)
                ->get();
            //Owners with lab
            $ownersWithLabsQuery->where('pemilik_id', $user->id);
            $assignedComputerLabs = $user->assignedComputerLabs;
            $campusIds = $assignedComputerLabs->pluck('campus_id')->unique();
            $campusList = Campus::with('computerLab')->whereIn('id', $campusIds)->get();
        }

        // Filters
        if ($request->filled('campus_id')) {
            $labManagementQuery->whereHas('computerLab', function ($query) use ($request) {
                $query->where('campus_id', $request->input('campus_id'));
            });
            // Also filter computer labs by campus
            $computerLabList = $computerLabList->where('campus_id', $request->input('campus_id'));
            $ownersWithLabsQuery->where('campus_id', $request->input('campus_id'));
        }

        if ($request->filled('computer_lab_id')) {
            $labManagementQuery->where('computer_lab_id', $request->input('computer_lab_id'));
            // Also filter computer labs by lab ID
            $computerLabList = $computerLabList->where('id', $request->input('computer_lab_id'));
            $ownersWithLabsQuery->where('id', $request->input('computer_lab_id'));
        }

        if ($request->filled('status')) {
            $labManagementQuery->where('status', $request->input('status'));
        }

        // Execute the query to fetch lab management data
        $labManagementData = $labManagementQuery->get();

        // Fetch filtered computer labs based on the previously applied filters
        $filteredComputerLabs = $computerLabList;

        // Get the results and group by campus_id
        $ownersWithLabs = $ownersWithLabsQuery->get()->groupBy('campus_id');

        $totalLab = $filteredComputerLabs->count();

        // Calculate totals
        $totalDihantarReports = $labManagementData->where('status', 'dihantar')->count();

        // Ensure total PC count is restricted to the user's computer labs
        $totalPC = $this->getTotalPC($filteredComputerLabs, $request->input('month'), $request->input('year'));

        // Only sum maintenance and damage PCs from the filtered lab management data
        $totalMaintenancePC = $labManagementData->whereIn('status', ['dihantar', 'telah_disemak'])->sum('pc_maintenance_no');
        $totalDamagePC = $labManagementData->whereIn('status', ['dihantar', 'telah_disemak'])->sum('pc_damage_no');
        $totalUnmaintenancePC = $totalPC - $totalMaintenancePC - $totalDamagePC;

        // Calculate PC count for each lab using the getTotalPC method
        foreach ($ownersWithLabs as $campusId => $labs) {
            foreach ($labs as $lab) {
                // Calculate the PC count for each lab by calling the getTotalPC method
                $lab->pc_count = $this->getTotalPC(collect([$lab]), $currentMonth, $currentYear);
            }
        }

        $months = range(1, 12);
        $campusData = [];
    
        foreach ($campusList as $campus) {
            if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
                // Admin and Superadmin see all labs in each campus
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->get();
            } elseif ($user->hasRole('Pegawai Penyemak')) {
                $userCampusIds = $user->campus->pluck('id')->toArray();
                
                // Pegawai Penyemak sees labs only in campuses they are associated with
                if (in_array($campus->id, $userCampusIds)) {
                    $computerLabList = ComputerLab::where('publish_status', 1)
                        ->where('campus_id', $campus->id)
                        ->get();
                } else {
                    $computerLabList = collect(); // Empty collection if they don’t have access
                }
            } else {
                // Regular Pemilik only sees labs they own
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->where('pemilik_id', $user->id)
                    ->get();
            }
        
            $maintainedLabsPerMonth = [];
            foreach ($months as $month) {
                $maintainedLabsThisMonth = LabManagement::query()  
                    ->whereMonth('end_time', $month)  
                    ->whereYear('end_time', $currentYear)  
                    ->whereHas('computerLab', function ($query) use ($campus) {
                        $query->where('campus_id', $campus->id);
                    })
                    ->whereIn('status', ['dihantar', 'telah_disemak'])
                    ->pluck('computer_lab_id')
                    ->unique();
        
                $maintainedLabsPerMonth[$month] = $computerLabList->mapWithKeys(function ($lab) use ($maintainedLabsThisMonth) {
                    return [$lab->id => $maintainedLabsThisMonth->contains($lab->id)];
                });
            }
        
            $campusData[] = [
                'campus' => $campus,
                'computerLabList' => $computerLabList,
                'maintainedLabsPerMonth' => $maintainedLabsPerMonth
            ];
        }
        
        $campusList = Campus::all();

        foreach ($labManagementData as $labManagement) {
            $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
            $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        }

        // Return view with data
        return view('pages.computer-lab-report.index', [
            'labManagementList' => $labManagementData,
            'totalDihantarReports' => $totalDihantarReports,
            'totalPC' => $totalPC,
            'totalMaintenancePC' => $totalMaintenancePC,
            'totalDamagePC' => $totalDamagePC,
            'totalUnmaintenancePC' => $totalUnmaintenancePC,
            'computerLabList' => $computerLabList,
            'campusList' => $campusList,
            'announcements' => $announcements,
            'totalLab' => $totalLab,
            'currentYear' =>  $currentYear,
            'currentMonth' =>  $currentMonth,
            'ownersWithLabs' => $ownersWithLabs,
            'months' => $months,
            'campusData' => $campusData
        ]);
    }

    private function getTotalPC($filteredComputerLabs, $selectedMonth = null, $selectedYear = null)
    {
        $totalPC = 0;
        foreach ($filteredComputerLabs as $computerLab) {
            $query = ComputerLabHistory::where('computer_lab_id', $computerLab->id);

            if ($selectedMonth) {
                // Get the latest history entry before or in the selected month
                $query->where(function ($q) use ($selectedMonth, $selectedYear) {
                    $q->whereYear('month_year', '<', $selectedYear)
                        ->orWhere(function ($query) use ($selectedMonth, $selectedYear) {
                            $query->whereYear('month_year', $selectedYear)
                                ->whereMonth('month_year', '<=', $selectedMonth);
                        });
                });
            }

            $latestHistory = $query->orderBy('month_year', 'desc')->first();

            if ($latestHistory) {
                // Add the latest pc_no to totalPC, defaulting to 0 if it's null
                $totalPC += $latestHistory->pc_no ?? 0;
            }
        }
        return $totalPC;
    }

    public function downloadPdf(Request $request)
    {
        $user = User::find(auth()->id());
        $currentYear = $request->input('year', date('Y'));
        $months = range(1, 12);
    
        // Filter labs based on user role
        $labManagementList = LabManagement::query();
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Superadmin can see all labs
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($user) {
                // Filter labs based on the campuses associated with the user
                $query->whereIn('campus_id', $user->campus->pluck('id'));
            });
        } else {
            // Filter based on assigned labs
            $assignedComputerLabs = $user->assignedComputerLabs;
            $labManagementList->whereIn('computer_lab_id', $assignedComputerLabs->pluck('id'));
        }
    
        // Filter campuses based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            $campusList = Campus::with('computerLab')->get();
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            // Get the campuses associated with the user
            $campusList = $user->campus()->with('computerLab')->get();        
        } else {
            $assignedComputerLabs = $user->assignedComputerLabs;
            $campusIds = $assignedComputerLabs->pluck('campus_id')->unique();
            $campusList = Campus::with('computerLab')->whereIn('id', $campusIds)->get();
        }

        $campusData = [];
    
        foreach ($campusList as $campus) {
            if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
                // Admin and Superadmin see all labs in each campus
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->get();
            } elseif ($user->hasRole('Pegawai Penyemak')) {
                $userCampusIds = $user->campus->pluck('id')->toArray();
                
                // Pegawai Penyemak sees labs only in campuses they are associated with
                if (in_array($campus->id, $userCampusIds)) {
                    $computerLabList = ComputerLab::where('publish_status', 1)
                        ->where('campus_id', $campus->id)
                        ->get();
                } else {
                    $computerLabList = collect(); // Empty collection if they don’t have access
                }
            } else {
                // Regular Pemilik only sees labs they own
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->where('pemilik_id', $user->id)
                    ->get();
            }
    
            $maintainedLabsPerMonth = [];
            foreach ($months as $month) {
                $maintainedLabsThisMonth = LabManagement::query()
                    ->whereMonth('end_time', $month)
                    ->whereYear('end_time', $currentYear)
                    ->whereHas('computerLab', function ($query) use ($campus) {
                        $query->where('campus_id', $campus->id);
                    })
                    ->whereIn('status', ['dihantar', 'telah_disemak'])
                    ->pluck('computer_lab_id')
                    ->unique();
    
                $maintainedLabsPerMonth[$month] = $computerLabList->mapWithKeys(function ($lab) use ($maintainedLabsThisMonth) {
                    return [$lab->id => $maintainedLabsThisMonth->contains($lab->id)];
                });
            }
    
            $campusData[] = [
                'campus' => $campus,
                'computerLabList' => $computerLabList,
                'maintainedLabsPerMonth' => $maintainedLabsPerMonth
            ];
        }
    
        // Render the view into HTML
        $html = view('pages.computer-lab-report.pdf', [
            'months' => $months,
            'campusData' => $campusData,
            'currentYear' => $currentYear,
            'username' => $user->name,
        ])->render();
    
        // Set up the filename
        $filename = "Laporan_Tahunan_Selenggara_Makmal_Komputer_{$currentYear}.pdf";
    
        // Initialize DomPDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
    
        // Create the Dompdf instance and load the HTML
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
    
        // Stream the generated PDF
        return $dompdf->stream($filename, ['Attachment' => true]);
    }
    
    
}