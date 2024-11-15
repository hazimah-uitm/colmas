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
            <form id="homeFilter" action="{{ route('computer-lab-report') }}" method="GET">
                <div class="d-flex flex-wrap justify-content-end">
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
                            <option value="">Semua Tahun</option>
                            @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                            <option value="{{ $i }}" {{ Request::get('year', date('Y')) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-2 ms-2 col-12 col-md-auto">
                        <button id="resetButton" class="btn btn-primary">Reset</button>
                    </div>
                    &nbsp;
                    <a href="{{ route('computer-lab-report.download-pdf', ['year' => $currentYear]) }}" class="btn btn-info mb-3">Muat Turun PDF</a>
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
                <h6 class="text-uppercase text-center mb-0">
                    {{ $labs->first()->campus->name ?? 'N/A' }}
                </h6> <!-- Campus name -->
            </div>
            <div class="card-body">
                @php
                $labsGroupedByOwner = $labs->groupBy('pemilik_id');
                @endphp
                <div class="table-responsive">
                <table class="table table-condensed table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="bg-dark text-white text-center">No.</th>
                            <th class="bg-dark text-white">Makmal Komputer</th>
                            <th class="bg-dark text-white text-center">Pemilik</th>
                            <th class="bg-dark text-white text-center">Jumlah PC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $counter = 1; // Initialize counter for continuous numbering
                        @endphp
                        @foreach ($labsGroupedByOwner as $ownerId => $ownerLabs)
                        @foreach ($ownerLabs as $labIndex => $lab)
                        <tr>
                            <td style="text-align: center">{{ $counter++ }}</td>
                            <td>{{ $lab->name }}</td>
                            <td class="text-center">{{ $lab->pemilik->name ?? 'N/A' }}</td>
                            <td style="text-align: center">
                                <span class="badge bg-info text-dark" style="font-size: 0.80rem; font-weight: 500;">
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
        window.location.href = url.toString();
    });
</script>
@endsection