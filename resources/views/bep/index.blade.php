@extends('templates.layout')
@section('breadcrumbs', 'Rekom Break Event Point (BEP)')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header">
      <h5 class="mb-0">Rekom BEP</h5>
      <small class="text-muted">Simulasi</small>
    </div>
    <div class="card-body">

      {{-- Biaya Tetap (Fixed Cost) --}}
      <h6 class="fw-bold text-primary mb-2">Biaya Tetap (Fixed Cost)</h6>
      <table class="table table-bordered table-sm align-middle" id="table-fixed-cost">
        <thead class="table-light">
          <tr>
            <th style="width:40px;">#</th>
            <th>Nama Biaya</th>
            <th style="width:150px;">Kategori</th>
            <th style="width:150px;">Total</th>
            <th style="width:50px;"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><input type="text" class="form-control" value="Sewa Gedung"></td>
            <td>
              <select class="form-select form-control">
                <option selected>Sewa</option>
                <option>Gaji</option>
              </select>
            </td>
            <td><input type="text" class="form-control"></td>
            <td class="text-center">
              <button class="btn btn-sm btn-danger btn-delete-row"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td><input type="text" class="form-control" value="Gaji Tetap"></td>
            <td>
              <select class="form-select form-control">
                <option>Sewa</option>
                <option selected>Gaji</option>
              </select>
            </td>
            <td><input type="text" class="form-control"></td>
            <td class="text-center">
              <button class="btn btn-sm btn-danger btn-delete-row"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
      <button class="btn btn-sm btn-primary mb-4 mt-2 btn-add-row" data-target="#table-fixed-cost"><i class="fas fa-plus"></i> Tambah Baris</button>

      {{-- Biaya Variabel Per Unit --}}
      <h6 class="fw-bold text-primary mb-2">Biaya Variabel Per Unit (Variable Cost)</h6>
      <table class="table table-bordered table-sm align-middle" id="table-variable-cost">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nama Biaya</th>
            <th style="width:150px;">Kategori</th>
            <th style="width:150px;">Biaya Per Unit</th>
            <th style="width:100px;">Satuan</th>
            <th style="width:150px;">Total Biaya</th>
            <th style="width:50px;"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><input type="text" class="form-control" value="Kain"></td>
            <td>
              <select class="form-select form-control">
                <option selected>Bahan Baku</option>
                <option>Kemasan</option>
              </select>
            </td>
            <td><input type="text" class="form-control"></td>
            <td><input type="text" class="form-control"></td>
            <td><input type="text" class="form-control"></td>
            <td class="text-center">
              <button class="btn btn-sm btn-danger btn-delete-row"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td><input type="text" class="form-control" value="Benang"></td>
            <td>
              <select class="form-select form-control">
                <option>Bahan Baku</option>
                <option selected>Kemasan</option>
              </select>
            </td>
            <td><input type="text" class="form-control"></td>
            <td><input type="text" class="form-control"></td>
            <td><input type="text" class="form-control"></td>
            <td class="text-center">
              <button class="btn btn-sm btn-danger btn-delete-row"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
      <button class="btn btn-sm btn-primary mb-4 mt-2 btn-add-row" data-target="#table-variable-cost"><i class="fas fa-plus"></i> Tambah Baris</button>

      {{-- Harga Jual --}}
      <h6 class="fw-bold text-primary mb-2">Harga Jual</h6>
      <table class="table table-bordered table-sm align-middle" id="table-selling-price">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Produk</th>
            <th style="width:150px;">Harga Jual Per Unit</th>
            <th style="width:100px;">Satuan</th>
            <th style="width:50px;"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><input type="text" class="form-control" value="Cardigan"></td>
            <td><input type="text" class="form-control"></td>
            <td><input type="text" class="form-control"></td>
            <td class="text-center">
              <button class="btn btn-sm btn-danger btn-delete-row"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td><input type="text" class="form-control" value="Batik"></td>
            <td><input type="text" class="form-control"></td>
            <td><input type="text" class="form-control"></td>
            <td class="text-center">
              <button class="btn btn-sm btn-danger btn-delete-row"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
      <button class="btn btn-sm btn-primary mb-4 mt-2 btn-add-row" data-target="#table-selling-price"><i class="fas fa-plus"></i> Tambah Baris</button>

      <div class="text-center">
        <button class="btn btn-success"><i class="fas fa-calculator"></i> Hitung BEP</button>
      </div>
    </div>

    {{-- Hasil Simulasi --}}
    <div class="card-footer bg-info text-white">
      <div class="row">
        <div class="col-md-6">
          <h6 class="fw-bold">HASIL SIMULASI</h6>
          <table class="table table-sm text-white">
            <tr><td>Total Biaya Tetap</td><td>Rp. xx.xxx.xxx</td></tr>
            <tr><td>Total Biaya Variabel per Unit</td><td>Rp. xx.xxx.xxx</td></tr>
            <tr><td>Harga Jual per Unit</td><td>Rp. xx.xxx.xxx</td></tr>
            <tr><td>Contribution Margin per Unit</td><td>Rp. xx.xxx.xxx</td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <h6 class="fw-bold">Break Even Point</h6>
          <table class="table table-sm text-white">
            <tr><td>Dalam Unit</td><td>xxxxx Unit</td></tr>
            <tr><td>Dalam Rupiah</td><td>Rp. xx.xxx.xxx</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- SCRIPT TAMBAH / HAPUS BARIS --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Tambah baris
    document.querySelectorAll('.btn-add-row').forEach(button => {
      button.addEventListener('click', function () {
        const tableSelector = this.dataset.target;
        const tbody = document.querySelector(`${tableSelector} tbody`);
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);

        // Kosongkan semua input di baris baru
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

        tbody.appendChild(newRow);
        updateRowNumbers(tbody);
      });
    });

    // Hapus baris (delegation)
    document.addEventListener('click', function (e) {
      if (e.target.closest('.btn-delete-row')) {
        const row = e.target.closest('tr');
        const tbody = row.parentElement;
        row.remove();
        updateRowNumbers(tbody);
      }
    });

    // Fungsi untuk memperbarui nomor urut
    function updateRowNumbers(tbody) {
      tbody.querySelectorAll('tr').forEach((tr, index) => {
        tr.querySelector('td:first-child').textContent = index + 1;
      });
    }
  });
</script>
@endsection
