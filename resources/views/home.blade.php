@extends('layouts.master')
@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col">
            <form id="homeFilter" action="{{ route('home') }}" method="GET">
                <div class="d-flex flex-wrap justify-content-end">
                    @hasanyrole('Admin|Superadmin|Pegawai Penyemak')
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
                            <option value="makmal_komputer" {{ request('category') == 'makmal_komputer' ? 'selected' : '' }}>Makmal Komputer</option>
                            <option value="sudut_it" {{ request('category') == 'sudut_it' ? 'selected' : '' }}>Sudut IT</option>
                            <option value="pusat_data" {{ request('category') == 'pusat_data' ? 'selected' : '' }}>Pusat Data</option>
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
                        <select name="month" id="month" class="form-select">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}"
                                {{ Request::get('month', date('m')) == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                                @endfor
                        </select>
                    </div>
                    <div class="mb-2 ms-2 col-12 col-md-auto">
                        <select name="year" id="year" class="form-select">
                            <option value="" disabled>Pilih Tahun</option>
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
                </div>
            </form>
        </div>
    </div>
</div>

@hasanyrole('Superadmin|Admin|Pegawai Penyemak')
<div class="row row-cols-xl">
    <div class="col">
        <div
            class="card radius-10 border-start border-0 border-4 {{ $totalDihantarReports > 0 ? 'alert alert-warning' : 'alert alert-primary' }}">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-0 text-uppercase">Jumlah Laporan Menunggu Semakan</h5>
                        <h4 class="my-1 {{ $totalDihantarReports > 0 ? 'text-warning' : 'text-primary' }}">
                            {{ $totalDihantarReports > 0 ? $totalDihantarReports : 0 }}
                        </h4>
                    </div>
                    @if ($totalDihantarReports > 0)
                    <div class="ms-auto mt-3">
                        <a href="{{ route('lab-management') }}" class="btn btn-warning">Papar</a>
                    </div>
                    @else
                    <div class="ms-auto mt-3">
                        <a href="{{ route('lab-management') }}" class="btn btn-primary"
                            style="display: none;">Papar</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endhasanyrole

<div class="accordion mb-3" id="mainAccordion">

    <!-- Section 1: Senarai Makmal Komputer -->
    <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="headingSection1">
            <button class="accordion-button text-uppercase collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseSection1" aria-expanded="false" aria-controls="collapseSection1">
                Senarai Makmal Komputer / Sudut IT / Pusat Data {{ $currentMonthName }} {{ $currentYear }}
            </button>
        </h2>
        <div id="collapseSection1" class="accordion-collapse collapse" aria-labelledby="headingSection1">
            <div class="accordion-body">
                <div class="row row-cols-1 g-4"> <!-- Three-column responsive grid for campuses -->
                    @foreach ($ownersWithLabs as $campusId => $labs)
                    <div class="col">
                        <div class="card border-secondary h-100">
                            <div class="card-header bg-light">
                                <h6 class="text-uppercase text-center mb-0">
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
                                        $counter = 1; // Initialize counter for continuous numbering
                                        $totalPCs = 0; // Initialize total PC counter
                                        @endphp
                                        @foreach ($labsGroupedByOwner as $ownerId => $ownerLabs)
                                        @foreach ($ownerLabs as $labIndex => $lab)
                                        <tr>
                                            <td class="text-center">{{ $counter++ }}</td>
                                            <td>{{ $lab->name }}</td>
                                            <td class="text-center">{{ $lab->pemilik->name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-dark" style="font-size: 0.80rem; font-weight: 500;">
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
            </div>
        </div>
    </div>

    <!-- Section 3: Senarai Makmal Komputer Telah Diselenggara -->
    <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="headingSection3">
            <button class="accordion-button collapsed text-uppercase" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseSection3" aria-expanded="false" aria-controls="collapseSection3">
                Status Selenggara Makmal Komputer / Sudut IT / Pusat Data {{ $currentYear }}
            </button>
        </h2>
        <div id="collapseSection3" class="accordion-collapse collapse" aria-labelledby="headingSection3">
            <div class="accordion-body">
                <div class="row mt-3">
                    @foreach ($campusData as $campusItem)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body text-uppercase">
                            <h4>{{ $campusItem['campus']->name }}</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover">
                                    <thead style="background-color: #ddd;" class="text-center">
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
                                            <td><span
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="right" 
                                                title="{{ $lab->pemilik->name }}">
                                                {{ $lab->name }}
                                            </span></td>
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
                </div>
            </div>
        </div>
    </div>
</div>


@php
$monthName = DateTime::createFromFormat('!m', $currentMonth)->format('F');
@endphp
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
    <div class="col">
        <div class="card radius-10 border-success border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Komputer Sewaan</span></p>
                        <p class="mb-0 text-uppercase fw-bold"><span class="fst-italic">Selesai Diselenggara</span>
                            ({{ $monthName }}, {{ $currentYear }})</p>
                        <h4 class="my-1 text-success">{{ $totalMaintenancePC > 0 ? $totalMaintenancePC : 0 }}</h4>
                    </div>
                    <div class="text-success ms-auto font-35"><i class='bx bx-check-square'></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 border-danger border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Komputer Sewaan</span></p>
                        <p class="mb-0 text-uppercase fw-bold"><span class="fst-italic">Rosak/Keluar</span>
                            ({{ $monthName }}, {{ $currentYear }})</b>
                        </p>
                        <h4 class="my-1 text-danger">{{ $totalDamagePC > 0 ? $totalDamagePC : 0 }}</h4>
                    </div>
                    <div class="text-danger ms-auto font-35"><i class="bx bx-error broken-computer-icon"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 border-start border-0 border-4" style="border-color: #FFBF00 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Komputer Sewaan</span></p>
                        <p class="mb-0 text-uppercase fw-bold"><span class="fst-italic">Belum Diselenggara</span>
                            ({{ $monthName }}, {{ $currentYear }})
                        </p>
                        <h4 class="my-1" style="color: #FFBF00;">
                            {{ $totalUnmaintenancePC > 0 ? $totalUnmaintenancePC : 0 }}
                        </h4>
                    </div>
                    <div class="ms-auto font-35" style="color: #FFBF00;"><i class='bx bx-time-five'></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card alert alert-info">
    <div class="card-body">
        <h5>MAKLUMAN</h5>
        @if ($announcements->isNotEmpty())
        @foreach ($announcements as $announcement)
        <div class="card mb-3">
            <div class="card-body bg-white">
                <div class="float-end text-primary"><i>{{ $announcement->created_at->format('j F Y') }}</i></div>
                <p class="card-title text-primary text-uppercase fw-bold">{{ $announcement->title }}</p>
                <p class="card-text text-dark">{!! nl2br(e($announcement->desc ?? '-')) !!}</p>
            </div>
        </div>
        @endforeach
        @else
        <div class="card-body bg-white" role="alert">
            Tiada makluman
        </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('homeFilter').addEventListener('change', function() {
        this.submit();
    });

    document.getElementById('resetButton').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default reset behavior
        const url = new URL(window.location.href);
        url.searchParams.delete('campus_id');
        url.searchParams.delete('computer_lab_id');
        url.searchParams.delete('category');
        url.searchParams.delete('month');
        url.searchParams.delete('year');
        window.location.href = url.toString(); // Redirect to the URL with reset filters
    });
</script>
@endsection