<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\LabChecklist;
use App\Models\LabManagement;
use App\Models\Software;
use App\Models\User;
use App\Models\WorkChecklist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(auth()->id());
        $perPage = $request->input('perPage', 10);

        // Determine assigned labs based on user role
        $assignedComputerLabs = ($user->hasAnyRole(['Admin', 'Superadmin']))
            ? ComputerLab::where('publish_status', 1)->get()
            : (($user->hasRole('Pegawai Penyemak'))
                ? ComputerLab::where('publish_status', 1)
                ->where('campus_id', $user->campus_id)
                ->get()
                : $user->assignedComputerLabs);

        $labManagementList = LabManagement::latest()
            ->where('status', 'telah_disemak');

        // Filter labs based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin or superadmin can access all labs
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($user) {
                $query->where('campus_id', $user->campus_id);
            });
        } else {
            $labManagementList->whereIn('computer_lab_id', $assignedComputerLabs->pluck('id'));
        }

        // Filter by campus
        if ($request->filled('campus_id')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($request) {
                $query->where('campus_id', $request->input('campus_id'));
            });
        }

        // Filter by month and year if provided in the request
        if ($request->filled('month')) {
            $labManagementList->whereMonth('start_time', $request->input('month'));
        }

        if ($request->filled('year')) {
            $labManagementList->whereYear('start_time', $request->input('year'));
        }

        // Filter by staff
        if ($request->filled('pemilik_id')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($request) {
                $query->where('pemilik_id', $request->input('pemilik_id'));
            });
        }

        // Filter by computer lab
        if ($request->filled('computer_lab_id')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($request) {
                $query->where('computer_lab_id', $request->input('computer_lab_id'));
            });
        }

        $labManagementList = $labManagementList->paginate($perPage);
        $softwareList = Software::where('publish_status', 1)->get();
        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $computerLabList = ComputerLab::whereIn('id', $assignedComputerLabs->pluck('id'))->get();
        $pemilikList = User::role('Pemilik')
            ->whereIn('id', $assignedComputerLabs->pluck('pemilik_id'))
            ->get();

        $campusList = Campus::whereIn('id', $assignedComputerLabs->pluck('campus_id'))->get();
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();

        foreach ($labManagementList as $labManagement) {
            $labManagement->date = Carbon::parse($labManagement->start_time)->format('d-m-Y');
            $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
            $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
            $labManagement->startTime = Carbon::parse($labManagement->start_time)->format('H:i');
            $labManagement->endTime = Carbon::parse($labManagement->end_time)->format('H:i');
        }

        return view('pages.report.index', [
            'labManagementList' => $labManagementList,
            'perPage' => $perPage,
            'softwareList' => $softwareList,
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
            'pemilikList' => $pemilikList,
            'campusList' => $campusList,
            'workChecklists' => $workChecklists,
        ]);
    }

    public function show($id)
    {
        $user = User::find(auth()->id());
        $computerLabList = $user->hasRole('Pemilik')
            ? $user->assignedComputerLabs
            : ComputerLab::where('publish_status', 1)->get();
        $softwareList = Software::where('publish_status', 1)->get();
        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $labManagement = LabManagement::findOrFail($id);
        $labManagement->date = Carbon::parse($labManagement->start_time)->format('d-m-Y');
        $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
        $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        $labManagement->startTime = Carbon::parse($labManagement->start_time)->format('H:i');
        $labManagement->endTime = $labManagement->end_time ? Carbon::parse($labManagement->end_time)->format('H:i') : null;

        return view('pages.report.view', [
            'labManagement' => $labManagement,
            'softwareList' => $softwareList,
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
            'workChecklists' => $workChecklists,
            'selectedlabChecks' => $labManagement->lab_checklist_id,
            'selectedWorkChecklists' => $labManagement->software_id,
        ]);
    }

    public function downloadPdf($id)
    {
        $user = Auth::user();
        $username = $user->name;
    
        $labManagement = LabManagement::findOrFail($id);
        $softwareList = Software::where('publish_status', 1)->get();
        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
    
        $labManagement->date = Carbon::parse($labManagement->start_time)->format('d-m-Y');
        $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
        $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        $labManagement->startTime = Carbon::parse($labManagement->start_time)->format('H:i');
        $labManagement->endTime = $labManagement->end_time ? Carbon::parse($labManagement->end_time)->format('H:i') : null;
    
        $labName = $labManagement->computerLab->name;
        $month = $labManagement->month;
        $year = $labManagement->year;
    
        $filename = $month . ' ' . $year . ' - ' . 'Laporan Selenggara ' . $labName . '.pdf';
    
        // Load the HTML view content as a string
        $html = view('pages.report.pdf', [
            'labManagement' => $labManagement,
            'softwareList' => $softwareList,
            'labCheckList' => $labCheckList,
            'workChecklists' => $workChecklists,
            'username' => $username,
        ])->render();
    
        // Create DomPDF instance
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Enable if you have images or assets that are not local
    
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
    
        // Set paper size and orientation (optional)
        $dompdf->setPaper('A4', 'portrait');
    
        // Render the PDF
        $dompdf->render();
    
        // Stream the PDF to the browser
        return $dompdf->stream($filename, ['Attachment' => false]);
    }
    
    
}
