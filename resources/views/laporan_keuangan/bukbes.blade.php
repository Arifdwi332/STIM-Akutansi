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
    {{-- ===== Tabel bawah ===== --}}
    <div class="row g-3 mt-1">


        <div class="col-lg-12">
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
    <!-- Modal Detail Jurnal -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jurnal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Nama</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="tblDetailBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            const rp = n => 'Rp. ' + Number(n || 0).toLocaleString('id-ID');

            function loadJurnalDetail(akunId = null, akunName = '') {
                $('#tblDetailBody').html(
                    '<tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>');
                const payload = {
                    per_page: 100
                };
                if (akunId) payload.akun_id = akunId;
                else if (akunName) payload.akun_name = akunName;

                $.getJSON('/buku_besar/get_jurnal', payload, function(res) {
                    const $body = $('#tblDetailBody').empty();
                    if (!res.data || res.data.length === 0) {
                        $body.html(
                            '<tr><td colspan="6" class="text-center text-muted">Tidak ada data jurnal</td></tr>'
                        );
                        return;
                    }
                    res.data.forEach(r => {
                        $body.append(`
                    <tr>
                        <td>${r.tanggal ?? ''}</td>
                        <td>${r.keterangan ?? ''}</td>
                        <td>${r.nama_akun ?? ''}</td>
                        <td class="text-success">${rp(r.debet)}</td>
                        <td class="text-danger">${rp(r.kredit)}</td>
                        <td>${rp(r.saldo)}</td>
                    </tr>
                `);
                    });
                });
            }

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
                        // [changes] simpan id & name ke data-*
                        const row = $(`
                    <tr class="clickable-row" style="cursor:pointer"
                        data-akun-id="${r.id_akun ?? ''}"
                        data-akun-name="${r.nama_akun ?? ''}">
                        <td>${r.nama_akun ?? ''}</td>
                        <td>${r.tanggal ?? ''}</td>
                        <td class="text-success">${rp(r.debet)}</td>
                        <td class="text-danger">${rp(r.kredit)}</td>
                        <td>${rp(r.saldo)}</td>
                    </tr>
                `);
                        $body.append(row);
                    });

                    // [changes] klik baris: pakai akun_id bila ada, fallback ke nama
                    $('.clickable-row').off('click').on('click', function() {
                        const akunId = $(this).data('akun-id') || null;
                        const akunName = $(this).data('akun-name') || '';
                        $('#modalDetail .modal-title').text(`Detail - ${akunName || 'Akun'}`);
                        loadJurnalDetail(akunId, akunName);
                        $('#modalDetail').modal('show');
                    });

                    $('#pgBuku').text(`Total: ${res.total} | Hal: ${res.page}`);
                });
            }

            $('#searchBuku').on('input', () => loadBuku(1));
            loadBuku();
        })();
    </script>
@endpush
