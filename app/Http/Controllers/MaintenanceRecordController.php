<?php

namespace App\Http\Controllers;

use App\Models\LabManagement;
use App\Models\MaintenanceRecord;
use App\Models\WorkChecklist;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class MaintenanceRecordController extends Controller
{
    use SoftDeletes;

    public function index(Request $request, $labManagementId)
    {
        $perPage = $request->input('perPage', 10);

        $labManagement = LabManagement::findOrFail($labManagementId);
        $month = Carbon::parse($labManagement->start_time)->format('F');
        $year = Carbon::parse($labManagement->start_time)->format('Y');

        $maintenanceRecordList = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->latest()
            ->paginate($perPage);

        $workChecklists = WorkChecklist::where('publish_status', 1)->get();

        return view('pages.maintenance-record.index', [
            'maintenanceRecordList' => $maintenanceRecordList,
            'workChecklists' => $workChecklists,
            'perPage' => $perPage,
            'labManagement' => $labManagement,
            'month' => $month,
            'year' => $year
        ]);
    }


    public function create($labManagementId, Request $request)
    {
        $ipAddress = $request->header('X-Forwarded-For') ? explode(',', $request->header('X-Forwarded-For'))[0] : $request->ip();
        $computerName = null;

        $labManagement = LabManagement::findOrFail($labManagementId);
        $computerLabName = $labManagement->computerLab->name;

        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $defaultEntryOption = old('entry_option', 'automatik');

        return view('pages.maintenance-record.create', [
            'save_route' => route('lab-management.maintenance-records.store', ['labManagement' => $labManagementId]),
            'str_mode' => 'Tambah',
            'workChecklists' => $workChecklists,
            'ipAddress' => $ipAddress,
            'computerName' => $computerName,
            'labManagement' => $labManagement,
            'computerLabName' => $computerLabName,
            'defaultEntryOption' => $defaultEntryOption
        ]);
    }


    public function store(Request $request, $labManagementId)
    {
        $request->validate([
            'computer_name'     => 'nullable|required_if:entry_option,pc_rosak|required_if:entry_option,manual',
            'ip_address'        => 'nullable',
            'lab_management_id' => 'required',
            'work_checklist_id' => 'nullable|array|required_if:entry_option,automatik|required_if:entry_option,manual',
            'vms_no'            => 'required_if:entry_option,pc_rosak|nullable',
            'aduan_unit_no'     => 'required_if:entry_option,manual|nullable',
            'remarks'           => 'nullable|required_if:entry_option,pc_rosak|required_if:entry_option,manual',
            'entry_option'      => 'required',
        ], [
            'computer_name.required_if'      => 'Sila isi nama komputer sebelum hantar',
            'work_checklist_id.required_if'  => 'Sila semak proses kerja sebelum hantar',
            'vms_no.required_if'             => 'Sila isi VMS No. sebelum hantar',
            'aduan_unit_no.required_if'      => 'Sila isi No. Aduan Unit sebelum hantar',
            'remarks.required_if'            => 'Sila isi Ulasan sebelum hantar',
            'lab_management_id.required'     => 'ID pengurusan makmal diperlukan.',
            'entry_option.required'          => 'Pilihan kemasukan rekod diperlukan.',
        ]);

        // Custom validation logic for work_checklist_id
        $entryOption = $request->input('entry_option');
        $workChecklistIds = $request->input('work_checklist_id', []);

        // Define the required IDs and retrieve their titles
        $requiredIds = [];
        if ($entryOption === 'manual') {
            $requiredIds = [1, 2, 3, 4, 5];
        } elseif ($entryOption === 'automatik') {
            $requiredIds = [1, 2, 3, 4, 5, 6];
        }

        $missingTitles = [];
        if ($requiredIds) {
            $missingIds = array_diff($requiredIds, $workChecklistIds);
            if ($missingIds) {
                $missingTitles = WorkChecklist::whereIn('id', $missingIds)->pluck('title')->toArray();
            }
        }

        if (!empty($missingTitles)) {
            $errorMessages = implode(', ', $missingTitles);
            return redirect()->back()->withErrors(['work_checklist_id' => 'Sila semak proses kerja: ' . $errorMessages])->withInput();
        }

        $computerName = $request->input('computer_name');
        $ipAddress = $request->input('entry_option') === 'automatik' ? $request->input('hidden_ip_address_automatik') : null;

        // Remove spaces from the computer name and convert to uppercase
        if ($computerName) {
            $computerName = strtoupper(str_replace(' ', '', $computerName));
        }

        // Retrieve the lab management record to get the start_time
        $labManagement = LabManagement::find($labManagementId);
        if (!$labManagement) {
            return redirect()->back()->withErrors('Lab management record not found.');
        }

        // Convert start_time to Carbon instance
        $startTime = Carbon::parse($labManagement->start_time);
        $month = $startTime->format('m');
        $year = $startTime->format('Y');

        // Check for existing records with the same computer_name, ip_address, and lab_management_id within the same month and year
        $existingRecord = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->withTrashed()
            ->where('computer_name', $computerName)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withInput() // Preserve the old input data
                ->withErrors('Rekod selenggara PC bagi nama komputer pada bulan dan tahun tersebut telah wujud atau masih dalam rekod dipadam');
        }

        // Only check for duplicate IP address if entry_option is 'automatik'
        if ($entryOption === 'automatik' && $ipAddress) {
            $existingRecord = MaintenanceRecord::where('lab_management_id', $labManagementId)
                ->withTrashed()
                ->where('ip_address', $ipAddress)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->first();

            if ($existingRecord) {
                return redirect()->back()
                    ->withInput() // Preserve the old input data
                    ->withErrors('Rekod selenggara PC bagi alamat IP pada bulan dan tahun tersebut telah wujud atau masih dalam rekod dipadam');
            }
        }

        // Check for unique aduan_unit_no and vms_no
        if ($request->input('aduan_unit_no') && MaintenanceRecord::where('aduan_unit_no', $request->input('aduan_unit_no'))->exists()) {
            return redirect()->back()->withInput()->withErrors('Aduan unit no sudah wujud.');
        }

        if ($request->input('vms_no') && MaintenanceRecord::where('vms_no', $request->input('vms_no'))->exists()) {
            return redirect()->back()->withInput()->withErrors('VMS no sudah wujud.');
        }

        $maintenanceRecord = new MaintenanceRecord();
        $maintenanceRecord->fill($request->except('entry_option', 'hidden_computer_name_manual', 'hidden_ip_address_automatik', 'hidden_ip_address_manual'));

        $maintenanceRecord->lab_management_id = $labManagementId;
        $maintenanceRecord->entry_option = $request->input('entry_option');
        $maintenanceRecord->computer_name = $computerName;
        $maintenanceRecord->ip_address = $ipAddress;

        if (in_array($request->input('entry_option'), ['automatik', 'manual'])) {
            $maintenanceRecord->work_checklist_id = $request->input('work_checklist_id', []);
        } else {
            $maintenanceRecord->work_checklist_id = [];
        }

        $maintenanceRecord->save();

        // Count active PC that have been maintained
        $pcMaintenanceNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->whereNull('deleted_at')
            ->count();

        // Count PC damage numbers where entry_option is 'pc_rosak' and deleted_at is null
        $pcDamageNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->where('entry_option', 'pc_rosak')
            ->whereNull('deleted_at')
            ->count();

        // Get the LabManagement record
        $labManagement = LabManagement::find($labManagementId);
        $labManagement->pc_damage_no = $pcDamageNo;
        $labManagement->pc_maintenance_no = $pcMaintenanceNo - $pcDamageNo;

        // Recalculate pc_unmaintenance_no as the difference between computer_no and pc_maintenance_no
        $labManagement->pc_unmaintenance_no = $labManagement->computer_no - $labManagement->pc_maintenance_no - $labManagement->pc_damage_no;

        $labManagement->save();

        return redirect()->route('lab-management.maintenance-records', ['labManagement' => $labManagementId])
            ->with('success', 'Maklumat berjaya disimpan');
    }


    public function show($labManagementId, $id)
    {
        $maintenanceRecord = MaintenanceRecord::findOrFail($id);
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $labManagement = LabManagement::findOrFail($labManagementId);
        $computerLabName = $labManagement->computerLab->name;
        $entryOption = $maintenanceRecord->entry_option;

        return view('pages.maintenance-record.view', [
            'maintenanceRecord' => $maintenanceRecord,
            'labManagement' => $labManagement,
            'computerLabName' => $computerLabName,
            'workChecklists' => $workChecklists,
            'entryOption' => $entryOption,
        ]);
    }

    public function edit($labManagementId, $recordId, Request $request)
    {
        $labManagement = LabManagement::findOrFail($labManagementId);
        $maintenanceRecord = MaintenanceRecord::findOrFail($recordId);
        $computerLabName = $labManagement->computerLab->name;

        // Determine computer name and IP address based on entry option
        $entryOption = $maintenanceRecord->entry_option;
        $ipAddressAuto = $request->header('X-Forwarded-For') ? explode(',', $request->header('X-Forwarded-For'))[0] : $request->ip();

        $computerName = $maintenanceRecord->computer_name;
        $ipAddress = $entryOption == 'automatik' ? $ipAddressAuto : $maintenanceRecord->ip_address;

        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $selectedWorkProcesses = $maintenanceRecord->work_checklist_id ?? [];

        return view('pages.maintenance-record.edit', [
            'save_route' => route('lab-management.maintenance-records.update', ['labManagement' => $labManagementId, 'record' => $recordId]),
            'str_mode' => 'Edit',
            'workChecklists' => $workChecklists,
            'computerName' => $computerName,
            'ipAddress' => $ipAddress,
            'ipAddressAuto' => $ipAddressAuto,
            'labManagement' => $labManagement,
            'computerLabName' => $computerLabName,
            'maintenanceRecord' => $maintenanceRecord,
            'selectedWorkProcesses' => $selectedWorkProcesses,
            'entryOption' => old('entry_option', $entryOption),
        ]);
    }

    public function update(Request $request, $labManagementId, $recordId)
    {
        $request->validate([
            'computer_name'     => 'nullable|required_if:entry_option,pc_rosak|required_if:entry_option,manual',
            'ip_address'        => 'nullable',
            'lab_management_id' => 'required',
            'work_checklist_id' => 'nullable|array|required_if:entry_option,automatik|required_if:entry_option,manual',
            'vms_no'            => 'required_if:entry_option,pc_rosak|nullable',
            'aduan_unit_no'     => 'required_if:entry_option,manual|nullable',
            'remarks'           => 'nullable|required_if:entry_option,pc_rosak|required_if:entry_option,manual',
            'entry_option'      => 'required',
        ], [
            'computer_name.required_if'      => 'Sila isi nama komputer sebelum hantar',
            'work_checklist_id.required_if'  => 'Sila semak proses kerja sebelum hantar',
            'vms_no.required_if'             => 'Sila isi VMS No. sebelum hantar',
            'aduan_unit_no.required_if'      => 'Sila isi No. Aduan Unit sebelum hantar',
            'remarks.required_if'            => 'Sila isi Ulasan sebelum hantar',
            'lab_management_id.required'     => 'ID pengurusan makmal diperlukan.',
            'entry_option.required'          => 'Pilihan kemasukan rekod diperlukan.',
        ]);

        // Custom validation logic for work_checklist_id
        $entryOption = $request->input('entry_option');
        $workChecklistIds = $request->input('work_checklist_id', []);

        // Define the required IDs and retrieve their titles
        $requiredIds = [];
        if ($entryOption === 'manual') {
            $requiredIds = [1, 2, 3, 4, 5];
        } elseif ($entryOption === 'automatik') {
            $requiredIds = [1, 2, 3, 4, 5, 6];
        }

        $missingTitles = [];
        if ($requiredIds) {
            $missingIds = array_diff($requiredIds, $workChecklistIds);
            if ($missingIds) {
                $missingTitles = WorkChecklist::whereIn('id', $missingIds)->pluck('title')->toArray();
            }
        }

        if (!empty($missingTitles)) {
            $errorMessages = implode(', ', $missingTitles);
            return redirect()->back()->withErrors(['work_checklist_id' => 'Sila semak proses kerja: ' . $errorMessages])->withInput();
        }

        $entryOption = $request->input('entry_option');

        $computerName = $request->input('computer_name');
        $ipAddress = $entryOption === 'automatik' ? $request->input('hidden_ip_address_automatik') : $request->input('ip_address');

        // Remove spaces from the computer name and convert to uppercase
        if ($computerName) {
            $computerName = strtoupper(str_replace(' ', '', $computerName));
        }

        $maintenanceRecord = MaintenanceRecord::findOrFail($recordId);
        $labManagement = LabManagement::findOrFail($labManagementId);

        // Check for existing records with the same computer_name, ip_address, and lab_management_id within the same month and year
        $startTime = Carbon::parse($labManagement->start_time);
        $month = $startTime->format('m');
        $year = $startTime->format('Y');

        $existingRecord = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->withTrashed()
            ->where('computer_name', $computerName)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('id', '!=', $recordId)
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withInput() // Preserve the old input data
                ->withErrors('Rekod selenggara PC bagi nama komputer pada bulan dan tahun tersebut telah wujud atau masih dalam rekod dipadam');
        }


        if ($entryOption === 'automatik' && $ipAddress) {
            $existingRecord = MaintenanceRecord::where('lab_management_id', $labManagementId)
                ->withTrashed()
                ->where('ip_address', $ipAddress)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('id', '!=', $recordId)
                ->first();
        }

        if ($existingRecord) {
            return redirect()->back()
                ->withInput() // Preserve the old input data
                ->withErrors('Rekod selenggara PC bagi alamat IP pada bulan dan tahun tersebut telah wujud atau masih dalam rekod dipadam');
        }

        if ($request->input('aduan_unit_no') && MaintenanceRecord::where('aduan_unit_no', $request->input('aduan_unit_no'))->where('id', '!=', $recordId)->exists()) {
            return redirect()->back()->withInput()->withErrors('No. Aduan Unit no sudah wujud.');
        }

        if ($request->input('vms_no') && MaintenanceRecord::where('vms_no', $request->input('vms_no'))->where('id', '!=', $recordId)->exists()) {
            return redirect()->back()->withInput()->withErrors('VMS no sudah wujud.');
        }

        $maintenanceRecord->fill($request->except('entry_option', 'hidden_ip_address_automatik'));
        $maintenanceRecord->lab_management_id = $labManagementId;
        $maintenanceRecord->entry_option = $entryOption;
        $maintenanceRecord->computer_name = $computerName;
        $maintenanceRecord->ip_address = $ipAddress;

        if (in_array($entryOption, ['automatik', 'manual'])) {
            $maintenanceRecord->work_checklist_id = $request->input('work_checklist_id', []);
        } else {
            $maintenanceRecord->work_checklist_id = [];
        }

        $maintenanceRecord->save();

        $pcMaintenanceNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->whereNull('deleted_at')
            ->count();

        $pcDamageNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->where('entry_option', 'pc_rosak')
            ->whereNull('deleted_at')
            ->count();

        $labManagement->pc_damage_no = $pcDamageNo;
        $labManagement->pc_maintenance_no = $pcMaintenanceNo - $pcDamageNo;
        $labManagement->pc_unmaintenance_no = $labManagement->computer_no - $labManagement->pc_maintenance_no - $labManagement->pc_damage_no;
        $labManagement->save();

        return redirect()->route('lab-management.maintenance-records', ['labManagement' => $labManagementId])
            ->with('success', 'Maklumat berjaya dikemaskini');
    }


    public function search(Request $request, $labManagementId)
    {
        $search = $request->input('search');

        $labManagement = LabManagement::findOrFail($labManagementId);
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();

        // Perform search only on relevant fields
        if ($search) {
            $maintenanceRecordList = MaintenanceRecord::where('computer_name', 'LIKE', "%$search%")
                ->where('lab_management_id', $labManagementId)
                ->latest()
                ->paginate(10);
        } else {
            // Fetch records without applying any search filters
            $maintenanceRecordList = MaintenanceRecord::where('lab_management_id', $labManagementId)
                ->latest()
                ->paginate(10);
        }

        // Pass additional data to the view
        return view('pages.maintenance-record.index', [
            'maintenanceRecordList' => $maintenanceRecordList,
            'labManagement' => $labManagement,
            'month' => $labManagement->start_time ? Carbon::parse($labManagement->start_time)->format('F') : '-',
            'year' => $labManagement->start_time ? Carbon::parse($labManagement->start_time)->format('Y') : '-',
            'search' => $search,
            'workChecklists' => $workChecklists,
        ]);
    }

    public function destroy(Request $request, $labManagementId, $id)
    {
        $maintenanceRecord = MaintenanceRecord::findOrFail($id);

        $maintenanceRecord->delete();

        // Recalculate pc_maintenance_no considering only non-deleted records
        $pcMaintenanceNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->whereNull('deleted_at')
            ->count();

        // Recalculate pc_damage_no where entry_option is 'pc_rosak' and deleted_at is null
        $pcDamageNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->where('entry_option', 'pc_rosak')
            ->whereNull('deleted_at')
            ->count();

        // Get the LabManagement record
        $labManagement = LabManagement::find($labManagementId);
        $labManagement->pc_damage_no = $pcDamageNo;
        $labManagement->pc_maintenance_no = $pcMaintenanceNo - $pcDamageNo;

        // Recalculate pc_unmaintenance_no as the difference between computer_no and pc_maintenance_no
        $labManagement->pc_unmaintenance_no = $labManagement->computer_no - $labManagement->pc_maintenance_no - $labManagement->pc_damage_no;

        // Save the updated LabManagement record
        $labManagement->save();

        return redirect()->route('lab-management.maintenance-records', ['labManagement' => $labManagementId, 'id' => $id])->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList($labManagementId)
    {
        $trashList = MaintenanceRecord::onlyTrashed()->latest()->paginate(10);
        $workChecklists = WorkChecklist::where('publish_status', 1)->get();
        $labManagement = LabManagement::findOrFail($labManagementId);
        $computerLabName = $labManagement->computerLab->name;

        return view('pages.maintenance-record.trash', [
            'trashList' => $trashList,
            'labManagement' => $labManagement,
            'computerLabName' => $computerLabName,
            'workChecklists' => $workChecklists
        ]);
    }

    public function restore(Request $request, $labManagementId, $id)
    {
        // Retrieve the soft-deleted record
        $deletedRecord = MaintenanceRecord::onlyTrashed()->findOrFail($id);

        // Get the start time of the lab management
        $labManagement = LabManagement::findOrFail($labManagementId);
        $startTime = Carbon::parse($labManagement->start_time);
        $month = $startTime->format('m');
        $year = $startTime->format('Y');

        // Check for existing records with the same computer_name, ip_address, and lab_management_id within the same month and year
        $existingRecord = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->where('computer_name', $deletedRecord->computer_name)
            ->where('ip_address', $deletedRecord->ip_address)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withInput()
                ->withErrors('Rekod selenggara PC bagi nama komputer atau alamat IP pada bulan dan tahun tersebut telah wujud dan tidak dapat dikembalikan.');
        }

        // Check for unique aduan_unit_no and vms_no
        if ($request->input('aduan_unit_no') && MaintenanceRecord::where('aduan_unit_no', $request->input('aduan_unit_no'))->exists()) {
            return redirect()->back()->withErrors('Aduan unit no sudah wujud.');
        }

        if ($request->input('vms_no') && MaintenanceRecord::where('vms_no', $request->input('vms_no'))->exists()) {
            return redirect()->back()->withErrors('VMS no sudah wujud.');
        }

        // Retrieve total number of PCs
        $totalPCs = $labManagement->computer_no;

        // Retrieve total number of maintenance records (excluding soft-deleted records)
        $pcMaintenanceNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->whereNull('deleted_at')
            ->count();

        // Check if the total number of maintenance records equals the total number of PCs
        if ($pcMaintenanceNo >= $totalPCs) {
            return redirect()->back()->withErrors('Pemulihan gagal kerana rekod penyelenggaraan telah mencapai jumlah keseluruhan PC.');
        }

        // Restore the record
        $deletedRecord->restore();

        // Recalculate the maintenance and damage numbers
        $pcMaintenanceNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->whereNull('deleted_at')
            ->count();

        $pcDamageNo = MaintenanceRecord::where('lab_management_id', $labManagementId)
            ->where('entry_option', 'pc_rosak')
            ->whereNull('deleted_at')
            ->count();

        $labManagement->pc_damage_no = $pcDamageNo;
        $labManagement->pc_maintenance_no = $pcMaintenanceNo - $pcDamageNo;
        $labManagement->pc_unmaintenance_no = $labManagement->computer_no - $labManagement->pc_maintenance_no - $labManagement->pc_damage_no;

        $labManagement->save();

        return redirect()->route('lab-management.maintenance-records', ['labManagement' => $labManagementId])
            ->with('success', 'Maklumat berjaya dipulihkan');
    }


    public function forceDelete($labManagementId, $id)
    {
        // Permanently delete the record
        MaintenanceRecord::withTrashed()->where('id', $id)->forceDelete();

        return redirect()->route('lab-management.maintenance-records.trash', ['labManagement' => $labManagementId, 'id' => $id])->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
