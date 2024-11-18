<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tahunan Selenggara Makmal Komputer {{ $currentYear }}</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            padding: 60px;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 260px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #000 !important;
        }

        .sub-header {
            color: #212f3c;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Campus Title Styles */
        .campus-section {
            margin-top: 30px;
        }

        .campus-section h2 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            background-color: #f4f4f4;
            padding: 10px 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .cross-icon {
            color: red;
            font-size: 16pt;
        }

        .tick-icon {
            color: green;
            font-size: 16pt;
        }

        .tick-icon,
        .cross-icon {
            font-family: 'DejaVu Sans', sans-serif;
            /* Increase icon size */
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            color: #000 !important;
        }


        th,
        td {
            text-align: center;
            font-size: 9pt;
            border: 1px solid #ddd;
            word-wrap: break-word;
            /* Allow wrapping of long text */
        }

        th {
            padding: 6px;
            background-color: #d6dbdf;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <!-- UiTM Logo -->
        <img src="{{ $logoBase64 }}" alt="UiTM Logo">
        <!-- Sub-header -->
        <p class="sub-header">Bahagian Infostruktur</p>
        <!-- Report Title -->
        <h1>LAPORAN TAHUNAN SELENGGARA MAKMAL KOMPUTER {{ $currentYear }}</h1>
    </div>

    @foreach ($campusData as $data)
    <div class="campus-section">
        <h2>{{ $data['campus']->name }}</h2>

        <table>
            <thead>
                <tr>
                    <th style="width:3%">No.</th>
                    <th style="width:20%">Makmal Komputer</th>
                    @foreach ($months as $month)
                    <th>{{ date('M', mktime(0, 0, 0, $month, 1)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data['computerLabList'] as $lab)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align: left; 
            padding-left: 6px;
            padding-right: 6px;">{{ $lab->name }}</td>
                    @foreach ($months as $month)
                    <td>
                        @if ($data['maintainedLabsPerMonth'][$month][$lab->id])
                        <span class="tick-icon">&#10003;</span>
                        @else
                        <span class="cross-icon">&#10005;</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</body>

</html>