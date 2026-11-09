<x-layouts.auth title="Daftar" judul="Daftar jadi anggota" subjudul="Gratis dan hanya butuh satu menit. Anda langsung mendapat nomor anggota.">
    <form method="POST" action="{{ route('register.proses') }}" class="space-y-5">
        @csrf

        <x-input label="Nama lengkap" name="name" icon="user" placeholder="Nama sesuai kartu identitas" autocomplete="name" autofocus required />

        <x-input label="Email" name="email" type="email" icon="mail" placeholder="nama@email.com" autocomplete="email" required />

        <x-input label="Nomor telepon" name="telepon" type="tel" icon="phone" placeholder="0812-xxxx-xxxx" autocomplete="tel" help="Opsional. Dipakai petugas bila perlu menghubungi Anda." />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-password label="Kata sandi" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required />
            <x-password label="Ulangi kata sandi" name="password_confirmation" placeholder="Ketik ulang" autocomplete="new-password" required />
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">
            Buat akun <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="link">Masuk</a>
    </p>
</x-layouts.auth>
