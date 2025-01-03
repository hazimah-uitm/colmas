@extends('layouts.master')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
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
    </div>
</div>
<!--end breadcrumb-->

<h6 class="mb-0 text-uppercase">{{ $str_mode }} Makmal Komputer</h6>
<hr />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $save_route }}">
            {{ csrf_field() }}

            @php
            $isPemilik = auth()->user()->hasRole('pemilik');
            $canEditAll = auth()
            ->user()
            ->hasAnyRole(['Superadmin', 'Admin', 'Pegawai Penyemak']);
            @endphp

            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <select class="form-select {{ $errors->has('category') ? 'is-invalid' : '' }}" id="category" name="category" {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                    <option value="" disabled {{ old('category', $computerLab->category ?? '') === '' ? 'selected' : '' }}>
                        --- Pilih Kategori ---
                    </option>
                    @foreach ($categories as $category)
                    <option value="{{ $category }}"
                        {{ old('category', $computerLab->category ?? '') == $category ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $category)) }}
                    </option>
                    @endforeach
                </select>
                @if (!($canEditAll || $isPemilik))
                <input type="hidden" name="category" value="{{ $computerLab->category }}">
                @endif
                @if ($errors->has('category'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('category') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Kod Ruang</label>
                <input type="text" class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" id="code"
                    name="code" value="{{ old('code') ?? ($computerLab->code ?? '') }}"
                    {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                @if (!($canEditAll || $isPemilik))
                <input type="hidden" name="code" value="{{ $computerLab->code }}">
                @endif
                @if ($errors->has('code'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('code') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Ruang</label>
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name"
                    name="name" value="{{ old('name', $computerLab->name ?? '') }}"
                    {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                @if (!($canEditAll || $isPemilik))
                <input type="hidden" name="name" value="{{ $computerLab->name }}">
                @endif
                @if ($errors->has('name'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('name') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>


            <div class="mb-3">
                <label for="location" class="form-label">Lokasi</label>
                <input type="text" class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}"
                    id="location" name="location" value="{{ old('location') ?? $computerLab->location }}"
                    {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                @if (!($canEditAll || $isPemilik))
                <input type="hidden" name="location" value="{{ $computerLab->location }}">
                @endif
                @if ($errors->has('location'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('location') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="campus_id" class="form-label">Kampus</label>
                <select class="form-select {{ $errors->has('campus_id') ? 'is-invalid' : '' }}" id="campus_id"
                    name="campus_id" {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                    <option value="" disabled>--- Pilih Kampus ---</option>
                    @foreach ($campusList as $campus)
                    <option value="{{ $campus->id }}"
                        {{ old('campus_id', $computerLab->campus_id ?? '') == $campus->id ? 'selected' : '' }}>
                        {{ $campus->name }}
                    </option>
                    @endforeach
                </select>
                @if (!($canEditAll || $isPemilik))
                <input type="hidden" name="campus_id" value="{{ $computerLab->campus_id }}">
                @endif
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

                <!-- Display the value if the select is disabled -->
                @if (!($canEditAll || $isPemilik))
                <input type="text" class="form-control" value="{{ old('pemilik_id', $computerLab->pemilik->name ?? '') }}" disabled>
                <input type="hidden" name="pemilik_id" value="{{ $computerLab->pemilik_id }}">
                @else
                <!-- Regular select for editable users -->
                <select class="form-select {{ $errors->has('pemilik_id') ? 'is-invalid' : '' }}" id="pemilik_id"
                    name="pemilik_id">
                    <option value="" disabled {{ is_null($computerLab->pemilik_id) ? 'selected' : '' }}>--- Pilih Pemilik ---</option>
                    @foreach ($pemilikList as $pemilik) <!-- Assuming $pemilikList contains available pemilik options -->
                    <option value="{{ $pemilik->id }}" {{ old('pemilik_id', $computerLab->pemilik_id) == $pemilik->id ? 'selected' : '' }}>
                        {{ $pemilik->name }}
                    </option>
                    @endforeach
                </select>
                @endif

                @if ($errors->has('pemilik_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('pemilik_id') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3" id="credentials-container">
                <label for="credential" class="form-label">Akaun</label>

                @if (!empty($userCredentials)) <!-- Check if $userCredentials is not empty -->
                @foreach($userCredentials as $index => $credential)
                <div class="credential-row d-flex align-items-center mb-2">
                    <input type="text" name="user_credentials[{{ $index }}][username]" class="form-control me-2" placeholder="Username" value="{{ $credential['username'] }}">
                    <input type="text" name="user_credentials[{{ $index }}][password]" class="form-control me-2" placeholder="Password" value="{{ $credential['password'] }}">
                    <button type="button" class="btn btn-danger remove-row">Padam</button>
                </div>
                @endforeach
                @else
                <div class="credential-row d-flex align-items-center mb-2">
                    <input type="text" name="user_credentials[0][username]" class="form-control me-2" placeholder="Username">
                    <input type="text" name="user_credentials[0][password]" class="form-control me-2" placeholder="Password">
                    <button type="button" class="btn btn-danger remove-row" disabled>Padam</button>
                </div>
                @endif
            </div>
            <button type="button" id="add-credential" class="btn btn-info mb-3">Tambah Akaun</button>

            <div class="mb-3">
                <label class="form-label">Senarai Perisian</label>
                <select class="form-select {{ $errors->has('software_id') ? 'is-invalid' : '' }}" name="software_id[]"
                    multiple="multiple" id="software-select">
                    @foreach ($softwareList as $software)
                    <option value="{{ $software->id }}"
                        @if (in_array($software->id, old('software_id', $computerLab->software->pluck('id')->toArray())))
                        selected
                        @endif>
                        {{ $software->title }} {{ $software->version ? $software->version : '' }}
                    </option>
                    @endforeach
                </select>

                @if ($errors->has('software_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('software_id') as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="no_of_computer" class="form-label">Bilangan Komputer</label>
                <input type="number" class="form-control {{ $errors->has('no_of_computer') ? 'is-invalid' : '' }}"
                    id="no_of_computer" name="no_of_computer"
                    value="{{ old('no_of_computer') ?? ($computerLab->no_of_computer ?? '') }}"
                    {{ $canEditAll || $isPemilik ? '' : 'disabled' }}>
                @if (!($canEditAll || $isPemilik))
                <input type="hidden" name="no_of_computer" value="{{ $computerLab->no_of_computer }}">
                @endif
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

                <!-- Hidden Input to Handle Disabled Radio Buttons -->
                <input type="hidden" name="publish_status" value="{{ $computerLab->publish_status == 'Aktif' ? '1' : ($computerLab->publish_status == 'Tidak Aktif' ? '0' : '') }}">

                <div class="form-check">
                    <input type="radio" id="aktif" name="publish_status" value="1"
                        class="form-check-input {{ $errors->has('publish_status') ? 'is-invalid' : '' }}"
                        {{ ($computerLab->publish_status ?? '') == 'Aktif' ? 'checked' : '' }}
                        {{ !$canEditAll && !$isPemilik ? 'disabled' : '' }}>
                    <label class="form-check-label" for="aktif">Aktif</label>
                </div>

                <div class="form-check">
                    <input type="radio" id="tidak_aktif" name="publish_status" value="0"
                        class="form-check-input {{ $errors->has('publish_status') ? 'is-invalid' : '' }}"
                        {{ ($computerLab->publish_status ?? '') == 'Tidak Aktif' ? 'checked' : '' }}
                        {{ !$canEditAll && !$isPemilik ? 'disabled' : '' }}>
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
<script>
    const pemilikUrl = "{{ route('computer-lab.getPemilikByCampus', ['campusId' => '']) }}";

    document.getElementById('campus_id').addEventListener('change', function() {
        const campusId = this.value;
        const pemilikSelect = document.getElementById('pemilik_id');

        // Clear current options
        pemilikSelect.innerHTML = '<option value="" disabled selected>--- Pilih Pemilik ---</option>';

        if (campusId) {
            fetch(`${pemilikUrl}/${campusId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Pemilik data:', data);
                    if (data.length > 0) {
                        data.forEach(pemilik => {
                            const option = document.createElement('option');
                            option.value = pemilik.id;
                            option.textContent = pemilik.name;

                            // Set the current pemilik as selected if editing
                            if (pemilik.id ==
                                "{{ old('pemilik_id') ?? ($computerLab->pemilik_id ?? '') }}") {
                                option.selected = true;
                            }
                            pemilikSelect.appendChild(option);
                        });
                    } else {
                        const noRecordOption = document.createElement('option');
                        noRecordOption.value = "";
                        noRecordOption.textContent = "Tiada rekod";
                        noRecordOption.disabled = true;
                        pemilikSelect.appendChild(noRecordOption);
                    }
                })
                .catch(error => console.error('Error fetching pemilik:', error));
        }
    });

    // Trigger the change event to load the current pemilik when the page loads
    document.getElementById('campus_id').dispatchEvent(new Event('change'));

    $(document).ready(function() {
        $('#software-select').select2({
            placeholder: 'Pilih Perisian',
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>
<script>
    document.getElementById('add-credential').addEventListener('click', function() {
        const container = document.getElementById('credentials-container');
        const newIndex = container.getElementsByClassName('credential-row').length; // Get the current count of rows
        const newRow = document.createElement('div');
        newRow.classList.add('credential-row', 'd-flex', 'align-items-center', 'mb-2');
        newRow.innerHTML = `
        <input type="text" name="user_credentials[${newIndex}][username]" class="form-control me-2" placeholder="Username">
        <input type="text" name="user_credentials[${newIndex}][password]" class="form-control me-2" placeholder="Password">
        <button type="button" class="btn btn-danger remove-row">Padam</button>
    `;
        container.appendChild(newRow);

        // Attach the remove button functionality
        newRow.querySelector('.remove-row').addEventListener('click', function() {
            newRow.remove();
        });
    });

    document.getElementById('credentials-container').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-row')) {
            event.target.parentElement.remove();
        }
    });
</script>
@endsection