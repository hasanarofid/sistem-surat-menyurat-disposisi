<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->date('tanggal_terima');
            $table->string('asal_surat');
            $table->string('perihal');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'processed', 'archived'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->string('tujuan_surat');
            $table->string('perihal');
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'sent'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuk')->onDelete('cascade');
            $table->foreignId('pengirim_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('penerima_id')->constrained('users')->onDelete('cascade');
            $table->text('instruksi');
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisi');
        Schema::dropIfExists('surat_keluar');
        Schema::dropIfExists('surat_masuk');
    }
};
