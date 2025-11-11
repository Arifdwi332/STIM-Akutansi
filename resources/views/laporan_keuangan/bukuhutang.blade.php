{{-- resources/views/buku_hutang/index.blade.php --}}
@extends('templates.layout')

@section('breadcrumbs')
@endsection

@section('content')
    <style>
        :root {
            --bb-border: #E8ECEF;
            --bb-primary: #1E5296;
            --headpad: 14px;
            --rowpad: 12px;
        }

        .bb-wrap {
            max-width: 100% !important;
            padding-left: 1rem;
            padding-right: 1rem;
        }

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

        .btn-group .btn.active {
            pointer-events: none;
        }

        .bb-badge {
            display: inline-block;
            padding: .15rem .5rem;
            border-radius: 999px;
            font-size: .75rem;
        }

        .bb-badge.red {
            background: #FEE2E2;
            color: #991B1B;
        }

        .bb-badge.green {
            background: #DCFCE7;
            color: #166534;
        }

        #tblKeu thead th {
            padding-top: var(--headpad) !important;
            padding-bottom: var(--headpad) !important;
            vertical-align: middle;
        }

        #tblKeu tbody td {
            padding-top: var(--rowpad) !important;
            padding-bottom: var(--rowpad) !important;
            vertical-align: middle;
        }
    </style>

    <div class="bb-wrap">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 id="pageTitle" class="mb-0">Buku Utang</h5>
            <div class="btn-group">
                <button id="btnUtang" class="btn btn-primary active">Buku Utang</button>
                <button id="btnPiutang" class="btn btn-outline-secondary">Buku Piutang</button>
            </div>
        </div>

        {{-- FILTER (dipakai untuk kedua mode; yang berubah hanya tabelnya) --}}
        <div class="bb-panel mb-3">
            <div class="bb-head">Filter</div>
            <div class="bb-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="small mb-1">Cari (Nama/No. Transaksi/Kode)</label>
                        <input id="q" type="text" class="form-control" placeholder="Ketik & Enter">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label id="labelContact" class="small mb-1">Nama Pemasok</label>
                        <select id="contactSelect" class="custom-select">
                            <option value="">Semua</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="small mb-1">Status</label>
                        <select id="status" class="custom-select">
                            <option value="">Semua</option>
                            <option value="0">Belum Lunas</option>
                            <option value="1">Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small mb-1">Dari Tanggal</label>
                        <input id="date_from" type="date" class="form-control">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small mb-1">Sampai Tanggal</label>
                        <input id="date_to" type="date" class="form-control">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button id="btnFilter" class="btn btn-primary">Terapkan</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL BERSAMA (JS mengganti header + rows) --}}
        <div class="bb-panel">
            <div class="bb-head">Daftar</div>
            <div class="bb-body">
                <div class="table-responsive">
                    <table id="tblKeu" class="table table-sm">
                        <thead class="thead-light">
                            <tr id="theadRow"></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small id="pageInfo" class="text-muted"></small>
                    <div>
                        <button id="btnPrev" class="btn btn-light border mr-2" disabled>← Sebelumnya</button>
                        <button id="btnNext" class="btn btn-light border" disabled>Berikutnya →</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            // ====== STATE ======
            let mode = 'utang'; // 'utang' | 'piutang'
            let page = 1,
                perPage = 20,
                totalPages = 1;

            // ====== UTIL ======
            const toNumber = v => Number(v || 0);
            const rp = n => 'Rp. ' + toNumber(n).toLocaleString('id-ID');
            const statusBadge = v => (Number(v || 0) === 1) ?
                '<span class="bb-badge green">Lunas</span>' :
                '<span class="bb-badge red">Belum Lunas</span>';

            // ====== MODE DEFINITIONS ======
            const MODES = {
                utang: {
                    title: 'Buku Utang',
                    endpoint: '/buku_hutang/data',
                    headers: [{
                            text: 'Tanggal',
                            width: '100px'
                        },
                        {
                            text: 'Kode Pemasok',
                            width: '110px'
                        },
                        {
                            text: 'Nama Pemasok',
                            width: '220px'
                        },
                        {
                            text: 'No. Transaksi'
                        },
                        {
                            text: 'Nominal',
                            width: '140px',
                            align: 'right'
                        },
                        {
                            text: 'Status',
                            width: '120px'
                        },
                        {
                            text: 'ID',
                            width: '100px'
                        },
                    ],
                    row: r => `
        <td>${r.tanggal ?? ''}</td>
        <td>${r.kode_pemasok ?? ''}</td>
        <td>${r.nama_pemasok || '-'}</td>
        <td>${r.no_transaksi ?? ''}</td>
        <td class="text-right">${rp(r.nominal)}</td>
        <td>${statusBadge(r.status)}</td>
        <td>${r.id_utang}</td>
      `,
                    addFilters: (params) => {
                        const val = document.getElementById('contactSelect').value; // kode_pemasok
                        if (val !== '') params.append('supplier', val);
                    }
                },
                piutang: {
                    title: 'Buku Piutang',
                    endpoint: '/buku_piutang/datapiutang',
                    headers: [{
                            text: 'Tanggal',
                            width: '100px'
                        },
                        {
                            text: 'ID Pelanggan',
                            width: '110px'
                        },
                        {
                            text: 'Nama Pelanggan',
                            width: '220px'
                        },
                        {
                            text: 'No. Transaksi'
                        },
                        {
                            text: 'Nominal',
                            width: '140px',
                            align: 'right'
                        },
                        {
                            text: 'Status',
                            width: '120px'
                        },
                        {
                            text: 'ID',
                            width: '100px'
                        },
                    ],
                    row: r => `
        <td>${r.tanggal ?? ''}</td>
        <td>${r.id_pelanggan ?? ''}</td>
        <td>${r.nama_pelanggan || '-'}</td>
        <td>${r.no_transaksi ?? ''}</td>
        <td class="text-right">${rp(r.nominal)}</td>
        <td>${statusBadge(r.status)}</td>
        <td>${r.id_piutang}</td>
      `,
                    addFilters: (params) => {
                        const val = document.getElementById('contactSelect').value; // id_pelanggan
                        if (val !== '') params.append('pelanggan', val);
                    }
                }
            };

            // ====== RENDER HELPERS ======
            function renderHeader() {
                const cols = MODES[mode].headers;
                const tr = document.getElementById('theadRow');
                tr.innerHTML = cols.map(c => {
                    const style = [
                        c.width ? `width:${c.width};` : '',
                        c.align === 'right' ? 'text-align:right;' : '',
                    ].join('');
                    return `<th style="${style}">${c.text}</th>`;
                }).join('');
            }

            function loadContactOptions() {
                const sel = document.getElementById('contactSelect');
                const label = document.getElementById('labelContact');

                let url, labelText, optionBuilder;
                if (mode === 'utang') {
                    url = '/buku_hutang/ref/pemasok';
                    labelText = 'Nama Pemasok';
                    optionBuilder = it => `<option value="${it.kode_pemasok}">${it.nama_pemasok}</option>`;
                } else {
                    url = '/buku_piutang/ref/pelanggan';
                    labelText = 'Nama Pelanggan';
                    optionBuilder = it => `<option value="${it.id_pelanggan}">${it.nama_pelanggan}</option>`;
                }

                label.textContent = labelText;
                sel.innerHTML = `<option value="">Semua</option>`;

                fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(res => {
                        (res.data || []).forEach(it => sel.insertAdjacentHTML('beforeend', optionBuilder(it)));
                    })
                    .catch(err => console.error(err));
            }

            function setActiveMode(nextMode) {
                mode = nextMode;
                document.getElementById('pageTitle').textContent = MODES[mode].title;

                document.getElementById('btnUtang').classList.toggle('active', mode === 'utang');
                document.getElementById('btnUtang').classList.toggle('btn-primary', mode === 'utang');
                document.getElementById('btnUtang').classList.toggle('btn-outline-secondary', mode !== 'utang');

                document.getElementById('btnPiutang').classList.toggle('active', mode === 'piutang');
                document.getElementById('btnPiutang').classList.toggle('btn-primary', mode === 'piutang');
                document.getElementById('btnPiutang').classList.toggle('btn-outline-secondary', mode !== 'piutang');

                renderHeader();
                loadContactOptions();
                loadData(1);
            }

            // ====== FETCH & RENDER DATA ======
            function loadData(goToPage = 1) {
                page = goToPage;

                const params = new URLSearchParams({
                    page: page,
                    per_page: perPage
                });

                const q = document.getElementById('q').value.trim();
                const st = document.getElementById('status').value;
                const df = document.getElementById('date_from').value;
                const dt = document.getElementById('date_to').value;

                if (q !== '') params.append('search', q);
                if (st !== '') params.append('status', st);
                if (df !== '') params.append('date_from', df);
                if (dt !== '') params.append('date_to', dt);

                MODES[mode].addFilters(params);

                fetch(MODES[mode].endpoint + '?' + params.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(res => {
                        const body = document.querySelector('#tblKeu tbody');
                        body.innerHTML = '';

                        (res.data || []).forEach(r => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = MODES[mode].row(r);
                            body.appendChild(tr);
                        });

                        const meta = res.meta || {};
                        totalPages = meta.total_pages || 1;
                        page = meta.page || 1;

                        document.getElementById('btnPrev').disabled = (page <= 1);
                        document.getElementById('btnNext').disabled = (page >= totalPages);
                        document.getElementById('pageInfo').textContent =
                            `Halaman ${page} dari ${totalPages} — Total ${meta.total ?? 0} baris`;
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal memuat data.');
                    });
            }

            // ====== EVENTS ======
            document.getElementById('btnUtang').addEventListener('click', () => setActiveMode('utang'));
            document.getElementById('btnPiutang').addEventListener('click', () => setActiveMode('piutang'));

            document.getElementById('btnFilter').addEventListener('click', () => loadData(1));
            document.getElementById('q').addEventListener('keyup', e => {
                if (e.key === 'Enter') loadData(1);
            });
            document.getElementById('contactSelect').addEventListener('change', () => loadData(1));
            document.getElementById('status').addEventListener('change', () => loadData(1));

            document.getElementById('btnPrev').addEventListener('click', () => {
                if (page > 1) loadData(page - 1);
            });
            document.getElementById('btnNext').addEventListener('click', () => {
                if (page < totalPages) loadData(page + 1);
            });

            // ====== INIT ======
            renderHeader();
            loadContactOptions();
            loadData(1);
        })();
    </script>
@endpush
