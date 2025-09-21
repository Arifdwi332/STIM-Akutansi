<div class="modal fade" id="modalSubAkunBaru" tabindex="-1" role="dialog" aria-labelledby="modalSubAkunBaruLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">
        <form id="formSubAkunBaru" class="w-100">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title mb-0" id="modalSubAkunBaruLabel">Daftar Sub Akun Baru</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="padding:1.25rem 1.25rem .75rem;">
                    <div class="form-group mb-3">
                        <label for="mst_akun_id" class="mb-1 font-weight-bold">Nama Akun (Induk)</label>
                        <select id="mst_akun_id" name="mst_akun_id" class="form-control" required>
                            <option value="" disabled selected>Pilih Akun</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label for="nama_sub" class="mb-1 font-weight-bold">Nama Sub Akun</label>
                        <input type="text" class="form-control" id="nama_sub" name="nama_sub"
                            placeholder="Nama Sub Akun" required>
                    </div>
                </div>

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
        // Load dropdown akun induk
        $('#modalSubAkunBaru').on('show.bs.modal', function() {
            const $select = $('#mst_akun_id');
            if ($select.data('loaded')) return;

            $.get("{{ route('buku_besar.mst_akun') }}", function(res) {
                if (res && res.ok) {
                    res.data.forEach(item => {
                        $select.append(
                            new Option(`${item.kode_akun} — ${item.nama_akun}`, item
                                .id)
                        );
                    });
                    $select.data('loaded', true);
                }
            });
        });

        // Submit sub akun
        $('#formSubAkunBaru').on('submit', function(e) {
            e.preventDefault();
            const $btn = $(this).find('button[type=submit]')
                .prop('disabled', true).text('Menyimpan...');

            $.post("{{ route('buku_besar.sub_akun') }}", $(this).serialize())
                .done(function(res) {
                    if (res && res.ok) {
                        $('#modalSubAkunBaru').modal('hide');
                        toastr.success('Sub akun berhasil ditambahkan');
                        $('#formSubAkunBaru')[0].reset();
                    } else {
                        toastr.error(res.message ?? 'Gagal menyimpan');
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Gagal menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message)
                        msg = xhr.responseJSON.message;
                    toastr.error(msg);
                })
                .always(function() {
                    $btn.prop('disabled', false).text('Simpan');
                });
        });
    });
</script>
