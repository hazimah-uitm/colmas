@extends('layouts.master')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Rekod Selenggara PC</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('lab-management') }}">Rekod
                            Selenggara Makmal Komputer</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Rekod Selenggara PC</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('lab-management.maintenance-records.trash', ['labManagement' => $labManagement->id]) }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
            </a>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Senarai Selenggara PC di {{ $labManagement->computerLab->name }}</h6>
    <hr />

    <div class="card">
        <div class="card-body">
            <!-- Info -->
            @include('pages.maintenance-record.main-info')
            <h6 class="mb-0 text-uppercase fw-bold">Senarai PC Diselenggara/Rosak</h6>
            <hr class="mt-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- Search Field -->
                <div class="position-relative me-2">
                    <!-- Search Form -->
                    <form
                        action="{{ route('lab-management.maintenance-records.search', ['labManagement' => $labManagement->id]) }}"
                        method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control search-input" placeholder="Carian..." name="search"
                                value="{{ request()->query('search') }}">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary search-button">
                                    <i class="bx bx-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>

                <!-- Button to Add Record -->
                @php
                    // Ensure the variables are not null and have the correct types for comparison
                    $pcMaintenanceNo = $labManagement->pc_maintenance_no;
                    $computerNo = $labManagement->computer_no;
                    $pcDamageNo = $labManagement->pc_damage_no;
                    $totalPCMaintenance = $pcMaintenanceNo + $pcDamageNo;
                @endphp

                @if ($totalPCMaintenance !== $computerNo)
                    <a href="{{ route('lab-management.maintenance-records.create', ['labManagement' => $labManagement->id]) }}"
                        class="btn btn-primary radius-30 mt-2">
                        <i class="bx bxs-plus-square"></i> Tambah Rekod PC
                    </a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Komputer</th>
                            <th>IP Address</th>
                            <th>Jenis Rekod</th>
                            <th>Proses Kerja</th>
                            <th>Catatan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($maintenanceRecordList) > 0)
                            @foreach ($maintenanceRecordList as $maintenanceRecord)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $maintenanceRecord->computer_name }}</td>
                                    <td>{{ $maintenanceRecord->ip_address }}</td>
                                    <td>{{ str_replace('_', ' ', ucwords(strtolower($maintenanceRecord->entry_option))) }}
                                    </td>
                                    <td>
                                        @if (!empty($maintenanceRecord->work_checklist_id))
                                            <ul class="list-unstyled">
                                                @foreach ($workChecklists as $workChecklist)
                                                    @php
                                                        $isSelected = in_array(
                                                            $workChecklist->id,
                                                            $maintenanceRecord->work_checklist_id);
                                                    @endphp
                                                    <li>
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
                                    <td>{!! nl2br(e($maintenanceRecord->remarks)) !!}</td>
                                    <td>
                                        @if (!in_array($labManagement->status, ['dihantar', 'telah_disemak']))
                                            <a href="{{ route('lab-management.maintenance-records.edit', ['labManagement' => $labManagement->id, 'id' => $maintenanceRecord->id]) }}"
                                                class="btn btn-info btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Kemaskini">
                                                <i class="bx bxs-edit"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('lab-management.maintenance-records.show', ['labManagement' => $labManagement->id, 'id' => $maintenanceRecord->id]) }}"
                                            class="btn btn-primary btn-sm" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" title="Papar">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        @if (!in_array($labManagement->status, ['dihantar', 'telah_disemak']))
                                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                data-bs-title="Padam">
                                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $maintenanceRecord->id }}">
                                                    <i class="bx bx-trash"></i>
                                                </span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">Tiada rekod</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="mt-3 d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                        <form
                            action="{{ route('lab-management.maintenance-records', ['labManagement' => $labManagement->id]) }}"
                            method="GET" id="perPageForm">
                            <select name="perPage" id="perPage" class="form-select"
                                onchange="document.getElementById('perPageForm').submit()">
                                <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                                <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                                <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                            </select>
                        </form>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <div class="mx-1 mt-2">{{ $maintenanceRecordList->firstItem() }} –
                            {{ $maintenanceRecordList->lastItem() }} dari
                            {{ $maintenanceRecordList->total() }} rekod
                        </div>
                        <div>{{ $maintenanceRecordList->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        @foreach ($maintenanceRecordList as $maintenanceRecord)
            <div class="modal fade" id="deleteModal{{ $maintenanceRecord->id }}" tabindex="-1"
                aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @isset($maintenanceRecord)
                                Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">
                                    {{ $maintenanceRecord->computer_name }}</span>?
                            @else
                                Error: Campus data not available.
                            @endisset
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            @isset($maintenanceRecord)
                                <form class="d-inline" method="POST"
                                    action="{{ route('lab-management.maintenance-records.destroy', ['labManagement' => $labManagement->id, 'id' => $maintenanceRecord->id]) }}">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Padam</button>
                                </form>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!--end page wrapper -->
    @endsection
