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
        $announcements = Announcement::where('publish_status', 1)->get();
        $currentYear = $request->input('year', date('Y'));
        $months = range(1, 12);
    
        // Filter labs based on user role
        $labManagementList = LabManagement::query();
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Superadmin can see all labs
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            // Filter based on campus
            $labManagementList->whereHas('computerLab', function ($query) use ($user) {
                $query->where('campus_id', $user->campus_id);
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
            $campusList = Campus::with('computerLab')->where('id', $user->campus_id)->get();
        } else {
            $assignedComputerLabs = $user->assignedComputerLabs;
            $campusIds = $assignedComputerLabs->pluck('campus_id')->unique();
            $campusList = Campus::with('computerLab')->whereIn('id', $campusIds)->get();
        }
    
        $campusData = [];
    
        foreach ($campusList as $campus) {
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->where('campus_id', $campus->id)
                ->get();
        
            $maintainedLabsPerMonth = [];
            foreach ($months as $month) {
                $maintainedLabsThisMonth = LabManagement::query()  // Use a fresh query here
                    ->whereMonth('end_time', $month)  // use `end_time`
                    ->whereYear('end_time', $currentYear)  // use `end_time`
                    ->whereHas('computerLab', function ($query) use ($campus) {
                        $query->where('campus_id', $campus->id);
                    })
                    ->whereIn('status', ['dihantar', 'telah_disemak'])
                    ->pluck('computer_lab_id')
                    ->unique();
        
                // Check if the lab was maintained
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
        
    
        return view('pages.yearly-report.index', [
            'months' => $months,
            'campusData' => $campusData,
            'currentYear' => $currentYear,
            'announcements' => $announcements,
        ]);
    }
    
}
