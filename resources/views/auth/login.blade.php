<x-layouts.auth title="Masuk" judul="Selamat datang kembali" subjudul="Masuk ke akun Anda untuk memantau peminjaman dan menjelajahi koleksi.">
    <form method="POST" action="{{ route('login.proses') }}" class="space-y-5">
        @csrf

        <x-input label="Email" name="email" type="email" icon="mail" placeholder="nama@email.com" autocomplete="email" autofocus required />

        <x-password label="Kata sandi" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required />

        <div class="flex items-center justify-between">
            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
                <input type="checkbox" name="ingat" value="1" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-600" @checked(old('ingat'))>
                Ingat saya
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">
            Masuk <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="link">Daftar jadi anggota</a>
    </p>

    @if (app()->environment('local'))
        <div class="mt-8 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
            <p class="flex items-center gap-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">
                <x-icon name="info" class="size-3.5" /> Akun demo (hanya tampil di mode lokal)
            </p>
            <dl class="mt-3 space-y-1.5 text-sm text-slate-600">
                <div class="flex justify-between gap-3"><dt>Administrator</dt><dd class="font-mono text-xs">admin@pustakaloka.test</dd></div>
                <div class="flex justify-between gap-3"><dt>Petugas</dt><dd class="font-mono text-xs">petugas@pustakaloka.test</dd></div>
                <div class="flex justify-between gap-3"><dt>Anggota</dt><dd class="font-mono text-xs">anggota@pustakaloka.test</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-200 pt-1.5"><dt>Kata sandi</dt><dd class="font-mono text-xs">password</dd></div>
            </dl>
        </div>
    @endif
</x-layouts.auth>
