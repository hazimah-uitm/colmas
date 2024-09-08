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
                    <li class="breadcrumb-item active" aria-current="page"><a
                            href="{{ route('lab-management.maintenance-records', ['labManagement' => $labManagement->id]) }}">Senarai
                            Rekod {{ $computerLabName }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Maklumat Komputer
                        {{ $maintenanceRecord->computer_name }}</li>
                </ol>
            </nav>
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
                            <td>{{ $entryOption == 'auto' ? 'Penyelenggaraan Automatik' : ($entryOption == 'manual' ? 'Penyelenggaraan Manual' : 'PC Rosak') }}
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
                                @if (!empty($maintenanceRecord->work_checklist_id))
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
                                @else
                                    <p>Komputer Bermasalah</p>
                                @endif
                            </td>
                        </tr>

                        @if ($entryOption == 'manual')
                            <tr>
                                <th>Aduan Unit No.</th>
                                <td>{{ $maintenanceRecord->aduan_unit_no ?? '-' }}</td>
                            </tr>
                        @elseif($entryOption == 'pc_rosak')
                            <tr>
                                <th>VMS No.</th>
                                <td>{{ $maintenanceRecord->vms_no ?? '-' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Catatan</th>
                            <td>{!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Wrapper -->
@endsection
