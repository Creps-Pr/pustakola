@php $edit = $petugas->exists; @endphp
<x-layouts.admin :title="$edit ? 'Ubah Akun Staf' : 'Tambah Akun Staf'">
    <x-page-header :title="$edit ? 'Ubah akun staf' : 'Tambah akun staf'" subtitle="Administrator dapat mengelola akun staf; petugas hanya mengelola koleksi dan sirkulasi.">
        <x-slot:actions>
            <a href="{{ route('admin.petugas.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chevron-left" class="size-4" /> Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ $edit ? route('admin.petugas.update', $petugas) : route('admin.petugas.store') }}" class="card mx-auto max-w-3xl space-y-6 p-6">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-input label="Nama lengkap" name="name" :value="$petugas->name" required />
            <x-input label="Email" name="email" type="email" :value="$petugas->email" required />
            <x-input label="Nomor telepon" name="telepon" type="tel" :value="$petugas->telepon" />
            <x-select label="Peran" name="role" :value="$petugas->role" :options="['petugas' => 'Petugas', 'admin' => 'Administrator']" required />
            <x-select label="Status" name="status" :value="$petugas->status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" required />
            <x-password :label="$edit ? 'Kata sandi baru' : 'Kata sandi'" name="password" autocomplete="new-password" :required="! $edit"
                :help="$edit ? 'Kosongkan bila tidak ingin mengganti.' : 'Minimal 8 karakter.'" />
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.petugas.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">{{ $edit ? 'Simpan perubahan' : 'Buat akun' }}</button>
        </div>
    </form>
</x-layouts.admin>
