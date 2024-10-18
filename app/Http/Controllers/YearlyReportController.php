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
        $campusList = Campus::with('computerLab')->get(); // Get all campuses
        $currentYear = $request->input('year', date('Y'));
        $months = range(1, 12); // January to December

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
                $maintainedLabsThisMonth = LabManagement::whereMonth('created_at', $month)
                    ->whereYear('created_at', $currentYear)
                    ->whereHas('computerLab', function ($query) use ($campus) {
                        $query->where('campus_id', $campus->id);
                    })
                    ->whereIn('status', ['dihantar', 'telah_disemak'])
                    ->pluck('computer_lab_id')
                    ->unique();

                // Map lab maintenance status
                // Update this part of your controller
                $maintainedLabsPerMonth[$month] = $computerLabList->map(function ($lab) use ($maintainedLabsThisMonth) {
                    // Return the lab ID instead of '✓' or 'X'
                    return $maintainedLabsThisMonth->contains($lab->id) ? $lab->id : null;
                })->filter(); // Remove null values
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
