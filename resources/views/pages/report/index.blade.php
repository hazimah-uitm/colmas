@extends('layouts.master')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Laporan Selenggara Makmal Komputer</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Senarai Laporan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Senarai Laporan Selenggara Makmal Komputer</h6>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-lg">
                    <form action="{{ route('report') }}" method="GET" id="searchForm"
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

                            <select name="computer_lab_id" class="form-select form-select-sm ms-2 rounded"
                                id="computerLabFilter">
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
                <table class="table table-condensed table-striped table-bordered table-hover">
                    <thead class="table-light text-center text-uppercase">
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
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $labManagement->computerLab->campus->name }}</td>
                                    <td>{{ $labManagement->computerLab->name }}</td>
                                    <td>{{ $labManagement->computerLab->pemilik->name }}</td>
                                    <td>{{ $labManagement->computer_no }}</td>
                                    <td>{{ $labManagement->month }}</td>
                                    <td>{{ $labManagement->year }}</td>
                                    <td class="text-center">{{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('report.show', $labManagement->id) }}"
                                            class="btn btn-primary btn-sm" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" title="Papar">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ route('report.pdf', $labManagement->id) }}" class="btn btn-info btn-sm"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Muat turun"
                                            target="_blank">
                                            <i class='bx bxs-file-pdf'></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <td colspan="9">Tiada rekod</td>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                    <form action="{{ route('report') }}" method="GET" id="perPageForm" class="d-flex align-items-center">
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
                        Menunjukkan {{ $labManagementList->firstItem() }} hingga {{ $labManagementList->lastItem() }}
                        daripada
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
                window.location.href = "{{ route('report') }}";
            });

        });
    </script>
@endsection
