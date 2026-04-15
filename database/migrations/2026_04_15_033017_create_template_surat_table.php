<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_surat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->string('kode_template')->unique()->nullable();
            $table->text('deskripsi')->nullable();

            // Kop surat
            $table->string('kop_nama_instansi')->nullable();
            $table->string('kop_alamat')->nullable();
            $table->string('kop_telp')->nullable();
            $table->string('kop_email')->nullable();
            $table->string('kop_website')->nullable();
            $table->string('kop_logo_path')->nullable();

            // Body template (HTML/text dengan placeholder)
            $table->longText('body_template')->nullable();

            // Footer
            $table->string('footer_kota')->nullable();
            $table->string('footer_jabatan_ttd')->nullable();
            $table->string('footer_nama_ttd')->nullable();
            $table->string('footer_nip_ttd')->nullable();

            // Metadata
            $table->enum('jenis_surat', ['masuk', 'keluar', 'umum'])->default('umum');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });

        // Tambahkan kolom template_id ke surat_keluar
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('id')
                ->constrained('template_surat')->onDelete('set null');
        });

        // Tambahkan kolom template_id ke surat_masuk
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('id')
                ->constrained('template_surat')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });

        Schema::dropIfExists('template_surat');
    }
};
