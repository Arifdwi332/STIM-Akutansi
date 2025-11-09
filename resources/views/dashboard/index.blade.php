@extends('templates.layout')
@section('breadcrumbs', 'Dashboard')

@section('content')
<div class="container-fluid">

  <!-- ROW: SUMMARY CARDS -->
  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box bg-primary shadow-sm rounded-4">
        <div class="inner">
          <h3>1,245</h3>
          <p>Lorem Ipsum</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <a href="#" class="small-box-footer rounded-bottom-4">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-success shadow-sm rounded-4">
        <div class="inner">
          <h3>320</h3>
          <p>Lorem Ipsum</p>
        </div>
        <div class="icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <a href="#" class="small-box-footer rounded-bottom-4">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-warning shadow-sm rounded-4">
        <div class="inner">
          <h3>87</h3>
          <p>Lorem Ipsum</p>
        </div>
        <div class="icon">
          <i class="fas fa-clock"></i>
        </div>
        <a href="#" class="small-box-footer rounded-bottom-4">Lihat daftar <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="small-box bg-danger shadow-sm rounded-4">
        <div class="inner">
          <h3>12</h3>
          <p>Lorem Ipsum</p>
        </div>
        <div class="icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <a href="#" class="small-box-footer rounded-bottom-4">Periksa <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>

  <!-- ROW: CHART & TABLE -->
  <div class="row">
    <!-- GRAFIK -->
    <div class="col-lg-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-header bg-light border-0">
          <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Statistik Aktivitas Bulanan</h5>
        </div>
        <div class="card-body">
          <canvas id="chartAktivitas" height="120"></canvas>
        </div>
      </div>
    </div>

    <!-- TABEL -->
    <div class="col-lg-4">
      <div class="card shadow-sm rounded-4">
        <div class="card-header bg-light border-0">
          <h5 class="card-title mb-0"><i class="fas fa-list me-2 text-success"></i>Aktivitas Terbaru</h5>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><i class="fas fa-user-plus text-primary me-2"></i> Lorem Ipsum</li>
            <li class="list-group-item"><i class="fas fa-file-alt text-warning me-2"></i> Lorem Ipsum</li>
            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> Lorem Ipsum</li>
            <li class="list-group-item"><i class="fas fa-times text-danger me-2"></i> Lorem Ipsum</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

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
        legend: { display: true, position: 'bottom' }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>
@endsection
