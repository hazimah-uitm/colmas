@extends('layouts.master')

@section('content')
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col">
                <form id="homeFilter" action="{{ route('yearly-report') }}" method="GET">
                    <div class="d-flex flex-wrap justify-content-end">
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <select name="year" id="year" class="form-select">
                                <option value="">Semua Tahun</option>
                                @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                                    <option value="{{ $i }}"
                                        {{ Request::get('year', date('Y')) == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-2 ms-2 col-12 col-md-auto">
                            <button id="resetButton" class="btn btn-primary">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Display Data for Each Campus in Separate Cards -->
    @foreach ($campusData as $campusItem)
        <div class="card mb-4 shadow-sm">
            <div class="card-body text-uppercase">
                <h4>{{ $campusItem['campus']->name }}</h4>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Makmal Komputer</th>
                            @foreach ($months as $month)
                                <th>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($campusItem['computerLabList'] as $lab)
                            <tr>
                                <td>{{ $lab->name }}</td>
                                @foreach ($months as $month)
                                    <td class="text-center">
                                        <span style="font-size: 12px;">
                                            {{ $campusItem['maintainedLabsPerMonth'][$month]->contains($lab->id) ? '✔️' : '❌' }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <script>
        document.getElementById('homeFilter').addEventListener('change', function() {
            this.submit();
        });

        document.getElementById('resetButton').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default reset behavior
            const url = new URL(window.location.href);
            url.searchParams.delete('year'); // Only reset the year filter
            window.location.href = url.toString();
        });
    </script>
@endsection
