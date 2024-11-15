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

        $currentYear = $request->input('year', date('Y'));
        $currentMonth = $request->input('month', date('n'));

        // Query to get Computer Labs grouped by campus, pemilik, and total PC
        $ownersWithLabsQuery = ComputerLab::with(['pemilik', 'campus'])
            ->select('id', 'name', 'pemilik_id', 'campus_id')
            ->where('publish_status', 1);

        // Filter based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $userCampusIds = $user->campus->pluck('id')->toArray();
            $ownersWithLabsQuery->whereIn('campus_id', $userCampusIds);
        } else {
            $ownersWithLabsQuery->where('pemilik_id', $user->id);
        }

        // Get the results and group by campus_id
        $ownersWithLabs = $ownersWithLabsQuery->get()->groupBy('campus_id')->map(function ($labs) {
            return $labs->sortBy('name'); // Sort labs by name for each campus
        });

        foreach ($ownersWithLabs as $campusId => $labs) {
            foreach ($labs as $lab) {
                $lab->pc_count = $this->getTotalPC(collect([$lab]), $currentMonth, $currentYear);
            }
        }

        return view('pages.computer-lab-report.index', [
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'ownersWithLabs' => $ownersWithLabs,
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
        $currentMonth = $request->input('month', date('n'));

        // Query to get Computer Labs grouped by campus, pemilik, and total PC
        $ownersWithLabsQuery = ComputerLab::with(['pemilik', 'campus'])
            ->select('id', 'name', 'pemilik_id', 'campus_id')
            ->where('publish_status', 1);

        // Filter based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $userCampusIds = $user->campus->pluck('id')->toArray();
            $ownersWithLabsQuery->whereIn('campus_id', $userCampusIds);
        } else {
            $ownersWithLabsQuery->where('pemilik_id', $user->id);
        }

        // Get the results and group by campus_id
        $ownersWithLabs = $ownersWithLabsQuery->get()->groupBy('campus_id')->map(function ($labs) {
            return $labs->sortBy('name'); // Sort labs by name for each campus
        });

        foreach ($ownersWithLabs as $campusId => $labs) {
            foreach ($labs as $lab) {
                $lab->pc_count = $this->getTotalPC(collect([$lab]), $currentMonth, $currentYear);
            }
        }

        // Render the view into HTML
        $html = view('pages.computer-lab-report.pdf', [
            'currentYear' =>  $currentYear,
            'currentMonth' =>  $currentMonth,
            'ownersWithLabs' => $ownersWithLabs,
            'username' => $user->name,
        ])->render();

        // Set up the filename
        $filename = "Laporan_Makmal_Komputer_{$currentYear}.pdf";

        // Initialize DomPDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Create the Dompdf instance and load the HTML
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->render();

        // Stream the generated PDF
        return $dompdf->stream($filename, ['Attachment' => true]);
    }
}
