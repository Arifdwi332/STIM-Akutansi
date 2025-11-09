@extends('templates.layout')

@section('breadcrumbs')
@endsection

@section('content')
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

    .nav-sidebar .nav-link {
        background: #fff !important;
        color: #1E5296 !important;
    }

    .nav-sidebar .nav-link:not(.active):hover {
        background: #f5f5f5 !important;
        color: #1E5296 !important;
    }

    .nav-sidebar .nav-link.active {
        background: #1E5296 !important;
        color: #fff !important;
    }
</style>

<div class="container-fluid inv-wrap">
    <h4 class="page-title">Data Transaksi</h4>

    {{-- ==================== FORM TRANSAKSI ==================== --}}
    <div class="bb-panel">
        <div class="bb-body">
            <div class="col-lg-12">
                <div class="bb-panel">
                    <div class="bb-head">Transaksi</div>
                    <div class="bb-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tipe Transaksi</label>
                                <select id="tipe_transaksi" class="custom-select">
                                    <!-- Pembayaran Operasional -->
                                    <option value="Bayar Gaji">Bayar Gaji</option>
                                    <option value="Bayar Listrik">Bayar Listrik</option>
                                    <option value="Bayar Listrik/Telepon/Internet/Air">Bayar
                                        Listrik/Telepon/Internet/Air</option>
                                    <option value="Bayar Iklan/Promosi">Bayar Iklan/Promosi</option>
                                    <option value="Bayar Transportasi (Ongkir, BBM, dll)">Bayar Transportasi (Ongkir,
                                        BBM, dll)</option>
                                    <option value="Bayar Sewa Ruko/Outlet/dll">Bayar Sewa Ruko/Outlet/dll</option>

                                    <option value="Bayar Pemeliharaan (Servis, dll)">Bayar Pemeliharaan (Servis, dll)
                                    </option>
                                    <option value="Bayar Pajak">Bayar Pajak</option>
                                    <option value="Bayar Lain-lain">Bayar Lain-lain</option>

                                    <!-- Utang & Bunga -->
                                    <option value="Bayar Utang Bank">Bayar Utang Bank</option>
                                    <option value="Bayar Utang Usaha">Bayar Utang Usaha</option>
                                    <option value="Bayar Utang Lainnya">Bayar Utang Lainnya</option>
                                    <option value="Bayar Bunga Bank">Bayar Bunga Bank</option>

                                    <!-- Pembelian Aset / Barang -->
                                    <option value="Beli Peralatan Tunai">Beli Peralatan Tunai</option>
                                    <option value="Beli ATK Tunai">Beli ATK Tunai</option>
                                    <option value="Beli Tanah Tunai">Beli Tanah Tunai</option>
                                    <option value="Membuat/Beli Bangunan Tunai">Membuat/Beli Bangunan Tunai</option>

                                    <option value="Beli Kendaraan Tunai">Beli Kendaraan Tunai</option>

                                    <!-- Penjualan Aset -->
                                    <option value="Jual Tanah">Jual Tanah</option>
                                    <option value="Jual Bangunan">Jual Bangunan</option>
                                    <option value="Jual Kendaraan">Jual Kendaraan</option>

                                    <!-- Pendanaan & Pendapatan -->
                                    <option value="Pinjam Uang di Bank">Pinjam Uang di Bank</option>
                                    <option value="Pinjam Uang Lainnya">Pinjam Uang Lainnya</option>
                                    <option value="Pendapatan Bunga">Pendapatan Bunga</option>
                                    <option value="Pendapatan Lain-lain (Komisi/Hadiah)">Pendapatan Lain-lain
                                        (Komisi/Hadiah)</option>
                                    <option value="Setoran Pemilik">Setoran Pemilik</option>
                                    <option value="Pengambilan Pribadi">Pengambilan Pribadi</option>



                                    <option value="Manual">Manual</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Nominal</label>
                                <input type="text" class="form-control rupiah" id="trx_nominal"
                                    placeholder="Rp. xx,xxx,xxx">
                            </div>
                        </div>

                        <!-- Akun Debet/Kredit: hanya tampil untuk 'Manual' -->
                        <div id="rowManualAccounts" class="form-row" style="display:none;">
                            <div class="form-group col-md-6">
                                <label>Akun Debet</label>
                                <select id="akun_debet_id" class="form-control">
                                    <option value="" disabled selected>Pilih Akun Debet</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Akun Kredit</label>
                                <select id="akun_kredit_id" class="form-control">
                                    <option value="" disabled selected>Pilih Akun Kredit</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tanggal</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="trx_tanggal"
                                        placeholder="xx/xx/xxxx">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea id="keterangan" name="keterangan" class="form-control" rows="1" placeholder="Tulis keterangan..."></textarea>
                            </div>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary" id="btnSimpanTransaksi">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- .bb-panel --}}


