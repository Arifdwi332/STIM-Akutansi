@extends('templates.layout')

@section('breadcrumbs')
@endsection

<link rel="stylesheet" href="https://cdn.datatables.net/v/bs4/dt-2.1.8/datatables.min.css" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root {
        --bb-primary: #1E5296;
        --bb-border: #E8ECEF;
    }

    .inv-wrap {
        max-width: 100% !important;
        padding-left: 1rem;
        padding-right: 1rem
    }

    .bb-panel {
        background: #fff;
        border: 1px solid var(--bb-border);
        border-radius: 8px
    }

    .bb-head {
        background: #EEF2F6;
        border-bottom: 1px solid var(--bb-border);
        padding: .6rem .9rem;
        font-weight: 600;
        color: #334155
    }

    .bb-body {
        padding: .9rem
    }

    .page-title {
        font-weight: 600;
        margin-bottom: 14px
    }

    /* tabel barang (form) */
    .table-inv th,
    .table-inv td {
        vertical-align: middle
    }

    .table-inv th {
        font-weight: 600
    }

    .w-idx {
        width: 40px
    }

    .w-qty {
        width: 70px
    }

    .w-unit {
        width: 150px
    }

    .w-price {
        width: 180px
    }

    .w-sub {
        width: 180px
    }

    .w-act {
        width: 60px
    }

    .btn-icon {
        width: 34px;
        height: 34px;
        display: inline-grid;
        place-items: center;
        padding: 0
    }

    .text-right .form-control {
        text-align: right
    }

    .inv-total-label {
        color: #16a34a;
        font-weight: 700
    }

    .btn-primary {
        background: #1E5296;
        border-color: #1E5296
    }

    .btn-primary:hover {
        filter: brightness(.95)
    }

    .form-control:focus,
    .custom-select:focus {
        border-color: #1E5296;
        box-shadow: 0 0 0 .2rem rgba(30, 82, 150, .15)
    }

    /* lebar fixed utk baris tanggal-pelanggan/pemasok-btn-no */
    .fx-200 {
        flex: 0 0 200px;
        max-width: 200px
    }

    .fx-260 {
        flex: 0 0 260px;
        max-width: 260px
    }

    .fx-140 {
        flex: 0 0 140px;
        max-width: 140px
    }

    .fx-180 {
        flex: 0 0 180px;
        max-width: 180px
    }

    .gap-12 {
        width: 12px
    }

    /* tombol setinggi input */
    .btn-equal {
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .6rem;
        line-height: 1.5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap
    }

    /* nav pills (panel bawah) */
    .nav-pills .nav-link {
        background: #E5E7EB;
        color: #111827;
        margin-right: 6px;
        font-weight: 600
    }

    .nav-pills .nav-link.active {
        background: #22c55e;
        color: #fff
    }

    @media (max-width:991.98px) {

        .fx-200,
        .fx-260,
        .fx-140,
        .fx-180 {
            flex: 0 0 100%;
            max-width: 100%
        }

        .gap-12 {
            display: none
        }
    }
</style>

