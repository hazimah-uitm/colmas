@extends('layouts.master')

@section('content')
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
            <div class="breadcrumb-title pe-3">Pengurusan Makluman</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('announcement') }}">Senarai Makluman</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Maklumat {{ $announcement->title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
            <a href="{{ route('announcement.edit', $announcement->id) }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
            </a>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<h6 class="mb-0 text-uppercase">Maklumat {{ $announcement->title }}</h6>
<hr />

<!-- announcement Information Table -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Tajuk Makluman</th>
                        <td>{{ $announcement->title }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{!! nl2br(e($announcement->desc ?? '-')) !!}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $announcement->publish_status }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End announcement Information Table -->
<!-- End Page Wrapper -->
@endsection