<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('isbn', 30)->nullable();
            $table->string('judul')->index();
            $table->string('penulis')->index();
            $table->string('penerbit')->nullable();
            $table->unsignedSmallInteger('tahun_terbit')->nullable();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->unsignedInteger('jumlah_stok')->default(1);
            $table->unsignedInteger('tersedia')->default(1);
            $table->unsignedSmallInteger('halaman')->nullable();
            $table->string('bahasa', 40)->default('Indonesia');
            $table->string('lokasi_rak', 30)->nullable();
            $table->text('sinopsis')->nullable();
            $table->string('sampul')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
