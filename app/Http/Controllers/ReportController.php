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
        $search = $request->input('search');
        $user = User::find(auth()->id());
        $perPage = $request->input('perPage', 10);

        // Determine assigned labs based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin or superadmin can access all labs
            $assignedComputerLabs = ComputerLab::where('publish_status', 1)->get();
            $labManagementList = LabManagement::latest()->where('status', 'telah_disemak');
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            // Pegawai Penyemak can access labs for their campus
            $assignedComputerLabs = ComputerLab::where('publish_status', 1)
                ->where('campus_id', $user->campus_id)
                ->get();
            $labManagementList = LabManagement::latest()
                ->where('status', 'telah_disemak')
                ->whereHas('computerLab', function ($query) use ($user) {
                    // Filter labs based on the campuses associated with the user
                    $query->whereIn('campus_id', $user->campus->pluck('id'));
                });
        } else {
            // For other roles, use assigned computer labs
            $assignedComputerLabs = $user->assignedComputerLabs;
            $labManagementList = LabManagement::latest()
                ->where('status', 'telah_disemak')
                ->whereIn('computer_lab_id', $assignedComputerLabs->pluck('id'));
        }

        // Apply search filter if present
        if ($search) {
            $labManagementList->where('remarks_submitter', 'LIKE', "%$search%");
        }

        // Filter by campus
        if ($request->filled('campus_id')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($request) {
                $query->where('campus_id', $request->input('campus_id'));
            });
        }

        // Filter by computer lab category
        if ($request->filled('category')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($request) {
                $query->where('category', $request->input('category'));
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
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();

        // Filter based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin and Superadmin see all computer labs, owners, and campuses
            $computerLabList = ComputerLab::where('publish_status', 1)->get();
            $pemilikList = User::role('Pemilik')->get();
            $campusList = Campus::with('computerLab')->get();
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $userCampusIds = $user->campus->pluck('id')->toArray();

            // Pegawai Penyemak only sees data related to campuses they are assigned to
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->whereIn('campus_id', $userCampusIds)
                ->get();

            $pemilikList = User::role('Pemilik')
                ->whereHas('assignedComputerLabs', function ($query) use ($userCampusIds) {
                    $query->whereIn('campus_id', $userCampusIds);
                })
                ->get();

            $campusList = Campus::with('computerLab')
                ->whereIn('id', $userCampusIds)
                ->get();
        } else {
            // Regular Pemilik only sees their own labs, associated owners, and campuses
            $assignedComputerLabs = $user->assignedComputerLabs;
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->whereIn('id', $assignedComputerLabs->pluck('id'))
                ->get();

            $pemilikList = User::role('Pemilik')
                ->whereIn('id', $assignedComputerLabs->pluck('pemilik_id'))
                ->get();

            $campusIds = $assignedComputerLabs->pluck('campus_id')->unique();
            $campusList = Campus::with('computerLab')->whereIn('id', $campusIds)->get();
        }

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
        ]);
    }

    public function downloadPdf($id)
    {
        $user = Auth::user();
        $username = $user->name;
        $currentDate = now()->format('d M Y');

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

        $path = public_path('assets/images/Logo-Infostruktur.svg');
        $logoData = base64_encode(file_get_contents($path));
        $logoMimeType = mime_content_type($path);

        // Load the HTML view content as a string
        $html = view('pages.report.pdf', [
            'labManagement' => $labManagement,
            'softwareList' => $softwareList,
            'labCheckList' => $labCheckList,
            'workChecklists' => $workChecklists,
            'username' => $username,
            'currentDate' => $currentDate,
            'logoBase64' => "data:{$logoMimeType};base64,{$logoData}",
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

        // Stream the PDF to the browser
        return $dompdf->stream($filename, ['Attachment' => false]);
    }
}
