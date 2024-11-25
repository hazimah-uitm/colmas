@extends('layouts.master')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
            <div class="breadcrumb-title pe-3">Pengurusan Rekod Selenggara</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('lab-management') }}">Rekod Selenggara Makmal
                                Komputer</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $str_mode }} Rekod</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<h6 class="mb-0 text-uppercase">{{ $str_mode }} Rekod</h6>
<hr />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $save_route }}">
            {{ csrf_field() }}

            @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
            @endforeach

            <div class="mb-3">
                <label for="computer_lab_id" class="form-label">Makmal Komputer</label>
                <select class="form-select {{ $errors->has('computer_lab_id') ? 'is-invalid' : '' }}"
                    id="computer_lab_id" name="computer_lab_id">
                    <option value="" disabled {{ old('computer_lab_id') ? '' : 'selected' }}>Pilih Makmal Komputer
                    </option>
                    @foreach ($computerLabList as $computerLab)
                    <option value="{{ $computerLab->id }}"
                        {{ old('computer_lab_id') == $computerLab->id ? 'selected' : '' }}
                        data-computers="{{ $computerLab->no_of_computer }}"
                        data-software='@json($computerLab->software)'>
                        {{ $computerLab->name }}
                    </option>
                    @endforeach
                </select>
                @if ($errors->has('computer_lab_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('computer_lab_id') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="computer_no" class="form-label">Bilangan Komputer</label>
                <input type="text" class="form-control {{ $errors->has('computer_no') ? 'is-invalid' : '' }}"
                    id="computer_no" value="{{ old('computer_no', $labManagement->computerLab->no_of_computer ?? 0) }}"
                    disabled>
                <input type="hidden" name="computer_no" id="hidden_computer_no"
                    value="{{ old('computer_no', $labManagement->computerLab->no_of_computer ?? 0) }}">
                @if ($errors->has('computer_no'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('computer_no') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_time" class="form-label">Masa Mula</label>
                    <input type="datetime-local"
                        class="form-control {{ $errors->has('start_time') ? 'is-invalid' : '' }}" id="start_time"
                        name="start_time" value="{{ now()->format('Y-m-d\TH:i') }}" disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_time" class="form-label">Masa Tamat</label>
                    <input type="text" class="form-control {{ $errors->has('end_time') ? 'is-invalid' : '' }}"
                        id="end_time" name="end_time"
                        value="{{ old('end_time') ?? ($maintenanceRecord->end_time ?? '') }}" disabled>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Senarai Semak Makmal</label>
                <div class="row">
                    @foreach ($labCheckList as $index => $labCheck)
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input {{ $errors->has('lab_checklist_id') ? 'is-invalid' : '' }}"
                                type="checkbox" id="lab_checklist_{{ $labCheck->id }}" name="lab_checklist_id[]"
                                value="{{ $labCheck->id }}" @if (in_array($labCheck->id, old('lab_checklist_id', $selectedlabChecks ?? []))) checked @endif>
                            <label class="form-check-label" for="lab_checklist_{{ $labCheck->id }}">
                                {{ $labCheck->title }}
                            </label>
                        </div>
                    </div>
                    <!-- Add a line break for every second checkbox to make them fit in two columns -->
                    @if (($index + 1) % 2 == 0 && $index != count($labCheckList) - 1)
                </div>
                <div class="row">
                    @endif
                    @endforeach
                </div>
                @if ($errors->has('lab_checklist_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('lab_checklist_id') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="software_list" class="form-label">Senarai Perisian</label>
                <div id="software_list">
                    <!-- Default message -->
                    <div class="text-muted">Tiada Rekod</div>
                </div>
            </div>

            <input type="hidden" name="status" value="draft">
            <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
        </form>
    </div>
</div>
<!-- End Page Wrapper -->
<script>
    document.getElementById('computer_lab_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var numberOfComputers = selectedOption.getAttribute('data-computers');
        document.getElementById('computer_no').value = numberOfComputers;
        document.getElementById('hidden_computer_no').value = numberOfComputers;
    });
</script>
<script>
    document.getElementById('computer_lab_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var softwareList = JSON.parse(selectedOption.getAttribute('data-software') || '[]');

        var softwareListContainer = document.getElementById('software_list');
        softwareListContainer.innerHTML = ''; // Clear existing list

        if (softwareList.length === 0) {
            // Display "Tiada Rekod" if no software is available
            softwareListContainer.innerHTML = '<div class="text-muted">Tiada Rekod</div>';
        } else {
            // Otherwise, create two columns
            var rowDiv = document.createElement('div');
            rowDiv.classList.add('row');

            softwareList.forEach(function(software, index) {
                var colDiv = document.createElement('div');
                colDiv.classList.add('col-md-6'); // This will create two columns (each taking up half width)

                var softwareItem = document.createElement('div');
                softwareItem.classList.add('d-flex', 'align-items-center', 'mb-2');
                softwareItem.innerHTML = `<span class="me-2">&#10004;</span> ${software.title} ${software.version}`;

                colDiv.appendChild(softwareItem);
                rowDiv.appendChild(colDiv);
            });

            softwareListContainer.appendChild(rowDiv);
        }
    });

    // Trigger change event on page load to show the correct message for the selected lab
    document.getElementById('computer_lab_id').dispatchEvent(new Event('change'));
</script>
@endsection