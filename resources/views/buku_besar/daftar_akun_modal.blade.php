<div class="modal fade" id="modalAkunBaru" tabindex="-1" role="dialog" aria-labelledby="modalAkunBaruLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">
        <form id="formAkunBaru" class="w-100">
            @csrf
            <div class="modal-content">
                {{-- Header polos --}}
                <div class="modal-header">
                    <h6 class="modal-title mb-0" id="modalAkunBaruLabel">Daftar Akun Baru</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Body: field sederhana seperti SS --}}
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
                            <option value="Liabilitas Lancar">Liabilitas Jangka Pendek</option>
                            <option value="Liabilitas Jangka Panjang">Liabilitas Jangka Panjang</option>
                            <option value="Ekuitas">Ekuitas</option>
                            <option value="Pendapatan">Pendapatan</option>
                            <option value="Pendapatan">Harga Pokok Penjualan</option>
                            <option value="Beban">Beban Penjualan</option>
                            <option value="Beban">Beban Umum & Administrasi</option>
                            <option value="Beban">Beban Lain-lain</option>
                        </select>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function() {
        // buka modal
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
                            $select.append(new Option(text, res.data.id, true,
                                true));
                            $select.trigger('change');
                        }

                        $('#modalAkunBaru').modal('hide');
                        toastr && toastr.success ? toastr.success('Akun berhasil disimpan') : alert(
                            'Akun berhasil disimpan');
                    } else {
                        const msg = (res && res.message) ? res.message : 'Gagal menyimpan data';
                        toastr && toastr.error ? toastr.error(msg) : alert(msg);
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Gagal menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON
                        .message;
                    toastr && toastr.error ? toastr.error(msg) : alert(msg);
                })
                .always(function() {
                    $btn.prop('disabled', false).text('Simpan');
                });
        });
    });
</script>
