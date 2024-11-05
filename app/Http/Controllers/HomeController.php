<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\ComputerLabHistory;
use App\Models\LabManagement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
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
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            // Pegawai Penyemak only sees lab management data for labs in their campus
            $labManagementQuery->whereHas('computerLab', function ($query) use ($user) {
                $query->where('campus_id', $user->campus_id);
            });
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->where('campus_id', $user->campus_id)
                ->get();
            // Owner with lab
            $ownersWithLabsQuery->where('campus_id', $user->campus_id);
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

        // Calculate unmaintained labs including drafts
        $maintainedLabIds = $labManagementData->whereIn('status', ['dihantar', 'telah_disemak'])
            ->pluck('computer_lab_id')
            ->unique();
        $totalUnmaintainedLabs = $totalLab - $maintainedLabIds->count();

        $months = range(1, 12); // Get months from January to December
        $unmaintainedLabsPerMonth = [];
        $maintainedLabsPerMonth = [];

        // If a specific month is selected, focus only on that month
        if ($currentMonth) {
            $months = [$currentMonth]; // Limit to the selected month
        }

        foreach ($months as $month) {
            // Fetch maintained labs for the specified month and year
            $maintainedLabsThisMonth = LabManagement::whereMonth('created_at', $month)
                ->whereYear('created_at', $currentYear)
                ->whereIn('status', ['dihantar', 'telah_disemak'])
                ->pluck('computer_lab_id')
                ->unique();

            // Filter unmaintained labs
            $unmaintainedLabsThisMonth = $computerLabList->filter(function ($lab) use ($maintainedLabsThisMonth) {
                return !$maintainedLabsThisMonth->contains($lab->id);
            });

            $unmaintainedLabsPerMonth[$month] = $unmaintainedLabsThisMonth;
        }

        foreach ($months as $month) {
            // Fetch maintained labs for the specified month and year
            $maintainedLabsThisMonth = LabManagement::whereMonth('created_at', $month)
                ->whereYear('created_at', $currentYear)
                ->whereIn('status', ['dihantar', 'telah_disemak'])
                ->pluck('computer_lab_id')
                ->unique(); // Get unique computer lab IDs that were maintained this month

            // Get the list of maintained labs for this month, filtered by $computerLabList
            $maintainedLabsPerMonth[$month] = $computerLabList->filter(function ($lab) use ($maintainedLabsThisMonth) {
                return $maintainedLabsThisMonth->contains($lab->id); // Check if the lab is maintained this month
            });
        }

        // Calculate PC count for each lab using the getTotalPC method
        foreach ($ownersWithLabs as $campusId => $labs) {
            foreach ($labs as $lab) {
                // Calculate the PC count for each lab by calling the getTotalPC method
                $lab->pc_count = $this->getTotalPC(collect([$lab]), $currentMonth, $currentYear);
            }
        }

        // Prepare arrays to hold PC counts for each lab
        $pcCounts = [];

        // Iterate through each lab
        foreach ($filteredComputerLabs as $lab) {
            // Get the total PCs in the lab
            $totalPCbyReport = $this->getTotalPC(collect([$lab]), $currentMonth, $currentYear);

            // Fetch lab management data related to this lab
            $labManagementDataForLab = $labManagementData->where('computer_lab_id', $lab->id)->whereIn('status', ['dihantar', 'telah_disemak']);

            // Calculate maintained and unmaintained PCs
            $totalMaintenancePCbyReport = $labManagementDataForLab->sum('pc_maintenance_no') + $labManagementDataForLab->sum('pc_damage_no');

            // Calculate the number of unmaintained PCs
            $totalUnmaintenancePCbyReport = $totalPCbyReport - $totalMaintenancePCbyReport;

            // Store the counts in the pcCounts array
            $pcCounts[$lab->id] = [
                'lab_name' => $lab->name,
                'total_maintenance' => $totalMaintenancePCbyReport,
                'total_unmaintenance' => $totalUnmaintenancePCbyReport,
            ];
        }

        // Fetch lists for the view
        $campusList = Campus::all();

        // Format lab management data for the view
        foreach ($labManagementData as $labManagement) {
            $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
            $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        }

        // Return view with data
        return view('home', [
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
            'totalUnmaintainedLabs' => $totalUnmaintainedLabs,
            'unmaintainedLabsPerMonth' => $unmaintainedLabsPerMonth,
            'maintainedLabsPerMonth' => $maintainedLabsPerMonth,
            'currentYear' =>  $currentYear,
            'ownersWithLabs' => $ownersWithLabs,
            'pcCounts' => $pcCounts
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
}
