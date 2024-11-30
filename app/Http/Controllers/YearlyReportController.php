<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\LabManagement;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class YearlyReportController extends Controller
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

        $currentYear = $request->input('year', date('Y')); // Get the selected year
        $currentMonth = $request->input('month', date('n')); // Get the selected month

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
            
            $campusList = Campus::with('computerLab')->whereIn('id', $userCampusIds)->get();        
        } else {
            // Regular Pemilik only sees lab management data for their own labs
            $labManagementQuery->whereHas('computerLab', function ($query) use ($user) {
                $query->where('pemilik_id', $user->id);
            });
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->where('pemilik_id', $user->id)
                ->get();
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
        }

        if ($request->filled('computer_lab_id')) {
            $labManagementQuery->where('computer_lab_id', $request->input('computer_lab_id'));
            // Also filter computer labs by lab ID
            $computerLabList = $computerLabList->where('id', $request->input('computer_lab_id'));
        }

        if ($request->filled('category')) {
            $category = $request->input('category');
            $labManagementQuery->whereHas('computerLab', function ($query) use ($category) {
                $query->where('category', $category);
            });
            $computerLabList = $computerLabList->where('category', $category);
        }
        
    
        // Execute the query to fetch lab management data
        $labManagementData = $labManagementQuery->get();

        // Fetch filtered computer labs based on the previously applied filters
        $filteredComputerLabs = $computerLabList;

        $months = range(1, 12);
        $campusData = [];
        
        // Filter campusList if campus_id is provided
        if ($request->filled('campus_id')) {
            $campusList = $campusList->where('id', $request->input('campus_id'));
        }
        
        foreach ($campusList as $campus) {
            if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
                // Admin and Superadmin see all labs in each campus
                $computerLabs = $filteredComputerLabs->where('campus_id', $campus->id);
            } elseif ($user->hasRole('Pegawai Penyemak')) {
                $userCampusIds = $user->campus->pluck('id')->toArray();
                
                // Pegawai Penyemak sees labs only in campuses they are associated with
                if (in_array($campus->id, $userCampusIds)) {
                    $computerLabs = $filteredComputerLabs->where('campus_id', $campus->id);
                } else {
                    $computerLabs = collect(); // Empty collection if they don’t have access
                }
            } else {
                // Regular Pemilik only sees labs they own
                $computerLabs = $filteredComputerLabs->where('pemilik_id', $user->id)->where('campus_id', $campus->id);
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
            
                $maintainedLabsPerMonth[$month] = $computerLabs->mapWithKeys(function ($lab) use ($maintainedLabsThisMonth) {
                    return [$lab->id => $maintainedLabsThisMonth->contains($lab->id)];
                });
            }
        
            // Add the campus data to the campusData array
            $campusData[] = [
                'campus' => $campus,
                'computerLabs' => $computerLabs,
                'maintainedLabsPerMonth' => $maintainedLabsPerMonth
            ];
        }
        
        $campusList = Campus::all();

        foreach ($labManagementData as $labManagement) {
            $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
            $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        }

        // Return view with data
        return view('pages.yearly-report.index', [
            'labManagementList' => $labManagementData,
            'computerLabList' => $computerLabList,
            'campusList' => $campusList,
            'currentYear' =>  $currentYear,
            'currentMonth' =>  $currentMonth,
            'months' => $months,
            'campusData' => $campusData
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $user = User::find(auth()->id());
        $currentYear = $request->input('year', date('Y'));
        $months = range(1, 12);
        $currentDate = now()->format('d M Y');

        // Filter labs based on user role
        $labManagementList = LabManagement::query();
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Superadmin can see all labs
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($user) {
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

        $path = public_path('assets/images/Logo-Infostruktur.svg');
        $logoData = base64_encode(file_get_contents($path));
        $logoMimeType = mime_content_type($path);

        // Render the view into HTML
        $html = view('pages.yearly-report.pdf', [
            'months' => $months,
            'campusData' => $campusData,
            'currentYear' => $currentYear,
            'username' => $user->name,
            'currentDate' => $currentDate,
            'logoBase64' => "data:{$logoMimeType};base64,{$logoData}",
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

        // Add custom header and footer to each page
        $canvas = $dompdf->getCanvas();
        $canvasHeight = $canvas->get_height();
        $canvasWidth = $canvas->get_width();  // Get the page width

        // Header
        $canvas->page_text(30, 30, "Computer Lab Maintenance System (COLMAS)", 'arial', 8, array(0, 0, 0), 0, false, false, '');

        // Footer: Left (Dijana oleh), Center (Tarikh), Right (Pagination)
        $footerLeftText = "Dijana oleh: {$user->name} - {$currentDate}";
        $footerRightText = "{PAGE_NUM}";

        // Left: Positioning
        $canvas->page_text(30, $canvasHeight - 40, $footerLeftText, null, 8, array(0, 0, 0));

        // Right: Positioning
        $canvas->page_text($canvasWidth - 40, $canvasHeight - 40, $footerRightText, null, 8, array(0, 0, 0));


        // Stream the generated PDF
        return $dompdf->stream($filename, ['Attachment' => false]);
    }
}
