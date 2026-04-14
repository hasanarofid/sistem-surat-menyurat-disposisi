<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add signature to users for their default electronic signature
        Schema::table('users', function (Blueprint $table) {
            $table->string('signature_image')->nullable()->after('email');
        });

        // Add e-sign columns to surat_keluar
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->boolean('is_signed')->default(false)->after('status');
            $table->timestamp('signed_at')->nullable()->after('is_signed');
            $table->string('signature_path')->nullable()->after('signed_at');
        });

        // Add e-sign columns to disposisi
        Schema::table('disposisi', function (Blueprint $table) {
            $table->boolean('is_signed')->default(false)->after('status');
            $table->timestamp('signed_at')->nullable()->after('is_signed');
            $table->string('signature_path')->nullable()->after('signed_at');
        });

        // Create settings table for numbering configuration
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed initial numbering settings
        \DB::table('settings')->insert([
            [
                'key' => 'nomor_surat_prefix',
                'value' => 'SK-BLU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'nomor_surat_counter',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        
        Schema::table('disposisi', function (Blueprint $table) {
            $table->dropColumn(['is_signed', 'signed_at', 'signature_path']);
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['is_signed', 'signed_at', 'signature_path']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('signature_image');
        });
    }
};
