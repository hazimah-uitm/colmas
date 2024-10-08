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
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-uppercase">Nama Pemilik</th>
                            <td class="text-uppercase">
                                {{ $labManagement->computerLab->pemilik->name }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-uppercase">Makmal Komputer</th>
                            <td class="mb-3 text-uppercase">{{ $labManagement->computerLab->name }},
                                {{ $labManagement->computerLab->campus->name }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Tarikh/Masa Mula</th>
                            <th>Tarikh/Masa Tamat</th>
                        </tr>
                        <tr>
                            <td>{{ $labManagement->month }}</td>
                            <td>{{ $labManagement->year }}</td>
                            <td>{{ $labManagement->start_time }}</td>
                            <td>{{ $labManagement->end_time ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">Bil. Keseluruhan Komputer</th>
                                <th class="text-center">Bil. Komputer Telah Diselenggara</th>
                                <th class="text-center">Bil. Komputer Rosak</th>
                                <th class="text-center">Bil. Komputer Belum Diselenggara</th>
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
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3 text-uppercase">Senarai Semak Makmal</h6>
                        <hr />
                        <div class="table-responsive">
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
                                            <span class="empty-icon" style="color: red">&#9744;</span>
                                            @endif
                                        </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3 text-uppercase">Senarai Rekod PC Diselenggara/Rosak</h6>
                        <hr />
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Nama Komputer</th>
                                        <th class="text-center" colspan="{{ count($workChecklists) }}">Kerja Selenggara</th>
                                        <th class="text-center">Catatan</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">IP Address</th>
                                        @foreach ($workChecklists as $workChecklist)
                                        <th class="text-center">{{ $workChecklist->title }}</th>
                                        @endforeach
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($labManagement->maintenanceRecords as $maintenanceRecord)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $maintenanceRecord->computer_name }} <br>
                                            {{ $maintenanceRecord->ip_address }}
                                        </td>
                                        @if (!empty($maintenanceRecord->work_checklist_id))
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
                                            <span class="empty-icon" style="color: red;">&#10006;</span>
                                            @endif
                                        </td>
                                        @endforeach
                                        @else
                                        <td class="text-center" colspan="{{ count($workChecklists) }}">Komputer Bermasalah</td>
                                        @endif
                                        <td class="text-center">{!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3 text-uppercase">Senarai Perisian</h6>
                        <div class="row">
                            @foreach ($softwareList as $software)
                            @if (!empty($labManagement->software_id) && in_array($software->id, $labManagement->software_id))
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="me-2">&#10004;</span> <!-- Tick icon -->
                                    {{ $software->title }}
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <th>Catatan/Ulasan Pemilik</th>
                            <td>{!! nl2br(e($labManagement->remarks_submitter ?? '-')) !!}</td>
                        </tr>
                        <tr>
                            <th>Catatan/Ulasan Pegawai Penyemak</th>
                            <td>{!! nl2br(e($labManagement->remarks_checker ?? '-')) !!}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <th>Dihantar oleh</th>
                            <td>{{ $labManagement->submittedBy->name ?? '-' }}</td>
                            <th>Dihantar pada</th>
                            <td>{{ $labManagement->submitted_at ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Disemak oleh</th>
                            <td>{{ $labManagement->checkedBy->name ?? '-' }}</td>
                            <th>Disemak pada</th>
                            <td>{{ $labManagement->checked_at ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection