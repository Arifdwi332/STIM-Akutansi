@extends('templates.layout')
@section('breadcrumbs', 'Dashboard')

@section('content')
    <div class="container-fluid">

        <!-- ROW: SUMMARY CARDS -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary shadow-sm rounded-4">
                    <div class="inner">
                        <h3>Kas Masuk/keluar</h3>
                        <p>
                            Rp {{ number_format($kasSaldo ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer rounded-bottom-4">Selengkapnya <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success shadow-sm rounded-4">
                    <div class="inner">
                        <h3>Data Barang</h3>
                        <p>Lorem ipsum</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ url('/inventaris?tab=barang#tblInventaris') }}" class="small-box-footer rounded-bottom-4">
                        Lihat detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning shadow-sm rounded-4">
                    <div class="inner">
                        <h3>Laba Rugi</h3>
                        <p id="total-laba-rugi-dashboard">Memuat...</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ url('/laporan_keuangan') }}" class="small-box-footer rounded-bottom-4">
                        Lihat daftar <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>


            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger shadow-sm rounded-4">
                    <div class="inner">
                        <h3>Utang/Piutang</h3>
                        <p>Lorem Ipsum</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <a href="{{ url('/buku_hutang') }}" class="small-box-footer rounded-bottom-4">Periksa <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- ROW: CHART & TABLE -->

    </div>

    <!-- CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartAktivitas').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: [120, 150, 180, 220, 200, 250, 270],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        $(function() {
            if (window.location.hash === '#tblInventaris') {
                // agak ditunda sedikit biar layout & tab sudah render
                setTimeout(function() {
                    const $target = $('#tblInventaris');
                    if ($target.length) {
                        $('html, body').animate({
                            scrollTop: $target.offset().top -
                                80 // sesuaikan offset kalau ada header
                        }, 500);
                    }
                }, 200);
            }
        });
    </script>
    <script>
        $(function() {
            function formatRupiah(n) {
                n = Number(n || 0);
                return 'Rp. ' + n.toLocaleString('id-ID');
            }

            $.getJSON("{{ route('laporan.labaRugi') }}", {
                    search: '',
                    page: 1,
                    per_page: 100
                })
                .done(function(res) {
                    const laba = (res && typeof res.laba_bersih !== 'undefined') ?
                        res.laba_bersih :
                        0;

                    $('#total-laba-rugi-dashboard').text(formatRupiah(laba));
                })
                .fail(function() {
                    $('#total-laba-rugi-dashboard').text('Rp. 0');
                });
        });
    </script>




@endsection
