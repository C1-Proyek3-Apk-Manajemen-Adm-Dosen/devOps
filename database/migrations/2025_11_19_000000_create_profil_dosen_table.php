<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_dosen', function (Blueprint $table) {
            $table->id('profil_id');
            $table->unsignedBigInteger('id_user'); // FK ke users

            // Data dari PDDikti
            $table->string('nidn', 20)->unique()->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            // Jabatan & Status
            $table->string('jabatan_fungsional')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('status_dosen')->nullable(); // Aktif, Non-Aktif, dll

            // Institusi
            $table->string('perguruan_tinggi')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('program_studi')->nullable();

            // Pendidikan (JSON array)
            $table->json('riwayat_pendidikan')->nullable();

            // Statistik
            $table->integer('jumlah_penelitian')->default(0);
            $table->integer('jumlah_publikasi')->default(0);
            $table->integer('jumlah_pengabdian')->default(0);

            // Sertifikasi
            $table->string('sertifikat_pendidik')->nullable();
            $table->year('tahun_sertifikasi')->nullable();

            // Kontak (manual input)
            $table->string('email_institusi')->nullable();
            $table->string('no_telepon', 20)->nullable();

            // Bio tambahan (manual input)
            $table->text('bio')->nullable();
            $table->text('bidang_keahlian')->nullable();
            $table->string('foto_profil')->nullable(); // path/url foto

            // Scraping metadata
            $table->string('pddikti_url')->nullable();
            $table->timestamp('last_scraped_at')->nullable();
            $table->boolean('is_verified')->default(false); // Apakah sudah diverifikasi dosen

            $table->timestamps();

            // Foreign key
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_dosen');
    }
};
