@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Edit Profil Pengguna</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('profile.show', ['id' => $user->id]) }}">Profil</a></li>
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

    <div class="container">
        <div class="main-body">
            <div class="row">
                <!-- Sidebar (User Info) -->
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <!-- User Image -->
                            <img src="{{ $user->profile_image ? asset('public/storage/' . $user->profile_image) : 'https://via.placeholder.com/150' }}"
                                alt="Profile Image" class="rounded-circle" width="150" height="150">
                            <!-- User Name and Position -->
                            <h5 class="mt-3">{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->position->title ?? 'Position' }}</p>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ $save_route }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                {{ method_field('put') }}

                                @php
                                    $isPemilik = auth()->user()->hasRole('pemilik');
                                    $canEditAll = auth()
                                        ->user()
                                        ->hasAnyRole(['Superadmin', 'Admin', 'Pegawai Penyemak']);
                                @endphp

                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Gambar Profil</label>
                                    <input type="file"
                                        class="form-control {{ $errors->has('profile_image') ? 'is-invalid' : '' }}"
                                        id="profile_image" name="profile_image" {{ $isPemilik ? 'disabled' : '' }}>
                                    @if ($errors->has('profile_image'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('profile_image') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Name Field -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name"
                                        name="name" value="{{ $user->name }}"
                                        {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                                    @if (!($canEditAll || $isPemilik))
                                        <input type="hidden" name="name" value="{{ $user->name }}">
                                    @endif
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('name') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Email Field -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Alamat Emel</label>
                                    <input type="email"
                                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email"
                                        name="email" value="{{ $user->email }}"
                                        {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                                    @if (!($canEditAll || $isPemilik))
                                        <input type="hidden" name="email" value="{{ $user->email }}">
                                    @endif
                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('email') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Staff ID Field -->
                                <div class="mb-3">
                                    <label for="staff_id" class="form-label">No. Pekerja</label>
                                    <input type="number"
                                        class="form-control {{ $errors->has('staff_id') ? 'is-invalid' : '' }}"
                                        id="staff_id" name="staff_id" value="{{ $user->staff_id }}"
                                        {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                                    @if (!($canEditAll || $isPemilik))
                                        <input type="hidden" name="staff_id" value="{{ $user->staff_id }}">
                                    @endif
                                    @if ($errors->has('staff_id'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('staff_id') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Position Field -->
                                <div class="mb-3">
                                    <label for="position_id" class="form-label">Jawatan</label>
                                    <select class="form-select {{ $errors->has('position_id') ? 'is-invalid' : '' }}"
                                        id="position_id" name="position_id"
                                        {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                                        @foreach ($positionList as $position)
                                            <option value="{{ $position->id }}"
                                                {{ old('position_id') == $position->id || ($user->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                                {{ $position->title }} ({{ $position->grade }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if (!($canEditAll || $isPemilik))
                                        <input type="hidden" name="position_id" value="{{ $user->position_id }}">
                                    @endif
                                    @if ($errors->has('position_id'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('position_id') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Office Phone Field -->
                                <div class="mb-3">
                                    <label for="office_phone_no" class="form-label">No. Telefon Pejabat</label>
                                    <input type="number"
                                        class="form-control {{ $errors->has('office_phone_no') ? 'is-invalid' : '' }}"
                                        id="office_phone_no" name="office_phone_no"
                                        value="{{ old('office_phone_no', $user->office_phone_no) }}"
                                        {{ $isPemilik ? 'disabled' : '' }}>
                                    @if ($errors->has('office_phone_no'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('office_phone_no') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Campus Field -->
                                <div class="mb-3">
                                    <label for="campus_id" class="form-label">Kampus</label>
                                    <div class="form-check">
                                        @foreach ($campusList as $campus)
                                            <input
                                                class="form-check-input {{ $errors->has('campus_id') ? 'is-invalid' : '' }}"
                                                type="checkbox" name="campus_id[]" value="{{ $campus->id }}"
                                                id="campus_{{ $campus->id }}"
                                                {{ in_array($campus->id, old('campus_id', $user->campus->pluck('id')->toArray())) ? 'checked' : '' }}
                                                {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                                            <label class="form-check-label" for="campus_{{ $campus->id }}">
                                                {{ $campus->name }}
                                            </label><br>

                                            @if (!($canEditAll || $isPemilik))
                                                @if (in_array($campus->id, $user->campus->pluck('id')->toArray()))
                                                    <input type="hidden" name="campus_id[]"
                                                        value="{{ $campus->id }}">
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                    @if ($errors->has('campus_id'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('campus_id') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary"
                                    {{ $isPemilik ? 'disabled' : '' }}>Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Profile Form -->

@endsection
