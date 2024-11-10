@extends('layouts.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Pengurusan Rekod Selenggara Makmal</div>
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
                        <option value="" disabled
                            {{ old('computer_lab_id', $labManagement->computer_lab_id) ? '' : 'selected' }}>Pilih Makmal
                            Komputer</option>
                        @foreach ($computerLabList as $computerLab)
                            <option value="{{ $computerLab->id }}" data-computers="{{ $computerLab->no_of_computer }}"
                                {{ old('computer_lab_id', $labManagement->computer_lab_id) == $computerLab->id ? 'selected' : '' }}>
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
                        id="computer_no" value="{{ old('computer_no', $labManagement->computer_no ?? 0) }}" disabled>
                    <input type="hidden" name="computer_no" id="hidden_computer_no"
                        value="{{ old('computer_no', $labManagement->computer_no ?? 0) }}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Masa Mula</label>
                        <input type="datetime-local"
                            class="form-control {{ $errors->has('start_time') ? 'is-invalid' : '' }}" id="start_time"
                            name="start_time" value="{{ old('start_time') ?? ($labManagement->start_time ?? '') }}"
                            disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">Masa Tamat</label>
                        <input type="text" class="form-control {{ $errors->has('end_time') ? 'is-invalid' : '' }}"
                            id="end_time" name="end_time"
                            value="{{ old('end_time') ?? ($labManagement->end_time ?? '') }}" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Senarai Semak Makmal</label>
                    @foreach ($labCheckList as $labCheck)
                        <div class="form-check">
                            <input class="form-check-input {{ $errors->has('lab_checklist_id') ? 'is-invalid' : '' }}"
                                type="checkbox" id="lab_checklist_{{ $labCheck->id }}" name="lab_checklist_id[]"
                                value="{{ $labCheck->id }}" @if (in_array($labCheck->id, old('lab_checklist_id', $labManagement->lab_checklist_id ?? []))) checked @endif>
                            <label class="form-check-label" for="lab_checklist_{{ $labCheck->id }}">
                                {{ $labCheck->title }}
                            </label>
                        </div>
                    @endforeach
                    @if ($errors->has('lab_checklist_id'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('lab_checklist_id') as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">Senarai Perisian</label>
                    <select class="form-select {{ $errors->has('software_id') ? 'is-invalid' : '' }}" name="software_id[]"
                        multiple="multiple" id="software-select">
                        @foreach ($softwareList as $software)
                            <option value="{{ $software->id }}" @if (in_array($software->id, old('software_id', $labManagement->software_id ?? []))) selected @endif>
                                {{ $software->title }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('software_id'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('software_id') as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
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

        $(document).ready(function() {
            $('#software-select').select2({
                placeholder: 'Pilih Perisian',
                allowClear: true
            });
        });
    </script>
@endsection
