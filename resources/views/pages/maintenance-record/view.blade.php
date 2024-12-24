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
                        <li class="breadcrumb-item active" aria-current="page"><a
                                href="{{ route('lab-management.maintenance-records', ['labManagement' => $labManagement->id]) }}">Senarai
                                Rekod {{ $computerLabName }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Maklumat Komputer
                            {{ $maintenanceRecord->computer_name }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--end breadcrumb-->


<h6 class="mb-0 text-uppercase">Maklumat Komputer {{ $maintenanceRecord->computer_name }}</h6>
<hr />

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Makmal Komputer</th>
                        <td>{{ $computerLabName }}</td>
                    </tr>
                    <tr>
                        <th>Pilihan Rekod</th>
                        <td>
                            @if ($entryOption == 'automatik')
                            Automatik
                            @elseif ($entryOption == 'manual')
                            Manual
                            @elseif ($entryOption == 'pc_keluar')
                            PC Keluar
                            @else
                            PC Rosak
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Nama Komputer</th>
                        <td>{{ $maintenanceRecord->computer_name }}</td>
                    </tr>
                    @if ($entryOption == 'automatik')
                    <tr>
                        <th>IP Address</th>
                        <td>{{ $maintenanceRecord->ip_address }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Proses Kerja</th>
                        <td>
                            @if ($maintenanceRecord->entry_option == "pc_rosak")
                            <p>Komputer Bermasalah</p>
                            @elseif ($maintenanceRecord->entry_option == "pc_keluar")
                            <p>PC dibawa keluar pada {{ $maintenanceRecord->keluar_date }} <br> ke {{ $maintenanceRecord->keluar_location }}</p>
                            @else
                            <ul style="list-style-type: none; padding: 0;">
                                @foreach ($workChecklists as $workChecklist)
                                @php
                                $isSelected = in_array(
                                $workChecklist->id,
                                $maintenanceRecord->work_checklist_id);
                                @endphp
                                <li style="list-style-type: none; margin-bottom: 5px;">
                                    @if ($isSelected)
                                    <span class="tick-icon">&#10004;</span>
                                    @else
                                    <span class="empty-icon" style="color: red;">&#10006;</span>
                                    @endif
                                    {{ $workChecklist->title }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </td>
                    </tr>
                    @if ($entryOption == 'manual')
                    <tr>
                        <th>No. Aduan (UNITS / GFM)</th>
                        <td>{{ $maintenanceRecord->aduan_unit_no ?? '-' }}</td>
                    </tr>
                    @elseif($entryOption == 'pc_rosak')
                    <tr>
                        <th>No. Aduan (VMS / UNITS)</th>
                        <td>{{ $maintenanceRecord->vms_no ?? '-' }}</td>
                    </tr>
                    @elseif($entryOption == 'pc_keluar')
                    <tr>
                        <th>Tarikh Keluar</th>
                        <td>{{ $maintenanceRecord->keluar_date ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tarikh Dikembalikan</th>
                        <td>{{ $maintenanceRecord->kembali_date ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Pegawai Bertanggungjawab</th>
                        <td>{{ $maintenanceRecord->keluar_officer ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi PC dibawa Keluar</th>
                        <td>{{ $maintenanceRecord->keluar_location ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Catatan</th>
                        <td style="word-wrap: break-word; white-space: normal;">
    <span class="badge text-dark mb-1" style="font-size: 12px; background-color: yellow">
        {{ $maintenanceRecord->computer_name }} selesai pada: 
        {{ $maintenanceRecord->created_at->format('d-m-Y') }} | 
        {{ $maintenanceRecord->created_at->format('h:i A') }}
    </span>
    <br>
    {!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}
</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Page Wrapper -->
@endsection