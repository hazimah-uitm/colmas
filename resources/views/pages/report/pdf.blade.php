<!DOCTYPE html>
<html>

<head>
    <title>{{ $labManagement->month }}-{{ $labManagement->year }} Laporan Selenggara
        {{ $labManagement->computerLab->name }}
    </title>
    <style>
        @page {
            size: A4;
            margin: 30mm 10mm 30mm 10mm; /* Adjust to provide more space for headers */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 240px;
            margin-bottom: 5px;
        }

        .card {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            padding: 10px;
            background-color: white;
            margin: 20px auto;
            max-width: 800px;
            border: 1px solid #d5d8dc;
        }

        .tick-icon,
        .empty-icon {
            font-size: 20pt;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .container {
            width: 100%;
            padding: 0px;
            box-sizing: border-box;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 15px;
        }

        .content {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 9pt;
        }

        th {
            background-color: #d6dbdf;
        }


        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        tbody tr:hover {
            background-color: #e6f7ff;
        }

        .tick-icon,
        .empty-icon {
            font-size: 12px;
        }

        h3 {
            font-size: 12pt;
            margin: 3px;
            text-transform: uppercase;
        }

        hr {
            margin: 5px 0;
            border: none;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- UiTM Logo -->
            <img src="{{ $logoBase64 }}" alt="UiTM Logo">
            <!-- Report Title -->
            <h2 style="text-transform: uppercase">Laporan Selenggara {{ $labManagement->computerLab->name }}
                {{ $labManagement->month }}
                {{ $labManagement->year }}
            </h2>
        </div>
        <div class="content">
            <table>
                <tr>
                    <th style="width: 50%; border: 0px">Nama Pemilik</th>
                    <td style="width: 50%; border: 0px">{{ $labManagement->computerLab->pemilik->name }}</td>
                </tr>
                <tr>
                    <th style="width: 50%; border: 0px">Makmal Komputer</th>
                    <td style="width: 50%; border: 0px">{{ $labManagement->computerLab->name }},
                        {{ $labManagement->computerLab->campus->name }}
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <th style="width: 25%; border: 0px">Bulan</th>
                    <th style="width: 25%; border: 0px">Tahun</th>
                    <th style="width: 25%; border: 0px">Masa Mula</th>
                    <th style="width: 25%; border: 0px">Masa Tamat</th>
                </tr>
                <tr>
                    <td style="border: 0px">{{ $labManagement->month }}</td>
                    <td style="border: 0px">{{ $labManagement->year }}</td>
                    <td style="border: 0px">{{ $labManagement->start_time }}</td>
                    <td style="border: 0px">{{ $labManagement->end_time ?? '-' }}</td>
                </tr>
            </table>


            <table>
                <tr>
                    <th style="border: 0px; text-align: center">Bil. Keseluruhan Komputer</th>
                    <th style="border: 0px; text-align: center">Bil. Komputer Telah Diselenggara</th>
                    <th style="border: 0px; text-align: center">Bil. Komputer Rosak/Keluar</th>
                    <th style="border: 0px; text-align: center">Bil. Komputer Belum Diselenggara</th>
                </tr>
                <tr>
                    <td style="border: 0px; text-align: center">{{ $labManagement->computer_no ?? '-' }}</td>
                    <td style="border: 0px; text-align: center">{{ $labManagement->pc_maintenance_no ?? '-' }}</td>
                    <td style="border: 0px; text-align: center">{{ $labManagement->pc_damage_no ?? '-' }}</td>
                    <td style="border: 0px; text-align: center">{{ $labManagement->pc_unmaintenance_no ?? '-' }}</td>
                </tr>
                </tbody>
            </table>


            <div class="card">
                <h3>Senarai Semak Makmal</h3>
                <table>
                    <thead>
                        <tr>
                            @foreach ($labCheckList as $labCheck)
                                <th style="text-align: center; border-color: #cacfd2">{{ $labCheck->title }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($labCheckList as $labCheck)
                                <td style="text-align: center; border-color: #cacfd2">
                                    @php
                                        $isSelected =
                                            !empty($labManagement->lab_checklist_id) &&
                                            in_array($labCheck->id, $labManagement->lab_checklist_id);
                                    @endphp
                                    @if ($isSelected)
                                        <span class="tick-icon">&#10004;</span>
                                    @else
                                        <span class="empty-icon" style="color: red">&#10006;</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>Senarai Rekod PC Diselenggara/Rosak</h3>
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align: center; border-color: #cacfd2">No.</th>
                            <th style="text-align: center; border-color: #cacfd2">Nama Komputer</th>
                            <th style="text-align: center; border-color: #cacfd2"
                                colspan="{{ count($workChecklists) }}">
                                Kerja Selenggara</th>
                            <th rowspan="2" style="text-align: center; border-color: #cacfd2">No. Aduan</th>
                            <th rowspan="2" style="width: 45%; text-align: center; border-color: #cacfd2">Catatan
                            </th>
                        </tr>
                        <tr>
                            <th style="text-align: center; border-color: #cacfd2">IP Address</th>
                            @foreach ($workChecklists as $workChecklist)
                                <th style="text-align: center; border-color: #cacfd2">{{ $workChecklist->title }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($labManagement->maintenanceRecords as $maintenanceRecord)
                            @php
                                $noAduan = '-';

                                // Check the entryOption for each maintenance record
                                if ($maintenanceRecord->entry_option == 'manual') {
                                    $noAduan = $maintenanceRecord->aduan_unit_no ?? '-';
                                } elseif ($maintenanceRecord->entry_option == 'pc_rosak') {
                                    $noAduan = $maintenanceRecord->vms_no ?? '-';
                                }
                            @endphp
                            <tr>
                                <td style="text-align: center; border-color: #cacfd2">{{ $loop->iteration }}</td>
                                <td style="text-align: center; border-color: #cacfd2">
                                    {{ $maintenanceRecord->computer_name }} <br>
                                    {{ $maintenanceRecord->ip_address }}
                                </td>
                                @if ($maintenanceRecord->entry_option == 'pc_rosak')
                                    <td style="text-align: center; border-color: #cacfd2"
                                        colspan="{{ count($workChecklists) }}">Komputer Bermasalah
                                    </td>
                                @elseif ($maintenanceRecord->entry_option == 'pc_keluar')
                                    <td style="text-align: center; border-color: #cacfd2"
                                        colspan="{{ count($workChecklists) }}">PC dibawa keluar
                                        pada {{ $maintenanceRecord->keluar_date }} <br> ke
                                        {{ $maintenanceRecord->keluar_location }}
                                    </td>
                                @else
                                    @foreach ($workChecklists as $workChecklist)
                                        <td style="text-align: center; border-color: #cacfd2">
                                            @php
                                                $isSelected = in_array(
                                                    $workChecklist->id,
                                                    $maintenanceRecord->work_checklist_id);
                                            @endphp
                                            @if ($isSelected)
                                                <span class="tick-icon">&#10004;</span>
                                            @else
                                                <span class="empty-icon" style="color: red;">&#10006;</span>
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                                <td style="text-align: center; border-color: #cacfd2">{{ $noAduan }}</td>
                                <td
                                    style="width: 45%; white-space: normal; word-wrap: break-word; word-break: break-word; max-width: 350px; border-color: #cacfd2">
                                    {!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>Senarai Perisian</h3>
                @php
                    // Extract selected software titles
                    $selectedSoftwareTitles = $softwareList
                        ->filter(function ($software) use ($labManagement) {
                            return !empty($labManagement->software_id) &&
                                in_array($software->id, $labManagement->software_id);
                        })
                        ->pluck('title');
                @endphp

                <table>
                    <tbody>
                        @if ($labManagement->computerLab->software->count() == 1)
                            <tr>
                                <td style="border: 0px">
                                    <span class="tick-icon">&#10004;</span>
                                    {{ $labManagement->computerLab->software->first()->title }}
                                    {{ $labManagement->computerLab->software->first()->version }}
                                </td>
                            </tr>
                        @else
                            @foreach ($labManagement->computerLab->software->chunk(2) as $chunk)
                                <tr>
                                    @foreach ($chunk as $software)
                                        <td style="border: 0px">
                                            <span class="tick-icon">&#10004;</span>
                                            {{ $software->title }} {{ $software->version }}
                                        </td>
                                    @endforeach
                                    @if ($chunk->count() == 1)
                                        <td style="border: 0px"></td>
                                        <!-- Empty cell if there's an odd number of items -->
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

            </div>

            <table>
                <tr>
                    <th style="width: 50%; border: 0px">Catatan/Ulasan Pemilik</th>
                    <td style="width: 50%; border: 0px">{!! nl2br(e($labManagement->remarks_submitter ?? '-')) !!}</td>
                </tr>
                <tr>
                    <th style="width: 50%; border: 0px">Catatan/Ulasan Pegawai Penyemak</th>
                    <td style="width: 50%; border: 0px">{!! nl2br(e($labManagement->remarks_checker ?? '-')) !!}</td>
                </tr>
                <tr>
                    <th style="width: 50%; border: 0px">Status</th>
                    <td style="width: 50%; border: 0px">
                        {{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <th style="width: 25%; border: 0px">Dihantar oleh</th>
                    <td style="width: 25%; border: 0px">{{ $labManagement->submittedBy->name ?? '-' }}</td>
                    <th style="width: 25%; border: 0px">Dihantar pada</th>
                    <td style="width: 25%; border: 0px">{{ $labManagement->submitted_at ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; border: 0px">Disemak oleh</th>
                    <td style="width: 25%; border: 0px">{{ $labManagement->checkedBy->name ?? '-' }}</td>
                    <th style="width: 25%; border: 0px">Disemak pada</th>
                    <td style="width: 25%; border: 0px">{{ $labManagement->checked_at ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
