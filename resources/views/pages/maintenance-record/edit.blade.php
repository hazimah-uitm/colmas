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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a
                                href="{{ route('lab-management.maintenance-records', ['labManagement' => $labManagement->id]) }}">Senarai
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
                <input type="text" class="form-control {{ $errors->has('lab_management_id') ? 'is-invalid' : '' }}"
                    id="lab_management_id" name="lab_management_id" value="{{ $computerLabName }}" disabled>
                <input type="hidden" name="lab_management_id" value="{{ $labManagement->id }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Pilihan Rekod </label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="automatik"
                            value="automatik" {{ old('entry_option', $entryOption) == 'automatik' ? 'checked' : '' }}>
                        <label class="form-check-label" for="automatik">Penyelenggaraan Automatik</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="manual" value="manual"
                            {{ old('entry_option', $entryOption) == 'manual' ? 'checked' : '' }}>
                        <label class="form-check-label" for="manual">Penyelenggaraan Manual</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="pcRosak"
                            value="pc_rosak" {{ old('entry_option', $entryOption) == 'pc_rosak' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pcRosak">PC Rosak</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_option" id="pcKeluar"
                            value="pc_keluar" {{ old('entry_option', $entryOption) == 'pc_keluar' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pcKeluar">PC Keluar</label>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="computerNameContainer">
                <label for="computer_name" class="form-label">Nama Komputer</label>
                <input type="text" class="form-control {{ $errors->has('computer_name') ? 'is-invalid' : '' }}"
                    id="computer_name" name="computer_name" value="{{ old('computer_name', $computerName) }}">
                <input type="hidden" id="hidden_computer_name_manual" value="{{ $computerName }}">
                @if ($errors->has('computer_name'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('computer_name') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3" id="ipAddressContainer">
                <label for="ip_address" class="form-label">Alamat IP</label>
                <input type="text" class="form-control{{ $errors->has('ip_address') ? 'is-invalid' : '' }}" id="ip_address"
                    name="ip_address" value="{{ old('ip_address', $ipAddress) }}" disabled>
                <input type="hidden" name="hidden_ip_address_automatik" id="hidden_ip_address_automatik"
                    value="{{ $ipAddressAuto }}">
            </div>

            <div class="mb-3" id="workChecklistContainer">
                <label class="form-label">Proses Kerja</label>
                @foreach ($workChecklists as $workChecklist)
                <div class="form-check">
                    <input class="form-check-input {{ $errors->has('work_checklist_id') ? 'is-invalid' : '' }}" type="checkbox" id="work_checklist_{{ $workChecklist->id }}"
                        name="work_checklist_id[]" value="{{ $workChecklist->id }}"
                        @if (in_array($workChecklist->id, old('work_checklist_id', $maintenanceRecord->work_checklist_id ?? []))) checked @endif>
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

            <div class="mb-3" id="vmsNoContainer">
                <label for="vms_no" class="form-label">No. Aduan<a href="https://units.uitm.edu.my/aduan_add.cfm" target="_blank" class="ms-2">(VMS / UNITS)</a></label>
                <input type="text" class="form-control {{ $errors->has('vms_no') ? 'is-invalid' : '' }}" id="vms_no"
                    name="vms_no" value="{{ old('vms_no') ?? ($maintenanceRecord->vms_no ?? '') }}">
                @if ($errors->has('vms_no'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('vms_no') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3" id="aduanUnitNoContainer">
                <label for="aduan_unit_no" class="form-label">No. Aduan Unit<a
                        href="https://units.uitm.edu.my/aduan_add.cfm" target="_blank"
                        class="ms-2">(UNITS)</a></label>
                <input type="text" class="form-control {{ $errors->has('aduan_unit_no') ? 'is-invalid' : '' }}"
                    id="aduan_unit_no" name="aduan_unit_no"
                    value="{{ old('aduan_unit_no') ?? ($maintenanceRecord->aduan_unit_no ?? '') }}">
                @if ($errors->has('aduan_unit_no'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('aduan_unit_no') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

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

            <div class="mb-3" id="remarksContainer">
                <label for="remarks" class="form-label">Catatan</label>
                <textarea class="form-control {{ $errors->has('remarks') ? 'is-invalid' : '' }}" id="remarks" name="remarks" rows="3">{{ old('remarks', $maintenanceRecord->remarks) }}</textarea>
                @if ($errors->has('remarks'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('remarks') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Hantar</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const entryOptionRadios = document.querySelectorAll('input[name="entry_option"]');
        const computerNameInput = document.getElementById('computer_name');
        const ipAddressInput = document.getElementById('ip_address');
        const computerNameContainer = document.getElementById('computerNameContainer');
        const ipAddressContainer = document.getElementById('ipAddressContainer');
        const vmsNoContainer = document.getElementById('vmsNoContainer');
        const aduanUnitNoContainer = document.getElementById('aduanUnitNoContainer');
        const remarksContainer = document.getElementById('remarksContainer');
        const workChecklistContainer = document.getElementById('workChecklistContainer');
        const hiddenComputerNameManual = document.getElementById('hidden_computer_name_manual').value;
        const hiddenIpAddress = document.getElementById('hidden_ip_address_automatik').value;
        const keluarDate = document.getElementById('keluarDate');
        const kembaliDate = document.getElementById('kembaliDate');
        const keluarLocation = document.getElementById('keluarLocation');
        const keluarOfficer = document.getElementById('keluarOfficer');

        function toggleFields() {
            const selectedOption = document.querySelector('input[name="entry_option"]:checked').value;

            if (selectedOption === 'automatik') {
                computerNameInput.value = hiddenComputerNameManual;
                computerNameInput.removeAttribute('disabled');
                ipAddressInput.value = hiddenIpAddress;
                ipAddressInput.setAttribute('disabled', true);
                computerNameContainer.style.display = 'block';
                ipAddressContainer.style.display = 'block';
                vmsNoContainer.style.display = 'none';
                aduanUnitNoContainer.style.display = 'none';
                remarksContainer.style.display = 'block';
                workChecklistContainer.style.display = 'block';
                keluarDate.style.display = 'none';
                kembaliDate.style.display = 'none';
                keluarLocation.style.display = 'none';
                keluarOfficer.style.display = 'none';
            } else if (selectedOption === 'manual') {
                computerNameInput.value = hiddenComputerNameManual;
                computerNameInput.removeAttribute('disabled');
                ipAddressInput.value = '';
                ipAddressInput.removeAttribute('disabled');
                computerNameContainer.style.display = 'block';
                ipAddressContainer.style.display = 'none';
                vmsNoContainer.style.display = 'none';
                aduanUnitNoContainer.style.display = 'block';
                remarksContainer.style.display = 'block';
                workChecklistContainer.style.display = 'block';
                keluarDate.style.display = 'none';
                kembaliDate.style.display = 'none';
                keluarLocation.style.display = 'none';
                keluarOfficer.style.display = 'none';
            } else if (selectedOption === 'pc_rosak') {
                computerNameInput.value = hiddenComputerNameManual;
                computerNameInput.removeAttribute('disabled');
                ipAddressInput.value = '';
                ipAddressInput.removeAttribute('disabled');
                computerNameContainer.style.display = 'block';
                ipAddressContainer.style.display = 'none';
                vmsNoContainer.style.display = 'block';
                aduanUnitNoContainer.style.display = 'none';
                remarksContainer.style.display = 'block';
                workChecklistContainer.style.display = 'none';
                keluarDate.style.display = 'none';
                kembaliDate.style.display = 'none';
                keluarLocation.style.display = 'none';
                keluarOfficer.style.display = 'none';
            } else if (selectedOption === 'pc_keluar') {
                computerNameInput.value = hiddenComputerNameManual;
                computerNameInput.removeAttribute('disabled');
                ipAddressInput.value = '';
                ipAddressInput.removeAttribute('disabled');
                computerNameContainer.style.display = 'block';
                ipAddressContainer.style.display = 'none';
                vmsNoContainer.style.display = 'none';
                aduanUnitNoContainer.style.display = 'none';
                remarksContainer.style.display = 'block';
                workChecklistContainer.style.display = 'none';
                keluarDate.style.display = 'block';
                kembaliDate.style.display = 'block';
                keluarLocation.style.display = 'block';
                keluarOfficer.style.display = 'block';
            }
        }

        entryOptionRadios.forEach(radio => {
            radio.addEventListener('change', toggleFields);
        });

        toggleFields();
    });
</script>
@endsection