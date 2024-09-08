@extends('layouts.master')
@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
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
<!--end breadcrumb-->
<h6 class="mb-0 text-uppercase">Senarai Laporan Selenggara Makmal Komputer</h6>
<hr />
<div class="card">
    <div class="card-body">
        <div class="d-lg-flex justify-content-end align-items-center mb-4 gap-2">
            <div class="ms-auto">
                <form id="report" action="{{ route('report') }}" method="GET">
                    <div class="d-flex flex-wrap justify-content-end">
                        @hasanyrole('Admin|Superadmin')
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="campus_id" id="campus_id" class="form-select">
                                <option value="">Semua Kampus</option>
                                @foreach ($campusList as $campus)
                                <option value="{{ $campus->id }}" {{ Request::get('campus_id') == $campus->id ? 'selected' : '' }}>
                                    {{ $campus->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endhasanyrole
                        @hasanyrole('Pegawai Penyemak|Superadmin')
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="pemilik_id" id="pemilik_id" class="form-select">
                                <option value="">Semua Pemilik</option>
                                @foreach ($pemilikList as $pemilik)
                                <option value="{{ $pemilik->id }}" {{ Request::get('pemilik_id') == $pemilik->id ? 'selected' : '' }}>
                                    {{ $pemilik->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endhasanyrole
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="computer_lab_id" id="computer_lab_id" class="form-select">
                                <option value="">Semua Makmal Komputer</option>
                                @foreach ($computerLabList as $computerLab)
                                <option value="{{ $computerLab->id }}" {{ Request::get('computer_lab_id') == $computerLab->id ? 'selected' : '' }}>
                                    {{ $computerLab->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="month" id="month" class="form-select">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ Request::get('month') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                    @endfor
                            </select>
                        </div>
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="year" id="year" class="form-select">
                                <option value="">Semua Tahun</option>
                                @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                                <option value="{{ $i }}" {{ Request::get('year') == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <button id="resetButton" class="btn btn-primary">Reset</button>
                        </div>
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
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $labManagement->computerLab->campus->name }}</td>
                        <td>{{ $labManagement->computerLab->name }}</td>
                        <td>{{ $labManagement->computerLab->pemilik->name }}</td>
                        <td>{{ $labManagement->computer_no }}</td>
                        <td>{{ $labManagement->month }}</td>
                        <td>{{ $labManagement->year }}</td>
                        <td>{{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}</td>
                        <td>
                            <a href="{{ route('report.show', $labManagement->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Papar">
                                <i class="bx bx-show"></i>
                            </a>
                            <a href="{{ route('report.pdf', $labManagement->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Muat turun" target="_blank">
                                <i class='bx bxs-file-pdf'></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <td colspan="7">Tiada rekod</td>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                <form action="{{ route('report') }}" method="GET" id="perPageForm">
                    <select name="perPage" id="perPage" class="form-select" onchange="document.getElementById('perPageForm').submit()">
                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                        <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                        <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                    </select>
                </form>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <div class="mx-1 mt-2">{{ $labManagementList->firstItem() }} –
                    {{ $labManagementList->lastItem() }} dari
                    {{ $labManagementList->total() }} rekod
                </div>
                <div>{{ $labManagementList->links() }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#campus_id, #pemilik_id, #computer_lab_id, #month, #year').on('change', function() {
            // Submit the form when either dropdown changes
            $('#report').submit();
        });
    });

    document.getElementById('resetButton').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default reset behavior
        const url = new URL(window.location.href);
        url.searchParams.delete('campus_id');
        url.searchParams.delete('pemilik_id');
        url.searchParams.delete('computer_lab_id');
        url.searchParams.delete('month');
        url.searchParams.delete('year');
        window.location.href = url.toString(); // Redirect to the URL with reset filters
    });
</script>
@endsection