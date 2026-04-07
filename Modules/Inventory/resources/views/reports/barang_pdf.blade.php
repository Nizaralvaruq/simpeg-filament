<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Barang</title>
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
        <div class="title">LAPORAN DATA BARANG INVENTARIS</div>
        <div class="subtitle">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Pemilik</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $index => $b)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $b->kode_barang }}</td>
                <td>{{ $b->nama_barang }}<br><small>{{ $b->jenis }}</small></td>
                <td>{{ $b->kategori?->nama_kategori ?? '-' }}</td>
                <td>{{ $b->unit?->name ?? 'Pusat/Global' }}</td>
                <td class="text-center">{{ $b->stok_saat_ini }}</td>
                <td class="text-center">{{ $b->is_active ? 'Aktif' : 'Non-aktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
