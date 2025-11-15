<div class="modal fade" id="modalListAkun" tabindex="-1" role="dialog" aria-labelledby="modalListAkunLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width:980px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mb-0" id="modalListAkunLabel">Daftar Nama Akun</h5>
        <div class="d-flex align-items-center">
          <div class="input-group input-group-sm mr-2" style="width:260px;">
            <div class="input-group-prepend">
              <span class="input-group-text">Search:</span>
            </div>
            <input type="text" id="akunSearch" class="form-control">
          </div>
          <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>
      </div>

      <div class="modal-body px-3 pb-0">
        <div class="table-responsive mt-2">
          <table id="tblAkunFlat" class="table table-sm table-hover mb-0 w-100">
            <thead>
              <tr>
                <th style="width:140px;">Kode Akun</th>
                <th>Nama Akun</th>
                <th>Sub Akun</th>
                <th style="width:180px;">Kategori</th>
                <th style="width:80px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light border" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<style>

  .modal-body {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }
 
  #tblAkunFlat .btn-edit {
    padding: .2rem .5rem;
  }

  #modalListAkun .modal-header {
    border-bottom: none;
}

/* Hilangkan garis ekstra di atas tabel */
#tblAkunFlat {
    border-top: none !important;
}

/* Rapikan garis bawah header tabel */
#tblAkunFlat thead th {
    border-bottom: 1px solid #dee2e6;
}
</style>

<script>
(function(){
  let dt = null;

  // function initDT(){
  //   if (dt) return dt;
  //   dt = $('#tblAkunFlat').DataTable({
  //     processing: true,
  //     serverSide: false,
  //     paging: false,
  //     info: false,
  //     ordering: false,      // jaga urutan parent->sub
  //     order: [],
  //     dom: 't',             // <-- hilangkan search bawaan DT
  //     ajax: {
  //       url: "/buku_besar/list_akun_flat",
  //       dataSrc: json => (json && json.data) ? json.data : []
  //     },
  //     columns: [
  //       { data: 'kode' },
  //       { data: 'nama_akun', render: (d, t, row) => row.is_sub ? '' : (d||'') },
  //       { data: 'sub_akun', render: (d, t, row) => row.is_sub ? (d||'') : '' },
  //       { data: 'kategori_akun', defaultContent: '-' },
  //       { data: null, orderable:false, searchable:false, className:'text-center',
  //         render: () => '<button type="button" class="btn btn-primary btn-sm" disabled>Edit</button>' }
  //     ],
  //     language: {
  //       emptyTable:
  //         `<div class="text-muted py-3">
  //           <i class="far fa-folder-open fa-lg mb-1 d-block"></i>
  //           Belum ada data akun.
  //         </div>`
  //     },
  //     rowCallback: function(row, data){
  //       if (data.is_sub) $(row).addClass('is-sub');
  //       else $(row).removeClass('is-sub');
  //     }
  //   });

    $('#akunSearch').on('input', function(){ dt.search(this.value).draw(); });
    return dt;
  }

  $(function(){ initDT(); });

  $('#modalListAkun').on('shown.bs.modal', function(){
    if (dt) dt.ajax.reload(null,false);
  });
})();
</script>
