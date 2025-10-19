<!-- =========================
     MODAL: Daftar Pelanggan Baru
========================= -->
<div class="modal fade" id="modalPelangganBaru" tabindex="-1" role="dialog" aria-labelledby="modalPelangganBaruLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">
        <form id="formPelangganBaru" class="w-100">
            @csrf
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h6 class="modal-title mb-0" id="modalPelangganBaruLabel">Daftar Pelanggan Baru</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body" style="padding: 1.25rem 1.25rem .75rem;">
                    <div class="form-group mb-3">
                        <label class="mb-1" for="nama_pelanggan" style="font-weight:600;">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                            placeholder="Nama Pelanggan" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="mb-1" for="no_telp_pelanggan" style="font-weight:600;">No. Telp</label>
                        <input type="text" class="form-control" id="no_telp_pelanggan" name="no_telp"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="form-group mb-3">
                        <label class="mb-1" for="perusahaan_pelanggan" style="font-weight:600;">Perusahaan</label>
                        <input type="text" class="form-control" id="perusahaan_pelanggan" name="perusahaan"
                            placeholder="Nama Perusahaan">
                    </div>

                    <div class="form-group mb-1">
                        <label class="mb-1" for="alamat_pelanggan" style="font-weight:600;">Alamat</label>
                        <textarea class="form-control" id="alamat_pelanggan" name="alamat" rows="3" placeholder="Alamat lengkap"></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPelangganBaru">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================
     MODAL: Daftar Pemasok Baru
========================= -->
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
<!-- Modal Detail Barang -->
<div class="modal fade" id="modalDetailBarang" tabindex="-1" role="dialog"
    aria-labelledby="modalDetailBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0" id="modalDetailBarangLabel">Detail Data Barang</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th>Nama Barang</th>
                        <td id="det_nama_barang"></td>
                    </tr>
                    <tr>
                        <th>Pemasok</th>
                        <td id="det_pemasok"></td>
                    </tr>
                    <tr>
                        <th>Satuan</th>
                        <td id="det_satuan"></td>
                    </tr>
                    <tr>
                        <th>Stok</th>
                        <td id="det_stok"></td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td id="det_harga_beli"></td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td id="det_harga_jual"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Barang -->
