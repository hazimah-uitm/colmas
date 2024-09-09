@extends('layouts.master')
@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pengurusan Rekod Selenggara Makmal</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Rekod Selenggara Makmal Komputer</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('lab-management.trash') }}">
            <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
        </a>
    </div>
</div>
<!--end breadcrumb-->
<h6 class="mb-0 text-uppercase">Senarai Rekod Selenggara Makmal Komputer</h6>
<hr />
<div class="card">
    <div class="card-body">
        <div class="d-lg-flex justify-content-end align-items-center mb-4 gap-2">
            <div class="position-relative">
                @hasanyrole('Pemilik|Superadmin')
                <a href="{{ route('lab-management.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                    <i class="bx bxs-plus-square"></i> Tambah Rekod Makmal
                </a>
                @endhasanyrole
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg">
                <form action="{{ route('lab-management.search') }}" method="GET" id="searchForm"
                    class="d-lg-flex align-items-center gap-3">

                    <div class="input-group">
                        <input type="text" class="form-control rounded" placeholder="Carian..." name="search"
                            value="{{ request('search') }}" id="searchInput">

                        @hasanyrole('Admin|Superadmin')
                        <select name="campus_id" class="form-select form-select-sm ms-2 rounded" id="campusFilter">
                            <option value="">Semua Kampus</option>
                            @foreach ($campusList as $campus)
                            <option value="{{ $campus->id }}"
                                {{ Request::get('campus_id') == $campus->id ? 'selected' : '' }}>
                                {{ $campus->name }}
                            </option>
                            @endforeach
                        </select>
                        @endhasanyrole

                        @hasanyrole('Pegawai Penyemak|Superadmin')
                        <select name="pemilik_id" class="form-select form-select-sm ms-2 rounded" id="pemilikFilter">
                            <option value="">Semua Pemilik</option>
                            @foreach ($pemilikList as $pemilik)
                            <option value="{{ $pemilik->id }}"
                                {{ Request::get('pemilik_id') == $pemilik->id ? 'selected' : '' }}>
                                {{ $pemilik->name }}
                            </option>
                            @endforeach
                        </select>
                        @endhasanyrole

                        <select name="computer_lab_id" class="form-select form-select-sm ms-2 rounded" id="computerLabFilter">
                            <option value="">Semua Makmal Komputer</option>
                            @foreach ($computerLabList as $computerLab)
                            <option value="{{ $computerLab->id }}"
                                {{ Request::get('computer_lab_id') == $computerLab->id ? 'selected' : '' }}>
                                {{ $computerLab->name }}
                            </option>
                            @endforeach
                        </select>

                        <select name="month" class="form-select form-select-sm ms-2 rounded" id="monthFilter">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}"
                                {{ Request::get('month') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                                @endfor
                        </select>

                        <select name="year" class="form-select form-select-sm ms-2 rounded" id="yearFilter">
                            <option value="">Semua Tahun</option>
                            @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                            <option value="{{ $i }}"
                                {{ Request::get('year') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                            @endfor
                        </select>

                        <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                        <button type="submit" class="btn btn-primary ms-1 rounded" id="searchButton">
                            <i class="bx bx-search"></i>
                        </button>
                        <button type="button" class="btn btn-secondary ms-1 rounded" id="resetButton">
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kampus</th>
                        <th>Makmal Komputer</th>
                        <th>Pemilik</th>
                        <th>Bil. PC</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($labManagementList) > 0)
                    @foreach ($labManagementList as $labManagement)
                    <tr>
                        <td>{{ ($labManagementList->currentPage() - 1) * $labManagementList->perPage() + $loop->iteration }}
                        </td>
                        <td>{{ $labManagement->computerLab->campus->name }}</td>
                        <td>{{ $labManagement->computerLab->name }}</td>
                        <td>{{ $labManagement->computerLab->pemilik->name }}</td>
                        <td>{{ $labManagement->computer_no }}</td>
                        <td>{{ $labManagement->month }}</td>
                        <td>{{ $labManagement->year }}</td>
                        <td>{{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}</td>
                        <td>
                            @if (!in_array($labManagement->status, ['dihantar', 'telah_disemak']))
                            <a href="{{ route('lab-management.maintenance-records', ['labManagement' => $labManagement->id]) }}"
                                class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Selenggara PC">
                                <i class='bx bx-wrench'></i>
                            </a>

                            @hasanyrole('Pemilik|Superadmin')
                            <a href="{{ route('lab-management.edit', $labManagement->id) }}"
                                class="btn btn-info btn-sm" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Kemaskini">
                                <i class="bx bxs-edit"></i>
                            </a>
                            @endhasanyrole
                            @endif

                            <a href="{{ route('lab-management.show', $labManagement->id) }}"
                                class="btn btn-primary btn-sm" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Papar">
                                <i class="bx bx-show"></i>
                            </a>

                            @if (!in_array($labManagement->status, ['dihantar', 'telah_disemak']))
                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                data-bs-title="Padam">
                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $labManagement->id }}"><i
                                        class="bx bx-trash"></i></span>
                            </a>

                            @hasanyrole('Pemilik|Superadmin')
                            @php
                            // Ensure the variables are not null and have the correct types for comparison
                            $pcMaintenanceNo = $labManagement->pc_maintenance_no;
                            $computerNo = $labManagement->computer_no;
                            $pcDamageNo = $labManagement->pc_damage_no;
                            $totalPCMaintenance = $pcMaintenanceNo + $pcDamageNo;
                            $isDisabled = $totalPCMaintenance != $computerNo ? 'disabled' : '';
                            @endphp

                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#submitModal{{ $labManagement->id }}"
                                {{ $isDisabled }}>
                                <i class="bx bx-send"></i> Hantar
                            </button>
                            @endhasanyrole
                            @endif

                            @if (auth()->user()->hasRole('Pegawai Penyemak') && $labManagement->status === 'dihantar')
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#checkModal{{ $labManagement->id }}">
                                <i class="bx bx-check"></i> Semak
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="9">Tiada rekod</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                <form action="{{ route('lab-management.search') }}" method="GET" id="perPageForm"
                    class="d-flex align-items-center">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="campus_id" value="{{ request('campus_id') }}">
                    <input type="hidden" name="pemilik_id" value="{{ request('pemilik_id') }}">
                    <input type="hidden" name="computer_lab_id" value="{{ request('computer_lab_id') }}">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <input type="hidden" name="year" value="{{ request('year') }}">
                    <select name="perPage" id="perPage" class="form-select form-select-sm"
                        onchange="document.getElementById('perPageForm').submit()">
                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                        <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                        <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                    </select>
                </form>
            </div>

            <div class="d-flex justify-content-end align-items-center">
                <span class="mx-2 mt-2 small text-muted">
                    Menunjukkan {{ $labManagementList->firstItem() }} hingga {{ $labManagementList->lastItem() }} daripada
                    {{ $labManagementList->total() }} rekod
                </span>
                <div class="pagination-wrapper">
                    {{ $labManagementList->appends([
                                'search' => request('search'),
                                'perPage' => request('perPage'),
                                'campus_id' => request('campus_id'),
                                'pemilik_id' => request('pemilik_id'),
                                'computer_lab_id' => request('computer_lab_id'),
                                'month' => request('month'),
                                'year' => request('year'),
                            ])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Submit Modal -->
@include('pages.lab-management.submit-report')

<!-- Semak Modal -->
@include('pages.lab-management.check-report')

<!-- Delete Confirmation Modal -->
@foreach ($labManagementList as $labManagement)
<div class="modal fade" id="deleteModal{{ $labManagement->id }}" tabindex="-1"
    aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @isset($labManagement)
                Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">
                    {{ $labManagement->computerLab->code }} - {{ $labManagement->computerLab->name }}</span>?
                @else
                Error: Campus data not available.
                @endisset
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @isset($labManagement)
                <form class="d-inline" method="POST"
                    action="{{ route('lab-management.destroy', $labManagement->id) }}">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-danger">Padam</button>
                </form>
                @endisset
            </div>
        </div>
    </div>
</div>
@endforeach
<!--end page wrapper -->

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

<script>
    function checkForm(labManagementId) {
        document.getElementById('checkForm' + labManagementId).submit();
    }
</script>


<!-- Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit the form on input change
        document.getElementById('searchInput').addEventListener('input', function() {
            document.getElementById('searchForm').submit();
        });

        document.getElementById('campusFilter').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });

        document.getElementById('computerLabFilter').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });

        document.getElementById('pemilikFilter').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });

        document.getElementById('monthFilter').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });

        document.getElementById('yearFilter').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });

        document.getElementById('resetButton').addEventListener('click', function() {
            // Redirect to the base route to clear query parameters
            window.location.href = "{{ route('lab-management') }}";
        });

    });
</script>
@endsection