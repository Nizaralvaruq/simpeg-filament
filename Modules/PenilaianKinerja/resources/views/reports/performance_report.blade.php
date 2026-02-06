<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Raport Kinerja Pegawai</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            margin: 5px 0 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
        }

        .info-table td.label {
            width: 150px;
            font-weight: bold;
        }

        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .score-table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .score-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .summary-box {
            margin-top: 30px;
            border: 2px solid #333;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .final-score {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #0284c7;
        }

        .grade {
            font-size: 18px;
            text-align: center;
            margin-top: 5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/logo1.png') }}" class="logo">
        <div class="title">Raport Hasil Penilaian Kinerja</div>
        <div class="subtitle">{{ $session->name }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Pegawai</td>
            <td>: {{ $ratee->nama }}</td>
        </tr>
        <tr>
            <td class="label">Unit Kerja</td>
            <td>: {{ $ratee->units->first()?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan (Amanah)</td>
            <td>: {{ $ratee->amanah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td>: {{ \Carbon\Carbon::parse($session->start_date)->translatedFormat('d F Y') }} -
                {{ \Carbon\Carbon::parse($session->end_date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <table class="score-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 60%">Aspek Penilaian</th>
                <th style="width: 15%; text-align: center;">Skor (1-5)</th>
                <th style="width: 20%; text-align: center;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categoryReport as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item['category_name'] }}</td>
                    <td style="text-align: center;">{{ number_format($item['average_score'], 2) }}</td>
                    <td style="text-align: center;">{{ $item['grade'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <div style="font-size: 14px; text-align: center; margin-bottom: 10px;">SKOR AKHIR TERAGREGASI:</div>
        <div class="final-score">{{ number_format($finalScore, 2) }}</div>
        <div class="grade">PREDIKAT: {{ $finalGrade }}</div>
    </div>

    <div class="footer">
        <div class="signature-box">
            <div>Dicetak pada: {{ now()->translatedFormat('d F Y') }}</div>
            <div class="signature-line">
                SDM / Pimpinan Unit
            </div>
        </div>
    </div>
</body>

</html>
