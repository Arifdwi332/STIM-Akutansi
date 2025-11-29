@extends('templates.layout')
@section('breadcrumbs', 'PPH')

@section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">PPH 0,5% UMKM</h5>

                {{-- Filter tahun (opsional) --}}
                <div class="d-flex align-items-center">
                    <label for="filter_tahun" class="mr-2 mb-0">Tahun</label>
                    <select id="filter_tahun" class="form-control form-control-sm" style="width: 120px;">
                        @for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" @if ($y == date('Y')) selected @endif>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel_pph" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Penjualan Barang</th>
                                <th>Penjualan Jasa</th>
                                <th>Akumulasi Penjualan</th>
                                <th>Pajak Sebulan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- dikosongkan, diisi lewat JS --}}
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

            function formatRupiah(angka) {
                const n = Number(angka) || 0;
                return n.toLocaleString('id-ID');
            }

            const table = $('#tabel_pph').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                pageLength: 12,
                ordering: false,
                ajax: {
                    url: '{{ route('pph.data') }}',
                    data: function(d) {
                        d.tahun = $('#filter_tahun').val();
                    },
                    dataSrc: function(json) {
                        return json.data || [];
                    }
                },
                columns: [{
                        data: 'bulan'
                    },
                    {
                        data: 'tahun'
                    },
                    {
                        data: 'penjualan_barang',
                        render: function(data) {
                            return 'Rp. ' + formatRupiah(data);
                        }
                    },
                    {
                        data: 'penjualan_jasa',
                        render: function(data) {
                            return 'Rp. ' + formatRupiah(data);
                        }
                    },
                    {
                        data: 'akumulasi_penjualan',
                        render: function(data) {
                            return 'Rp. ' + formatRupiah(data);
                        }
                    },
                    {
                        data: 'pajak_sebulan',
                        render: function(data) {
                            return 'Rp. ' + formatRupiah(data);
                        }
                    }
                ]
            });

            // reload ketika tahun diganti
            $('#filter_tahun').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
