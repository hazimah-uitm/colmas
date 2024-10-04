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
        $announcements = Announcement::all();

        // Initialize LabManagement query based on user roles
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            $labManagementData = LabManagement::query();
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $labManagementData = LabManagement::whereHas('computerLab', function ($query) use ($user) {
                $query->where('campus_id', $user->campus_id);
            });
        } else {
            $labManagementData = LabManagement::whereHas('computerLab', function ($query) use ($user) {
                $query->where('pemilik_id', $user->id);
            });
        }

        // Apply filters
        if ($request->filled('campus_id')) {
            $labManagementData->whereHas('computerLab', function ($query) use ($request) {
                $query->where('campus_id', $request->input('campus_id'));
            });
        }

        if ($request->filled('month')) {
            $labManagementData->whereMonth('start_time', $request->input('month'));
        } else {
            // Use the current month if no month is provided
            $labManagementData->whereMonth('start_time', date('m'));
        }
        
        if ($request->filled('year')) {
            $labManagementData->whereYear('start_time', $request->input('year'));
        } else {
            // Use the current year if no year is provided
            $labManagementData->whereYear('start_time', date('Y'));
        }
        

        if ($request->filled('pemilik_id')) {
            $labManagementData->whereHas('computerLab', function ($query) use ($request) {
                $query->where('pemilik_id', $request->input('pemilik_id'));
            });
        }

        if ($request->filled('computer_lab_id')) {
            $labManagementData->where('computer_lab_id', $request->input('computer_lab_id'));
        }

        if ($request->filled('status')) {
            $labManagementData->where('status', $request->input('status'));
        }

        // Fetch lab management data
        $labManagementData = $labManagementData
            ->whereMonth('created_at', date('m'))  // Filters by the current month
            ->whereYear('created_at', date('Y'))   // Filters by the current year
            ->get();

        // Fetch all computer labs for dropdown
        $allComputerLabs = ComputerLab::where('publish_status', 1)->get();

        // Fetch filtered computer labs based on filter
        $filteredComputerLabsQuery = ComputerLab::where('publish_status', 1);
        if ($request->filled('computer_lab_id')) {
            $filteredComputerLabsQuery->where('id', $request->input('computer_lab_id'));
        }
        $filteredComputerLabs = $filteredComputerLabsQuery->get();
        $totalLab = $filteredComputerLabs->count();

        // Retrieve selected month and year from request
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        // Calculate totals
        $totalDihantarReports = $labManagementData->where('status', 'dihantar')->count();
        $totalPC = $this->getTotalPC($filteredComputerLabs, $selectedMonth, $selectedYear);
        $totalMaintenancePC = $labManagementData->sum('pc_maintenance_no');
        $totalDamagePC = $labManagementData->sum('pc_damage_no');
        $totalUnmaintenancePC = $labManagementData->sum('pc_unmaintenance_no');

        // Calculate unmaintained labs including drafts
        $maintainedLabIds = $labManagementData->whereIn('status', ['dihantar', 'telah_disemak'])
            ->pluck('computer_lab_id')
            ->unique();
        $totalUnmaintainedLabs = $totalLab - $maintainedLabIds->count();

        // Determine assigned labs based on user role
        $assignedComputerLabs = ($user->hasAnyRole(['Admin', 'Superadmin']))
            ? $filteredComputerLabs
            : (($user->hasRole('Pegawai Penyemak'))
                ? $filteredComputerLabs->where('campus_id', $user->campus_id)
                : $user->assignedComputerLabs->intersect($filteredComputerLabs));

        // Fetch lists for the view
        $computerLabList = $allComputerLabs;
        $pemilikList = User::role('Pemilik')
            ->whereIn('id', $assignedComputerLabs->pluck('pemilik_id'))
            ->get();
        $campusList = Campus::whereIn('id', $assignedComputerLabs->pluck('campus_id'))->get();
        $statusList = LabManagement::select('status')->distinct()->pluck('status');

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
            'pemilikList' => $pemilikList,
            'campusList' => $campusList,
            'statusList' => $statusList,
            'announcements' => $announcements,
            'totalLab' => $totalLab,
            'totalUnmaintainedLabs' => $totalUnmaintainedLabs,
        ]);
    }

    private function getTotalPC($filteredComputerLabs, $selectedMonth = null, $selectedYear = null)
    {
        $totalPC = 0;
        foreach ($filteredComputerLabs as $computerLab) {
            $query = ComputerLabHistory::where('computer_lab_id', $computerLab->id);

            if ($selectedMonth) {
                $query->whereMonth('month_year', $selectedMonth);
            }

            if ($selectedYear) {
                $query->whereYear('month_year', $selectedYear);
            }

            $latestHistory = $query->orderBy('month_year', 'desc')->first();
            if ($latestHistory) {
                $totalPC += $latestHistory->pc_no;
            }
        }
        return $totalPC;
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
        ]);

        Announcement::updateOrCreate(
            ['id' => $request->input('announcement_id')],
            $request->only('title', 'desc')
        );

        return redirect()->route('home');
    }

    public function edit($id)
    {
        // Fetch the necessary data to pass to the view
        $user = User::find(auth()->id());
        $announcements = Announcement::all();
        $announcement = Announcement::find($id); // Fetch the specific announcement for editing

        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            $labManagementData = LabManagement::query();
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $labManagementData = LabManagement::whereHas('computerLab', function ($query) use ($user) {
                $query->where('campus_id', $user->campus_id);
            });
        } else {
            $labManagementData = LabManagement::whereHas('computerLab', function ($query) use ($user) {
                $query->where('pemilik_id', $user->id);
            });
        }

        $labManagementData = $labManagementData->get();
        $totalLab = ComputerLab::where('publish_status', 1)->count();
        $totalDihantarReports = $labManagementData->where('status', 'dihantar')->count();
        $totalPC = $labManagementData->sum('computer_no');
        $totalMaintenancePC = $labManagementData->sum('pc_maintenance_no');
        $totalDamagePC = $labManagementData->sum('pc_damage_no');
        $totalUnmaintenancePC = $labManagementData->sum('pc_unmaintenance_no');

        $assignedComputerLabs = ($user->hasAnyRole(['Admin', 'Superadmin']))
            ? ComputerLab::where('publish_status', 1)->get()
            : (($user->hasRole('Pegawai Penyemak'))
                ? ComputerLab::where('publish_status', 1)->where('campus_id', $user->campus_id)->get()
                : $user->assignedComputerLabs);

        $computerLabList = ComputerLab::whereIn('id', $assignedComputerLabs->pluck('id'))->get();
        $pemilikList = User::role('Pemilik')->whereIn('id', $assignedComputerLabs->pluck('pemilik_id'))->get();
        $campusList = Campus::whereIn('id', $assignedComputerLabs->pluck('campus_id'))->get();
        $statusList = LabManagement::select('status')->distinct()->pluck('status');

        foreach ($labManagementData as $labManagement) {
            $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
            $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        }

        $maintainedLabIds = $labManagementData->whereIn('status', ['dihantar', 'telah_disemak'])->pluck('computer_lab_id')->unique();
        $totalUnmaintainedLabs = $totalLab - $maintainedLabIds->count();

        return view('home', [
            'labManagementList' => $labManagementData,
            'totalDihantarReports' => $totalDihantarReports,
            'totalPC' => $totalPC,
            'totalMaintenancePC' => $totalMaintenancePC,
            'totalDamagePC' => $totalDamagePC,
            'totalUnmaintenancePC' => $totalUnmaintenancePC,
            'computerLabList' => $computerLabList,
            'pemilikList' => $pemilikList,
            'campusList' => $campusList,
            'statusList' => $statusList,
            'announcements' => $announcements,
            'announcement' => $announcement, // Pass the specific announcement to the view
            'totalLab' => $totalLab,
            'totalUnmaintainedLabs' => $totalUnmaintainedLabs
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update($request->only('title', 'desc'));

        return redirect()->route('home');
    }


    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('home');
    }
}
