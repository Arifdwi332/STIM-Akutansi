@extends('templates.layout')

@section('breadcrumbs')
    {{-- breadcrumb kalau perlu --}}
@endsection

{{-- ============= CSS Inline agar langsung ter-load ============= --}}
<style>
    :root {
        --bb-primary: #1E5296;
        --bb-border: #E8ECEF;
    }

    /* wrapper halaman */
    .bb-wrap {
        max-width: 100% !important;
        /* supaya full */
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* ===== Summary cards ===== */
    .bb-stat {
        background: #fff;
        border: 1px solid var(--bb-border);
        border-radius: 8px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
    }

    .bb-stat .bb-ico {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 16px;
    }

    .bb-ico.prev {
        background: #2DA4A8;
    }

    .bb-ico.open {
        background: #2EAF5B;
    }

    .bb-ico.end {
        background: #F4B400;
    }

    .bb-stat .bb-title {
        font-weight: 600;
        font-size: .9rem;
        color: #334155;
        margin-bottom: 2px;
    }

    .bb-stat .bb-amount {
        font-weight: 700;
        font-size: .98rem;
        color: #111827;
    }

    /* ===== Panel putih ===== */
    .bb-panel {
        background: #fff;
        border: 1px solid var(--bb-border);
        border-radius: 8px;
    }

    .bb-head {
        background: #EEF2F6;
        border-bottom: 1px solid var(--bb-border);
        padding: .6rem .9rem;
        font-weight: 600;
        color: #334155;
    }

    .bb-body {
        padding: .9rem;
    }

    /* sejajarkan tinggi dua panel (desktop) */
    @media(min-width:992px) {
        .eq-row .col-lg-6>.bb-panel {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .eq-row .col-lg-6>.bb-panel .bb-body {
            flex: 1;
        }
    }

    /* ===== baris detail & nominal sejajar ===== */
    .bb-inline-row {
        display: flex;
        gap: 12px;
    }

    .bb-inline-row .left {
        flex: 0 0 58%;
    }

    .bb-inline-row .right {
        flex: 1;
    }

    .bb-input-with-btn {
        display: flex;
        gap: 8px;
    }

    .bb-input-with-btn .form-control {
        flex: 1;
    }

    /* ===== table box ===== */
    .bb-tablebox {
        background: #fff;
        border: 1px solid var(--bb-border);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
        padding: .6rem;
    }

    .bb-tablebar {
        display: flex;
        justify-content: flex-end;
        padding: 0 0 .4rem;
    }

    .bb-tablebar .form-control {
        max-width: 270px;
    }

    .bb-footline {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .4rem .2rem 0;
    }

    .table-sm td,
    .table-sm th {
        padding: .55rem .6rem;
        vertical-align: middle;
    }

    /* aksen umum */
    .btn-primary {
        background: #1E5296;
        border-color: #1E5296;
    }

    .btn-primary:hover {
        filter: brightness(.95);
    }

    .form-control:focus,
    .custom-select:focus {
        border-color: #1E5296;
        box-shadow: 0 0 0 .2rem rgba(30, 82, 150, .15);
    }

    /* opsional: rapikan tinggi kontrol di baris sejajar */
    .bb-inline-row .left .form-control,
    .bb-inline-row .right .form-control {
        height: calc(2.25rem + 2px);
    }

    .bb-pair+.bb-pair {
        margin-top: .25rem;
    }
</style>

@section('content')
    <div class="container-fluid bb-wrap">
        <h4 class="mb-3">Buku Besar</h4>

        {{-- ===== Ringkasan ===== --}}
        {{-- <div class="row g-3 mb-2">
            <div class="col-md-4">
                <div class="bb-stat">
                    <div class="bb-ico prev"><i class="fas fa-wallet"></i></div>
                    <div>
                        <div class="bb-title">Saldo Periode Sebelumnya</div>
                        <div class="bb-amount">Rp. 50.000.000</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bb-stat">
                    <div class="bb-ico open"><i class="fas fa-piggy-bank"></i></div>
                    <div>
                        <div class="bb-title">Saldo Awal Periode</div>
                        <div class="bb-amount">Rp. 100.000.000</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bb-stat">
                    <div class="bb-ico end"><i class="fas fa-cash-register"></i></div>
                    <div>
                        <div class="bb-title">Saldo Akhir</div>
                        <div class="bb-amount">Rp. 35.000.000</div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row g-3 eq-row">
            {{-- Input Saldo Awal --}}
            <div class="col-lg-12">
                <div class="bb-panel">
                    <div class="bb-head d-flex justify-content-between align-items-center">
                        <span>Input Saldo Awal</span>
                        <div>
                            @include('buku_besar.list_akun_modal')
                            <button class="btn btn-light border" data-toggle="modal" data-target="#modalListAkun">Lihat
                                Daftar Akun</button>

                            <button class="btn btn-success text-white" data-toggle="modal" data-target="#modalAkunBaru"
                                hidden>
                                Daftar Akun
                            </button>
                            @include('buku_besar.daftar_subakun_modal')
                            <button class="btn btn-success text-white" data-toggle="modal" data-target="#modalSubAkunBaru"
                                hidden>
                                Daftar Sub Akun
                            </button>
                            <button class="btn btn-primary text-white" data-toggle="modal" data-target="#modalPemasokBaru">
                                Tambah Persediaan
                            </button>
                            <button class="btn btn-danger text-white" data-toggle="modal" data-target="#modalResetData">
                                Reset Data
                            </button>
                            <button class="btn btn-danger text-white" data-toggle="modal"
                                data-target="#modalResetTransaksi">
                                Reset Transaksi
                            </button>
                            @include('buku_besar.daftar_akun_modal')
                        </div>
                    </div>

                    <div class="bb-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="kode_akun_id">Kode Akun</label>
                                <select id="kode_akun_id" name="mst_akun_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Kode Akun</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nama_akun_input">Nama Akun</label>
                                <input type="text" id="nama_akun_input" class="form-control" placeholder="">
                            </div>
                        </div>

                        <!-- ===== Pasangan Sub Akun ↔ Nominal (sejajar) ===== -->
                        <div class="form-row">
                            <div class="col-12">
                                <label class="mb-2 d-block">Tanggal & Nominal</label>

                                <!-- container semua pasangan -->
                                <div id="pair-wrap">
                                    <!-- pasangan pertama -->
                                    <div class="form-row align-items-start bb-pair">
                                        <div class="col-md-6 mb-2">
                                            <select class="form-control sub-akun-select" id="sub_akun_id"
                                                name="sub_akun_id[]" hidden>
                                                <option value="" hidden selected>Pilih Sub Akun</option>
                                            </select>
                                            <input type="date" class="form-control bb-tanggal" name="tanggal[]">
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control bb-nominal" name="nominal[]"
                                                    placeholder="Rp">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-danger remove-pair">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button id="btnAddDetail" type="button" class="btn btn-outline-primary btn-sm mt-1" hidden>
                                    Tambah Detail
                                </button>
                            </div>
                        </div>


                        <div class="form-group mt-3">
                            <label>Total Saldo</label>
                            <input type="text" class="form-control" id="bb-total" value="Rp. 0" readonly>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" id="btnSimpanSaldoAwal">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transaksi --}}


        </div>



    </div>
@endsection

{{-- ===== Modal Akun Baru ===== --}}
<div class="modal fade" id="modalAkunBaru" tabindex="-1" role="dialog" aria-labelledby="modalAkunBaruLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">
        <form id="formAkunBaru" class="w-100">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title mb-0" id="modalAkunBaruLabel">Daftar Akun Baru</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 1.25rem 1.25rem .75rem;">
                    <div class="form-group mb-3">
                        <label for="kode_akun_baru" class="mb-1" style="font-weight:600;">Kode Akun</label>
                        <input type="text" class="form-control" id="kode_akun_baru" name="kode_akun"
                            placeholder="1140" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_akun_baru" class="mb-1" style="font-weight:600;">Nama Akun</label>
                        <input type="text" class="form-control" id="nama_akun_baru" name="nama_akun"
                            placeholder="Nama Akun" required>
                    </div>
                    <div class="form-group mb-1">
                        <label for="kategori_akun_baru" class="mb-1" style="font-weight:600;">Kategori</label>
                        <select id="kategori_akun_baru" name="kategori_akun" class="form-control" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            <option value="Aset Lancar">Aset Lancar</option>
                            <option value="Aset Tetap">Aset Tetap</option>
                            <option value="Liabilitas Jangka Pendek">Liabilitas Jangka Pendek</option>
                            <option value="Liabilitas Jangka Panjang">Liabilitas Jangka Panjang</option>
                            <option value="Ekuitas">Ekuitas</option>
                            <option value="Pendapatan">Pendapatan</option>
                            <option value="Harga Pokok Penjualan">Harga Pokok Penjualan</option>
                            <option value="Beban Penjualan">Beban Penjualan</option>
                            <option value="Beban Umum & Administrasi">Beban Umum & Administrasi</option>
                            <option value="Beban Lain-lain">Beban Lain-lain</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Tutup</button>
                    <button type="submit" id="btnSimpanAkunBaru" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Persediaan -->
<div class="modal fade" id="modalPemasokBaru" tabindex="-1" role="dialog" aria-labelledby="modalPemasokBaruLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">
        <form id="formPemasokBaru" class="w-100">
            @csrf
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h6 class="modal-title mb-0" id="modalPemasokBaruLabel">Daftar Pemasok Baru</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body" style="padding: 1.25rem 1.25rem .75rem;">
                    <div class="form-group">

                        <input type="hidden" class="form-control" id="kode_pemasok" name="kode_pemasok" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1" for="nama_pemasok" style="font-weight:600;">Nama Pemasok</label>
                        <input type="text" class="form-control" id="nama_pemasok" name="nama_pemasok"
                            placeholder="Nama Pemasok" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1" for="nama_barang" style="font-weight:600;">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                            placeholder="Nama Barang" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1" for="harga_satuan" style="font-weight:600;">Harga Satuan</label>
                        <input type="text" class="form-control rupiah" id="harga_satuan" name="harga_satuan"
                            placeholder="Harga Satuan" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1" for="satuan_ukur" style="font-weight:600;">Satuan Ukur</label>
                        <input type="text" class="form-control" id="satuan_ukur" name="satuan_ukur"
                            placeholder="Satuan Ukur" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1" for="harga_jual " style="font-weight:600;">Harga Jual</label>
                        <input type="text" class="form-control rupiah" id="harga_jual" name="harga_jual"
                            placeholder="Harga Jual" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1" for="stok " style="font-weight:600;">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" placeholder="Stok"
                            required>

                    </div>
                    <div class="form-group mb-3">
                        <label class="mb-1" for="alamat_pemasok" style="font-weight:600;">Alamat</label>
                        <textarea class="form-control" id="alamat_pemasok" name="alamat" rows="3" placeholder="Alamat lengkap"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="mb-1" for="no_hp_pemasok" style="font-weight:600;">No. HP</label>
                        <input type="text" class="form-control" id="no_hp_pemasok" name="no_hp"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="form-group mb-3">
                        <label class="mb-1" for="email_pemasok" style="font-weight:600;">Email</label>
                        <input type="email" class="form-control" id="email_pemasok" name="email"
                            placeholder="email@domain.com">
                    </div>

                    <div class="form-group mb-1">
                        <label class="mb-1" for="npwp_pemasok" style="font-weight:600;">NPWP</label>
                        <input type="text" class="form-control" id="npwp_pemasok" name="npwp"
                            placeholder="xx.xxx.xxx.x-xxx.xxx">
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPemasokBaru">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalResetData" tabindex="-1" role="dialog" aria-labelledby="modalResetDataLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="{{ route('buku-besar.reset-data') }}" class="modal-content" id="formResetData">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalResetDataLabel">Reset Data?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning mb-0">
                    Tindakan ini akan <u>menghapus semua isi</u> tabel:
                    <code>dat_barang, dat_buku_besar, dat_detail_jurnal, dat_detail_transaksi, dat_header_jurnal,
                        dat_pelanggan, dat_pemasok, dat_transaksi, dat_utang, dat_piutang</code><br>
                    dan <u>mereset</u> <code>saldo_awal</code> & <code>saldo_berjalan</code> di <code>mst_akun</code>
                    menjadi <b>0</b>.
                    <br><br>Yakin lanjut?
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal">Tidak</button>
                <button type="submit" class="btn btn-danger" id="btnEksekusiReset">Ya, Reset</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalResetTransaksi" tabindex="-1" role="dialog"
    aria-labelledby="modalResetTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="{{ route('buku-besar.reset-transaksi') }}" class="modal-content"
            id="formResetTransaksi">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalResetTransaksiLabel">Reset Transaksi?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning mb-0">
                    Tindakan ini akan <u>menghapus semua isi</u> tabel:
                    <code> dat_buku_besar, dat_detail_jurnal, dat_detail_transaksi, dat_header_jurnal,
                        dat_transaksi, dat_utang, dat_piutang</code><br>
                    dan <u>mereset</u> <code>saldo_awal</code> & <code>saldo_berjalan</code> di <code>mst_akun</code>
                    menjadi <b>0</b>.
                    <br><br>Yakin lanjut?
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal">Tidak</button>
                <button type="submit" class="btn btn-danger" id="btnEksekusiReset">Ya, Reset</button>
            </div>
        </form>
    </div>
</div>


{{-- Flash message --}}
@if (session('status'))
    <div class="alert alert-success mt-3">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger mt-3">
        {{ $errors->first() }}
    </div>
@endif

@push('scripts')
    <script>
        (function() {
            const $kode = $('#kode_akun_id');
            const $nama = $('#nama_akun_input');
            const $pairs = $('#pair-wrap');
            const $btnAdd = $('#btnAddDetail');

            // format rupiah sederhana
            const parseR = v => !v ? 0 : parseInt((v + '').replace(/[^\d-]/g, '') || '0', 10);
            const fmtR = x => `Rp. ${(x||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')}`;

            let subOptionsHtml = '<option value="" disabled selected>Pilih Sub Akun</option>';

            function recompute() {
                let sum = 0;
                $pairs.find('.bb-nominal').each(function() {
                    sum += parseR(this.value);
                });
                $('#bb-total').val(fmtR(sum));
            }

            function buildPair() {
                return $(`
                <div class="form-row align-items-start bb-pair">
                <div class="col-md-6 mb-2">
                    <select class="form-control sub-akun-select" name="sub_akun_id[]">
                    ${subOptionsHtml}
                    </select>
                    <input type="date" class="form-control bb-tanggal mt-2" name="tanggal[]">
                </div>
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                    <input type="text" class="form-control bb-nominal" name="nominal[]" placeholder="Rp">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-pair">
                        <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    </div>
                </div>
                </div>
            `);
            }



            $btnAdd.on('click', function() {
                const $row = buildPair();
                const disabled = $pairs.find('.sub-akun-select').first().prop('disabled');
                $row.find('.sub-akun-select').prop('disabled', disabled);
                $pairs.append($row);
            });

            $pairs.on('click', '.remove-pair', function() {
                $(this).closest('.bb-pair').remove();
                recompute();
            });

            $pairs.on('input', '.bb-nominal', recompute);

            $.get('/buku_besar/mst_akun', function(res) {
                if (!res || !res.ok) return;
                res.data.forEach(it => {
                    $('#kode_akun_id').append(
                        $('<option>', {
                            value: it.id,
                            text: `${it.kode_akun} - ${it.nama_akun}`
                        })
                        .attr('data-nama', it.nama_akun)
                    );
                });
            });

            $('#kode_akun_id').on('change', function() {
                const nama = $(this).find('option:selected').data('nama') || '';
                $nama.val(nama);

                $pairs.find('.sub-akun-select').prop('disabled', true)
                    .html('<option value="" disabled selected>Memuat...</option>');

                $.get('/buku_besar/sub_akun_list', {
                        mst_akun_id: $(this).val()
                    })
                    .done(function(r) {
                        if (r && r.ok && r.data.length) {
                            subOptionsHtml = '<option value="" disabled selected>Pilih Sub Akun</option>';
                            r.data.forEach(s => {
                                subOptionsHtml +=
                                    `<option value="${s.id}">${s.kode_sub} - ${s.nama_sub}</option>`;
                            });
                            // set ke SEMUA dropdown sub akun dan enable
                            $pairs.find('.sub-akun-select').each(function() {
                                $(this).html(subOptionsHtml).prop('disabled', false);
                            });
                        } else {
                            subOptionsHtml =
                                '<option value="" disabled selected>Tidak ada sub akun</option>';
                            $pairs.find('.sub-akun-select').html(subOptionsHtml).prop('disabled', true);
                        }
                    })
                    .fail(function() {
                        subOptionsHtml =
                            '<option value="" disabled selected>Gagal memuat sub akun</option>';
                        $pairs.find('.sub-akun-select').html(subOptionsHtml).prop('disabled', true);
                    });
            });

            recompute();
        })();
    </script>


    <script>
        $(function() {
            $('#btnAkunBaruOpen').on('click', function() {
                $('#formAkunBaru')[0].reset();
                $('#modalAkunBaru').modal('show');
            });

            $('#formAkunBaru').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSimpanAkunBaru').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                        method: 'POST',
                        url: "{{ route('mst_akun.store') }}",
                        data: $(this).serialize(),
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (res && res.ok) {
                            const $select = $('select.form-control').first();
                            if ($select.length) {
                                const text = res.data.nama_akun + ' (' + res.data.kode_akun + ')';
                                $select.append(new Option(text, res.data.id, true, true));
                                $select.trigger('change');
                            }
                            $('#modalAkunBaru').modal('hide');
                            (window.toastr && toastr.success) ? toastr.success(
                                'Akun berhasil disimpan'): alert('Akun berhasil disimpan');
                        } else {
                            const msg = (res && res.message) ? res.message : 'Gagal menyimpan data';
                            (window.toastr && toastr.error) ? toastr.error(msg): alert(msg);
                        }
                    })
                    .fail(function(xhr) {
                        let msg = 'Gagal menyimpan data';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON
                            .message;
                        (window.toastr && toastr.error) ? toastr.error(msg): alert(msg);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text('Simpan');
                    });
            });
        });
    </script>
    <script>
        (function() {
            $('#btnSimpanSaldoAwal').on('click', function(e) {
                e.preventDefault();

                const payload = {
                    mst_akun_id: $('#kode_akun_id').val(),
                    'sub_akun_id[]': [],
                    'nominal[]': [],
                    'tanggal[]': []
                };

                $('#pair-wrap .bb-pair').each(function() {
                    const sid = $(this).find('.sub-akun-select').val();
                    const val = $(this).find('.bb-nominal').val();
                    const tgl = $(this).find('.bb-tanggal').val();
                    payload['sub_akun_id[]'].push(sid);
                    payload['nominal[]'].push(val);
                    payload['tanggal[]'].push(tgl);
                });

                $.ajax({
                        method: 'POST',
                        url: "{{ route('buku_besar.saldo_awal.store') }}",
                        data: {
                            mst_akun_id: payload.mst_akun_id,
                            'sub_akun_id': payload['sub_akun_id[]'],
                            'nominal': payload['nominal[]'],
                            tanggal: payload['tanggal[]'],
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (res && res.ok) {
                            if (typeof res.total !== 'undefined') {
                                const fmt = x => 'Rp. ' + (x || 0).toString().replace(
                                    /\B(?=(\d{3})+(?!\d))/g, '.');
                                $('#bb-total').val(fmt(res.total));
                            }
                            (window.toastr && toastr.success) ? toastr.success('Saldo awal tersimpan'):
                                alert('Saldo awal tersimpan');
                        } else {
                            const msg = (res && res.message) ? res.message : 'Gagal menyimpan saldo awal';
                            (window.toastr && toastr.error) ? toastr.error(msg): alert(msg);
                        }
                    })
                    .fail(function(xhr) {
                        let msg = 'Gagal menyimpan saldo awal';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        (window.toastr && toastr.error) ? toastr.error(msg): alert(msg);
                    });
            });
        })();

        (function() {
            const $tipe = $('#tipe_transaksi');
            const $rowManual = $('#rowManualAccounts');
            const $deb = $('#akun_debet_id');
            const $kred = $('#akun_kredit_id');

            function loadAkunOptions() {
                if ($deb.data('loaded') === true) return;
                $.get('/buku_besar/mst_akun', function(res) {
                    if (!res || !res.ok) return;
                    const opts = ['<option value="" disabled selected>Pilih Akun</option>'];
                    res.data.forEach(a => opts.push(
                        `<option value="${a.id}">${a.kode_akun} - ${a.nama_akun}</option>`));
                    $deb.html(opts.join(''));
                    $kred.html(opts.join(''));
                    $deb.data('loaded', true);
                });
            }

            function applyTipe() {
                const isManual = $tipe.val() === 'Manual';
                if (isManual) {
                    $rowManual.show();
                    $deb.prop('required', true);
                    $kred.prop('required', true);
                    loadAkunOptions();
                } else {
                    $rowManual.hide();
                    $deb.prop('required', false).val('');
                    $kred.prop('required', false).val('');
                }
            }

            applyTipe();
            $tipe.on('change', applyTipe);

            $('#btnSimpanTransaksi').on('click', function(e) {
                e.preventDefault();

                const toNumber = v => parseInt(String(v || '').replace(/[^\d-]/g, ''), 10) || 0;

                const tipe = $tipe.val();
                const nominal = toNumber($('#trx_nominal').val());
                const tanggal = $('#trx_tanggal').val();
                const keterangan = $('#keterangan').val();
                const akunDebet = $deb.val() || null;
                const akunKredit = $kred.val() || null;

                if (!tipe) return (toastr?.error?.('Pilih tipe transaksi') || alert('Pilih tipe transaksi'));
                if (!tanggal) return (toastr?.error?.('Tanggal wajib diisi') || alert('Tanggal wajib diisi'));
                if (!nominal) return (toastr?.error?.('Nominal tidak valid') || alert('Nominal tidak valid'));
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
        })();
    </script>
    <script>
        (function() {
            const rp = n => {
                n = Number(n || 0);
                return 'Rp. ' + n.toLocaleString('id-ID');
            };

            // ====== JURNAL UMUM ======
            function loadJurnal(page = 1) {
                const q = $('#searchJurnal').val() || '';
                $.getJSON('/buku_besar/get_jurnal', {
                    search: q,
                    page,
                    per_page: 20
                }, function(res) {
                    const $tb = $('#tblJurnal tbody');
                    if ($tb.length === 0) $('#tblJurnal').append('<tbody></tbody>');
                    const $body = $('#tblJurnal tbody').empty();

                    (res.data || []).forEach(r => {
                        $body.append(
                            `<tr>
            <td>${r.tanggal ?? ''}</td>
            <td>${r.keterangan ?? ''}</td>
            <td>${r.nama_akun ?? ''}</td>
            <td class="text-success">${rp(r.debet)}</td>
            <td class="text-danger">${rp(r.kredit)}</td>
          </tr>`
                        );
                    });
                    $('#pgJurnal').text(`Total: ${res.total} | Hal: ${res.page}`);
                });
            }
            $('#searchJurnal').on('input', () => loadJurnal(1));
            loadJurnal();

            // ====== BUKU BESAR ======
            function loadBuku(page = 1) {
                const q = $('#searchBuku').val() || '';
                $.getJSON('/buku_besar/get_buku_besar', {
                    search: q,
                    page,
                    per_page: 20
                }, function(res) {
                    if ($('#tblBuku tbody').length === 0) $('#tblBuku').append('<tbody></tbody>');
                    const $body = $('#tblBuku tbody').empty();

                    (res.data || []).forEach(r => {
                        $body.append(
                            `<tr>
            <td>${r.nama_akun ?? ''}</td>
            <td>${r.tanggal ?? ''}</td>
            <td class="text-success">${rp(r.debet)}</td>
            <td class="text-danger">${rp(r.kredit)}</td>
            <td>${rp(r.saldo)}</td>
          
          </tr>`
                        );
                    });
                    $('#pgBuku').text(`Total: ${res.total} | Hal: ${res.page}`);
                });
            }
            $('#searchBuku').on('input', () => loadBuku(1));
            loadBuku();
        })();
    </script>
    <script>
        $('#formPemasokBaru').off('submit').on('submit', function(e) {
            e.preventDefault();

            $(this).find('.rupiah').each(function() {
                $(this).val(parseRupiah($(this).val()));
            });

            const $btn = $('#btnSimpanPemasokBaru').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                    method: 'POST',
                    url: "{{ route('storePersediaan') }}",
                    data: $(this).serialize(),
                    dataType: 'json'
                })
                .done(function(res) {
                    console.log(res);
                    if (res && res.ok === true) {
                        const d = res.data || {};
                        d.nama = d.nama || d.nama_pemasok;

                        if (typeof appendToSelect === 'function') {
                            appendToSelect(d, '#pemasok_id, #party_id');
                        }

                        $('#modalPemasokBaru').modal('hide');
                        $('#formPemasokBaru')[0].reset();
                        if (window.toastr) toastr.success('Pemasok berhasil disimpan');
                        else alert('Pemasok berhasil disimpan');
                    } else {
                        const msg = (res && (res.message || res.error)) || 'Gagal menyimpan data';
                        if (window.toastr) toastr.error(msg);
                        else alert(msg);
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Gagal menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (window.toastr) toastr.error(msg);
                    else alert(msg);
                })
                .always(function() {
                    $btn.prop('disabled', false).text('Simpan');
                });

            return false;
        });
    </script>
    <script>
        // cegah double-submit
        (function() {
            const form = document.getElementById('formResetData');
            const btn = document.getElementById('btnEksekusiReset');
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.textContent = 'Memproses...';
            });
        })();
    </script>
@endpush
