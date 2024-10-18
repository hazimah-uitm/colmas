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
        $months = range(1, 12); // January to December

        // Filter labs based on user role
        $labManagementList = LabManagement::query(); // Initialize the query
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin or superadmin can access all labs, so no further filtering needed
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($user) {
                $query->where('campus_id', $user->campus_id);
            });
        } else {
            $assignedComputerLabs = $user->assignedComputerLabs; // Assuming you have a relationship for assigned labs
            $labManagementList->whereIn('computer_lab_id', $assignedComputerLabs->pluck('id'));
        }

        // Filter campuses based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin or superadmin can access all campuses
            $campusList = Campus::with('computerLab')->get();
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            // Pegawai Penyemak can access only their campus
            $campusList = Campus::with('computerLab')
                ->where('id', $user->campus_id)
                ->get();
        } else {
            // Other users can access only the campuses with labs they are assigned to
            $assignedComputerLabs = $user->assignedComputerLabs; // Assuming this relationship exists
            $campusIds = $assignedComputerLabs->pluck('campus_id')->unique();
            $campusList = Campus::with('computerLab')
                ->whereIn('id', $campusIds)
                ->get();
        }

        // Prepare an array to store the results for each campus
        $campusData = [];

        foreach ($campusList as $campus) {
            // Get the labs for each campus
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->where('campus_id', $campus->id)
                ->get();

            // Query for lab management data for each campus and year
            $maintainedLabsPerMonth = [];
            foreach ($months as $month) {
                $maintainedLabsThisMonth = $labManagementList->whereMonth('created_at', $month)
                    ->whereYear('created_at', $currentYear)
                    ->whereHas('computerLab', function ($query) use ($campus) {
                        $query->where('campus_id', $campus->id);
                    })
                    ->whereIn('status', ['dihantar'])
                    ->pluck('computer_lab_id')
                    ->unique();

                // Map lab maintenance status
                $maintainedLabsPerMonth[$month] = $computerLabList->map(function ($lab) use ($maintainedLabsThisMonth) {
                    return $maintainedLabsThisMonth->contains($lab->id) ? $lab->id : null;
                })->filter(); 
            }

            // Store the data for this campus
            $campusData[] = [
                'campus' => $campus,
                'computerLabList' => $computerLabList,
                'maintainedLabsPerMonth' => $maintainedLabsPerMonth
            ];
        }

        return view('pages.yearly-report.index', [
            'months' => $months,
            'campusData' => $campusData, // Send campus data to the view
            'currentYear' => $currentYear,
            'announcements' => $announcements,
        ]);
    }


}
