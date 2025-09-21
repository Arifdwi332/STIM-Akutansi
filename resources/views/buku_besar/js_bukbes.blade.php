@push('scripts')
    <script>
        (function() {
            const detailWrap = document.getElementById('bb-detail-wrap');
            const nominalWrap = document.getElementById('bb-nominal-wrap');
            const btnAdd = document.getElementById('btnAddDetail');
            const totalField = document.getElementById('bb-total');

            const parseR = v => !v ? 0 : parseInt((v + '').replace(/[^\d-]/g, '') || '0', 10);
            const fmtR = x => `Rp. ${(x||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')}`;

            function recompute() {
                let sum = 0;
                document.querySelectorAll('.bb-nominal').forEach(i => sum += parseR(i.value));
                if (totalField) totalField.value = fmtR(sum);
            }

            function addRow() {
                const g = document.createElement('div');
                g.className = 'bb-input-with-btn mb-2 bb-detail-row';
                g.innerHTML = `<input type="text" class="form-control" placeholder="Detail">
                 <button type="button" class="btn btn-danger remove-detail"><i class="fas fa-trash"></i></button>`;
                detailWrap.appendChild(g);

                const n = document.createElement('input');
                n.className = 'form-control bb-nominal mb-2';
                n.placeholder = 'Rp';
                nominalWrap.appendChild(n);

                n.addEventListener('input', recompute);
                g.querySelector('.remove-detail').addEventListener('click', () => {
                    g.remove();
                    n.remove();
                    recompute();
                });
            }
            document.querySelectorAll('.bb-nominal').forEach(i => i.addEventListener('input', recompute));
            document.querySelectorAll('.remove-detail').forEach(btn => btn.addEventListener('click', e => {
                const row = e.currentTarget.closest('.bb-detail-row');
                const idx = [...detailWrap.children].indexOf(row);
                const n = nominalWrap.children[idx];
                row.remove();
                if (n) n.remove();
                recompute();
            }));
            if (btnAdd) btnAdd.addEventListener('click', addRow);
        })();
    </script>
@endpush
