// Helper untuk format Rupiah
function formatRupiah(angka, prefix = 'Rp. ') {
    let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split        = number_string.split(','),
        sisa         = split[0].length % 3,
        rupiah       = split[0].substr(0, sisa),
        ribuan       = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah ? prefix + rupiah : '';
}

// Global init untuk semua input dengan class .rupiah
function initRupiahInputs() {
    const rupiahInputs = document.querySelectorAll('.rupiah');
    rupiahInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            this.value = formatRupiah(this.value);
        });
    });
}

// panggil setelah DOM ready
document.addEventListener('DOMContentLoaded', initRupiahInputs);
