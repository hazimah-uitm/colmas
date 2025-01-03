<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\LabChecklist;
use App\Models\LabManagement;
use App\Models\MaintenanceRecord;
use App\Models\Software;
use App\Models\User;
use App\Models\WorkChecklist;
use App\Notifications\ReportNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabManagementController extends Controller
{
    use SoftDeletes;

    private function getLabManagementData(Request $request, $applySearch = false)
    {
        $perPage = $request->input('perPage', 10);
        $user = User::find(auth()->id());
    
        $labManagementList = LabManagement::latest()->where('status', '<>', 'telah_disemak');
    
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin and Superadmin see everything
            $computerLabList = ComputerLab::where('publish_status', 1)->get();
            $pemilikList = User::role('Pemilik')->get();
            $campusList = Campus::with('computerLab')->get();
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            // Pegawai Penyemak sees data related to their campuses
            $userCampusIds = $user->campus->pluck('id')->toArray();
    
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
    
            $labManagementList->whereHas('computerLab', function ($query) use ($userCampusIds) {
                $query->whereIn('campus_id', $userCampusIds);
            });
        } else {
            // Pemilik sees only their data
            $assignedComputerLabs = $user->assignedComputerLabs;
    
            $computerLabList = ComputerLab::where('publish_status', 1)
                ->whereIn('id', $assignedComputerLabs->pluck('id'))
                ->get();
    
            $pemilikList = User::where('id', $user->id)->get(); // Only the logged-in user as Pemilik
    
            $campusIds = $assignedComputerLabs->pluck('campus_id')->unique();
            $campusList = Campus::with('computerLab')->whereIn('id', $campusIds)->get();
    
            $labManagementList->whereHas('computerLab', function ($query) use ($assignedComputerLabs) {
                $query->whereIn('id', $assignedComputerLabs->pluck('id'));
            });
        }
    
        // Apply additional filters if needed
        if ($applySearch) {
            if ($request->filled('search')) {
                $search = $request->input('search');
                $labManagementList->where('remarks_submitter', 'LIKE', "%$search%");
            }
            if ($request->filled('campus_id')) {
                $selectedCampusId = $request->input('campus_id');
                $labManagementList->whereHas('computerLab', function ($query) use ($selectedCampusId) {
                    $query->where('campus_id', $selectedCampusId);
                });
                $computerLabList = $computerLabList->where('campus_id', $selectedCampusId);
                $pemilikList = $pemilikList->filter(function ($pemilik) use ($selectedCampusId) {
                    return $pemilik->assignedComputerLabs->pluck('campus_id')->contains($selectedCampusId);
                });
            }
            if ($request->filled('pemilik_id')) {
                $pemilikId = $request->input('pemilik_id');
                $labManagementList->whereHas('computerLab', function ($query) use ($pemilikId) {
                    $query->where('pemilik_id', $pemilikId);
                });
                $computerLabList = $computerLabList->where('pemilik_id', $pemilikId);
            }
            if ($request->filled('computer_lab_id')) {
                $computerLabId = $request->input('computer_lab_id');
                $labManagementList->whereHas('computerLab', function ($query) use ($computerLabId) {
                    $query->where('id', $computerLabId);
                });
            }
            if ($request->filled('month')) {
                $labManagementList->whereMonth('start_time', $request->input('month'));
            }
            if ($request->filled('year')) {
                $labManagementList->whereYear('start_time', $request->input('year'));
            }
        }
    
        $labManagementList = $labManagementList->paginate($perPage);
    
        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
    
        foreach ($labManagementList as $labManagement) {
            $startTime = Carbon::parse($labManagement->start_time)->timezone('Asia/Kuching');
            $endTime = Carbon::parse($labManagement->end_time)->timezone('Asia/Kuching');
            $labManagement->date = $startTime->format('d-m-Y');
            $labManagement->month = $startTime->format('F');
            $labManagement->year = $startTime->format('Y');
            $labManagement->startTime = $startTime->format('H:i');
            $labManagement->endTime = $endTime->format('H:i');
        }
    
        return [
            'labManagementList' => $labManagementList,
            'perPage' => $perPage,
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
            'pemilikList' => $pemilikList,
            'campusList' => $campusList,
            'workChecklists' => $workChecklists,
        ];
    }
    

    public function index(Request $request)
    {
        $data = $this->getLabManagementData($request);
        return view('pages.lab-management.index', $data);
    }


    public function create()
    {
        $user = User::find(auth()->id());
        // Only retrieve assigned computer labs for Pemilik
        $computerLabList = $user->hasRole('Pemilik')
            ? $user->assignedComputerLabs()->with('software')->get()
            : ComputerLab::where('publish_status', 1)->with('software')->get();

        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        return view('pages.lab-management.create', [
            'save_route' => route('lab-management.store'),
            'str_mode' => 'Tambah',
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'computer_lab_id' => 'required',
            'lab_checklist_id' => 'required|array',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'computer_no' => 'required',
            'pc_maintenance_no' => 'nullable',
            'pc_unmaintenance_no' => 'nullable',
            'pc_damage_no' => 'nullable',
            'remarks_submitter' => 'nullable',
            'remarks_checker' => 'nullable',
            'status' => 'required',
            'checked_by' => 'nullable',
            'checked_at' => 'nullable',
            'submitted_by' => 'nullable',
            'submitted_at' => 'nullable',
        ], [
            'computer_lab_id.required' => 'Sila pilih makmal komputer.',
            'lab_checklist_id.required' => 'Sila isi senarai semak makmal sebelum hantar.',
        ]);

        $startDateMonthYear = Carbon::parse($request->start_time)->timezone('Asia/Kuching')->format('Y-m');

        $existingRecord = LabManagement::where('computer_lab_id', $request->computer_lab_id)
            ->withTrashed()
            ->whereRaw("DATE_FORMAT(start_time, '%Y-%m') = ?", [$startDateMonthYear])
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withErrors(['error' => 'Rekod selenggara makmal komputer pada bulan dan tahun tersebut telah wujud atau masih dalam rekod telah dipadam'])
                ->withInput();
        }

        $labManagement = new LabManagement();
        $labManagement->fill($request->all());

        // Record start time if status is "draft" and it's not provided in the request
        if ($request->status == 'draft' && !$request->has('start_time')) {
            $labManagement->start_time = Carbon::now();
        }

        // Record end time if status is "dihantar"
        if ($request->status == 'dihantar') {
            $labManagement->end_time = Carbon::now();
        } else {
            $labManagement->end_time = null;
        }

        $labManagement->lab_checklist_id = $request->input('lab_checklist_id', []);
        $labManagement->pc_unmaintenance_no = $labManagement->computer_no - $labManagement->pc_maintenance_no - $labManagement->pc_damage_no;
        $labManagement->save();

        return redirect()->route('lab-management')
            ->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $user = User::find(auth()->id());
        $computerLabList = $user->hasRole('Pemilik')
            ? $user->assignedComputerLabs
            : ComputerLab::where('publish_status', 1)->get();

        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $labManagement = LabManagement::findOrFail($id);

        // Check for MaintenanceRecord
        $maintenanceRecord = MaintenanceRecord::find($id);
        $entryOption = $maintenanceRecord ? $maintenanceRecord->entry_option : null;

        $labManagement->date = Carbon::parse($labManagement->start_time)->format('d-m-Y');
        $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
        $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        $labManagement->startTime = Carbon::parse($labManagement->start_time)->format('H:i');
        $labManagement->endTime = $labManagement->end_time ? Carbon::parse($labManagement->end_time)->format('H:i') : null;

        return view('pages.lab-management.view', [
            'labManagement' => $labManagement,
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
            'workChecklists' => $workChecklists,
            'selectedlabChecks' => $labManagement->lab_checklist_id,
            'entryOption' => $entryOption,
        ]);
    }


    public function edit(Request $request, $id)
    {
        $user = User::find(auth()->id());
        $computerLabList = $user->hasRole('Pemilik')
            ? $user->assignedComputerLabs
            : ComputerLab::where('publish_status', 1)->get();
        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $labManagement = LabManagement::findOrFail($id);

        return view('pages.lab-management.edit', [
            'save_route' => route('lab-management.update', $id),
            'str_mode' => 'Kemas Kini',
            'labManagement' => $labManagement,
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
            'selectedlabChecks' => $labManagement->lab_checklist_id,
            'selectedWorkChecklists' => $labManagement->software_id,
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'computer_lab_id' => 'required',
            'lab_checklist_id' => 'required|array',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'computer_no' => 'required',
            'pc_maintenance_no' => 'nullable',
            'pc_unmaintenance_no' => 'nullable',
            'pc_damage_no' => 'nullable',
            'remarks_submitter' => 'nullable',
            'remarks_checker' => 'nullable',
            'status' => 'required',
            'checked_by' => 'nullable',
            'checked_at' => 'nullable',
            'submitted_by' => 'nullable',
            'submitted_at' => 'nullable',
        ], [
            'computer_lab_id.required' => 'Sila pilih makmal komputer.',
            'lab_checklist_id.required' => 'Sila isi senarai semak makmal sebelum hantar.',
        ]);

        $labManagement = LabManagement::findOrFail($id);


        // Normalize the start date to Year-Month format
        $startDateMonthYear = Carbon::parse($request->start_time)->timezone('Asia/Kuching')->format('Y-m');

        // Check for existing record with the same computer_lab_id and start_date (month and year), excluding the current record
        $existingRecord = LabManagement::where('computer_lab_id', $request->computer_lab_id)
            ->withTrashed()
            ->whereRaw("DATE_FORMAT(start_time, '%Y-%m') = ?", [$startDateMonthYear])
            ->where('id', '<>', $id)
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withErrors(['error' => 'Rekod selenggara makmal komputer pada bulan dan tahun tersebut telah wujud'])
                ->withInput();
        }

        // Record start time only if it is not already set and the status is "draft"
        if ($request->status == 'draft' && !$labManagement->start_time) {
            $labManagement->start_time = Carbon::now();
        }

        // Record end time if status is "dihantar"
        if ($request->status == 'dihantar') {
            $labManagement->end_time = Carbon::now();
        } else {
            $labManagement->end_time = null;
        }

        // Update fields
        $labManagement->fill($request->all());
        $labManagement->lab_checklist_id = $request->input('lab_checklist_id', []);
        $labManagement->pc_unmaintenance_no = $labManagement->computer_no - $labManagement->pc_maintenance_no - $labManagement->pc_damage_no;
        $labManagement->save();

        return redirect()->route('lab-management')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function submit(Request $request, $id)
    {
        $user = User::find(auth()->id());
        $labManagement = LabManagement::findOrFail($id);
        $labManagement->submitted_by = $user->id;
        $labManagement->submitted_at = Carbon::now();
        $labManagement->remarks_submitter = $request->input('remarks_submitter');
        $assignedComputerLabs = $user->hasRole('Pemilik')
            ? $user->assignedComputerLabs
            : ComputerLab::where('publish_status', 1)->get();

        // Check if the lab belongs to the user's assigned labs
        if ($user->hasRole('Pemilik') && !$assignedComputerLabs->contains($labManagement->computerLab)) {
            abort(403, 'Unauthorized action.');
        }

        $labManagement->status = 'dihantar';

        // Set end_time only if status is 'dihantar'
        if ($labManagement->status == 'dihantar') {
            $labManagement->end_time = Carbon::now();
        }

        $labManagement->save();

        $submitterName = $labManagement->submittedBy->name;

        $this->sendNotificationEmail($labManagement, $submitterName);

        return redirect()->route('lab-management')->with('success', 'Laporan berjaya dihantar');
    }

    public function reportDetail(Request $request, $id)
    {
        $user = User::find(auth()->id());
        $computerLabList = $user->hasRole('Pemilik')
            ? $user->assignedComputerLabs
            : ComputerLab::where('publish_status', 1)->get();
        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $labManagement = LabManagement::findOrFail($id);
        $labManagement->date = Carbon::parse($labManagement->start_time)->format('d-m-Y');
        $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
        $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        $labManagement->startTime = Carbon::parse($labManagement->start_time)->format('H:i');
        $labManagement->endTime = $labManagement->end_time ? Carbon::parse($labManagement->end_time)->format('H:i') : null;

        return view('pages.lab-management.report-detail', [
            'save_route' => route('lab-management.submit', $id),
            'labManagement' => $labManagement,
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
            'workChecklists' => $workChecklists,
            'selectedlabChecks' => $labManagement->lab_checklist_id,
            'selectedWorkChecklists' => $labManagement->software_id,
        ]);
    }

    protected function sendNotificationEmail(LabManagement $labManagement, $submitterName)
    {
        $pemilik = $labManagement->submittedBy;

        $campuses = $pemilik->campus;

        // Retrieve all Pegawai Penyemak associated with the pemilik's campuses
        $pegawaiPenyemak = User::role('Pegawai Penyemak')
            ->whereHas('campus', function ($query) use ($campuses) {
                $query->whereIn('campuses.id', $campuses->pluck('id'));
            })
            ->get();

        if ($pegawaiPenyemak->isNotEmpty()) {
            // Notify each Pegawai Penyemak
            foreach ($pegawaiPenyemak as $penyemak) {
                $penyemak->notify(new ReportNotification($labManagement, $penyemak->name, $submitterName));
            }
        } else {
            Log::error('No Pegawai Penyemak found for the campuses with IDs: ' . implode(',', $campuses->pluck('id')->toArray()));
        }
    }

    public function check(Request $request, $id)
    {
        $user = User::find(auth()->id());
        $labManagement = LabManagement::findOrFail($id);
        $labManagement->checked_by = $user->id;
        $labManagement->checked_at = Carbon::now();
        $labManagement->status = 'telah_disemak';
        $labManagement->remarks_checker = $request->input('remarks_checker');
        $labManagement->save();

        return redirect()->route('report')->with('success', 'Laporan telah disemak');
    }

    public function checkDetail(Request $request, $id)
    {
        $user = User::find(auth()->id());
        $computerLabList = $user->hasRole('Pemilik')
            ? $user->assignedComputerLabs
            : ComputerLab::where('publish_status', 1)->get();
        $labCheckList = LabChecklist::where('publish_status', 1)->get();
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $labManagement = LabManagement::findOrFail($id);
        $labManagement->date = Carbon::parse($labManagement->start_time)->format('d-m-Y');
        $labManagement->month = Carbon::parse($labManagement->start_time)->format('F');
        $labManagement->year = Carbon::parse($labManagement->start_time)->format('Y');
        $labManagement->startTime = Carbon::parse($labManagement->start_time)->format('H:i');
        $labManagement->endTime = Carbon::parse($labManagement->end_time)->format('H:i');

        return view('pages.lab-management.check-detail', [
            'save_route' => route('lab-management.check', $id),
            'labManagement' => $labManagement,
            'labCheckList' => $labCheckList,
            'computerLabList' => $computerLabList,
            'workChecklists' => $workChecklists,
            'selectedlabChecks' => $labManagement->lab_checklist_id,
            'selectedWorkChecklists' => $labManagement->software_id,
        ]);
    }

    public function search(Request $request)
    {
        $data = $this->getLabManagementData($request, true);
        return view('pages.lab-management.index', $data);
    }

    public function destroy(Request $request, $id)
    {
        $labManagement = LabManagement::findOrFail($id);

        $labManagement->delete();

        return redirect()->route('lab-management')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        // Get the authenticated user
        $user = User::find(auth()->id());

        // Initialize the query for trashed LabManagement records
        $trashQuery = LabManagement::onlyTrashed();

        // Filter based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
            // Admin and Superadmin can access all trashed records
        } elseif ($user->hasRole('Pegawai Penyemak')) {
            // Filter by campuses associated with the user
            $trashQuery->whereHas('computerLab', function ($query) use ($user) {
                $query->whereIn('campus_id', $user->campus->pluck('id'));
            });
        } else {
            // Filter by assigned computer labs for other roles
            $assignedComputerLabs = $user->assignedComputerLabs;
            $trashQuery->whereIn('computer_lab_id', $assignedComputerLabs->pluck('id'));
        }

        // Paginate the filtered trash list
        $trashList = $trashQuery->latest()->paginate($perPage);

        // Fetch additional lists
        $softwareList = Software::where('publish_status', 1)->get();
        $labCheckList = LabChecklist::where('publish_status', 1)->get();

        // Format dates for trashed records
        foreach ($trashList as $trash) {
            $startTime = Carbon::parse($trash->start_time)->timezone('Asia/Kuching');
            $endTime = Carbon::parse($trash->end_time)->timezone('Asia/Kuching');

            $trash->date = $startTime->format('d-m-Y');
            $trash->month = $startTime->format('F');
            $trash->year = $startTime->format('Y');
            $trash->startTime = $startTime->format('H:i');
            $trash->endTime = $endTime->format('H:i');
        }

        return view('pages.lab-management.trash', [
            'trashList' => $trashList,
            'perPage' => $perPage,
            'softwareList' => $softwareList,
            'labCheckList' => $labCheckList,
        ]);
    }

    public function restore($id)
    {
        LabManagement::withTrashed()->where('id', $id)->restore();

        return redirect()->route('lab-management')->with('success', 'Maklumat berjaya dikembalikan');
    }

    public function forceDelete($id)
    {
        // Permanently delete the record
        LabManagement::withTrashed()->where('id', $id)->forceDelete();

        return redirect()->route('lab-management.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
