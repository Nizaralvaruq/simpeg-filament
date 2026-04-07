<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi Stok</title>
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
        <div class="title">LAPORAN MUTASI STOK BARANG</div>
        <div class="subtitle">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th class="text-center">Jenis</th>
                <th class="text-center">Jumlah</th>
                <th>Keterangan</th>
                <th>Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $t)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $t->barang?->nama_barang }} <br><small>({ $t->barang?->kode_barang })</small></td>
                <td class="text-center">
                    @if($t->type == 'in') Masuk
                    @elseif($t->type == 'out') Keluar
                    @else Opname @endif
                </td>
                <td class="text-center">{{ $t->quantity }}</td>
                <td>{{ $t->remarks ?? '-' }}</td>
                <td>{{ $t->createdBy?->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
