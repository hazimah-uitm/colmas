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
                            <option value="">Semua Bulan</option>
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

<div class="row row-cols-xl">
    <div class="col">
        <div class="card radius-10 border-danger border-start border-0 border-4">
            <div class="card-body">
                <h5 class="mb-0 text-uppercase">Senarai Makmal Komputer Belum Diselenggara {{ $currentYear }}</h5>
                <div class="row mt-3">
                    @foreach ($unmaintainedLabsPerMonth as $month => $unmaintainedLabs)
                    <div class="col-md-6 col-lg-4 mb-3"> <!-- Adjust the column width here -->
                        <strong>{{ date('F', mktime(0, 0, 0, $month, 1)) }}:</strong>
                        @if ($unmaintainedLabs->isEmpty())
                        <p class="text-success">Semua makmal telah diselenggara</p>
                        @else
                        <ul>
                            @foreach ($unmaintainedLabs as $lab)
                            <li>{{ $lab->name }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-xl">
    <div class="col">
        <div class="card radius-10 border-success border-start border-0 border-4">
            <div class="card-body">
                <h5 class="mb-0 text-uppercase">Senarai Makmal Komputer Telah Diselenggara {{ $currentYear }}</h5>
                <div class="row mt-3">
                    @foreach ($maintainedLabsPerMonth as $month => $maintainedLabs)
                    <div class="col-md-6 col-lg-4 mb-3"> <!-- Adjust the column width here -->
                        <strong>{{ date('F', mktime(0, 0, 0, $month, 1)) }}:</strong>
                        @if ($maintainedLabs->isEmpty())
                        <p class="text-danger">Semua makmal belum diselenggara</p>
                        @else
                        <ul>
                            @foreach ($maintainedLabs as $lab)
                            <li>{{ $lab->name }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row row-cols-1 row-cols-md-2 row-cols-xl-2">
    <div class="col">
        <div class="card radius-10 border-primary border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Makmal Komputer</span></p>
                        <h4 class="text-primary my-1">{{ $totalLab > 0 ? $totalLab : 0 }}</h4>
                    </div>
                    <div class="text-primary ms-auto font-35"><i class="bx bxs-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-primary border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Komputer Sewaan</span></p>
                        <h4 class="text-primary my-1">{{ $totalPC > 0 ? $totalPC : 0 }}</h4>
                    </div>
                    <div class="text-primary ms-auto font-35"><i class="bx bx-desktop computer-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
    <div class="col">
        <div class="card radius-10 border-primary border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Komputer Sewaan</span></p>
                        <p class="mb-0 text-uppercase">Selesai Diselenggara</p>
                        <h4 class="my-1 text-primary">{{ $totalMaintenancePC > 0 ? $totalMaintenancePC : 0 }}</h4>
                    </div>
                    <div class="text-primary ms-auto font-35"><i class='bx bx-check-square'></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="card radius-10 border-primary border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Komputer Sewaan</span></p>
                        <p class="mb-0 text-uppercase">Rosak</p>
                        <h4 class="my-1 text-primary">{{ $totalDamagePC > 0 ? $totalDamagePC : 0 }}</h4>
                    </div>
                    <div class="text-primary ms-auto font-35"><i class="bx bx-error broken-computer-icon"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 border-primary border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-uppercase">Jumlah <span class="fw-bold">Komputer Sewaan</span></p>
                        <p class="mb-0 text-uppercase">Belum Diselenggara</p>
                        <h4 class="my-1 text-primary">{{ $totalUnmaintenancePC > 0 ? $totalUnmaintenancePC : 0 }}</h4>
                    </div>
                    <div class="text-primary ms-auto font-35"><i class='bx bx-time-five'></i></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <h5>Makluman</h5>
        @if ($announcements->isNotEmpty())
        @foreach ($announcements as $announcement )
        <div class="card mb-3">
            <div class="card-body bg-white">
                <h6 class="card-title">{{ $announcement->title }}</h6>
                <p class="card-text">{{ $announcement->desc }}</p>
                <p class="card-footer">{{ $announcement->created_at->format('j F Y') }}</p>
            </div>
        </div>
        @endforeach
        @else
        <div class="alert alert-info" role="alert">
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