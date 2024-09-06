@extends('layouts.master')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pengurusan Makmal Komputer</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('computer-lab') }}">Senarai Makmal Komputer</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $str_mode }} Makmal Komputer</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<h6 class="mb-0 text-uppercase">{{ $str_mode }} Makmal Komputer</h6>
<hr />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $save_route }}">
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="code" class="form-label">Kod Makmal Komputer</label>
                <input type="text" class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" id="code"
                    name="code" value="{{ old('code') ?? ($computerLab->code ?? '') }}">
                @if ($errors->has('code'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('code') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Makmal Komputer</label>
                <input type="text" class="form-control{{ $errors->has('name') ? 'is-invalid' : '' }}" id="name"
                    name="name" value="{{ old('name') ?? ($computerLab->name ?? '') }}">
                @if ($errors->has('name'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('name') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="campus_id" class="form-label">Kampus</label>
                <select class="form-select {{ $errors->has('campus_id') ? 'is-invalid' : '' }}" id="campus_id" name="campus_id">
                    @foreach ($campusList as $campus)
                    <option value="{{ $campus->id }}"
                        {{ old('campus_id') == $campus->id || ($user->campus_id ?? '') == $campus->id ? 'selected' : '' }}>
                        {{ $campus->name }}
                    </option>
                    @endforeach
                </select>
                @if ($errors->has('campus_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('campus_id') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="pemilik_id" class="form-label">Pemilik</label>
                <select class="form-select {{ $errors->has('pemilik_id') ? 'is-invalid' : '' }}" id="pemilik_id" name="pemilik_id">
                    @if ($pemilikList->isEmpty())
                    <option value="" disabled>Tiada rekod</option>
                    @else
                    @foreach ($pemilikList as $pemilik)
                    <option value="{{ $pemilik->id }}"
                        {{ old('pemilik_id') == $pemilik->id || ($user->pemilik_id ?? '') == $pemilik->id ? 'selected' : '' }}>
                        {{ $pemilik->name }}
                    </option>
                    @endforeach
                    @endif
                </select>
                @if ($errors->has('pemilik_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('pemilik_id') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username"
                    name="username" value="{{ old('username') ?? $computerLab->username }}">
                @if ($errors->has('username'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('username') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata Laluan</label>
                <input type="text" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password"
                    name="password" value="{{ old('password') ?? $computerLab->password }}">
                @if ($errors->has('password'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('password') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="no_of_computer" class="form-label">Bilangan Komputer</label>
                <input type="number" class="form-control {{ $errors->has('no_of_computer') ? 'is-invalid' : '' }}"
                    id="no_of_computer" name="no_of_computer"
                    value="{{ old('no_of_computer') ?? ($computerLab->no_of_computer ?? '') }}">
                @if ($errors->has('no_of_computer'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('no_of_computer') as $error)
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
                        {{ ($computerLab->publish_status ?? '') == 'Aktif' ? 'checked' : '' }}>
                    <label class="form-check-label" for="aktif">Aktif</label>
                </div>
                <div class="form-check">
                    <input type="radio" id="tidak_aktif" name="publish_status" value="0"
                        class="form-check-input {{ $errors->has('publish_status') ? 'is-invalid' : '' }}"
                        {{ ($computerLab->publish_status ?? '') == 'Tidak Aktif' ? 'checked' : '' }}>
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