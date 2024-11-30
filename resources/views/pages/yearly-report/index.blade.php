@extends('layouts.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Laporan Tahunan Selenggara Makmal Komputer</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Laporan Tahunan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Laporan Tahunan</h6>
    <hr />
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col">
                <form id="homeFilter" action="{{ route('yearly-report') }}" method="GET">
                    <div class="d-flex flex-wrap justify-content-end">
                        @hasanyrole('Admin|Superadmin')
                            <div class="mb-2 ms-2 col-12 col-md-auto">
                                <select name="campus_id" id="campus_id" class="form-select">
                                    <option value="">Semua Kampus</option>
                                    @foreach ($campusList as $campus)
                                        <option value="{{ $campus->id }}"
                                            {{ Request::get('campus_id') == $campus->id ? 'selected' : '' }}>
                                            {{ $campus->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endhasanyrole
                        @hasanyrole('Admin|Superadmin|Pegawai Penyemak')
                            <div class="mb-2 ms-2 col-12 col-md-auto">
                                <select name="category" id="category" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <option value="makmal_komputer"
                                        {{ request('category') == 'makmal_komputer' ? 'selected' : '' }}>Makmal Komputer
                                    </option>
                                    <option value="sudut_it" {{ request('category') == 'sudut_it' ? 'selected' : '' }}>Sudut IT
                                    </option>
                                    <option value="pusat_data" {{ request('category') == 'pusat_data' ? 'selected' : '' }}>Pusat
                                        Data</option>
                                </select>
                            </div>
                        @endhasanyrole
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="computer_lab_id" id="computer_lab_id" class="form-select">
                                <option value="">Semua Ruang</option>
                                @foreach ($computerLabList as $computerLab)
                                    <option value="{{ $computerLab->id }}"
                                        {{ Request::get('computer_lab_id') == $computerLab->id ? 'selected' : '' }}>
                                        {{ $computerLab->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="year" id="year" class="form-select">
                                <option value="">Semua Tahun</option>
                                @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                                    <option value="{{ $i }}"
                                        {{ Request::get('year', date('Y')) == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <button id="resetButton" class="btn btn-primary">Reset</button>
                        </div>
                        &nbsp;
                        <a href="{{ route('yearly-report.download-pdf', ['year' => $currentYear]) }}" target="_blank"
                            class="btn btn-info mb-3">Muat Turun PDF</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Display Data for Each Campus in Separate Cards -->
    @foreach ($campusData as $campusItem)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="text-uppercase text-center mb-0 fw-bold">
                    {{ $campusItem['campus']->name }}
                </h6>
            </div>
            <div class="card-body text-uppercase">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover">
                        <thead class="table-light text-center text-uppercase">
                            <tr>
                                <th>No.</th>
                                <th>Nama Ruang</th>
                                @foreach ($months as $month)
                                    <th>{{ date('M', mktime(0, 0, 0, $month, 1)) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campusItem['computerLabs'] as $lab)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $lab->name }}</td>
                                    @foreach ($months as $month)
                                        <td class="text-center">
                                            <span style="font-size: 12px;">
                                                {{ $campusItem['maintainedLabsPerMonth'][$month]->get($lab->id) ? '✔️' : '❌' }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.getElementById('homeFilter').addEventListener('change', function() {
            this.submit();
        });

        document.getElementById('resetButton').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default reset behavior
            const url = new URL(window.location.href);
            url.searchParams.delete('year');
            url.searchParams.delete('category');
            url.searchParams.delete('campus_id');
            url.searchParams.delete('computer_lab_id');
            window.location.href = url.toString();
        });
    </script>
@endsection
