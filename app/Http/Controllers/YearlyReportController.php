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

        // Filter by computer lab category
        if ($request->filled('category')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($request) {
                $query->where('category', $request->input('category'));
            });
        }

        $campusData = [];

        foreach ($campusList as $campus) {
            if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
                // Admin and Superadmin see all labs in each campus
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->get();
            } elseif ($user->hasRole('Pegawai Penyemak')) {
                $userCampusIds = $user->campus->pluck('id')->toArray();

                // Pegawai Penyemak sees labs only in campuses they are associated with
                if (in_array($campus->id, $userCampusIds)) {
                    $computerLabList = ComputerLab::where('publish_status', 1)
                        ->where('campus_id', $campus->id)
                        ->get();
                } else {
                    $computerLabList = collect(); // Empty collection if they don’t have access
                }
            } else {
                // Regular Pemilik only sees labs they own
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->where('pemilik_id', $user->id)
                    ->get();
            }

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
        $currentDate = now()->format('d M Y');

        // Filter labs based on user role
        $labManagementList = LabManagement::query();
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Superadmin can see all labs
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            $labManagementList->whereHas('computerLab', function ($query) use ($user) {
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
            $campusList = $user->campus()->with('computerLab')->get();
        } else {
            $assignedComputerLabs = $user->assignedComputerLabs;
            $campusIds = $assignedComputerLabs->pluck('campus_id')->unique();
            $campusList = Campus::with('computerLab')->whereIn('id', $campusIds)->get();
        }

        $campusData = [];

        foreach ($campusList as $campus) {
            if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
                // Admin and Superadmin see all labs in each campus
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->get();
            } elseif ($user->hasRole('Pegawai Penyemak')) {
                $userCampusIds = $user->campus->pluck('id')->toArray();

                // Pegawai Penyemak sees labs only in campuses they are associated with
                if (in_array($campus->id, $userCampusIds)) {
                    $computerLabList = ComputerLab::where('publish_status', 1)
                        ->where('campus_id', $campus->id)
                        ->get();
                } else {
                    $computerLabList = collect(); // Empty collection if they don’t have access
                }
            } else {
                // Regular Pemilik only sees labs they own
                $computerLabList = ComputerLab::where('publish_status', 1)
                    ->where('campus_id', $campus->id)
                    ->where('pemilik_id', $user->id)
                    ->get();
            }

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

        $path = public_path('assets/images/Logo-Infostruktur.svg');
        $logoData = base64_encode(file_get_contents($path));
        $logoMimeType = mime_content_type($path);

        // Render the view into HTML
        $html = view('pages.yearly-report.pdf', [
            'months' => $months,
            'campusData' => $campusData,
            'currentYear' => $currentYear,
            'username' => $user->name,
            'currentDate' => $currentDate,
            'logoBase64' => "data:{$logoMimeType};base64,{$logoData}",
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


        // Stream the generated PDF
        return $dompdf->stream($filename, ['Attachment' => false]);
    }
}
