<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('buku_id')->constrained('buku');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            $table->string('status', 20)->default('dipinjam')->index(); // dipinjam | dikembalikan
            $table->unsignedInteger('denda')->default(0);
            $table->boolean('denda_lunas')->default(false);
            $table->unsignedTinyInteger('perpanjangan')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('jatuh_tempo');
            $table->index('tanggal_pinjam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
