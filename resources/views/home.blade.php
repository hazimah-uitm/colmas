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
                    @hasanyrole('Pegawai Penyemak|Superadmin')
                    <div class="mb-2 ms-2 col-12 col-md-auto">
                        <select name="pemilik_id" id="pemilik_id" class="form-select">
                            <option value="">Semua Pemilik</option>
                            @foreach ($pemilikList as $pemilik)
                            <option value="{{ $pemilik->id }}"
                                {{ Request::get('pemilik_id') == $pemilik->id ? 'selected' : '' }}>
                                {{ $pemilik->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endhasanyrole
                    <div class="mb-2 ms-2 col-12 col-md-auto">
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach ($statusList as $status)
                            <option value="{{ $status }}"
                                {{ Request::get('status') == $status ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
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
                                {{ Request::get('month') == $i ? 'selected' : '' }}>
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
                                {{ Request::get('year') == $i ? 'selected' : '' }}>
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
        <div class="card radius-10 border-start border-0 border-4 {{ $totalDihantarReports > 0 ? 'alert alert-warning' : 'alert alert-primary' }}">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Jumlah Laporan Menunggu Semakan</p>
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
                        <a href="{{ route('lab-management') }}" class="btn btn-primary" style="display: none;">Papar</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endhasanyrole

@hasanyrole('Superadmin|Admin|Pemilik')
<div class="row row-cols-xl">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 {{ $totalUnmaintainedLabs > 0 ? 'alert alert-warning' : 'alert alert-primary' }}">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Jumlah Makmal Belum Diselenggara</p>
                        <h4 class="my-1 {{ $totalUnmaintainedLabs > 0 ? 'text-warning' : 'text-primary' }}">
                            {{ $totalUnmaintainedLabs > 0 ? $totalUnmaintainedLabs : 0 }}
                        </h4>
                    </div>
                    @if ($totalUnmaintainedLabs > 0)
                    <div class="ms-auto mt-3">
                        <a href="{{ route('lab-management') }}" class="btn btn-warning">Selenggara</a>
                    </div>
                    @else
                    <div class="ms-auto mt-3">
                        <a href="{{ route('lab-management') }}" class="btn btn-primary" style="display: none;">Selenggara</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endhasanyrole

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-2">
    <div class="col">
        <div class="card radius-10 border-info border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Jumlah Makmal Komputer</p>
                        <h4 class="text-info my-1">{{ $totalLab > 0 ? $totalLab : 0 }}</h4>
                    </div>
                    <div class="text-info ms-auto font-35"><i class="bx bxs-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-secondary border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Jumlah Makmal Komputer Belum Diselenggara</p>
                        <h4 class="text-secondary my-1">{{ $totalUnmaintainedLabs > 0 ? $totalUnmaintainedLabs : 0 }}
                        </h4>
                    </div>
                    <div class="text-secondary ms-auto font-35"><i class="bx bx-wrench"></i>
                    </div>
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
                        <p class="mb-0">Jumlah Komputer</p>
                        <h4 class="text-primary my-1">{{ $totalPC > 0 ? $totalPC : 0 }}</h4>
                    </div>
                    <div class="text-primary ms-auto font-35"><i class="bx bx-desktop computer-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-success border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Jumlah Komputer Selesai Diselenggara</p>
                        <h4 class="my-1 text-success">{{ $totalMaintenancePC > 0 ? $totalMaintenancePC : 0 }}</h4>
                    </div>
                    <div class="text-success ms-auto font-35"><i class='bx bx-check-square'></i></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-2">
    <div class="col">
        <div class="card radius-10 border-danger border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Jumlah Komputer Rosak</p>
                        <h4 class="my-1 text-danger">{{ $totalDamagePC > 0 ? $totalDamagePC : 0 }}</h4>
                    </div>
                    <div class="text-danger ms-auto font-35"><i class="bx bx-error broken-computer-icon"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-warning border-start border-0 border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Jumlah Komputer Belum Diselenggara</p>
                        <h4 class="my-1 text-warning">{{ $totalUnmaintenancePC > 0 ? $totalUnmaintenancePC : 0 }}</h4>
                    </div>
                    <div class="text-warning ms-auto font-35"><i class='bx bx-time-five'></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>Makluman</h5>

        @hasanyrole('Superadmin|Admin')
        <div class="mb-4 bg-white p-4 border rounded">
            <form action="{{ isset($announcement) ? route('home.update', $announcement->id) : route('home.store') }}"
                method="POST">
                {{ csrf_field() }}
                @if (isset($announcement))
                {{ method_field('put') }}
                @endif
                <div class="mb-3">
                    <label for="title" class="form-label">Tajuk</label>
                    <input type="text" class="form-control" id="title" name="title"
                        value="{{ $announcement->title ?? old('title') }}" required>
                </div>
                <div class="mb-3">
                    <label for="desc" class="form-label">Keterangan</label>
                    <textarea class="form-control" id="desc" name="desc" rows="3" required>{{ $announcement->desc ?? old('desc') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">{{ isset($announcement) ? 'Kemas Kini' : 'Tambah' }}
                    Makluman</button>
            </form>
        </div>
        @endhasanyrole

        <hr>

        <!-- Display makluman -->
        @forelse($announcements as $announcement)
        <div class="card mb-3">
            <div class="card-body bg-white">
                <h6 class="card-title">{{ $announcement->title }}</h6>
                <p class="card-text">{{ $announcement->desc }}</p>
                <p class="card-footer text-muted"><i>{{ $announcement->created_at->format('j F Y') }}</i></p>

                @hasanyrole('Superadmin|Admin')
                <a href="{{ route('home.edit', $announcement->id) }}" class="btn btn-warning">Kemas Kini</a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $announcement->id }}">Padam</button>
                @endhasanyrole
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal{{ $announcement->id }}" tabindex="-1"
            aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti ingin memadam rekod <span
                            style="font-weight: 600;">{{ $announcement->title }}</span>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <form class="d-inline" method="POST"
                            action="{{ route('home.destroy', $announcement->id) }}">
                            {{ method_field('delete') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger">Padam</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info" role="alert">
            Tiada makluman
        </div>
        @endforelse
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
        url.searchParams.delete('pemilik_id');
        url.searchParams.delete('computer_lab_id');
        url.searchParams.delete('status');
        url.searchParams.delete('month');
        url.searchParams.delete('year');
        window.location.href = url.toString(); // Redirect to the URL with reset filters
    });
</script>
@endsection