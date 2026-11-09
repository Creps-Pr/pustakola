@php
    if (session('sukses')) {
        $pesan = ['tipe' => 'sukses', 'judul' => 'Berhasil', 'isi' => session('sukses')];
    } elseif (session('error')) {
        $pesan = ['tipe' => 'error', 'judul' => 'Gagal', 'isi' => session('error')];
    } elseif ($errors->any()) {
        $pesan = ['tipe' => 'error', 'judul' => 'Periksa kembali', 'isi' => 'Ada isian formulir yang belum sesuai. Silakan perbaiki lalu coba lagi.'];
    } else {
        $pesan = null;
    }
@endphp
@if ($pesan)
    <div
        x-data="{ tampil: true }"
        x-init="setTimeout(() => tampil = false, 6500)"
        x-show="tampil"
        x-transition.opacity.duration.300ms
        x-cloak
        class="animate-toast-in fixed top-4 right-4 z-[100] w-[calc(100%-2rem)] max-w-sm print:hidden"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3 rounded-xl bg-white p-4 shadow-lift ring-1 ring-slate-200">
            <span class="mt-0.5 grid size-8 shrink-0 place-items-center rounded-full {{ $pesan['tipe'] === 'sukses' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                <x-icon :name="$pesan['tipe'] === 'sukses' ? 'circle-check' : 'circle-alert'" class="size-5" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-900">{{ $pesan['judul'] }}</p>
                <p class="mt-0.5 text-sm text-slate-600">{{ $pesan['isi'] }}</p>
            </div>
            <button type="button" x-on:click="tampil = false" class="btn-icon -mt-1 -mr-1" aria-label="Tutup notifikasi">
                <x-icon name="x" class="size-4" />
            </button>
        </div>
    </div>
@endif
