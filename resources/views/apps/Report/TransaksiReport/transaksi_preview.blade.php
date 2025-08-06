@extends("template.partials.app")

@section("content")
    <div class="container">
        <h4>Laporan Transaksi ({{ $start }} sampai {{ $end }})</h4>

        <div class="mb-3">
            <a href="{{ route("report.transaksi.download", ["start" => $start, "end" => $end, "tipe" => request("tipe")]) }}"
                class="btn btn-primary" target="_blank">
                <i class="fas fa-download"></i> Download PDF
            </a>
        </div>

        <iframe
            src="{{ route("report.transaksi.preview") . "?start=" . $start . "&end=" . $end . "&tipe=" . request("tipe") . "&embed=true" }}"
            style="width: 100%; height: 800px; border: 1px solid #ccc;">
        </iframe>
    </div>
@endsection
