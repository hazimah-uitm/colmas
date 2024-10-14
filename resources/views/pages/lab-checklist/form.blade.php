@extends('layouts.master')

@section('content')
<!-- Breadcrumb -->
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
            <div class="breadcrumb-title pe-3">Pengurusan Senarai Semak Makmal</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lab-checklist') }}">Senarai Senarai Semak Makmal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $str_mode }} Senarai Semak Makmal</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<h6 class="mb-0 text-uppercase">{{ $str_mode }} Senarai Semak Makmal</h6>
<hr />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $save_route }}">
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="title" class="form-label">Perkara</label>
                <input type="text" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" id="title"
                    name="title" value="{{ old('title') ?? ($labChecklist->title ?? '') }}">
                @if ($errors->has('title'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('title') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="publish_status" class="form-label">Status</label>
                <div class="form-check">
                    <input type="radio" id="aktif" name="publish_status" value="1"
                        class="form-check-input {{ $errors->has('publish_status') ? 'is-invalid' : '' }}"
                        {{ ($labChecklist->publish_status ?? '') == 'Aktif' ? 'checked' : '' }}>
                    <label class="form-check-label" for="aktif">Aktif</label>
                </div>
                <div class="form-check">
                    <input type="radio" id="tidak_aktif" name="publish_status" value="0"
                        class="form-check-input {{ $errors->has('publish_status') ? 'is-invalid' : '' }}"
                        {{ ($labChecklist->publish_status ?? '') == 'Tidak Aktif' ? 'checked' : '' }}>
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

            <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
        </form>
    </div>
</div>
<!-- End Page Wrapper -->
@endsection