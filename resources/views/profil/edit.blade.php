<x-dynamic-component :component="'layouts.'.($pengguna->isStaf() ? 'admin' : 'app')" title="Profil saya">
    <div @class(['mx-auto max-w-3xl', 'px-4 py-10 sm:px-6' => ! $pengguna->isStaf()])>
        <x-page-header title="Profil saya" subtitle="Perbarui informasi kontak dan kata sandi akun Anda." />

        <form method="POST" action="{{ route('profil.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="card p-6">
                <div class="mb-6 flex items-center gap-4">
                    <x-avatar :user="$pengguna" size="lg" />
                    <div>
                        <p class="font-semibold text-slate-900">{{ $pengguna->name }}</p>
                        <p class="text-sm text-slate-500">
                            {{ $pengguna->label_peran }}
                            @if ($pengguna->kode_anggota) &middot; <span class="font-mono">{{ $pengguna->kode_anggota }}</span> @endif
                        </p>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-input label="Nama lengkap" name="name" :value="$pengguna->name" required />
                    <x-input label="Email" name="email" type="email" :value="$pengguna->email" required />
                    <x-input label="Nomor telepon" name="telepon" type="tel" :value="$pengguna->telepon" />
                    <div class="sm:col-span-2">
                        <x-textarea label="Alamat" name="alamat" :value="$pengguna->alamat" rows="3" />
                    </div>
                </div>
            </section>

            <section class="card p-6">
                <h2 class="text-base font-semibold">Ubah kata sandi</h2>
                <p class="mt-1 text-sm text-slate-500">Kosongkan bagian ini bila Anda tidak ingin menggantinya.</p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-password label="Kata sandi saat ini" name="password_lama" autocomplete="current-password" />
                    </div>
                    <x-password label="Kata sandi baru" name="password" autocomplete="new-password" help="Minimal 8 karakter." />
                    <x-password label="Ulangi kata sandi baru" name="password_confirmation" autocomplete="new-password" />
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ $pengguna->homeUrl() }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
            </div>
        </form>
    </div>
</x-dynamic-component>
