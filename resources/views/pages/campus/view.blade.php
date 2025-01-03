@extends('layouts.master')

@section('content')
<!-- Breadcrumb -->
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
            <div class="breadcrumb-title pe-3">Pengurusan Kampus</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('campus') }}">Senarai Kampus</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Maklumat {{ $campus->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
            <a href="{{ route('campus.edit', $campus->id) }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
            </a>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<h6 class="mb-0 text-uppercase">Maklumat {{ $campus->name }}</h6>
<hr />

<!-- Campus Information Table -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Nama Kampus</th>
                        <td>{{ $campus->name }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $campus->publish_status }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Campus Information Table -->
<!-- End Page Wrapper -->
@endsection