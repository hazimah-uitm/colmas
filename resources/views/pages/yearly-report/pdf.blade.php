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

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 14px;
            color: #555;
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
        }

        th,
        td {
            padding-top: 4px;
            padding-bottom: 4px;
            padding-left: 8px;
            padding-right: 8px;
            text-align: center;
            border: 1px solid #ddd;
            word-wrap: break-word;
            /* Allow wrapping of long text */
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        /* Monthly columns */
        .month-column {
            width: 5%;
            text-align: center;
        }

        /* Campus and lab columns */
        .campus-column {
            width: 20%;
            font-weight: bold;
        }

        .lab-column {
            width: 15%;
            text-align: left;
            padding-left: 8px;
        }

        .footer p {
            font-size: 10pt;
            margin-top: 30px;
            text-align: center;
        }

        .dijana-oleh {
            font-size: 10pt;
            color: #555;
            margin-top: 15px;
            text-align: center;
        }

        hr {
            margin: 10px 0;
            border: none;
            border-top: 1px solid #ccc;
        }


        /* Footer styles */
        .footer hr {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN TAHUNAN SELENGGARA MAKMAL KOMPUTER {{ $currentYear }}</h1>
    </div>

    @foreach ($campusData as $data)
    <div class="campus-section">
        <h2>{{ $data['campus']->name }}</h2>

        <table>
            <thead>
                <tr>
                    <th style="width: 2%">No.</th>
                    <th class="lab-column">Makmal Komputer</th>
                    @foreach ($months as $month)
                    <th class="month-column">{{ date('M', mktime(0, 0, 0, $month, 1)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data['computerLabList'] as $lab)
                <tr>
                <th style="width: 2%">{{ $loop->iteration }}</td>
                    <td style="text-align: left;">{{ $lab->name }}</td>
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
    
    <div class="footer">
            <hr>
            <p class="dijana-oleh">Dijana oleh: {{ $username }}</p>
        </div>
</body>

</html>