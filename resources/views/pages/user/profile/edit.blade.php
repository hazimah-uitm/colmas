@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
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
    <!-- End Breadcrumb -->

    <h6 class="mb-0 text-uppercase">Edit Profil {{ $user->name }}</h6>
    <hr />

    <!-- Edit Profile Form -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ $save_route }}" method="POST">
                        {{ csrf_field() }}
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $user->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Staff ID</label>
                            <input type="number" class="form-control" id="staff_id" name="staff_id"
                                value="{{ $user->staff_id }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="position_id" class="form-label">Jawatan</label>
                            <select class="form-select @error('position_id') is-invalid @enderror" id="position_id"
                                name="position_id">
                                @foreach ($positionList as $position)
                                    <option value="{{ $position->id }}"
                                        {{ old('position_id') == $position->id || ($user->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                        {{ $position->title }} ({{ $position->grade }})
                                    </option>
                                @endforeach
                            </select>
                            @error('position_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Emel</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') ?? ($user->email ?? '') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="office_phone_no" class="form-label">No. Telefon Pejabat</label>
                            <input type="number" class="form-control @error('office_phone_no') is-invalid @enderror"
                                id="office_phone_no" name="office_phone_no"
                                value="{{ old('office_phone_no', $user->office_phone_no) }}">
                            @error('office_phone_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="campus_id" class="form-label">Kampus</label>
                            <select class="form-select @error('campus_id') is-invalid @enderror" id="campus_id"
                                name="campus_id">
                                @foreach ($campusList as $campus)
                                    <option value="{{ $campus->id }}"
                                        {{ old('campus_id') == $campus->id || ($user->campus_id ?? '') == $campus->id ? 'selected' : '' }}>
                                        {{ $campus->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('campus_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Profile Form -->
@endsection
