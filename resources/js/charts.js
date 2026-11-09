import {
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    DoughnutController,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
);

Chart.defaults.font.family = '"Inter Variable", ui-sans-serif, system-ui, sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.color = '#64748b';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 8;
Chart.defaults.plugins.legend.labels.boxHeight = 8;
Chart.defaults.plugins.tooltip.backgroundColor = '#0f172a';
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = { weight: '600' };

const PALET = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#f43f5e', '#14b8a6', '#94a3b8'];

function gradien(ctx, warna) {
    const tinggi = ctx.canvas.clientHeight || 280;
    const g = ctx.createLinearGradient(0, 0, 0, tinggi);
    g.addColorStop(0, warna + '33');
    g.addColorStop(1, warna + '00');
    return g;
}

function grafikTren(kanvas, data) {
    const ctx = kanvas.getContext('2d');

    return new Chart(kanvas, {
        type: 'line',
        data: {
            labels: data.label,
            datasets: [
                {
                    label: 'Dipinjam',
                    data: data.dipinjam,
                    borderColor: '#4f46e5',
                    backgroundColor: gradien(ctx, '#4f46e5'),
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Dikembalikan',
                    data: data.dikembalikan,
                    borderColor: '#10b981',
                    backgroundColor: gradien(ctx, '#10b981'),
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom' } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: '#f1f5f9' },
                    border: { display: false },
                },
            },
        },
    });
}

function grafikKategori(kanvas, data) {
    return new Chart(kanvas, {
        type: 'doughnut',
        data: {
            labels: data.label,
            datasets: [
                {
                    data: data.total,
                    backgroundColor: data.label.map((_, i) => PALET[i % PALET.length]),
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: { legend: { position: 'bottom' } },
        },
    });
}

const PEMBUAT = {
    tren: grafikTren,
    kategori: grafikKategori,
};

export function mulaiGrafik() {
    document.querySelectorAll('[data-chart]').forEach((kanvas) => {
        const buat = PEMBUAT[kanvas.dataset.chart];

        if (!buat) {
            return;
        }

        try {
            buat(kanvas, JSON.parse(kanvas.dataset.config || '{}'));
        } catch (galat) {
            console.error('Gagal membuat grafik:', galat);
        }
    });
}
