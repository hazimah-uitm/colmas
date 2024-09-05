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
                @csrf

                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
                
                <div class="mb-3">
                    <label for="code" class="form-label">Kod Makmal Komputer</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                        name="code" value="{{ old('code') ?? ($computerLab->code ?? '') }}">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Makmal Komputer</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') ?? ($computerLab->name ?? '') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="campus_id" class="form-label">Kampus</label>
                    <select class="form-select @error('campus_id') is-invalid @enderror" id="campus_id" name="campus_id">
                        <option value="" disabled selected>--- Pilih Kampus ---</option>
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

                <div class="mb-3">
                    <label for="pemilik_id" class="form-label">Pemilik</label>
                    <select class="form-select @error('pemilik_id') is-invalid @enderror" id="pemilik_id" name="pemilik_id">
                        <option value="" disabled selected>--- Pilih Pemilik ---</option>
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
                    @error('pemilik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                        name="username" value="{{ old('username') ?? ($computerLab->username ?? '') }}">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata Laluan</label>
                    <input type="text" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" value="{{ old('password') }}">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="no_of_computer" class="form-label">Bilangan Komputer</label>
                    <input type="integer" class="form-control @error('no_of_computer') is-invalid @enderror" id="name"
                        name="no_of_computer" value="{{ old('no_of_computer') ?? ($computerLab->no_of_computer ?? '') }}">
                    @error('no_of_computer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="publish_status" class="form-label">Status</label>
                    <div class="form-check">
                        <input type="radio" id="aktif" name="publish_status" value="1"
                            {{ old('publish_status') == '1' || ($computerLab->publish_status ?? false) ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="aktif">Aktif</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" id="tidak_aktif" name="publish_status" value="0"
                            {{ old('publish_status') == '0' || !($computerLab->publish_status ?? true) ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="tidak_aktif">Tidak Aktif</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
            </form>
        </div>
    </div>
    <!-- End Page Wrapper -->
@endsection
