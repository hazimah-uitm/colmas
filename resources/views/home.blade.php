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
        <div class="card radius-10 alert alert-info text-dark">
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

        @hasanyrole('Superadmin|Admin|Pegawai Penyemak')
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
                <div class="mb-3">
                    <label for="publish_status" class="form-label">Status</label>
                    <div class="form-check">
                        <input type="radio" id="aktif" name="publish_status" value="1"
                            class="form-check-input {{ $errors->has('publish_status') ? 'is-invalid' : '' }}"
                            {{ ($announcement->publish_status ?? '') == 'Aktif' ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktif">Aktif</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" id="tidak_aktif" name="publish_status" value="0"
                            class="form-check-input {{ $errors->has('publish_status') ? 'is-invalid' : '' }}"
                            {{ ($announcement->publish_status ?? '') == 'Tidak Aktif' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tidak_aktif">Tidak Aktif</label>
                    </div>
                    @if ($errors->has('publish_status'))
                    <div class="invalid-feedback d-block">
                        @foreach ($errors->get('publish_status') as $error)
                        {{ $errors->first('publish_status') }}
                        @endforeach
                    </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">{{ isset($announcement) ? 'Kemas Kini' : 'Tambah' }}
                    Makluman</button>
            </form>
        </div>
        @endhasanyrole

        <hr>

        <!-- Display makluman -->
        @php
        // Filter active announcements for Pemilik role
        $activeAnnouncements = $announcements->filter(function ($announcement) {
        return $announcement->publish_status === 'Aktif';
        });
        @endphp

        @forelse($announcements as $announcement)
        <!-- Check if the user is Superadmin or Admin to see all announcements -->
        @if (auth()->user()->hasAnyRole(['Superadmin', 'Admin', 'Pegawai Penyemak']))
        <div class="card mb-3">
            <div class="card-body bg-white">
                <h6 class="card-title">{{ $announcement->title }}</h6>
                <p class="card-text">{{ $announcement->desc }}</p>
                <p class="card-footer">{{ $announcement->created_at->format('j F Y') }}
                    @if ($announcement->publish_status === 'Aktif')
                    | <span class="card-text badge bg-success">Aktif</span>
                    @else
                    <span class="card-text badge bg-danger">Tidak Aktif</span>
                    @endif
                </p>

                <a href="{{ route('home.edit', $announcement->id) }}" class="btn btn-warning">Kemas Kini</a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $announcement->id }}">Padam</button>
            </div>
        </div>

        @elseif (auth()->user()->hasRole('Pemilik') && $announcement->publish_status === 'Aktif')
        <!-- Pemilik role can see only Aktif announcements -->
        <div class="card mb-3">
            <div class="card-body bg-white">
                <h6 class="card-title">{{ $announcement->title }}</h6>
                <p class="card-text">{{ $announcement->desc }}</p>
                <p class="card-footer">{{ $announcement->created_at->format('j F Y') }}</p>
            </div>
        </div>
        @endif
        @empty
        <!-- Show message when there are no announcements -->
        <div class="alert alert-info" role="alert">
            Tiada makluman
        </div>
        @endforelse

        @if ($activeAnnouncements->isEmpty() && auth()->user()->hasRole('Pemilik'))
        <div class="alert alert-info" role="alert">
            Tiada makluman
        </div>
        @endif

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal{{ $announcement->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">{{ $announcement->title }}</span>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <form class="d-inline" method="POST" action="{{ route('home.destroy', $announcement->id) }}">
                            {{ method_field('delete') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger">Padam</button>
                        </form>
                    </div>
                </div>
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