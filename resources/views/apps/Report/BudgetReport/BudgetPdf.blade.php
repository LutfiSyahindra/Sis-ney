 <style>
     body {
         font-family: Arial, sans-serif;
         font-size: 12px;
         margin: 20px;
     }

     h2 {
         text-align: center;
         margin-bottom: 30px;
     }

     table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 30px;
     }

     th,
     td {
         border: 1px solid #999;
         padding: 8px;
         text-align: left;
     }

     th {
         background-color: #f2f2f2;
     }

     .text-green {
         color: green;
         font-weight: bold;
     }

     .text-red {
         color: red;
         font-weight: bold;
     }

     .text-right {
         text-align: right;
     }
 </style>

 @foreach ($groupedData as $periode => $g)
     @php
         $totalTerpakai = array_sum(array_column($g["items"], "terpakai"));
         $totalSisa = array_sum(array_column($g["items"], "sisa"));
     @endphp

     <table>
         <thead>
             <tr>
                 <th colspan="8" class="tg-header" style="text-align: center">LAPORAN BUDGET<br>PERIODE
                     {{ strtoupper($periode) }}</th>
             </tr>
         </thead>
         <tbody>
             {{-- Ringkasan --}}
             <tr class="ringkasan-row">
                 <td style="text-align: center; font-weight: bold">Total Budget</td>
                 <td colspan="2" style="text-align: center; font-weight: bold">Teralokasi</td>
                 <td colspan="2" style="text-align: center; font-weight: bold">Belum Teralokasi</td>
                 <td style="text-align: center; font-weight: bold">Terpakai</td>
                 <td colspan="2" style="text-align: center; font-weight: bold">Sisa</td>
             </tr>
             <tr>
                 <td style="text-align: center">Rp {{ number_format($g["total_budget"], 0, ",", ".") }}</td>
                 <td colspan="2" style="text-align: center">Rp {{ number_format($g["total_alokasi"], 0, ",", ".") }}
                 </td>
                 <td colspan="2" style="text-align: center">Rp
                     {{ number_format($g["total_belum_alokasi"], 0, ",", ".") }}</td>
                 <td style="text-align: center">Rp {{ number_format($totalTerpakai, 0, ",", ".") }}</td>
                 <td colspan="2" style="text-align: center">Rp {{ number_format($totalSisa, 0, ",", ".") }}</td>
             </tr>

             {{-- Header Detail --}}
             <tr class="tg-header">
                 <td class="text-center" style="text-align: center; font-weight: bold">No</td>
                 <td style="text-align: center; font-weight: bold">Kategori</td>
                 <td style="text-align: center; font-weight: bold">Jumlah Budget</td>
                 <td style="text-align: center; font-weight: bold">Terpakai</td>
                 <td style="text-align: center; font-weight: bold">Sisa</td>
                 <td colspan="3" class="text-center" style="text-align: center; font-weight: bold">Status</td>
             </tr>

             {{-- Data Detail --}}
             @foreach ($g["items"] as $i => $item)
                 <tr>
                     <td class="text-center" style="text-align: center">{{ $i + 1 }}</td>
                     <td style="text-align: center">{{ $item["kategori"] }}</td>
                     <td style="text-align: center">Rp {{ number_format($item["jumlah"], 0, ",", ".") }}</td>
                     <td style="text-align: center">Rp {{ number_format($item["terpakai"], 0, ",", ".") }}</td>
                     <td style="text-align: center">Rp {{ number_format($item["sisa"], 0, ",", ".") }}</td>
                     <td colspan="3"
                         style="color: {{ strtolower($item["status"]) == "on budget" ? "green" : "red" }}; text-align: center;">
                         {{ $item["status"] }}
                     </td>

                 </tr>
             @endforeach

         </tbody>
     </table>
 @endforeach
