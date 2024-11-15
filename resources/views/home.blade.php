@extends('layouts.master')
@section('content')
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col">
                <form id="homeFilter" action="{{ route('home') }}" method="GET">
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
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="computer_lab_id" id="computer_lab_id" class="form-select">
                                <option value="">Semua Makmal Komputer</option>
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

        <!-- Section 1: Senarai Makmal Komputer mengikut Pemilik -->
        <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="headingSection1">
                <button class="accordion-button text-uppercase collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseSection1" aria-expanded="false" aria-controls="collapseSection1">
                    Senarai Makmal Komputer mengikut Pemilik
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
                                            {{ $labs->first()->campus->name ?? 'N/A' }}</h6> <!-- Campus name -->
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $labsGroupedByOwner = $labs->groupBy('pemilik_id');
                                        @endphp
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%; background-color: #ddd; text-align: center">No.</th>
                                                    <th style="width: 25%; background-color: #ddd;">Pemilik</th>
                                                    <th style="background-color: #ddd;">Makmal Komputer</th>
                                                    <th style="width: 10%; background-color: #ddd; text-align: center">Bil.
                                                        PC</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($labsGroupedByOwner as $ownerId => $ownerLabs)
                                                    <tr>
                                                        <td style="text-align: center" rowspan="{{ $ownerLabs->count() }}">
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td rowspan="{{ $ownerLabs->count() }}">
                                                            {{ $ownerLabs->first()->pemilik->name ?? 'N/A' }}</td>
                                                        <td>{{ $ownerLabs->first()->name }}</td>
                                                        <td style="text-align: center">
                                                            <span class="badge bg-info text-dark"
                                                                style="font-size: 0.80rem; font-weight: 500;">
                                                                {{ $ownerLabs->first()->pc_count }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    @foreach ($ownerLabs->slice(1) as $lab)
                                                        <tr>
                                                            <td>{{ $lab->name }}</td>
                                                            <td style="text-align: center">
                                                                <span class="badge bg-info text-dark"
                                                                    style="font-size: 0.80rem; font-weight: 500;">
                                                                    {{ $lab->pc_count }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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

        <!-- Section 3: Senarai Makmal Komputer Telah Diselenggara -->
        <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="headingSection3">
                <button class="accordion-button collapsed text-uppercase" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseSection3" aria-expanded="false" aria-controls="collapseSection3">
                    Status Selenggara Makmal Komputer {{ $currentYear }}
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
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">No.</th>
                                                    <th>Makmal Komputer</th>
                                                    @foreach ($months as $month)
                                                        <th class="text-center">{{ date('M', mktime(0, 0, 0, $month, 1)) }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($campusItem['computerLabList'] as $lab)
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
                            <p class="mb-0 text-uppercase fw-bold"><span class="fst-italic">Rosak</span>
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
                                {{ $totalUnmaintenancePC > 0 ? $totalUnmaintenancePC : 0 }}</h4>
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
                            <div class="float-end text-dark"><i>{{ $announcement->created_at->format('j F Y') }}</i></div>
                            <p class="card-title text-primary text-uppercase fw-bold">{{ $announcement->title }}</p>
                            <ul>
                                <li>
                                    <p class="card-text">{!! nl2br(e($announcement->desc ?? '-')) !!}</p>
                                </li>
                            </ul>
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
            url.searchParams.delete('status');
            url.searchParams.delete('month');
            url.searchParams.delete('year');
            window.location.href = url.toString(); // Redirect to the URL with reset filters
        });
    </script>
@endsection
