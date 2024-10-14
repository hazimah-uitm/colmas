@extends('layouts.master')
@section('content')
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
                        <li class="breadcrumb-item active" aria-current="page">Senarai Selenggara Makmal Komputer Dipadam
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<h6 class="mb-0 text-uppercase">Senarai Selenggara Makmal Komputer Dipadam</h6>
<hr />
<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Makmal Komputer</th>
                        <th>Masa Mula</th>
                        <th>Masa Tamat</th>
                        <th>Senarai Semak Makmal</th>
                        <th>Senarai Perisian</th>
                        <th>Bil. Komputer</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($trashList) > 0)
                    @foreach ($trashList as $trash)
                    <tr>
                        <td>{{ ($trashList->currentPage() - 1) * $trashList->perPage() + $loop->iteration }}
                        </td>
                        <td>{{ $trash->computerLab->name }}</td>
                        <td>{{ $trash->start_time }}</td>
                        <td>{{ $trash->end_time }}</td>
                        <td>
                            @if (!empty($trash->lab_checklist_id))
                            <ul style="list-style-type: none; padding: 0;">
                                @foreach ($labCheckList as $labCheck)
                                @php
                                $isSelected = in_array($labCheck->id, $trash->lab_checklist_id);
                                @endphp
                                <li style="list-style-type: none; margin-bottom: 5px;">
                                    @if ($isSelected)
                                    <span class="tick-icon">&#10004;</span>
                                    @else
                                    <span class="empty-icon" style="color: red">&#9744;</span>
                                    @endif
                                    {{ $labCheck->title }}
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p>-</p>
                            @endif
                        </td>
                        <td>
                            @if (!empty($trash->software_id))
                            <ul style="list-style-type: none; padding: 0;">
                                @foreach ($softwareList as $software)
                                @php
                                $isSelected = in_array($software->id, $trash->software_id);
                                @endphp
                                <li style="list-style-type: none; margin-bottom: 5px;">
                                    @if ($isSelected)
                                    <span class="tick-icon">&#10004;</span>
                                    @else
                                    <span class="empty-icon" style="color: red">&#9744;</span>
                                    @endif
                                    {{ $software->title }}
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p>-</p>
                            @endif
                        </td>
                        <td>{{ $trash->computer_no }}</td>
                        <td>{{ $trash->status }}</td>
                        <td>
                            <a href="{{ route('lab-management.restore', $trash->id) }}"
                                class="btn btn-success btn-sm">
                                <i class="bx bx-undo"></i>
                            </a>
                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                data-bs-title="Padam">
                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $trash->id }}"><i
                                        class="bx bx-trash"></i></span>
                            </a>
                        </td>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <td colspan="4">Tiada rekod</td>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                <form action="{{ route('lab-management') }}" method="GET" id="perPageForm">
                    <select name="perPage" id="perPage" class="form-select"
                        onchange="document.getElementById('perPageForm').submit()">
                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                        <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                        <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                    </select>
                </form>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <span class="mx-2 mt-2 small text-muted">
                    Menunjukkan {{ $trashList->firstItem() }} hingga {{ $trashList->lastItem() }} daripada
                    {{ $trashList->total() }} rekod
                </span>
                <div class="pagination-wrapper">
                    {{ $trashList->appends([
                                'search' => request('search'),
                            ])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@foreach ($trashList as $trash)
<div class="modal fade" id="deleteModal{{ $trash->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @isset($trash)
                Adakah anda pasti ingin memadam kekal rekod <span style="font-weight: 600;">
                    {{ $trash->computerLab->code }} - {{ $trash->computerLab->name }}</span>?
                @else
                Tiada rekod.
                @endisset
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @isset($trash)
                <form class="d-inline" method="POST"
                    action="{{ route('lab-management.forceDelete', $trash->id) }}">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-danger">Padam</button>
                </form>
                @endisset
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection