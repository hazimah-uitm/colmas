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
            // Get campuses associated with the user and eagerly load 'computerLab' relationship
            $campusList = $user->campus()->with('computerLab')->get();        
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
                $maintainedLabsThisMonth = LabManagement::query()  
                    ->whereMonth('end_time', $month)  
                    ->whereYear('end_time', $currentYear)  
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
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->where('campus_id', $campus->id)
                ->get();
    
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
        $html = view('pages.yearly-report.pdf', [
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
