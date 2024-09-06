@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Senarai Semak Makmal</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lab-checklist') }}">Senarai Semak Makmal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Maklumat {{ $labChecklist->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('lab-checklist.edit', $labChecklist->id) }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
            </a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <h6 class="mb-0 text-uppercase">Maklumat {{ $labChecklist->title }}</h6>
    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Perkara</th>
                            <td>{{ $labChecklist->title }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $labChecklist->publish_status }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Wrapper -->
@endsection
