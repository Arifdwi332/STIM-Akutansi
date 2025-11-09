@extends('templates.layout')

@section('title', 'Faktur / Nota')

@section('breadcrumbs')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Faktur / Nota</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Tabel --}}
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="tblFaktur" class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Tanggal</th>
                                <th>Tipe Transaksi</th>
                                <th>No. Transaksi</th>
                                <th>Nama Pelanggan</th>
                                <th>Deskripsi</th>
                                <th style="width:80px;">Qty</th>
                                <th style="width:150px;">Harga</th>
                                <th style="width:100px;">Aksi</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>

        </div>
    </section>

    @push('scripts')
    <script>
        (function($) {
            // helper
            window.toRp = window.toRp || function(n) {
                n = Number(n || 0);
                return 'Rp. ' + (Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
            };
            window.fmtDateYmdToId = window.fmtDateYmdToId || function(ymd) {
                if (!ymd) return '-';
                const [y, m, d] = String(ymd).split('-');
                return (y && m && d) ? `${d}/${m}/${y}` : ymd;
            };

            $(function() {
                window.DT_FAKTUR = $('#tblFaktur').DataTable({
                    ajax: "{{ route('faktur.dt.transaksi') }}",
                    paging: true,
                    searching: true,
                    info: true,
                    lengthChange: false,
                    pageLength: 10,
                    ordering: false,
                    columns: [{
                            data: null,
                            render: (d, t, r, meta) => meta.row + 1,
                            className: 'text-center',
                            width: '60px'
                        },
                        {
                            data: 'tgl',
                            render: (v) => fmtDateYmdToId(v)
                        },
                        {
                            data: 'tipe_label'
                        },
                        {
                            data: 'no_transaksi'
                        },
                        {
                            data: 'nama_kontak'
                        },
                        {
                            data: 'deskripsi'
                        },
                        {
                            data: 'qty',
                            className: 'text-right',
                            width: '80px',
                            render: (v) => Number(v || 0).toString()
                        },
                        {
                            data: 'total',
                            className: 'text-right',
                            width: '150px',
                            render: (v) => toRp(v)
                        },
                        {
                            data: null,
                            width: '160px',
                            className: 'text-center',
                            render: (row) => {
                                const base = '{{ url('faktur') }}' + '/' + encodeURIComponent(
                                    row.no_transaksi);
                                return `
   
      <a href="${base}/export/pdf" class="btn btn-outline-danger btn-sm">PDF</a>
    `;
                            }
                        }


                    ],
                    language: {
                        search: "Search:",
                        paginate: {
                            previous: "Previous",
                            next: "Next"
                        },
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries",
                        zeroRecords: "No matching records found"
                    }
                });
            });
        })(jQuery);
    </script>
@endpush

@endsection


