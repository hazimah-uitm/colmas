@extends('layouts.master')

@section('content')
<!-- Breadcrumb -->
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
                        <li class="breadcrumb-item"><a href="{{ route('lab-management') }}">Rekod Selenggara Makmal
                                Komputer</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Selenggara
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--end breadcrumb-->


<h6 class="mb-0 text-uppercase">Laporan Selenggara Berkala Makmal Komputer</h6>
<hr />

<div class="row">
    <div class="col-md">
        <div class="card">
            <div class="card-body">
                <form id="submitForm{{ $labManagement->id }}"
                    action="{{ route('lab-management.submit', $labManagement->id) }}" method="POST" class="d-inline">
                    {{ csrf_field() }}
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th class="mb-3 text-uppercase">Nama Pemilik</th>
                                <td class="mb-3 text-uppercase">
                                    {{ $labManagement->computerLab->pemilik->name }}
                                </td>
                            </tr>
                            <tr>
                                <th class="mb-3 text-uppercase">Makmal Komputer</th>
                                <td class="mb-3 text-uppercase">{{ $labManagement->computerLab->name }},
                                    {{ $labManagement->computerLab->campus->name }}
                                </td>
                            </tr>
                            <tr>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Masa Mula</th>
                                <th>Masa Tamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $labManagement->month }}</td>
                                <td>{{ $labManagement->year }}</td>
                                <td>{{ $labManagement->start_time }}</td>
                                <td>{{ $labManagement->end_time ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 25%" class="text-center">Bil. Keseluruhan Komputer</th>
                                <th style="width: 25%" class="text-center">Bil. Komputer Telah Diselenggara</th>
                                <th style="width: 25%" class="text-center">Bil. Komputer Rosak</th>
                                <th style="width: 25%" class="text-center">Bil. Komputer Belum Diselenggara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">{{ $labManagement->computer_no ?? '-' }}</td>
                                <td class="text-center">{{ $labManagement->pc_maintenance_no ?? '-' }}</td>
                                <td class="text-center">{{ $labManagement->pc_damage_no ?? '-' }}</td>
                                <td class="text-center">{{ $labManagement->pc_unmaintenance_no ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3 text-uppercase">Senarai Semak Makmal</h6>
                            <hr />
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        @foreach ($labCheckList as $labCheck)
                                        <th class="text-center">{{ $labCheck->title }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($labCheckList as $labCheck)
                                        <td class="text-center">
                                            @php
                                            $isSelected =
                                            !empty($labManagement->lab_checklist_id) &&
                                            in_array($labCheck->id, $labManagement->lab_checklist_id);
                                            @endphp
                                            @if ($isSelected)
                                            <span class="tick-icon">&#10004;</span>
                                            @else
                                            <span class="empty-icon" style="color: red">&#10006;</span>
                                            @endif
                                        </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3 text-uppercase">Senarai Rekod PC Diselenggara/Rosak</h6>
                            <hr />
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                    <th style="width: 5%" class="text-center">No.</th>
                                        <th style="width: 10%" class="text-center">Nama Komputer</th>
                                        <th style="width: 30%" class="text-center" colspan="{{ count($workChecklists) }}">Kerja Selenggara</th>
                                        <th style="width: 15%" class="text-center">No. Aduan</th>
                                        <th style="width: 40%" class="text-center">Catatan</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">IP Address</th>
                                        @foreach ($workChecklists as $workChecklist)
                                        <th class="text-center">{{ $workChecklist->title }}</th>
                                        @endforeach
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($labManagement->maintenanceRecords as $maintenanceRecord)
                                    
                                    @php
                                    $noAduan = '-';

                                    // Check the entryOption for each maintenance record
                                    if ($maintenanceRecord->entry_option == 'manual') {
                                    $noAduan = $maintenanceRecord->aduan_unit_no ?? '-';
                                    } elseif ($maintenanceRecord->entry_option == 'pc_rosak') {
                                    $noAduan = $maintenanceRecord->vms_no ?? '-';
                                    }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $maintenanceRecord->computer_name }} <br>
                                            {{ $maintenanceRecord->ip_address }}
                                        </td>
                                       @if ($maintenanceRecord->entry_option == "pc_rosak")
                                            <td class="text-center" colspan="{{ count($workChecklists) }}">Komputer Bermasalah</td>
                                            @elseif ($maintenanceRecord->entry_option == "pc_keluar")
                                            <td class="text-center" colspan="{{ count($workChecklists) }}">PC dibawa keluar pada {{ $maintenanceRecord->keluar_date }} <br> ke {{ $maintenanceRecord->keluar_location }}</td>
                                            @else
                                            @foreach ($workChecklists as $workChecklist)
                                            <td class="text-center">
                                                @php
                                                $isSelected = in_array(
                                                $workChecklist->id,
                                                $maintenanceRecord->work_checklist_id);
                                                @endphp
                                                @if ($isSelected)
                                                <span class="tick-icon">&#10004;</span>
                                                @else
                                                <span class="empty-icon"
                                                    style="color: red;">&#10006;</span>
                                                @endif
                                            </td>
                                            @endforeach
                                            @endif
                                        <td class="text-center">{{ $noAduan }}</td>
                                        <td class="text-center">{!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3 text-uppercase">Senarai Perisian</h6>
                            <div class="row">
                                @foreach ($labManagement->computerLab->software as $software)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">&#10004;</span>
                                        {{ $software->title }} {{ $software->version }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <table class="table table-borderless">
                        <tr>
                            <th>Catatan/Ulasan</th>
                            <td>
                                <div class="mb-3">
                                    <textarea class="form-control {{ $errors->has('remarks_submitter') ? 'is-invalid' : '' }}" id="remarks_submitter"
                                        name="remarks_submitter" rows="3">{{ old('remarks_submitter') ?? ($labManagement->remarks_submitter ?? '') }}</textarea>
                                    @if ($errors->has('remarks_submitter'))
                                    <div class="invalid-feedback">
                                        @foreach ($errors->get('remarks_submitter') as $error)
                                        {{ $error }}
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}</td>
                        </tr>
                    </table>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox"
                            id="confirmationCheckbox{{ $labManagement->id }}">
                        <label class="form-check-label" for="confirmationCheckbox{{ $labManagement->id }}">
                            <strong>Saya mengesahkan bahawa setiap komputer telah diselenggara.</strong>
                        </label>
                        <div id="checkboxAlert{{ $labManagement->id }}" class="alert alert-danger mt-2 d-none">
                            Sila beri pengesahan sebelum menghantar.
                        </div>
                    </div>
                    <input type="hidden" name="submitted_by" value="{{ auth()->id() }}">
                    <input type="hidden" name="submitted_at" value="{{ now() }}">
                    @php
                    $isButtonEnabled = $labManagement->pc_unmaintenance_no == 0;
                    @endphp
                    <button type="button" class="btn btn-success" onclick="submitForm('{{ $labManagement->id }}')"
                        @if (!$isButtonEnabled) disabled @endif>Hantar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function submitForm(labManagementId) {
        var checkbox = document.getElementById('confirmationCheckbox' + labManagementId);
        var remarksInput = document.getElementById('remarks' + labManagementId);
        var checkboxAlert = document.getElementById('checkboxAlert' + labManagementId);

        if (checkbox.checked) {
            checkboxAlert.classList.add('d-none');
            document.getElementById('submitForm' + labManagementId).submit();
        } else {
            checkboxAlert.classList.remove('d-none');
        }
    }
</script>
@endsection