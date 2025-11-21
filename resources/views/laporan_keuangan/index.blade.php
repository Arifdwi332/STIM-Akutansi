{{-- resources/views/laporan_keuangan/index.blade.php --}}
@extends('templates.layout')

@section('breadcrumbs')
    {{-- breadcrumb kalau perlu --}}
@endsection

@section('content')
    <style>
        :root {
            --bb-border: #E8ECEF;
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

        #tblJurnal .row-section td {
            background: #d6d8db;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
            padding-left: 0 !important;
        }

        #tblJurnal .row-total td {
            font-weight: 700;
        }

        #tblJurnal .row-grand td {
            font-weight: 800;
        }

        #tblJurnal td,
        #tblJurnal th {
            border-top: 1px solid #e5e7eb;
        }

        #tblJurnal td:last-child,
        #tblJurnal th:last-child {
            text-align: left;
            white-space: nowrap;
        }
    </style>
    <div class="container-fluid bb-wrap">
        <h4 class="mb-3">Laporan Keuangan</h4>

        <div class="row g-3 mt-1">
            <div class="col-lg-6">
                <div class="bb-panel">
                    <div class="bb-head">Laba Rugi</div>
                    <div class="bb-body">
                        <div class="bb-tablebar">
                            <input id="searchJurnal" type="text" class="form-control" placeholder="Search">
                        </div>

                        <div class="table-responsive">
                            <table id="tblJurnal" class="table table-sm table-hover w-100">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Keterangan</th>
                                        <th class="text-end">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="bb-footline">
                            <small class="text-muted">Jurnal periode berjalan</small>
                            <div id="pgJurnal"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-6">
                <div class="bb-panel">
                    <div class="bb-head">Neraca</div>
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
                                            <th class="text-end">Saldo Akhir</th>
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

    @push('scripts')
        <script>
            (function() {
                const toNum = v => Number(String(v ?? 0).replace(/[^\d.-]/g, '')) || 0;
                const rp = n => 'Rp. ' + toNum(n).toLocaleString('id-ID');

                let labaRugiBerjalan = 0;

                function loadLabaRugi(page = 1) {
                    const q = $('#searchJurnal').val() || '';
                    const url = "{{ route('laporan_keuangan.get_laba_rugi') }}";

                    $.getJSON(url, {
                        search: q,
                        page,
                        per_page: 100
                    }, function(res) {
                        const rows = res?.data || [];

                        // klasifikasi: penjualan | hpp | pendapatan_lain | beban
                        const items = rows.map(r => {
                            const debet = Number(r.debet ?? 0);
                            const kredit = Number(r.kredit ?? 0);
                            const kat = String(r.kategori_akun ?? '').toLowerCase();
                            const nama = String(r.nama_akun ?? '').toLowerCase();
                            const kode = String(r.kode_akun ?? '');

                            // pakai r.jenis dari BE kalau ada; kalau tidak, fallback
                            let jenis = r.jenis;
                            if (!jenis) {
                                const isPendapatan = kat === 'pendapatan';
                                const isHpp = (kode === '5104' || Number(kode) === 5104 || /hpp|harga pokok/
                                    .test(nama));
                                const isPenjualan = isPendapatan && (/(penjualan|sales)/i.test(nama) ||
                                    /^(40|41)\d{2,}$/.test(kode));
                                if (isPendapatan) jenis = isPenjualan ? 'penjualan' : 'pendapatan_lain';
                                else jenis = isHpp ? 'hpp' : 'beban';
                            }

                            // nilai basis (positif untuk tampilan)
                            const nilai = (jenis === 'penjualan' || jenis === 'pendapatan_lain') ?
                                (kredit - debet) // pendapatan
                                :
                                (debet - kredit); // HPP/Beban
                            return {
                                nama: r.nama_akun ?? '',
                                jenis,
                                nilai: Math.max(0, nilai)
                            };
                        }).filter(i => i.nilai > 0);

                        const penjualan = items.filter(i => i.jenis === 'penjualan');
                        const hpp = items.filter(i => i.jenis === 'hpp');
                        const pendLain = items.filter(i => i.jenis === 'pendapatan_lain');
                        const beban = items.filter(i => i.jenis === 'beban');

                        const totalPenjualan = penjualan.reduce((s, i) => s + i.nilai, 0);
                        const totalHPP = hpp.reduce((s, i) => s + i.nilai, 0);
                        const totalPendLain = pendLain.reduce((s, i) => s + i.nilai, 0);

                        // >>> Total Pendapatan NET: penjualan + pendapatan lain - HPP
                        const totalPendapatanNet = totalPenjualan + totalPendLain - totalHPP;

                        const totalBeban = beban.reduce((s, i) => s + i.nilai, 0);
                        const labaBersih = totalPendapatanNet - totalBeban;

                        labaRugiBerjalan = labaBersih;

                        if ($('#tblJurnal tbody').length === 0) $('#tblJurnal').append('<tbody></tbody>');
                        const $body = $('#tblJurnal tbody').empty();

                        // ====== PENDAPATAN ======
                        $body.append(`<tr class="row-section"><td colspan="2" class="pl-0">Pendapatan</td></tr>`);

                        // 1) Penjualan Barang Dagang
                        if (penjualan.length === 0) {
                            $body.append(`<tr><td>-</td><td>Rp. 0</td></tr>`);
                        } else {
                            penjualan.forEach(i => $body.append(
                                `<tr><td>${i.nama}</td><td>${rp(i.nilai)}</td></tr>`));
                        }
                        $body.append(
                            `<tr class="row-total"><td>Total Penjualan</td><td>${rp(totalPenjualan)}</td></tr>`);

                        // 2) HPP — ditempatkan persis DI BAWAH penjualan (tanpa header), sebagai pengurang
                        hpp.forEach(i => {
                            $body.append(
                                `<tr>
             <td class="text-muted">(-) ${i.nama}</td>
             <td class="text-end text-danger">- ${rp(i.nilai)}</td>
           </tr>`
                            );
                        });

                        // 3) Pendapatan lain (jika ada)
                        pendLain.forEach(i => $body.append(`<tr><td>${i.nama}</td><td>${rp(i.nilai)}</td></tr>`));

                        // 4) Total Pendapatan setelah HPP
                        $body.append(
                            `<tr class="row-total">
           <td>Total Pendapatan</td>
           <td>${rp(totalPendapatanNet)}</td>
         </tr>`
                        );

                        // ====== BEBAN OPERASIONAL ======
                        $body.append(`<tr class="row-section"><td colspan="2">Beban Operasional</td></tr>`);
                        if (beban.length === 0) {
                            $body.append(`<tr><td>-</td><td>Rp. 0</td></tr>`);
                        } else {
                            beban.forEach(i => $body.append(`<tr><td>${i.nama}</td><td>${rp(i.nilai)}</td></tr>`));
                        }
                        $body.append(`<tr class="row-total"><td>Total Beban</td><td>${rp(totalBeban)}</td></tr>`);

                        // ====== Laba/Rugi Bersih ======
                        $body.append(
                            `<tr class="row-grand"><td>Total Laba/Rugi</td><td>${rp(labaBersih)}</td></tr>`);

                        $('#pgJurnal').text(`Total baris: ${rows.length} | Hal: ${res.page ?? 1}`);
                        loadNeraca(1);
                    }).fail(xhr => console.error('loadLabaRugi error:', xhr?.responseText || xhr.statusText));
                }

                $('#searchJurnal').on('input', () => loadLabaRugi(1));
                loadLabaRugi();


                function loadNeraca(page = 1) {
                    const q = $('#searchBuku').val() || '';
                    const url = "{{ route('laporan_keuangan.get_neraca') }}";

                    $.getJSON(url, {
                        search: q,
                        page,
                        per_page: 20
                    }, function(res) {
                        if ($('#tblBuku tbody').length === 0) $('#tblBuku').append('<tbody></tbody>');
                        const $body = $('#tblBuku tbody').empty();

                        const kategoriMap = {
                            aset: "ASET",
                            liabilitas: "LIABILITAS",
                            ekuitas: "EKUITAS"
                        };

                        let totalAset = 0,
                            totalLiabilitas = 0,
                            totalEkuitas = 0;

                        Object.keys(kategoriMap).forEach(key => {
                            const rows = res.data?.[key] || [];
                            if (rows.length === 0) return;

                            // header kategori
                            $body.append(`
                            <tr class="table-secondary fw-bold">
                                <td colspan="2">${kategoriMap[key]}</td>
                            </tr>
                        `);

                            let subtotal = 0;
                            rows.forEach(r => {
                                const namaAkun = r.nama_akun ?? '';
                                let saldo = Number(r.saldo ?? 0);

                                // [CHANGES] ambil tambahan informasi akun
                                const akunId = r.id_akun ?? r.id ?? null;
                                const kodeAkun = String(r.kode_akun ?? '');

                                // [CHANGES] khusus LIABILITAS → selalu positif,
                                // kecuali akun Utang PPN (id = 72) yang pakai saldo asli
                                if (key === "liabilitas" && akunId !== 72) {
                                    saldo = Math.abs(saldo);
                                }

                                // [CHANGES] sesuaikan dengan kode akun saldo laba kamu: 3201
                                if (key === "ekuitas") {
                                    const namaLower = namaAkun.toLowerCase();
                                    if (
                                        kodeAkun === '3201' || kodeAkun === 3201 ||
                                        // [CHANGES] dari 3301 -> 3201
                                        /laba/.test(namaLower) ||
                                        /rugi/.test(namaLower)
                                    ) {
                                        saldo = labaRugiBerjalan;
                                    }
                                }



                                subtotal += saldo;

                                $body.append(`
                                <tr>
                                    <td>${namaAkun}</td>
                                    <td class="text-end">${rp(saldo)}</td>
                                </tr>
                            `);
                            });

                            if (key === "aset") totalAset = subtotal;
                            if (key === "liabilitas") totalLiabilitas = subtotal;
                            if (key === "ekuitas") totalEkuitas = subtotal;

                            // total kategori
                            $body.append(`
                            <tr class="font-weight-bold text-black">
                                <td>TOTAL ${kategoriMap[key]}</td>
                                <td class="text-end">${rp(subtotal)}</td>
                            </tr>
                        `);
                        });

                        const totalLiabEkuitas = totalLiabilitas + totalEkuitas;
                        $body.append(`
                     <tr class="row-grand" style="font-weight: 700; text-transform: uppercase;">
    <td>Total Liabilitas + Ekuitas</td>
    <td class="text-end">${rp(totalLiabEkuitas)}</td>
</tr>


                    `);
                        $('#pgBuku').text(`Total akun: ${res.total ?? 0} | Hal: ${res.page ?? 1}`);
                    }).fail(function(xhr) {
                        console.error('loadNeraca error:', xhr?.responseText || xhr.statusText);
                    });
                }

                $('#searchBuku').on('input', () => loadNeraca(1));

            })();
        </script>
    @endpush
@endsection
