<div
    x-data="konfirmasi"
    x-on:konfirmasi.window="buka($event.detail)"
    x-on:keydown.escape.window="tutup()"
    x-cloak
    class="print:hidden"
>
    <div x-show="terbuka" class="fixed inset-0 z-[90] flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="judul-konfirmasi">
        <div
            x-show="terbuka"
            x-transition.opacity
            x-on:click="tutup()"
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
        ></div>

        <div
            x-show="terbuka"
            x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="translate-y-4 opacity-0 sm:scale-95 sm:translate-y-0"
            x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
            x-transition:leave="transition duration-150 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-lift ring-1 ring-slate-200"
        >
            <div class="flex items-start gap-4">
                <span
                    class="grid size-11 shrink-0 place-items-center rounded-full"
                    x-bind:class="bahaya ? 'bg-rose-50 text-rose-600' : 'bg-brand-50 text-brand-600'"
                >
                    <x-icon name="triangle-alert" class="size-5" />
                </span>
                <div class="min-w-0">
                    <h2 id="judul-konfirmasi" class="text-base font-semibold text-slate-900" x-text="judul"></h2>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600" x-text="pesan"></p>
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" class="btn btn-secondary" x-on:click="tutup()">Batal</button>
                <button type="button" class="btn" x-bind:class="bahaya ? 'btn-danger' : 'btn-primary'" x-on:click="lanjut()" x-text="tombol"></button>
            </div>
        </div>
    </div>
</div>
