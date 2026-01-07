// File: public/js/sweetalert-handler.js

document.addEventListener('DOMContentLoaded', function() {
    // Ambil data dari meta tag yang akan kita buat
    const successMsg = document.querySelector('meta[name="alert-success"]');
    const errorMsg = document.querySelector('meta[name="alert-error"]');
    const warningMsg = document.querySelector('meta[name="alert-warning"]');
    const infoMsg = document.querySelector('meta[name="alert-info"]');

    // Success Alert
    if (successMsg && successMsg.content) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: successMsg.content,
            showConfirmButton: true,
            timer: 3000,
            timerProgressBar: true,
        });
    }

    // Error Alert
    if (errorMsg && errorMsg.content) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: errorMsg.content,
            showConfirmButton: true,
        });
    }

    // Warning Alert
    if (warningMsg && warningMsg.content) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: warningMsg.content,
            showConfirmButton: true,
        });
    }

    // Info Alert
    if (infoMsg && infoMsg.content) {
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: infoMsg.content,
            showConfirmButton: true,
        });
    }
});