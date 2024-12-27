@extends('layouts.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Senarai Makmal Komputer</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Senarai Makmal Komputer</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Senarai Makmal Komputer</h6>
    <hr />
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col">
                <form id="homeFilter" action="{{ route('computer-lab-report') }}" method="GET">
                    <div class="d-flex flex-wrap justify-content-end align-items-center gap-1">
                        @hasanyrole('Admin|Superadmin|Pegawai Penyemak')
                            <div>
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
                            <div>
                                <select name="category" id="category" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <option value="makmal_komputer"
                                        {{ request('category') == 'makmal_komputer' ? 'selected' : '' }}>
                                        Makmal Komputer
                                    </option>
                                    <option value="sudut_it" {{ request('category') == 'sudut_it' ? 'selected' : '' }}>Sudut IT
                                    </option>
                                    <option value="pusat_data" {{ request('category') == 'pusat_data' ? 'selected' : '' }}>Pusat
                                        Data</option>
                                </select>
                            </div>
                        @endhasanyrole
                        <div>
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
                        <div>
                            <select name="month" id="month" class="form-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}"
                                        {{ Request::get('month', date('m')) == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
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
                        <div>
                            <button id="resetButton" class="btn btn-primary">Reset</button>
                        </div>
                        <a href="{{ route('computer-lab-report.download-pdf', request()->query()) }}" target="_blank"
                            class="btn btn-info mb-0">Muat Turun PDF</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="row row-cols-1 g-4">
        @foreach ($ownersWithLabs as $campusId => $labs)
            <div class="col">
                <div class="card border-secondary h-100">
                    <div class="card-header bg-light">
                        <h6 class="text-uppercase text-center mb-0 fw-bold">
                            {{ $labs->first()->campus->name ?? 'N/A' }}
                        </h6> <!-- Campus name -->
                    </div>
                    <div class="card-body">
                        @php
                            $labsGroupedByOwner = $labs->groupBy('pemilik_id');
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead class="table-light text-center text-uppercase">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Ruang</th>
                                        <th>Pemilik</th>
                                        <th>Jumlah PC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $counter = 1;
                                        $totalPCs = 0; 
                                    @endphp
                                    @foreach ($labsGroupedByOwner as $ownerId => $ownerLabs)
                                        @foreach ($ownerLabs as $labIndex => $lab)
                                            <tr>
                                                <td class="text-center">{{ $counter++ }}</td>
                                                <td>{{ $lab->name }}</td>
                                                <td class="text-center">{{ $lab->pemilik->name ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-info text-dark"
                                                        style="font-size: 0.80rem; font-weight: 500;">
                                                        {{ $lab->pc_count }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @php
                                            $totalPCs += $lab->pc_count; // Accumulate total PCs
                                            @endphp
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light text-center text-uppercase">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Jumlah PC</strong></td>
                                        <td class="text-center">
                                            <strong>{{ $totalPCs }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <script>
        document.getElementById('homeFilter').addEventListener('change', function() {
            this.submit();
        });

        document.getElementById('resetButton').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default reset behavior
            const url = new URL(window.location.href);
            url.searchParams.delete('month');
            url.searchParams.delete('year');
            url.searchParams.delete('category');
            url.searchParams.delete('campus_id');
            url.searchParams.delete('computer_lab_id');
            window.location.href = url.toString();
        });
    </script>
@endsection