<div class="modal fade" id="modalEditBarang" tabindex="-1" role="dialog" aria-labelledby="modalEditBarangLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0" id="modalEditBarangLabel">Edit Data Barang</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditBarang">
                    <input type="hidden" id="edit_id_barang">

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="edit_nama_barang" required>
                    </div>

                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" class="form-control" id="edit_satuan" required>
                    </div>

                    <div class="form-group">
                        <label>Harga Beli</label>
                        <input type="text" class="form-control rupiah" id="edit_harga_beli" required>
                    </div>

                    <div class="form-group">
                        <label>Harga Jual</label>
                        <input type="text" class="form-control rupiah" id="edit_harga_jual" required>
                    </div>

                    <div class="form-group">
                        <label>Pemasok</label>
                        <input type="text" class="form-control" id="edit_pemasok" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnUpdateBarang">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // ========= Helpers =========
        function appendToSelect(data, preferSelector) {
            let $target = $(); // empty set

            if (preferSelector) {
                // pecah daftar selector prioritas dan ambil yang pertama ada di DOM
                const list = preferSelector.split(',').map(s => s.trim()).filter(Boolean);
                for (const sel of list) {
                    const $el = $(sel);
                    if ($el.length) {
                        $target = $el.eq(0);
                        break;
                    }
                }
            }

            if (!$target.length) {
                // fallback umum
                $target = $('#pelanggan_id');
                if (!$target.length) $target = $('#pemasok_id');
                if (!$target.length) $target = $('#party_id');
                if (!$target.length) $target = $('select.form-control').first();
            }

            if ($target.length) {
                const text = (data.nama || data.nama_pelanggan || data.nama_pemasok || 'Baru') +
                    (data.kode ? (' (' + data.kode + ')') : '');
                const val = data.id ?? data.value ?? (data.nama || String(Date.now()));
                $target.append(new Option(text, val, true, true));
                $target.trigger('change');
            }
        }


        // ========= Pelanggan =========
        $('#btnPelangganBaruOpen').on('click', function() {
            $('#formPelangganBaru')[0].reset();
            $('#modalPelangganBaru').modal('show');
        });

        $('#formPelangganBaru').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnSimpanPelangganBaru').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                    method: 'POST',
                    url: "{{ route('inventaris.pelanggan.store') }}", // <- sesuaikan jika perlu
                    data: $(this).serialize(),
                    dataType: 'json'
                })
                .done(function(res) {
                    if (res && (res.ok || res.success)) {
                        // Normalisasi objek data (id, nama)
                        const d = res.data || {};
                        d.nama = d.nama || d.nama_pelanggan;
                        appendToSelect(d, '#pelanggan_id'); // prioritas: pelanggan_id
                        $('#modalPelangganBaru').modal('hide');
                        (window.toastr && toastr.success) ? toastr.success(
                            'Pelanggan berhasil disimpan'): alert('Pelanggan berhasil disimpan');
                    } else {
                        const msg = (res && (res.message || res.error)) || 'Gagal menyimpan data';
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

        // ========= Pemasok =========
        $('#btnPemasokBaruOpen').on('click', function() {
            $('#formPemasokBaru')[0].reset();
            $('#modalPemasokBaru').modal('show');
        });

        $('#formPemasokBaru').on('submit', function(e) {
            e.preventDefault();

            $(this).find('.rupiah').each(function() {
                $(this).val(parseRupiah($(this).val()));
            });

            const $btn = $('#btnSimpanPemasokBaru').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                    method: 'POST',
                    url: "{{ route('inventaris.pemasok.store') }}", // <- sesuaikan jika perlu
                    data: $(this).serialize(),
                    dataType: 'json'
                })
                .done(function(res) {
                    if (res && (res.ok || res.success)) {
                        const d = res.data || {};
                        d.nama = d.nama || d.nama_pemasok;
                        // prioritas select pemasok/party
                        appendToSelect(d, '#pemasok_id, #party_id');
                        $('#modalPemasokBaru').modal('hide');
                        (window.toastr && toastr.success) ? toastr.success(
                            'Pemasok berhasil disimpan'): alert('Pemasok berhasil disimpan');
                    } else {
                        const msg = (res && (res.message || res.error)) || 'Gagal menyimpan data';
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

    // ==== DETAIL BARANG ====
    $(document).on('click', '.btnDetailBarang', function() {
        const data = $(this).data();
        $('#det_nama_barang').text(data.nama);
        $('#det_pemasok').text(data.pemasok);
        $('#det_satuan').text(data.satuan);
        $('#det_stok').text(data.stok);
        $('#det_harga_beli').text(toRp(data.hargabeli));
        $('#det_harga_jual').text(toRp(data.hargajual));
        $('#modalDetailBarang').modal('show');
    });

    // ==== EDIT BARANG ====
    $(document).on('click', '.btnEditBarang', function() {
        const data = $(this).data();
        $('#edit_id_barang').val(data.id);
        $('#edit_nama_barang').val(data.nama);
        $('#edit_satuan').val(data.satuan);
        $('#edit_harga_beli').val(data.hargabeli);
        $('#edit_harga_jual').val(data.hargajual);
        $('#edit_pemasok').val(data.pemasok);
        $('#modalEditBarang').modal('show');
    });

    // Simpan perubahan
    $(document).on('click', '#btnUpdateBarang', function() {
        const payload = {
            id_barang: $('#edit_id_barang').val(),
            nama_barang: $('#edit_nama_barang').val(),
            satuan: $('#edit_satuan').val(),
            harga_satuan: parseRupiah($('#edit_harga_beli').val()), // parse jadi angka
            harga_jual: parseRupiah($('#edit_harga_jual').val()), // parse jadi angka
        };

        $.ajax({
                method: 'POST',
                url: "{{ route('inventaris.updateBarang') }}",
                data: payload,
                dataType: 'json',
            })
            .done(res => {
                if (res.ok) {
                    $('#modalEditBarang').modal('hide');
                    toastr.success(res.message || 'Berhasil disimpan');
                    DT_INVENTARIS.ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'Gagal update');
                }
            })
            .fail(xhr => {
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                toastr.error(msg);
            });
    });
</script>
