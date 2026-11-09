@php $edit = $anggota->exists; @endphp
<x-layouts.admin :title="$edit ? 'Ubah Anggota' : 'Daftarkan Anggota'">
    <x-page-header :title="$edit ? 'Ubah data anggota' : 'Daftarkan anggota baru'" :subtitle="$edit ? $anggota->kode_anggota : 'Nomor anggota dibuat otomatis setelah disimpan.'">
        <x-slot:actions>
            <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chevron-left" class="size-4" /> Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ $edit ? route('admin.anggota.update', $anggota) : route('admin.anggota.store') }}" class="card mx-auto max-w-3xl space-y-6 p-6">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-input label="Nama lengkap" name="name" :value="$anggota->name" required />
            <x-input label="Email" name="email" type="email" :value="$anggota->email" required />
            <x-input label="Nomor telepon" name="telepon" type="tel" :value="$anggota->telepon" />
            <x-select label="Status keanggotaan" name="status" :value="$anggota->status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" required />
            <x-textarea class="sm:col-span-2" label="Alamat" name="alamat" :value="$anggota->alamat" rows="3" />
            <x-password class="sm:col-span-2" :label="$edit ? 'Kata sandi baru' : 'Kata sandi'" name="password" autocomplete="new-password" :required="! $edit"
                :help="$edit ? 'Kosongkan bila tidak ingin mengganti. Minimal 8 karakter.' : 'Minimal 8 karakter.'" />
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">{{ $edit ? 'Simpan perubahan' : 'Daftarkan anggota' }}</button>
        </div>
    </form>
</x-layouts.admin>
