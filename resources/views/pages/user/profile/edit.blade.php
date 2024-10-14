@extends('layouts.master')

@section('content')
<!-- Breadcrumb -->
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
            <div class="breadcrumb-title pe-3">Edit Profil Pengguna</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('profile.show', ['id' => $user->id]) }}">Profil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Profil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<h6 class="mb-0 text-uppercase">Edit Profil {{ $user->name }}</h6>
<hr />

<!-- Edit Profile Form -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $save_route }}">
                    {{ csrf_field() }}
                    {{ method_field('put') }}

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name"
                            value="{{ $user->name }}" required>
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('name') as $error)
                            {{ $error }}
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Emel</label>
                        <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email"
                            value="{{ $user->email }}" required>
                        @if ($errors->has('email'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('email') as $error)
                            {{ $error }}
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">No. Pekerja</label>
                        <input type="number" class="form-control {{ $errors->has('staff_id') ? 'is-invalid' : '' }}" id="staff_id" name="staff_id"
                            value="{{ $user->staff_id }}" required>
                        @if ($errors->has('staff_id'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('staff_id') as $error)
                            {{ $error }}
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="position_id" class="form-label">Jawatan</label>
                        <select class="form-select {{ $errors->has('position_id') ? 'is-invalid' : '' }}" id="position_id"
                            name="position_id">
                            @foreach ($positionList as $position)
                            <option value="{{ $position->id }}"
                                {{ old('position_id') == $position->id || ($user->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                {{ $position->title }} ({{ $position->grade }})
                            </option>
                            @endforeach
                        </select>
                        @if ($errors->has('position_id'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('position_id') as $error)
                            {{ $error }}
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="office_phone_no" class="form-label">No. Telefon Pejabat</label>
                        <input type="number" class="form-control  {{ $errors->has('office_phone_no') ? 'is-invalid' : '' }}"
                            id="office_phone_no" name="office_phone_no"
                            value="{{ old('office_phone_no', $user->office_phone_no) }}">
                        @if ($errors->has('username'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('username') as $error)
                            {{ $error }}
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="campus_id" class="form-label">Kampus</label>
                        <select class="form-select {{ $errors->has('campus_id') ? 'is-invalid' : '' }}" id="campus_id"
                            name="campus_id">
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

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Edit Profile Form -->
@endsection