@section('content')
    <div class="container-fluid inv-wrap">
        <h4 class="page-title">Inventaris</h4>

        {{-- ==================== FORM TRANSAKSI ==================== --}}
        <div class="bb-panel">
            <div class="bb-head">Transaksi</div>

            <div class="bb-body">
                {{-- Baris 0 --}}
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label class="mb-1">Tipe Transaksi</label>
                        <select id="tipe_transaksi" class="form-control">
                            <option value="Penjualan">Penjualan</option>
                            <option value="Inventaris" selected>Inventaris</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="mb-1">Tipe Pembayaran</label>
                        <select id="tipe_pembayaran" name="tipe_pembayaran" class="form-control">
                            <option value="1">Tunai</option>
                            <option value="2" selected>Non Tunai</option>
                        </select>
                    </div>
                </div>

                {{-- Baris 1 --}}
                <div class="form-row align-items-end" style="display:flex;flex-wrap:wrap;">
                    {{-- Tanggal --}}
                    <div class="form-group d-flex flex-column fx-200">
                        <label class="mb-1">Tanggal</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="tgl_transaksi" placeholder="xx/xx/xxxx">
                        </div>
                    </div>

                    <div class="gap-12"></div>

                    {{-- Pelanggan/Pemasok --}}
                    <div class="form-group d-flex flex-column fx-260">
                        <label class="mb-1" id="party_label">Pelanggan</label>
                        <select id="party_id" class="form-control"></select>
                    </div>

                    <div class="gap-12"></div>

                    {{-- Tombol Tambah --}}
                    <div class="form-group d-flex align-items-end fx-140">
                        <button class="btn btn-info btn-sm btn-equal w-100" id="btnAddParty" type="button">
                            Tambah Pelanggan
                        </button>
                    </div>

                    <div class="gap-12"></div>

                    {{-- No Transaksi --}}
                    <div class="form-group d-flex flex-column fx-180">
                        {{-- <label class="mb-1">No Transaksi</label> --}}
                        <input type="hidden" class="form-control" id="no_transaksi" readonly>
                    </div>
                </div>

                {{-- Tabel barang --}}
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-inv">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-idx">#</th>
                                <th>Barang</th>
                                <th class="w-qty">Qty</th>
                                <th class="w-unit">Satuan Ukur</th>
                                <th class="w-price">Harga Satuan Beli</th>
                                <th class="w-price">Harga Satuan Jual</th>
                                <th class="w-sub">Total</th>
                                <th class="w-act"></th>
                            </tr>
                        </thead>
                        <tbody id="inv-rows">
                            <tr>
                                <td class="align-middle">1</td>
                                <td>
                                    <select class="form-control item-nama">
                                        <option>Choose</option>
                                    </select>
                                </td>
                                <td><input type="number" min="0" value="0" class="form-control item-qty"></td>
                                <td><input type="text" class="form-control item-satuan" readonly placeholder="-"></td>
                                <td class="text-right">
                                    <input type="text" class="form-control item-harga" value="0">
                                </td>
                                <td class="text-right">
                                    <input type="text" class="form-control item-jual" value="0">
                                </td>
                                <td class="text-right">
                                    <input type="text" class="form-control item-subtotal" value="0" readonly>
                                </td>
                                <td class="text-right">
                                    <button class="btn btn-danger btn-icon inv-del" type="button">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-primary btn-sm" id="inv-add"><strong>Tambah</strong></button>

                {{-- Ringkasan kanan --}}
                <div class="row mt-4">
                    <div class="col-md-6 offset-md-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Biaya lainya</label>
                            <div class="col-sm-8">
                                <input type="text" id="biaya_lain" class="form-control text-right" value="0">
                            </div>
                        </div>

                        {{-- Diskon % -> otomatis sembunyi saat Inventaris --}}
                        <div class="form-group row" id="group_diskon">
                            <label class="col-sm-4 col-form-label">Diskon %</label>
                            <div class="col-sm-8">
                                <input type="number" id="diskon_persen" class="form-control text-right" value="0">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Pajak</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">11%</span></div>
                                    <input type="text" id="pajak_nominal" class="form-control text-right"
                                        value="0" readonly>
                                </div>
                                <small class="text-muted">PPN 11% dihitung otomatis</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label inv-total-label">Total</label>
                            <div class="col-sm-8">
                                <input type="text" id="grand_total" class="form-control text-right font-weight-bold"
                                    value="Rp. 0" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4 text-right">
                                <button class="btn btn-primary" id="btnSimpanInventaris" type="button">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- .bb-body --}}
        </div>{{-- .bb-panel --}}

        {{-- ==================== LIST (opsional, tetap seperti sebelumnya) ==================== --}}
        <div class="bb-panel mt-3">
            <div class="bb-head">Penjualan</div>
            <div class="bb-body">
                <ul class="nav nav-pills mb-3" id="invTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabTransaksi">Data
                            Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabInventaris">Inventaris</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabTransaksi">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tblTransaksi" style="width:100%">
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
                                <tbody>


                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tabInventaris">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tblInventaris" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">No</th>
                                        <th>Nama Barang</th>
                                        <th>Pemasok</th>
                                        <th style="width:90px;">Stok</th>
                                        <th style="width:100px;">Satuan</th>
                                        <th style="width:150px;">Total Harga</th>
                                        <th style="width:100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> {{-- .tab-content --}}
            </div>
        </div>
    </div>
    @include('inventaris.modal')
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-2.1.8/datatables.min.js"></script>
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });
        });

        // === Helpers global ===
        window.toNumber = function(v) {
            let s = String(v ?? '').trim();
            if (!s) return 0;
            s = s.replace(/\s/g, '');
            if (s.includes('.') && s.includes(',')) {
                s = s.replace(/\./g, '').replace(',', '.');
            } else if (s.includes(',')) {
                s = s.replace(',', '.');
            }
            s = s.replace(/[^\d.-]/g, '');
            return parseFloat(s) || 0;
        };

        (function() {
            const $body = $('#inv-rows');
            const toNumber = window.toNumber; // alias lokal

            const fmt = n => 'Rp. ' + (Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));

            function renumber() {
                $body.find('tr').each((i, tr) => $(tr).find('td:first').text(i + 1));
            }

            function recompute() {
                let sub = 0;
                $body.find('.item-subtotal').each(function() {
                    sub += toNumber($(this).val());
                });
                const biaya = toNumber($('#biaya_lain').val());
                const discP = toNumber($('#diskon_persen').val());
                const afterDisc = sub - (sub * (discP / 100));
                const pajak = afterDisc * 0.11;
                const grand = afterDisc + pajak + biaya;
                $('#pajak_nominal').val(Math.round(pajak));
                $('#grand_total').val(fmt(grand));
            }

            function hitungSubtotal($tr) {
                const qty = toNumber($tr.find('.item-qty').val());
                const hargaJual = toNumber($tr.find('.item-jual').val());
                const subtotal = qty * hargaJual;
                $tr.find('.item-subtotal').val(subtotal);
            }

            function bindRow($tr) {
                $tr.on('input', '.item-qty,.item-jual', () => {
                    hitungSubtotal($tr);
                    recompute();
                });

                $tr.find('.inv-del').on('click', function() {
                    if ($body.find('tr').length <= 1) return;
                    $tr.remove();
                    renumber();
                    recompute();
                });
            }

            // init rows
            $body.find('tr').each(function() {
                bindRow($(this));
            });

            // add row
            $('#inv-add').on('click', function() {
                const $c = $body.find('tr:last').clone();
                $c.find('input').val('0');
                $c.find('.item-satuan').val('').attr('placeholder', '-');
                $body.append($c);
                renumber();
                bindRow($c);
            });

            // recompute on extra fields
            $('#biaya_lain,#diskon_persen').on('input', recompute);
        })();

        function hydrateBarangSelects() {
            $('select.item-nama').each(function() {
                fillBarangOptions($(this), BARANG);
            });
        }

        // --- Loader barang: Penjualan => ALL, Inventaris => by pemasok
        function loadBarang(mode, pemasokId) {
            const isPenjualan = mode === 'Penjualan';

            // OPSI A (disarankan): pakai endpoint khusus all items
            const url = isPenjualan ?
                "{{ route('inventaris.barangSemua') }}" :
                "{{ route('inventaris.barangByPemasok') }}";

            const params = isPenjualan ? {} : {
                pemasok_id: pemasokId
            };

            $.get(url, params)
                .done(function(res) {
                    if (res && res.ok && Array.isArray(res.data)) {
                        BARANG = res.data;
                        hydrateBarangSelects();
                    } else {
                        BARANG = [];
                        hydrateBarangSelects();
                    }
                })
                .fail(function() {
                    alert('Gagal memuat data barang.');
                });
        }
    </script>

    <script>
        window.applyMode = function(mode) {
            const isInv = (mode === 'Inventaris');
            $('#party_label').text(isInv ? 'Pemasok' : 'Pelanggan');
            $('#btnAddParty').text(isInv ? 'Tambah Pemasok' : 'Tambah Pelanggan');

            $('#no_transaksi').val('');

            $('#group_diskon').toggle(!isInv);
        };


        function loadPartyOptions(tipe) {
            $.get("{{ route('inventaris.parties') }}", {
                    tipe: tipe
                })
                .done(function(res) {
                    const $sel = $('#party_id');
                    $sel.empty().append('<option value="">Pilih</option>');
                    if (res && res.ok && Array.isArray(res.data)) {
                        res.data.forEach(function(row) {
                            $sel.append(new Option(row.nama, row.id));
                        });
                    }
                })
                .fail(function() {
                    alert('Gagal memuat data ' + (tipe === 'Inventaris' ? 'pemasok' : 'pelanggan'));
                });
        }

        $(function() {
            const $tipe = $('#tipe_transaksi');

            applyMode($tipe.val());
            loadPartyOptions($tipe.val());
            loadBarang($tipe.val(), $('#party_id').val());

            $tipe.on('change', function() {
                const tipeBaru = this.value;
                applyMode(tipeBaru);
                loadPartyOptions(tipeBaru);
                loadBarang(tipeBaru, $('#party_id').val());
            });

            $(document).on('click', '#btnAddParty', function() {
                const isInv = $('#tipe_transaksi').val() === 'Inventaris';
                if (isInv) {
                    $('#formPemasokBaru')[0] && $('#formPemasokBaru')[0].reset();
                } else {
                    $('#formPelangganBaru')[0] && $('#formPelangganBaru')[0].reset();
                }
                $(isInv ? '#modalPemasokBaru' : '#modalPelangganBaru').modal('show');
            });
        });
    </script>

    <script>
        // Cache barang terakhir sesuai pemasok terpilih
        let BARANG = [];

        function fillBarangOptions($select, list) {
            $select.empty().append('<option value="">Pilih Barang</option>');
            (list || []).forEach(function(b) {
                // gunakan nama & id sesuai response API
                $select.append(new Option(b.nama_barang, b.id_barang));
            });
        }

        function findBarangById(id) {
            return BARANG.find(b => String(b.id_barang) === String(id));
        }

        // Ketika user memilih barang di baris tabel
        $(document).on('change', 'select.item-nama', function() {
            const $tr = $(this).closest('tr');
            const id = $(this).val();
            const data = findBarangById(id);

            if (!data) {
                $tr.find('.item-satuan').val('').attr('placeholder', '-');
                $tr.find('.item-harga').val('0');
                $tr.find('.item-jual').val('0');
                $tr.find('.item-subtotal').val('0');
                return;
            }

            const satuan = data.satuan_ukur || '';
            $tr.find('.item-satuan').val(satuan).attr('placeholder', satuan || '-');

            const hargaBeli = toNumber(data.harga_satuan || 0);
            $tr.find('.item-harga').val(hargaBeli);

            const hargaJual = toNumber(data.harga_jual || 0);
            $tr.find('.item-jual').val(hargaJual);

            const $qty = $tr.find('.item-qty');
            if (!parseFloat(($qty.val() || '').replace(',', '.'))) {
                $qty.val('1');
            }

            $tr.find('.item-jual').trigger('input');
        });

        $(document).on('click', '#inv-add', function() {
            setTimeout(function() {
                const $last = $('#inv-rows tr:last');
                fillBarangOptions($last.find('select.item-nama'), BARANG);
            }, 0);
        });

        function refreshHargaSemuaBaris() {
            $('#inv-rows tr').each(function() {
                const $tr = $(this);
                const id = $tr.find('select.item-nama').val();
                const data = findBarangById(id);
                if (!data) return;
                $tr.find('.item-harga').val(toNumber(data.harga_satuan || 0));
                $tr.find('.item-jual').val(toNumber(data.harga_jual || 0)).trigger('input');
            });
        }

        $(document).on('change', '#tipe_transaksi', function() {
            refreshHargaSemuaBaris();
        });
    </script>


    <script>
        function _num(v) {
            try {
                return (typeof toNumber === 'function') ? toNumber(v) : (parseFloat(String(v).replace(/[^\d.-]/g, '')) ||
                    0);
            } catch (e) {
                return 0;
            }
        }

        $(document).on('click', '#btnSimpanInventaris', function() {
            const $btn = $(this).prop('disabled', true).text('Menyimpan...');

            // Kumpulkan item dari tabel
            const items = [];
            $('#inv-rows tr').each(function() {
                const $tr = $(this);
                const id = $tr.find('select.item-nama').val();
                const qty = _num($tr.find('.item-qty').val());
                const satuan = ($tr.find('.item-satuan').val() || '').trim() || null;
                const harga = _num($tr.find('.item-harga').val());
                const hargajual = _num($tr.find('.item-jual').val());

                if (id && qty > 0) {
                    items.push({
                        barang_id: id,
                        qty: qty,
                        satuan: satuan,
                        harga: harga,
                        hargajual: hargajual
                    });
                }
            });

            if (!items.length) {
                alert('Minimal 1 item harus diisi.');
                return $btn.prop('disabled', false).text('Simpan');
            }

            const payload = {
                tipe: $('#tipe_transaksi').val(),
                tanggal: $('#tgl_transaksi').val(),
                party_id: $('#party_id').val() || null,
                tipe_pembayaran: Number($('#tipe_pembayaran').val()),
                biaya_lain: _num($('#biaya_lain').val()),
                diskon_persen: _num($('#diskon_persen').val()),
                pajak_persen: 11,
                items: items
            };


            $.ajax({
                    method: 'POST',
                    url: "{{ route('inventaris.store') }}",
                    data: payload,
                    dataType: 'json'
                })
                .done(function(res) {
                    if (res && res.ok) {
                        // Beres
                        if (window.toastr && toastr.success) toastr.success(res.message || 'Tersimpan');
                        else alert(res.message || 'Tersimpan');

                        // reset ringan: kosongkan qty/harga/subtotal
                        $('#inv-rows tr').each(function() {
                            $(this).find('select.item-nama').val('');
                            $(this).find('.item-qty').val('0');
                            $(this).find('.item-satuan').val('').attr('placeholder', '-');
                            $(this).find('.item-harga').val('0');
                            $(this).find('.item-subtotal').val('0');
                        });
                        $('#grand_total').val('Rp. 0');
                        $('#pajak_nominal').val('0');
                    } else {
                        const msg = (res && res.message) ? res.message : 'Gagal menyimpan';
                        if (window.toastr && toastr.error) toastr.error(msg);
                        else alert(msg);
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Gagal menyimpan';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (window.toastr && toastr.error) toastr.error(msg);
                    else alert(msg);
                })
                .always(function() {
                    $btn.prop('disabled', false).text('Simpan');
                });
        });
    </script>

    <script>
        function toRp(n) {
            n = Number(n || 0);
            return 'Rp. ' + (Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        }

        function fmtDateYmdToId(ymd) {
            if (!ymd) return '-';
            const [y, m, d] = ymd.split('-');
            return `${d}/${m}/${y}`;
        }

        let DT_TRANSAKSI, DT_INVENTARIS;

        $(function() {
            DT_TRANSAKSI = $('#tblTransaksi').DataTable({
                ajax: "{{ route('inventaris.dt.transaksi') }}",
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
                    }, // Tanggal
                    {
                        data: 'tipe_label'
                    }, // Tipe Transaksi
                    {
                        data: 'no_transaksi'
                    }, // No. Transaksi
                    {
                        data: 'nama_kontak'
                    }, // Nama Pelanggan/Pemasok
                    {
                        data: 'deskripsi'
                    }, // Deskripsi (nama barang pertama)
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
                        width: '100px',
                        render: (row) => `<a href="#" class="btn btn-primary btn-sm">Detail</a>`
                    },
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

            DT_INVENTARIS = $('#tblInventaris').DataTable({
                ajax: "{{ route('inventaris.dt.inventaris') }}",
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
                        data: 'nama_barang'
                    }, // Nama Barang
                    {
                        data: 'pemasok'
                    }, // Pemasok
                    {
                        data: 'stok',
                        className: 'text-right',
                        width: '90px',
                        render: (v) => Number(v || 0).toString()
                    }, // Stok (dari jml_barang transaksi)
                    {
                        data: 'satuan',
                        width: '100px'
                    }, // Satuan
                    {
                        data: 'total',
                        className: 'text-right',
                        width: '150px',
                        render: (v) => toRp(v)
                    }, // Total Harga
                    {
                        data: null,
                        width: '100px',
                        render: () => `<a href="#" class="btn btn-primary btn-sm">Detail</a>`
                    },
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

            $('a[data-toggle="tab"][href="#tabTransaksi"]').on('shown.bs.tab', () => DT_TRANSAKSI.ajax.reload(null,
                false));
            $('a[data-toggle="tab"][href="#tabInventaris"]').on('shown.bs.tab', () => DT_INVENTARIS.ajax.reload(
                null, false));
        });
    </script>

    <script>
        $(document).on('change', '#party_id', function() {
            const mode = $('#tipe_transaksi').val();
            const pemasokId = $(this).val();

            if (mode === 'Inventaris') {
                loadBarang('Inventaris', pemasokId || null);
            } else {
                if (!BARANG.length) {
                    loadBarang('Penjualan', null);
                }
            }
        });
    </script>
@endpush