</div>

@push('scripts')
<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content')
            }
        });

        const $tipe = $('#tipe_transaksi');
        const $deb = $('#akun_debet_id');
        const $kred = $('#akun_kredit_id');

        // helper: ubah string rupiah ke angka
        function toNumber(v) {
            return parseInt(String(v || '').replace(/[^\d-]/g, ''), 10) || 0;
        }

        // toggle field akun manual
        $tipe.on('change', function() {
            if ($(this).val() === 'Manual') {
                $('#rowManualAccounts').show();
            } else {
                $('#rowManualAccounts').hide();
                $deb.val('');
                $kred.val('');
            }
        });

        // tombol simpan
        $('#btnSimpanTransaksi').on('click', function(e) {
            e.preventDefault();

            const tipe = $tipe.val();
            const nominal = toNumber($('#trx_nominal').val());
            const tanggal = $('#trx_tanggal').val();
            const keterangan = $('#keterangan').val();
            const akunDebet = $deb.val() || null;
            const akunKredit = $kred.val() || null;

            if (!tipe) return (toastr?.error?.('Pilih tipe transaksi') || alert(
                'Pilih tipe transaksi'));
            if (!tanggal) return (toastr?.error?.('Tanggal wajib diisi') || alert(
                'Tanggal wajib diisi'));
            if (!nominal) return (toastr?.error?.('Nominal tidak valid') || alert(
                'Nominal tidak valid'));
            if (tipe === 'Manual') {
                if (!akunDebet || !akunKredit)
                    return (toastr?.error?.('Pilih akun debet & akun kredit') || alert(
                        'Pilih akun debet & akun kredit'));
                if (akunDebet === akunKredit)
                    return (toastr?.error?.('Akun debet & kredit tidak boleh sama') || alert(
                        'Akun debet & kredit tidak boleh sama'));
            }

            const $btn = $(this).prop('disabled', true).text('Menyimpan...');

            $.post('/buku_besar/storetransaksi', {
                    tipe,
                    nominal,
                    tanggal,
                    keterangan,
                    akun_debet_id: akunDebet,
                    akun_kredit_id: akunKredit,
                    _token: "{{ csrf_token() }}"
                })
                .done(function(res) {
                    if (res?.ok) {
                        toastr?.success?.('Transaksi berhasil disimpan') || alert(
                            'Transaksi berhasil disimpan');
                        $('#trx_nominal').val('');
                        $('#keterangan').val('');
                        if (tipe === 'Manual') {
                            $deb.val('').trigger('change');
                            $kred.val('').trigger('change');
                        }
                    } else {
                        const msg = res?.message || 'Gagal menyimpan transaksi';
                        toastr?.error?.(msg) || alert(msg);
                    }
                })
                .fail(function(xhr) {
                    const msg = xhr?.responseJSON?.message || 'Terjadi kesalahan server';
                    toastr?.error?.(msg) || alert(msg);
                })
                .always(function() {
                    $btn.prop('disabled', false).text('Simpan');
                });
        });
    });
</script>
@endpush

@endsection

