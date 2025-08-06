<style type="text/css">
    .tg {
        border-collapse: collapse;
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 13px;
    }

    .tg th,
    .tg td {
        border: 1px solid black;
        padding: 8px;
        word-break: break-word;
        vertical-align: top;
    }

    .tg th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    h3,
    h4 {
        margin: 5px 0;
        font-family: Arial, sans-serif;
    }

    .section-title {
        background-color: #e0e0e0;
        font-weight: bold;
        text-align: center;
    }

    .spacer-row td {
        border: none;
        padding: 10px 0;
    }
</style>

<h3 class="text-center">LAPORAN ASET TABUNGAN</h3>
<h4 class="text-center">PERIODE {{ \Carbon\Carbon::parse($start)->translatedFormat("d F Y") }} s.d
    {{ \Carbon\Carbon::parse($end)->translatedFormat("d F Y") }}</h4>

@foreach ($groupedData as $group)
    @php
        $pengeluaran = $group["pengeluaran"]->values()->all();
        $pemasukan = $group["pemasukan"]->values()->all();
        $budgetPlans = $group["budgetPlans"] ?? collect();
        $max = max(count($pengeluaran), count($pemasukan));
    @endphp

    <table class="tg">
        <tr>
            <td class="section-title" colspan="10">
                {{ $group["nama_aset"] ?? "ID " . $group["aset_tabungan_id"] }}
            </td>
        </tr>

        <tr>
            <th colspan="5">Pengeluaran</th>
            <th colspan="5">Pemasukan</th>
        </tr>

        <tr>
            <th style="width:10%">Tanggal</th>
            <th colspan="2">Kategori</th>
            <th colspan="2">Jumlah</th>
            <th style="width:10%">Tanggal</th>
            <th colspan="2">Kategori</th>
            <th colspan="2">Jumlah</th>
        </tr>
        @for ($i = 0; $i < $max; $i++)
            <tr>
                <td>{{ $pengeluaran[$i]->tanggal_transaksi ?? "" }}</td>
                <td colspan="2">{{ $pengeluaran[$i]->kategori->nama_kategori ?? "-" }}</td>
                <td colspan="2">
                    {{ isset($pengeluaran[$i]) ? number_format($pengeluaran[$i]->nominal, 0, ",", ".") : "-" }}</td>

                <td>{{ $pemasukan[$i]->tanggal_transaksi ?? "" }}</td>
                <td colspan="2">{{ $pemasukan[$i]->kategori->nama_kategori ?? "-" }}</td>
                <td colspan="2">
                    {{ isset($pemasukan[$i]) ? number_format($pemasukan[$i]->nominal, 0, ",", ".") : "-" }}
                </td>
            </tr>
        @endfor
        <tr>
            <td colspan="5"><strong>Total Pengeluaran : Rp.
                    {{ number_format($group["totalPengeluaran"], 0, ",", ".") }}</strong>
            </td>
            <td colspan="5"><strong>Total Pemasukan : Rp.
                    {{ number_format($group["totalPemasukan"], 0, ",", ".") }}</strong>
            </td>
        </tr>

        <tr>
            <td class="section-title" colspan="10">Setoran Ke Budget Plan</td>
        </tr>

        <tr>
            <th colspan="3">Tanggal</th>
            <th colspan="4">Budget Plan</th>
            <th colspan="3">Jumlah</th>
        </tr>
        @forelse ($budgetPlans as $plan)
            <tr>
                <td colspan="3">{{ \Carbon\Carbon::parse($plan["created_at"])->translatedFormat("d F Y") }}</td>
                <td colspan="4">{{ $plan->BudgetPlan->nama ?? "-" }}</td>
                <td colspan="3">{{ number_format($plan["nominal"], 0, ",", ".") }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada setoran ke Budget Plan</td>
            </tr>
        @endforelse

        <tr>
            <td colspan="10"><strong>Total Setor Budget Plan : Rp.
                    {{ number_format($group["totalBudgetPlan"], 0, ",", ".") }}</strong>
        </tr>
        <tr>
            <td colspan="10"><strong>
                    Total Aset : Rp. {{ number_format($group["total_aset"], 0, ",", ".") }}</td></strong>
        </tr>

        <tr class="spacer-row">
            <td colspan="10"></td>
        </tr>
    </table>
@endforeach

{{-- <style type="text/css">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
    }

    .tg td,
    .tg th {
        border: 1px solid black;
        font-family: Arial, sans-serif;
        font-size: 12px;
        padding: 8px;
        word-break: normal;
    }

    .tg th {
        font-weight: bold;
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .bg-gray {
        background-color: #f0f0f0;
    }

    .mt {
        margin-top: 20px;
    }

    .bg-green {
        background-color: #d4edda;
    }
</style>

<h3 class="text-center">LAPORAN ASET TABUNGAN</h3>
<h4 class="text-center">PERIODE {{ \Carbon\Carbon::parse($start)->translatedFormat("d F Y") }} s.d
    {{ \Carbon\Carbon::parse($end)->translatedFormat("d F Y") }}</h4>

@foreach ($groupedData as $group)
    @php
        $pengeluaran = $group["pengeluaran"]->values()->all();
        $pemasukan = $group["pemasukan"]->values()->all();
        $budgetPlans = $group["budgetPlans"] ?? [];
        $max = max(count($pengeluaran), count($pemasukan));
    @endphp

    <table class="tg mt">
        <thead>
            <tr>
                <th colspan="10" class="text-left bg-gray">
                    {{ $group["nama_aset"] ?? "ID " . $group["aset_tabungan_id"] }}
                </th>
            </tr>
            <tr>
                <th colspan="5" class="text-center">Pengeluaran</th>
                <th colspan="5" class="text-center">Pemasukan</th>
            </tr>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th colspan="3" class="text-right">Jumlah</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th colspan="3" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $max; $i++)
                <tr>
                    <td>{{ $pengeluaran[$i]->tanggal_transaksi ?? "" }}</td>
                    <td>{{ $pengeluaran[$i]->kategori->nama_kategori ?? "-" }}</td>
                    <td colspan="3" class="text-right">
                        {{ isset($pengeluaran[$i]) ? number_format($pengeluaran[$i]->nominal, 0, ",", ".") : "-" }}
                    </td>

                    <td>{{ $pemasukan[$i]->tanggal_transaksi ?? "" }}</td>
                    <td>{{ $pemasukan[$i]->kategori->nama_kategori ?? "-" }}</td>
                    <td colspan="3" class="text-right">
                        {{ isset($pemasukan[$i]) ? number_format($pemasukan[$i]->nominal, 0, ",", ".") : "-" }}
                    </td>
                </tr>
            @endfor

            <tr class="bg-gray">
                <td colspan="5" style="font-weight: bold"><strong>Grand Total:</strong> Rp
                    {{ number_format($group["totalPengeluaran"], 0, ",", ".") }}</td>
                <td colspan="5" style="font-weight: bold"><strong>Grand Total:</strong> Rp
                    {{ number_format($group["totalPemasukan"], 0, ",", ".") }}</td>
            </tr>
        </tbody>
    </table>
@endforeach --}}
