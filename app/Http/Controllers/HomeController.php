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
        
        $currentMonth = date('m');
        $currentYear = date('Y');
    
        // Initialize LabManagement query
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
        } else {
            // Regular Pemilik only sees lab management data for their own labs
            $labManagementQuery->whereHas('computerLab', function ($query) use ($user) {
                $query->where('pemilik_id', $user->id);
            });
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->where('pemilik_id', $user->id)
                ->get();
        }
    
        // Apply additional filters based on request inputs
        if ($request->filled('campus_id')) {
            $labManagementQuery->whereHas('computerLab', function ($query) use ($request) {
                $query->where('campus_id', $request->input('campus_id'));
            });
        }
    
        if ($request->filled('month')) {
            $labManagementQuery->whereMonth('start_time', $request->input('month'));
        } else {
            $labManagementQuery->whereMonth('start_time', date('m'));
        }
    
        if ($request->filled('year')) {
            $labManagementQuery->whereYear('start_time', $request->input('year'));
        } else {
            $labManagementQuery->whereYear('start_time', date('Y'));
        }
    
        if ($request->filled('pemilik_id')) {
            $labManagementQuery->whereHas('computerLab', function ($query) use ($request) {
                $query->where('pemilik_id', $request->input('pemilik_id'));
            });
        }
    
        if ($request->filled('computer_lab_id')) {
            $labManagementQuery->where('computer_lab_id', $request->input('computer_lab_id'));
        }
    
        if ($request->filled('status')) {
            $labManagementQuery->where('status', $request->input('status'));
        }
    
        // Execute the query to fetch lab management data
        $labManagementData = $labManagementQuery->get();
    
        // Fetch filtered computer labs based on filter
        $filteredComputerLabsQuery = ComputerLab::where('publish_status', 1);
        
        // Ensure Pemilik only sees their own computer labs in the filtered query
        if ($user->hasRole('Pemilik')) {
            $filteredComputerLabsQuery->where('pemilik_id', $user->id);
        }
        
        if ($request->filled('computer_lab_id')) {
            $filteredComputerLabsQuery->where('id', $request->input('computer_lab_id'));
        }
        
        $filteredComputerLabs = $filteredComputerLabsQuery->get();
        $totalLab = $filteredComputerLabs->count();
    
        // Calculate totals
        $totalDihantarReports = $labManagementData->where('status', 'dihantar')->count();
        
        // Ensure total PC count is restricted to the user's computer labs
        $totalPC = $this->getTotalPC($filteredComputerLabs, $request->input('month'), $request->input('year'));
        
        // Only sum maintenance and damage PCs from the filtered lab management data
        $totalMaintenancePC = $labManagementData->sum('pc_maintenance_no');
        $totalDamagePC = $labManagementData->sum('pc_damage_no');
        $totalUnmaintenancePC = $totalPC - $totalMaintenancePC - $totalDamagePC;
    
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
        $campusList = Campus::whereIn('id', $assignedComputerLabs->pluck('campus_id'))->get();
    
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
        $campusList = Campus::whereIn('id', $assignedComputerLabs->pluck('campus_id'))->get();

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
            'campusList' => $campusList,
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
