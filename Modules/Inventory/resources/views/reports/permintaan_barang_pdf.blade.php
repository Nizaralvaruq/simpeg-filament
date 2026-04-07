<!DOCTYPE html>
<html>
<head>
    <title>Laporan Permintaan Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { max-height: 80px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 14px; }
    </style>
</head>
<body>

    <div class="header">
        @if(file_exists(public_path('images/logo1.png')))
            <img src="{{ public_path('images/logo1.png') }}" class="logo" />
        @endif
        <div class="title">LAPORAN REKAPITULASI PERMINTAAN BARANG</div>
        <div class="subtitle">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Nomor / Pemohon</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Detail Barang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permintaans as $index => $p)
            <tr>
                <td class="text-center" style="vertical-align: top;">{{ $index + 1 }}</td>
                <td style="vertical-align: top;">{{ $p->tanggal_permintaan->format('d/m/Y') }}</td>
                <td style="vertical-align: top;">
                    <strong>{{ $p->nomor_permintaan }}</strong><br>
                    {{ $p->user?->name }}
                </td>
                <td style="vertical-align: top;">{{ $p->unit?->name ?? '-' }}</td>
                <td class="text-center" style="vertical-align: top;">{{ strtoupper($p->status) }}</td>
                <td style="vertical-align: top;">
                    <ul style="margin: 0; padding-left: 15px;">
                        @foreach($p->details as $d)
                            <li>{{ $d->barang?->nama_barang }} (Ajuan: {{ $d->jumlah_diminta }}, ACC: {{ $d->jumlah_disetujui }})</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
