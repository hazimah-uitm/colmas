@extends('layouts.master')
@section('content')
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Pengurusan Perisian</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('software') }}">Senarai Perisian</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Senarai Perisian Dipadam
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <h6 class="mb-0 text-uppercase">Senarai Perisian Dipadam</h6>
    <hr />
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                 <table class="table table-sm table-striped table-hover">
                    <thead class="table-light text-center text-uppercase">
                        <tr>
                            <th>Perisian</th>
                            <th>Versi</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($trashList) > 0)
                            @foreach ($trashList as $software)
                                <tr>
                                    <td>{{ $software->title }}</td>
                                    <td class="text-center">{{ $software->version }}</td>
                                    <td class="text-center">
                                        @if ($software->publish_status == 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('software.restore', $software->id) }}"
                                            class="btn btn-success btn-sm" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" title="Kembalikan">
                                            <i class="bx bx-undo"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Padam">
                                            <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $software->id }}"><i
                                                    class="bx bx-trash"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <td colspan="5">Tiada rekod</td>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                    <form action="{{ route('software') }}" method="GET" id="perPageForm">
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
                        {{ $trashList->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @foreach ($trashList as $software)
        <div class="modal fade" id="deleteModal{{ $software->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @isset($software)
                            Adakah anda pasti ingin memadam kekal rekod <span style="font-weight: 600;">Perisian
                                {{ $software->title }}</span>?
                        @else
                            Tiada Rekod.
                        @endisset
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        @isset($software)
                            <form class="d-inline" method="POST" action="{{ route('software.forceDelete', $software->id) }}">
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
