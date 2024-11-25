@extends('layouts.master')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
            <div class="breadcrumb-title pe-3">Pengurusan Rekod Selenggara Komputer</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('lab-management.maintenance-records', ['labManagement' => $labManagement->id]) }}">Senarai
                                Rekod {{ $computerLabName }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $str_mode }} Rekod Selenggara</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<h6 class="mb-0 text-uppercase">{{ $str_mode }} Rekod Selenggara</h6>
<hr />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $save_route }}">
            {{ csrf_field() }}

            @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
            @endforeach

            <div class="mb-3">
                <label for="lab_management_id" class="form-label">Makmal Komputer</label>
                <input type="text" class="form-control {{ $errors->has('lab_management_id') ? 'is-invalid' : '' }}" name="lab_management_id" value="{{ $computerLabName }}" disabled>
                <input type="hidden" name="lab_management_id" id="lab_management_id" value="{{ $computerLabName }}">
            </div>

            <!-- Toggle button to switch between automatic and manual entry -->
            <div class="mb-3">
                <label class="form-label">Pilihan Rekod </label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="automatik" value="automatik" {{ old('entry_option', $defaultEntryOption) == 'automatik' ? 'checked' : '' }}>
                        <label class="form-check-label" for="automatik">Penyelenggaraan Automatik</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="manual" value="manual" {{ old('entry_option', $defaultEntryOption) == 'manual' ? 'checked' : '' }}>
                        <label class="form-check-label" for="manual">Penyelenggaraan Manual</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="pcRosak" value="pc_rosak" {{ old('entry_option', $defaultEntryOption) == 'pc_rosak' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pcRosak">PC Rosak</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="pcKeluar" value="pc_keluar" {{ old('entry_option', $defaultEntryOption) == 'pc_keluar' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pcKeluar">PC Keluar</label>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="computerNameContainer">
                <label for="computer_name" class="form-label">Nama Komputer</label>
                <input type="text" class="form-control {{ $errors->has('computer_name') ? 'is-invalid' : '' }}" id="computer_name" name="computer_name" value="{{ old('computer_name') }}">
                @if ($errors->has('computer_name'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('computer_name') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3" id="ipAddressContainer">
                <label for="ip_address" class="form-label">IP Address</label>
                <input type="text" class="form-control {{ $errors->has('ip_address') ? 'is-invalid' : '' }}" id="ip_address" name="ip_address" value="{{ old('ip_address', $ipAddress) }}" {{ old('entry_option', $defaultEntryOption) == 'automatik' ? 'disabled' : '' }}>
                <input type="hidden" name="hidden_ip_address_automatik" value="{{ $ipAddress }}">
            </div>

            <div class="mb-3" id="workProcessSection">
                <label class="form-label">Proses Kerja</label>
                @foreach ($workChecklists as $workChecklist)
                <div class="form-check">
                    <input class="form-check-input {{ $errors->has('work_checklist_id') ? 'is-invalid' : '' }}" type="checkbox" id="work_checklist_{{ $workChecklist->id }}" name="work_checklist_id[]" value="{{ $workChecklist->id }}" @if (in_array($workChecklist->id, old('work_checklist_id', $selectedWorkProcesses ?? []))) checked @endif>
                    <label class="form-check-label" for="work_checklist_{{ $workChecklist->id }}">
                        {{ $workChecklist->title }}
                    </label>
                </div>
                @endforeach
                @if ($errors->has('work_checklist_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('work_checklist_id') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3" id="vmsNo">
                <label for="vms_no" class="form-label">No. Aduan<a href="https://units.uitm.edu.my/aduan_add.cfm" target="_blank" class="ms-2">(VMS / UNITS)</a></label>
                <input type="text" class="form-control {{ $errors->has('vms_no') ? 'is-invalid' : '' }}" id="vms_no" name="vms_no" value="{{ old('vms_no') ?? ($maintenanceRecord->vms_no ?? '') }}">
                @if ($errors->has('vms_no'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('vms_no') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3" id="aduanUnitNo">
                <label for="aduan_unit_no" class="form-label">No. Rujukan Aduan (<a href="https://units.uitm.edu.my/aduan_add.cfm" target="_blank" class="ms-0">UNITS</a> / GFM)</label>
                <input type="text" class="form-control {{ $errors->has('aduan_unit_no') ? 'is-invalid' : '' }}" id="aduan_unit_no" name="aduan_unit_no" value="{{ old('aduan_unit_no') ?? ($maintenanceRecord->aduan_unit_no ?? '') }}">
                @if ($errors->has('aduan_unit_no'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('aduan_unit_no') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <!-- pc keluar -->

            <div class="row mb-3">
                <div class="col-6" id="keluarDate">
                    <label for="keluar_date" class="form-label">Tarikh Keluar</label>
                    <input type="date" class="form-control {{ $errors->has('keluar_date') ? 'is-invalid' : '' }}" id="keluar_date" name="keluar_date" value="{{ old('keluar_date') ?? ($maintenanceRecord->keluar_date ?? '') }}">
                    @if ($errors->has('keluar_date'))
                    <div class="invalid-feedback">
                        @foreach ($errors->get('keluar_date') as $error)
                        {{ $error }}
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="col-6" id="kembaliDate">
                    <label for="kembali_date" class="form-label">Tarikh Dikembalikan (Optional)</label>
                    <input type="date" class="form-control {{ $errors->has('kembali_date') ? 'is-invalid' : '' }}" id="kembali_date" name="kembali_date" value="{{ old('kembali_date') ?? ($maintenanceRecord->kembali_date ?? '') }}">
                    @if ($errors->has('kembali_date'))
                    <div class="invalid-feedback">
                        @foreach ($errors->get('kembali_date') as $error)
                        {{ $error }}
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="mb-3" id="keluarOfficer">
                <label for="keluar_officer" class="form-label">Pegawai Bertanggungjawab</label>
                <input type="text" class="form-control {{ $errors->has('keluar_officer') ? 'is-invalid' : '' }}" id="keluar_officer" name="keluar_officer" value="{{ old('keluar_officer') ?? ($maintenanceRecord->keluar_officer ?? '') }}">
                @if ($errors->has('keluar_officer'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('keluar_officer') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3" id="keluarLocation">
                <label for="keluar_location" class="form-label">Lokasi PC Dibawa Keluar</label>
                <input type="text" class="form-control {{ $errors->has('keluar_location') ? 'is-invalid' : '' }}" id="keluar_location" name="keluar_location" value="{{ old('keluar_location') ?? ($maintenanceRecord->keluar_location ?? '') }}">
                @if ($errors->has('keluar_location'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('keluar_location') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>


            <div class="mb-3">
                <label for="remarks" class="form-label">Catatan</label>
                <textarea class="form-control {{ $errors->has('remarks') ? 'is-invalid' : '' }}" id="remarks" name="remarks" rows="3">{{ old('remarks') ?? ($maintenanceRecord->remarks ?? '') }}</textarea>
                @if ($errors->has('remarks'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('remarks') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
        </form>

    </div>
</div>

<script>
    // UNTUK CHANGE FORM BY PILIHAN REKOD
    document.addEventListener('DOMContentLoaded', function() {
        const ipAddressContainer = document.getElementById('ipAddressContainer');
        const autoRadio = document.getElementById('automatik');
        const pcRosakRadio = document.getElementById('pcRosak');
        const manualRadio = document.getElementById('manual');
        const pcKeluarRadio = document.getElementById('pcKeluar');
        const computerNameInput = document.getElementById('computer_name');
        const ipAddressInput = document.getElementById('ip_address');
        const hiddenIpAddressAuto = document.querySelector('input[name="hidden_ip_address_automatik"]').value;
        const workProcessSection = document.getElementById('workProcessSection');
        const vmsNo = document.getElementById('vmsNo');
        const aduanUnitNo = document.getElementById('aduanUnitNo');
        const keluarDate = document.getElementById('keluarDate');
        const kembaliDate = document.getElementById('kembaliDate');
        const keluarLocation = document.getElementById('keluarLocation');
        const keluarOfficer = document.getElementById('keluarOfficer');

        function toggleEntryOptions() {
            if (autoRadio.checked) {
                computerNameInput.style.display = 'block';
                ipAddressInput.value = hiddenIpAddressAuto;
                ipAddressContainer.style.display = 'block';
                ipAddressInput.disabled = true;
                workProcessSection.style.display = 'block';
                vmsNo.style.display = 'none';
                aduanUnitNo.style.display = 'none';
                keluarDate.style.display = 'none';
                kembaliDate.style.display = 'none';
                keluarLocation.style.display = 'none';
                keluarOfficer.style.display = 'none';
            } else if (pcRosakRadio.checked) {
                computerNameInput.style.display = 'block';
                ipAddressContainer.style.display = 'none';
                ipAddressInput.disabled = false;
                workProcessSection.style.display = 'none';
                vmsNo.style.display = 'block';
                aduanUnitNo.style.display = 'none';
                keluarDate.style.display = 'none';
                kembaliDate.style.display = 'none';
                keluarLocation.style.display = 'none';
                keluarOfficer.style.display = 'none';
            } else if (manualRadio.checked) {
                computerNameInput.style.display = 'block';
                ipAddressContainer.style.display = 'none';
                ipAddressInput.disabled = false;
                workProcessSection.style.display = 'block';
                vmsNo.style.display = 'none';
                aduanUnitNo.style.display = 'block';
                keluarDate.style.display = 'none';
                kembaliDate.style.display = 'none';
                keluarLocation.style.display = 'none';
                keluarOfficer.style.display = 'none';
            } else if (pcKeluarRadio.checked) {
                computerNameInput.style.display = 'block';
                ipAddressContainer.style.display = 'none';
                ipAddressInput.disabled = false;
                workProcessSection.style.display = 'none';
                vmsNo.style.display = 'none';
                aduanUnitNo.style.display = 'none';
                keluarDate.style.display = 'block';
                kembaliDate.style.display = 'block';
                keluarLocation.style.display = 'block';
                keluarOfficer.style.display = 'block';
            }
        }

        autoRadio.addEventListener('change', toggleEntryOptions);
        pcRosakRadio.addEventListener('change', toggleEntryOptions);
        manualRadio.addEventListener('change', toggleEntryOptions);
        pcKeluarRadio.addEventListener('change', toggleEntryOptions);

        toggleEntryOptions();
    });

    // UNTUK DISPLAY CERTAIN WORK CHECKLISTS FOR MANUAL
    document.addEventListener('DOMContentLoaded', function() {
        const workChecklists = <?php echo json_encode($workChecklists); ?>;
        const manualRadio = document.getElementById('manual');
        const workChecklistContainer = document.getElementById('workProcessSection');
        const allowedIds = [1, 2, 5]; // Only show these work checklist IDs for manual option

        // Preserve state for checked checklists
        const initialStates = {};
        workChecklists.forEach(function(workChecklist) {
            const checkbox = document.getElementById('work_checklist_' + workChecklist.id);
            initialStates[workChecklist.id] = checkbox.checked;
        });

        // Function to filter work checklists
        function filterWorkChecklists() {
            const isManual = manualRadio.checked;

            workChecklists.forEach(function(workChecklist) {
                const checkbox = document.getElementById('work_checklist_' + workChecklist.id);
                const container = checkbox.closest('.form-check');

                if (isManual) {
                    if (allowedIds.includes(workChecklist.id)) {
                        container.style.display = 'block'; // Show allowed IDs
                    } else {
                        container.style.display = 'none'; // Hide other IDs
                        checkbox.checked = false; // Auto-clear non-allowed IDs
                    }
                } else {
                    container.style.display = 'block'; // Show all IDs
                    checkbox.checked = initialStates[workChecklist.id]; // Restore initial state
                }
            });
        }

        // Initial filtering based on the selected option
        filterWorkChecklists();

        // Add event listeners to radio buttons
        manualRadio.addEventListener('change', filterWorkChecklists);
        document.getElementById('automatik').addEventListener('change', filterWorkChecklists);
        document.getElementById('pcRosak').addEventListener('change', filterWorkChecklists);
        document.getElementById('pcKeluar').addEventListener('change', filterWorkChecklists);
    });
</script>
<!-- End Page Wrapper -->
@endsection