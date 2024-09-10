<!DOCTYPE html>
<html>

<head>
    <title>{{ $labManagement->month }}-{{ $labManagement->year }} Laporan Selenggara
        {{ $labManagement->computerLab->name }}
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            /* Reduced font size */
            margin: 10px;
            /* Added margin */
            padding: 0;
        }

        .tick-icon,
        .empty-icon {
            font-size: 20pt;
            font-family: 'DejaVu Sans', sans-serif;
            /* Increase icon size */
        }

        .container {
            width: 100%;
            padding: 5px;
            /* Reduced padding */
            box-sizing: border-box;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 15px;
            /* Reduced margin */
        }

        .header h1 {
            font-size: 16pt;
            /* Reduced header font size */
            margin: 5px 0;
            /* Reduced margin */
        }

        .content {
            margin-bottom: 15px;
            /* Reduced margin */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            /* Reduced margin */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            /* Reduced padding */
            text-align: left;
            font-size: 9pt;
            /* Reduced font size for table cells */
        }

        th {
            background-color: #f2f2f2;
        }

        .tick-icon,
        .empty-icon {
            font-size: 12px;
            /* Adjusted icon size */
        }

        h3 {
            font-size: 14pt;
            /* Reduced font size */
            margin: 10px 0 5px;
            /* Reduced margin */
        }

        hr {
            margin: 5px 0;
            /* Reduced margin */
            border: none;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Selenggara {{ $labManagement->computerLab->name }}
                ({{ $labManagement->month }}-{{ $labManagement->year }})</h1>
        </div>
        <div class="content">
            <table>
                <tr>
                    <th>Nama Pemilik</th>
                    <td>{{ $labManagement->computerLab->pemilik->name }}</td>
                </tr>
                <tr>
                    <th>Makmal Komputer</th>
                    <td>{{ $labManagement->computerLab->name }}, {{ $labManagement->computerLab->campus->name }}</td>
                </tr>
            </table>

            <table>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Masa Mula</th>
                    <th>Masa Tamat</th>
                </tr>
                <tr>
                    <td>{{ $labManagement->month }}</td>
                    <td>{{ $labManagement->year }}</td>
                    <td>{{ $labManagement->start_time }}</td>
                    <td>{{ $labManagement->end_time ?? '-' }}</td>
                </tr>
            </table>


            <table>
                <tr>
                    <th>Bil. Keseluruhan Komputer</th>
                    <th>Bil. Komputer Telah Diselenggara</th>
                    <th>Bil. Komputer Rosak</th>
                    <th>Bil. Komputer Belum Diselenggara</th>
                </tr>
                <tr>
                    <td>{{ $labManagement->computer_no ?? '-' }}</td>
                    <td>{{ $labManagement->pc_maintenance_no ?? '-' }}</td>
                    <td>{{ $labManagement->pc_damage_no ?? '-' }}</td>
                    <td>{{ $labManagement->pc_unmaintenance_no ?? '-' }}</td>
                </tr>
                </tbody>
            </table>

            <h3>Senarai Rekod PC Diselenggara/Rosak</h3>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Komputer</th>
                        <th>IP Address</th>
                        @foreach ($workChecklists as $workChecklist)
                        <th>{{ $workChecklist->title }}</th>
                        @endforeach
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($labManagement->maintenanceRecords as $maintenanceRecord)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $maintenanceRecord->computer_name }}</td>
                        <td>{{ $maintenanceRecord->ip_address }}</td>
                        @if (!empty($maintenanceRecord->work_checklist_id))
                        @foreach ($workChecklists as $workChecklist)
                        <td>
                            @php
                            $isSelected = in_array(
                            $workChecklist->id,
                            $maintenanceRecord->work_checklist_id);
                            @endphp
                            @if ($isSelected)
                            <span class="tick-icon">&#10004;</span>
                            @else
                            <span class="empty-icon" style="color: red">&#10006;</span>
                            @endif
                        </td>
                        @endforeach
                        @else
                        <td colspan="{{ count($workChecklists) }}">Komputer Bermasalah</td>
                        @endif
                        <td>{!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h3>Senarai Semak Makmal</h3>
            <table>
                <thead>
                    <tr>
                        @foreach ($labCheckList as $labCheck)
                        <th>{{ $labCheck->title }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($labCheckList as $labCheck)
                        <td>
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
                    @if ($selectedSoftwareTitles->count() == 1)
                    <tr>
                        <td>
                            <span class="tick-icon">&#10004;</span> {{ $selectedSoftwareTitles->first() }}
                        </td>
                    </tr>
                    @else
                    @foreach ($selectedSoftwareTitles->chunk(2) as $chunk)
                    <tr>
                        @foreach ($chunk as $title)
                        <td>
                            <span class="tick-icon">&#10004;</span> {{ $title }}
                        </td>
                        @endforeach
                        @if (count($chunk) == 1)
                        <td></td> <!-- Empty cell if there's an odd number of titles -->
                        @endif
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>

            <table>
                <tr>
                    <th>Catatan/Ulasan Pemilik</th>
                    <td>{!! nl2br(e($labManagement->remarks_submitter ?? '-')) !!}</td>
                </tr>
                <tr>
                    <th>Catatan/Ulasan Pegawai Penyemak</th>
                    <td>{!! nl2br(e($labManagement->remarks_checker ?? '-')) !!}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}</td>
                </tr>
            </table>

            <table>
                <tr>
                    <th>Dihantar oleh</th>
                    <td>{{ $labManagement->submittedBy->name ?? '-' }}</td>
                    <th>Dihantar pada</th>
                    <td>{{ $labManagement->submitted_at ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Disemak oleh</th>
                    <td>{{ $labManagement->checkedBy->name ?? '-' }}</td>
                    <th>Disemak pada</th>
                    <td>{{ $labManagement->checked_at ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>