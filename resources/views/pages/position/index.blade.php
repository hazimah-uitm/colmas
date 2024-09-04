@extends('layouts.master')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Jawatan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Senarai Jawatan</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('position.trash') }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
            </a>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Senarai Jawatan</h6>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="d-lg-flex align-items-center mb-4 gap-3">
                <div class="position-relative">
                    <form action="{{ route('position.search') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control search-input" placeholder="Carian..." name="search">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary search-button">
                                    <i class="bx bx-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
                @hasanyrole('Superadmin|Admin')
                    <div class="ms-auto">
                        <a href="{{ route('position.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                            <i class="bx bxs-plus-square"></i> Tambah Jawatan
                        </a>
                    </div>
                @endhasanyrole
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jawatan</th>
                            <th>Gred</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($positionList) > 0)
                            @foreach ($positionList as $position)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $position->title }}</td>
                                    <td>{{ $position->grade }}</td>
                                    <td>
                                        @if ($position->publish_status == 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @hasanyrole('Superadmin|Admin')
                                            <a href="{{ route('position.edit', $position->id) }}" class="btn btn-info btn-sm"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kemaskini">
                                                <i class="bx bxs-edit"></i>
                                            </a>
                                        @endhasanyrole
                                        <a href="{{ route('position.show', $position->id) }}" class="btn btn-primary btn-sm"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Papar">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        @hasanyrole('Superadmin|Admin')
                                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                data-bs-title="Padam">
                                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $position->id }}"><i
                                                        class="bx bx-trash"></i></span>
                                            </a>
                                        @endhasanyrole
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
                    <form action="{{ route('position') }}" method="GET" id="perPageForm">
                        <select name="perPage" id="perPage" class="form-select"
                            onchange="document.getElementById('perPageForm').submit()">
                            <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                            <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                            <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                        </select>
                    </form>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <div class="mx-1 mt-2">{{ $positionList->firstItem() }} – {{ $positionList->lastItem() }} dari
                        {{ $positionList->total() }} rekod
                    </div>
                    <div>{{ $positionList->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @foreach ($positionList as $position)
        <div class="modal fade" id="deleteModal{{ $position->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @isset($position)
                            Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">Jawatan
                                {{ $position->title }}</span>?
                        @else
                            Error: Position data not available.
                        @endisset
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        @isset($position)
                            <form class="d-inline" method="POST" action="{{ route('position.destroy', $position->id) }}">
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
    <!--end page wrapper -->
@endsection
