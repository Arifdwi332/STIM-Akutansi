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
        <div class="row g-3 mb-2">
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
        </div>

        <div class="row g-3 eq-row">
            {{-- Input Saldo Awal --}}
            <div class="col-lg-6">
                <div class="bb-panel">
                    <div class="bb-head d-flex justify-content-between align-items-center">
                        <span>Input Saldo Awal</span>
                        <div>
                            @include('buku_besar.list_akun_modal')
                            <button class="btn btn-light border" data-toggle="modal" data-target="#modalListAkun">Lihat
                                Daftar Akun</button>

                            <button class="btn btn-success text-white" data-toggle="modal" data-target="#modalAkunBaru">
                                Daftar Akun
                            </button>
                            @include('buku_besar.daftar_subakun_modal')
                            <button class="btn btn-success text-white" data-toggle="modal" data-target="#modalSubAkunBaru">
                                Daftar Sub Akun
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
                                <label class="mb-2 d-block">Sub Akun & Nominal</label>

                                <!-- container semua pasangan -->
                                <div id="pair-wrap">
                                    <!-- pasangan pertama -->
                                    <div class="form-row align-items-start bb-pair">
                                        <div class="col-md-6 mb-2">
                                            <select class="form-control sub-akun-select" id="sub_akun_id"
                                                name="sub_akun_id[]" disabled>
                                                <option value="" disabled selected>Pilih Sub Akun</option>
                                            </select>
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

                                <button id="btnAddDetail" type="button" class="btn btn-outline-primary btn-sm mt-1">
                                    Tambah Detail
                                </button>
                            </div>
                        </div>


                        <div class="form-group mt-3">
                            <label>Total Saldo</label>
                            <input type="text" class="form-control" id="bb-total" value="Rp. 0" readonly>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transaksi --}}
            <div class="col-lg-6">
                <div class="bb-panel">
                    <div class="bb-head">Transaksi</div>
                    <div class="bb-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tipe Transaksi</label>
                                <select class="custom-select">
                                    <option>Pembelian</option>
                                    <option>Penjualan</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nominal</label>
                                <input type="text" class="form-control" placeholder="Rp. xx,xxx,xxx">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Tipe Bayar</label>
                                <select class="custom-select">
                                    <option>Tunai</option>
                                    <option>Non Tunai</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tanggal</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" placeholder="xx/xx/xxxx">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea id="keterangan" name="keterangan" class="form-control" rows="1" placeholder="Tulis keterangan..."></textarea>
                            </div>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Tabel bawah ===== --}}
        <div class="row g-3 mt-1">
            <div class="col-lg-6">
                <div class="bb-panel">
                    <div class="bb-head">Jurnal Umum</div>
                    <div class="bb-body">
                        <div class="bb-tablebox">
                            <div class="bb-tablebar">
                                <input id="searchJurnal" type="text" class="form-control" placeholder="Search">
                            </div>
                            <div class="table-responsive">
                                <table id="tblJurnal" class="table table-sm table-hover w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                            <th>Nama Akun</th>
                                            <th class="text-success">Debet</th>
                                            <th class="text-danger">Kredit</th>
                                            <th>Tipe</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="bb-footline">
                                <small class="text-muted">Jurnal periode berjalan</small>
                                <div id="pgJurnal"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="bb-panel">
                    <div class="bb-head">Buku Besar</div>
                    <div class="bb-body">
                        <div class="bb-tablebox">
                            <div class="bb-tablebar">
                                <input id="searchBuku" type="text" class="form-control" placeholder="Search">
                            </div>
                            <div class="table-responsive">
                                <table id="tblBuku" class="table table-sm table-hover w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nama Akun</th>
                                            <th>Tanggal</th>
                                            <th class="text-success">Debet</th>
                                            <th class="text-danger">Kredit</th>
                                            <th>Saldo</th>
                                            <th>Tipe</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="bb-footline">
                                <small class="text-muted">Ringkasan saldo akun</small>
                                <div id="pgBuku"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

{{-- ===== JS Dinamis: pasangan Sub Akun <-> Nominal ===== --}}
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


            // Tambah pasangan
            $btnAdd.on('click', function() {
                const $row = buildPair();
                const disabled = $pairs.find('.sub-akun-select').first().prop('disabled');
                $row.find('.sub-akun-select').prop('disabled', disabled);
                $pairs.append($row);
            });

            // Hapus 1 pasangan
            $pairs.on('click', '.remove-pair', function() {
                $(this).closest('.bb-pair').remove();
                recompute();
            });

            // Hitung total saat nominal diketik
            $pairs.on('input', '.bb-nominal', recompute);

            // ===== Load master akun (Kode Akun) =====
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

            // ===== Saat Kode Akun dipilih: set nama & muat sub akun =====
            $('#kode_akun_id').on('change', function() {
                const nama = $(this).find('option:selected').data('nama') || '';
                $nama.val(nama);

                // tampilkan loading di semua dropdown sub
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

            // init total
            recompute();
        })();
    </script>


    <script>
        $(function() {
            // Buka modal akun baru (jika ada tombol pembuka di tempat lain)
            $('#btnAkunBaruOpen').on('click', function() {
                $('#formAkunBaru')[0].reset();
                $('#modalAkunBaru').modal('show');
            });

            // Submit akun baru
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
                            // inject ke select pertama (sesuaikan target select spesifik bila perlu)
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
            // submit saldo awal
            $(document).on('click', '.bb-panel .btn.btn-primary:contains("Simpan")', function(e) {
                e.preventDefault();

                // serialize form area Input Saldo Awal saja:
                const payload = {
                    mst_akun_id: $('#kode_akun_id').val(),
                    'sub_akun_id[]': [],
                    'nominal[]': []
                };

                // ambil semua pair
                $('#pair-wrap .bb-pair').each(function() {
                    const sid = $(this).find('.sub-akun-select').val();
                    const val = $(this).find('.bb-nominal').val();
                    // tetap kirim walau kosong; backend akan filter
                    payload['sub_akun_id[]'].push(sid);
                    payload['nominal[]'].push(val);
                });

                $.ajax({
                        method: 'POST',
                        url: "{{ route('buku_besar.saldo_awal.store') }}",
                        data: {
                            mst_akun_id: payload.mst_akun_id,
                            'sub_akun_id': payload['sub_akun_id[]'],
                            'nominal': payload['nominal[]'],
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (res && res.ok) {
                            // update total UI bila perlu
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
    </script>
@endpush
