@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
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
                                        {{ $labManagement->computerLab->campus->name }} </td>
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
                        <table class="table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Bil. Keseluruhan Komputer</th>
                                    <th>Bil. Komputer Telah Diselenggara</th>
                                    <th>Bil. Komputer Rosak</th>
                                    <th>Bil. Komputer Belum Diselenggara</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $labManagement->computer_no ?? '-' }}</td>
                                    <td>{{ $labManagement->pc_maintenance_no ?? '-' }}</td>
                                    <td>{{ $labManagement->pc_damage_no ?? '-' }}</td>
                                    <td>{{ $labManagement->pc_unmaintenance_no ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="card shadow">
                            <div class="card-body">
                                <h6 class="mb-3 text-uppercase">Senarai Rekod PC Diselenggara/Rosak</h6>
                                <hr />
                                <table class="table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Komputer</th>
                                            <th colspan="{{ count($workChecklists) }}">Kerja Selenggara</th>
                                            <th>Catatan</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>IP Address</th>
                                            @foreach ($workChecklists as $workChecklist)
                                                <th>{{ $workChecklist->title }}</th>
                                            @endforeach
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($labManagement->maintenanceRecords as $maintenanceRecord)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $maintenanceRecord->computer_name }} <br>
                                                    {{ $maintenanceRecord->ip_address }}
                                                </td>
                                                @if (!empty($maintenanceRecord->work_checklist_id))
                                                    @foreach ($workChecklists as $workChecklist)
                                                        <td>
                                                            @php
                                                                $isSelected = in_array(
                                                                    $workChecklist->id,
                                                                    $maintenanceRecord->work_checklist_id);
                                                            @endphp
                                                            @if ($isSelected)
                                                                <span class="tick-icon">&#10004;</span>
                                                            @else
                                                                <span class="empty-icon" style="color: red">&#10006;</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                @else
                                                    <td colspan="{{ count($workChecklists) }}">Komputer Bermasalah</td>
                                                @endif
                                                <td>{!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card shadow">
                            <div class="card-body">
                                <h6 class="mb-3 text-uppercase">Senarai Semak Makmal</h6>
                                <hr />
                                <table class="table">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach ($labCheckList as $labCheck)
                                                <th>{{ $labCheck->title }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($labCheckList as $labCheck)
                                                <td>
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
                        <div class="card shadow">
                            <div class="card-body">
                                <h6 class="mb-3 text-uppercase">Senarai Perisian</h6>
                                <div class="row">
                                    @foreach ($softwareList as $software)
                                        @if (!empty($labManagement->software_id) && in_array($software->id, $labManagement->software_id))
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">&#10004;</span> 
                                                    {{ $software->title }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <table class="table table-borderless">
                            <tr>
                                <th>Catatan/Ulasan</th>
                                <td>
                                    <div class="mb-3">
                                        <textarea class="form-control @error('remarks_submitter') is-invalid @enderror"
                                            id="remarks_submitter{{ $labManagement->id }}" name="remarks_submitter" rows="3">{{ old('remarks_submitter') ?? ($labManagement->remarks_submitter ?? '') }}</textarea>
                                        @error('remarks_submitter')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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