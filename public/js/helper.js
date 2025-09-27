// helper format rupiah
function formatRupiah(angka, prefix = 'Rp. ') {
    if (!angka) return '';
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

// inisialisasi format untuk banyak class
function initRupiahByClass(classNames) {
    classNames.forEach(className => {
        const inputs = document.querySelectorAll('.' + className);
        inputs.forEach(input => {
            // format langsung saat load
            input.value = formatRupiah(input.value);

            // kalau bisa diketik, auto format saat user mengetik
            input.addEventListener('keyup', function() {
                this.value = formatRupiah(this.value);
            });
        });
    });
}

// jalankan saat halaman siap
document.addEventListener('DOMContentLoaded', function() {
    initRupiahByClass(['item-harga', 'item-jual', 'rupiah']);
});
