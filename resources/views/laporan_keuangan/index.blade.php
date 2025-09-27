{{-- resources/views/laporan_keuangan/index.blade.php --}}
@extends('templates.layout')

@section('breadcrumbs')
    {{-- breadcrumb kalau perlu --}}
@endsection

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
</style>

@section('content')
    <div class="container-fluid bb-wrap">
        <h4 class="mb-3">Laporan Keuangan</h4>

        <div class="row g-3 mt-1">
            <div class="col-lg-6">
                <div class="bb-panel">
                    <div class="bb-head">Laba Rugi</div>
                    <div class="bb-body">
                        <div class="bb-tablebox">
                            <div class="bb-tablebar">
                                <input id="searchJurnal" type="text" class="form-control" placeholder="Search">
                            </div>
                            <div class="table-responsive">
                                <table id="tblJurnal" class="table table-sm table-hover w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nama Akun</th>
                                            <th class="text-success">Debet</th>
                                            <th class="text-danger">Kredit</th>
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
@endsection

@push('scripts')
    <script>
        (function() {
            const toNum = v => Number(String(v ?? 0).replace(/[^\d.-]/g, '')) || 0;
            const rp = n => 'Rp. ' + toNum(n).toLocaleString('id-ID');

            // ====== LABA RUGI ======
            function loadLabaRugi(page = 1) {
                const q = $('#searchJurnal').val() || '';
                const url = "{{ route('laporan_keuangan.get_laba_rugi') }}";

                $.getJSON(url, {
                    search: q,
                    page,
                    per_page: 20
                }, function(res) {
                    if ($('#tblJurnal tbody').length === 0) $('#tblJurnal').append('<tbody></tbody>');
                    const $body = $('#tblJurnal tbody').empty();

                    (res.data || []).forEach(r => {
                        const namaAkun = r.nama_akun ?? r.namaAkun ?? r.nama ?? '';
                        const debetVal = r.debet ?? r.debit ?? r.jml_debit ?? 0;
                        const kreditVal = r.kredit ?? r.credit ?? r.jml_kredit ?? 0;

                        $body.append(`
                        <tr>
                            <td>${namaAkun}</td>
                            <td class="text-success">${rp(debetVal)}</td>
                            <td class="text-danger">${rp(kreditVal)}</td>
                        </tr>
                        `);
                    });

                    $('#pgJurnal').text(
                        `Total: ${res.total ?? (res.data?.length || 0)} | Hal: ${res.page ?? 1}`);
                }).fail(function(xhr) {
                    console.error('loadLabaRugi error:', xhr?.responseText || xhr.statusText);
                });
            }

            $('#searchJurnal').on('input', () => loadLabaRugi(1));
            loadLabaRugi();

            // ====== NERACA ======
            // function loadNeraca(page = 1) {
            //     const q = $('#searchBuku').val() || '';
            //     const url = "{{ route('laporan_keuangan.get_neraca') }}";

            //     $.getJSON(url, {
            //         search: q,
            //         page,
            //         per_page: 20
            //     }, function(res) {
            //         if ($('#tblBuku tbody').length === 0) $('#tblBuku').append('<tbody></tbody>');
            //         const $body = $('#tblBuku tbody').empty();

            //         const kategoriMap = {
            //             aset: "ASET",
            //             liabilitas: "LIABILITAS",
            //             ekuitas: "EKUITAS"
            //         };

            //         let totalAset = 0,
            //             totalLiabilitas = 0,
            //             totalEkuitas = 0;

            //         Object.keys(kategoriMap).forEach(key => {
            //             const rows = res.data?.[key] || [];
            //             if (rows.length === 0) return;

            //             // header kategori (highlight)
            //             $body.append(`
        //     <tr class="table-secondary fw-bold">
        //         <td colspan="2">${kategoriMap[key]}</td>
        //     </tr>
        // `);

            //             let subtotal = 0;
            //             rows.forEach(r => {
            //                 const namaAkun = r.nama_akun ?? '';
            //                 const saldo = Number(r.saldo ?? 0);
            //                 subtotal += saldo;

            //                 $body.append(`
        //         <tr>
        //             <td>${namaAkun}</td>
        //             <td class="text-end">${rp(saldo)}</td>
        //         </tr>
        //     `);
            //             });

            //             if (key === "aset") totalAset = subtotal;
            //             if (key === "liabilitas") totalLiabilitas = subtotal;
            //             if (key === "ekuitas") totalEkuitas = subtotal;

            //             // baris total kategori (bold tanpa highlight)
            //             $body.append(`
        //     <tr class="fw-bold">
        //         <td>TOTAL ${kategoriMap[key]}</td>
        //         <td class="text-end">${rp(subtotal)}</td>
        //     </tr>
        // `);
            //         });

            //         // Tambahkan total Liabilitas + Ekuitas (dibuat bold + highlight khusus)
            //         if (totalLiabilitas || totalEkuitas) {
            //             $body.append(`
        //     <tr class="fw-bold table-dark text-white">
        //         <td>TOTAL LIABILITAS + EKUITAS</td>
        //         <td class="text-end">${rp(totalLiabilitas + totalEkuitas)}</td>
        //     </tr>
        // `);
            //         }

            //         $('#pgBuku').text(`Total akun: ${res.total ?? 0} | Hal: ${res.page ?? 1}`);
            //     }).fail(function(xhr) {
            //         console.error('loadNeraca error:', xhr?.responseText || xhr.statusText);
            //     });
            // }

            // ====== NERACA ======
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

                            // khusus LIABILITAS → selalu positif
                            if (key === "liabilitas") {
                                saldo = Math.abs(saldo);
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



                    $('#pgBuku').text(`Total akun: ${res.total ?? 0} | Hal: ${res.page ?? 1}`);
                }).fail(function(xhr) {
                    console.error('loadNeraca error:', xhr?.responseText || xhr.statusText);
                });
            }




            $('#searchBuku').on('input', () => loadNeraca(1));
            loadNeraca();


        })();
    </script>
@endpush
