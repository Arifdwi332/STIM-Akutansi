@extends('templates.layout')
@section('breadcrumbs', 'PPH')

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Buku Besar</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tabel_pph" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Pendapatan</th>
                            <th>PPH</th>
                            <th>Nominal PPH</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- Banyak Data Dummy -->
                        <tr><td>Januari</td><td>2025</td><td>Rp. 500.000.000</td><td>-</td><td>-</td><td>Rp. 500.000.000</td></tr>
                        <tr><td>Februari</td><td>2025</td><td>Rp. 100.000.000</td><td>0.5 %</td><td>Rp. 500.000</td><td>Rp. 600.000.000</td></tr>
                        <tr><td>Maret</td><td>2025</td><td>Rp. 150.000.000</td><td>0.5 %</td><td>Rp. 750.000</td><td>Rp. 750.000.000</td></tr>

                        <tr><td>April</td><td>2025</td><td>Rp. 200.000.000</td><td>0.5 %</td><td>Rp. 1.000.000</td><td>Rp. 950.000.000</td></tr>
                        <tr><td>Mei</td><td>2025</td><td>Rp. 180.000.000</td><td>0.5 %</td><td>Rp. 900.000</td><td>Rp. 1.130.000.000</td></tr>
                        <tr><td>Juni</td><td>2025</td><td>Rp. 220.000.000</td><td>0.5 %</td><td>Rp. 1.100.000</td><td>Rp. 1.350.000.000</td></tr>
                        <tr><td>Juli</td><td>2025</td><td>Rp. 250.000.000</td><td>0.5 %</td><td>Rp. 1.250.000</td><td>Rp. 1.600.000.000</td></tr>
                        <tr><td>Agustus</td><td>2025</td><td>Rp. 270.000.000</td><td>0.5 %</td><td>Rp. 1.350.000</td><td>Rp. 1.870.000.000</td></tr>
                        <tr><td>September</td><td>2025</td><td>Rp. 300.000.000</td><td>0.5 %</td><td>Rp. 1.500.000</td><td>Rp. 2.170.000.000</td></tr>
                        <tr><td>Oktober</td><td>2025</td><td>Rp. 320.000.000</td><td>0.5 %</td><td>Rp. 1.600.000</td><td>Rp. 2.490.000.000</td></tr>
                        <tr><td>November</td><td>2025</td><td>Rp. 280.000.000</td><td>0.5 %</td><td>Rp. 1.400.000</td><td>Rp. 2.770.000.000</td></tr>
                        <tr><td>Desember</td><td>2025</td><td>Rp. 350.000.000</td><td>0.5 %</td><td>Rp. 1.750.000</td><td>Rp. 3.120.000.000</td></tr>

                        <!-- Tambah data tahun lain -->
                        <tr><td>Januari</td><td>2026</td><td>Rp. 400.000.000</td><td>-</td><td>-</td><td>Rp. 400.000.000</td></tr>
                        <tr><td>Februari</td><td>2026</td><td>Rp. 120.000.000</td><td>0.5 %</td><td>Rp. 600.000</td><td>Rp. 520.000.000</td></tr>
                        <tr><td>Maret</td><td>2026</td><td>Rp. 180.000.000</td><td>0.5 %</td><td>Rp. 900.000</td><td>Rp. 700.000.000</td></tr>
                        <tr><td>April</td><td>2026</td><td>Rp. 210.000.000</td><td>0.5 %</td><td>Rp. 1.050.000</td><td>Rp. 910.000.000</td></tr>
                        <tr><td>Mei</td><td>2026</td><td>Rp. 230.000.000</td><td>0.5 %</td><td>Rp. 1.150.000</td><td>Rp. 1.140.000.000</td></tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#tabel_pph').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: 10
        });
    });
</script>
@endpush
