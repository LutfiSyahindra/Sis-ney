<style type="text/css">
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
        margin: 20px;
    }

    .title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .section-title {
        font-weight: bold;
        background-color: #f2f2f2;
        padding: 5px;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 8px;
        vertical-align: top;
    }

    th {
        background-color: #e0e0e0;
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }
</style>

@php
    use Carbon\Carbon;
    $formatTanggal = fn($tgl) => Carbon::parse($tgl)->format("d-m-Y");
    $rupiah = fn($angka) => "Rp " . number_format($angka, 0, ",", ".");
@endphp

<div class="title">
    LAPORAN KEUANGAN<br>
    PERIODE {{ $formatTanggal($start) }} s.d {{ $formatTanggal($end) }}
</div>

<div class="section-title">PENGELUARAN</div>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>User</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Tipe</th>
            <th>Aset</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pengeluaran as $trx)
            <tr>
                <td>{{ $formatTanggal($trx->tanggal_transaksi) }}</td>
                <td>{{ $trx->user->name ?? "-" }}</td>
                <td>{{ $trx->kategori->nama_kategori ?? "-" }}</td>
                <td class="text-right">{{ $rupiah($trx->nominal) }}</td>
                <td class="text-center">{{ ucfirst($trx->tipe) }}</td>
                <td>{{ $trx->aset->nama_tabungan ?? "-" }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data pengeluaran</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="3" class="bold text-right">Total Pengeluaran</td>
            <td class="bold text-right">{{ $rupiah($totalPengeluaran) }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>

<div class="section-title">PEMASUKAN</div>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>User</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Tipe</th>
            <th>Aset</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pemasukan as $trx)
            <tr>
                <td>{{ $formatTanggal($trx->tanggal_transaksi) }}</td>
                <td>{{ $trx->user->name ?? "-" }}</td>
                <td>{{ $trx->kategori->nama_kategori ?? "-" }}</td>
                <td class="text-right">{{ $rupiah($trx->nominal) }}</td>
                <td class="text-center">{{ ucfirst($trx->tipe) }}</td>
                <td>{{ $trx->aset->nama_tabungan ?? "-" }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data pemasukan</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="3" class="bold text-right">Total Pemasukan</td>
            <td class="bold text-right">{{ $rupiah($totalPemasukan) }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
