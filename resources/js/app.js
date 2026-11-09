import Alpine from 'alpinejs';

window.Alpine = Alpine;

/*
 | Dialog konfirmasi global.
 | Pakai pada form: <form data-konfirmasi="Pesan..." data-judul="Judul" data-tombol="Hapus">
 */
Alpine.data('konfirmasi', () => ({
    terbuka: false,
    judul: 'Konfirmasi',
    pesan: '',
    tombol: 'Ya, lanjutkan',
    bahaya: true,
    form: null,

    buka(detail) {
        this.form = detail.form;
        this.judul = detail.judul;
        this.pesan = detail.pesan;
        this.tombol = detail.tombol;
        this.bahaya = detail.bahaya;
        this.terbuka = true;
    },

    tutup() {
        this.terbuka = false;
        this.form = null;
    },

    lanjut() {
        const form = this.form;
        this.terbuka = false;
        this.form = null;

        if (form) {
            form.dataset.terkonfirmasi = '1';
            form.requestSubmit();
        }
    },
}));

Alpine.start();

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    // Minta konfirmasi lebih dulu bila diperlukan.
    if (form.dataset.konfirmasi && !form.dataset.terkonfirmasi) {
        event.preventDefault();
        window.dispatchEvent(
            new CustomEvent('konfirmasi', {
                detail: {
                    form,
                    pesan: form.dataset.konfirmasi,
                    judul: form.dataset.judul || 'Konfirmasi',
                    tombol: form.dataset.tombol || 'Ya, lanjutkan',
                    bahaya: form.dataset.bahaya !== 'false',
                },
            }),
        );
        return;
    }

    // Cegah klik ganda: nonaktifkan tombol kirim setelah form dikirim.
    window.setTimeout(() => {
        form.querySelectorAll('button[type="submit"], button:not([type])').forEach((tombol) => {
            tombol.disabled = true;
        });
    }, 0);
});

// Aktifkan kembali tombol bila halaman dipulihkan dari cache tombol "kembali".
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        document.querySelectorAll('form').forEach((form) => {
            delete form.dataset.terkonfirmasi;
            form.querySelectorAll('button[disabled]').forEach((tombol) => {
                tombol.disabled = false;
            });
        });
    }
});

// Muat Chart.js hanya di halaman yang memerlukannya.
if (document.querySelector('[data-chart]')) {
    import('./charts.js').then((modul) => modul.mulaiGrafik());
}
