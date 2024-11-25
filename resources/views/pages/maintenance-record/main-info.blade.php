<!-- Optional button section -->
<!-- <div class="d-flex justify-content-end mb-2">
    @if (!in_array($labManagement->status, ['dihantar', 'telah_disemak']))
<a href="{{ route('lab-management.maintenance-records.create', ['labManagement' => $labManagement->id]) }}"
            class="btn btn-primary radius-30 mt-2 mt-lg-0 ms-2">
            <i class="bx bxs-plus-square"></i> Tambah Rekod PC
        </a>
@endif
</div> -->

<!-- Lab Management Info -->
<table class="table table-borderless mb-2">
    <tr>
        <th class="text-uppercase">Nama Pemilik</th>
        <td class="text-uppercase">{{ $labManagement->computerLab->pemilik->name ?? '-' }}</td>
    </tr>
    <tr>
        <th class="text-uppercase">Makmal Komputer</th>
        <td class="mb-3 text-uppercase">
            {{ $labManagement->computerLab->name ?? '-' }},
            {{ $labManagement->computerLab->campus->name ?? '-' }}
        </td>
    </tr>
</table>

<!-- Date Info -->
<table class="table table-borderless mb-2">
    <tr>
        <th style="width:25%">Bulan</th>
        <th style="width:25%">Tahun</th>
        <th style="width:25%">Tarikh/Masa Mula</th>
        <th style="width:25%">Tarikh/Masa Tamat</th>
    </tr>
    <tr>
        <td>{{ $month ?? '-' }}</td>
        <td>{{ $year ?? '-' }}</td>
        <td>{{ $labManagement->start_time ?? '-' }}</td>
        <td>{{ $labManagement->end_time ?? '-' }}</td>
    </tr>
</table>

<!-- Computer Stats -->
<table class="table mb-5">
    <thead class="bg-light">
        <tr>
            <th style="width:25%" class="text-center">Bil. Keseluruhan Komputer</th>
            <th style="width:25%" class="text-center">Bil. Komputer Telah Diselenggara</th>
            <th style="width:25%" class="text-center">Bil. Komputer Rosak/Keluar</th>
            <th style="width:25%" class="text-center">Bil. Komputer Belum Diselenggara</th>
        </tr>
    </thead>
    <tbody>
        @php
            $pcMaintenanceNo = $labManagement->pc_maintenance_no;
            $computerNo = $labManagement->computer_no;
            $pcDamageNo = $labManagement->pc_damage_no ?? 0;
            $totalPCMaintenance = $pcMaintenanceNo ?? 0;
            $pcUnmaintenanceNo = $computerNo - $totalPCMaintenance - $pcDamageNo;
        @endphp
        <tr>
            <td class="text-center">{{ $computerNo }}</td>
            <td class="text-center">{{ $totalPCMaintenance }}</td>
            <td class="text-center">{{ $pcDamageNo }}</td>
            <td class="text-center">{{ $pcUnmaintenanceNo }}</td>
        </tr>
    </tbody>
</table>
