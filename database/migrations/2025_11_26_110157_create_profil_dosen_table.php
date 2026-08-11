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
            $table->unsignedBigInteger('id_user')->unique();

            // Data dari PDDikti
            $table->string('nidn', 20)->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('nama_lengkap', 255)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan', '-'])->default('-');
            $table->string('jabatan_fungsional', 100)->nullable();
            $table->string('pangkat_golongan', 100)->nullable();
            $table->string('status_dosen', 100)->nullable();
            $table->string('status_aktivitas', 50)->nullable();
            $table->string('perguruan_tinggi', 255)->nullable();
            $table->string('fakultas', 255)->nullable();
            $table->string('program_studi', 255)->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();

            // Riwayat Pendidikan (JSON array)
            $table->json('riwayat_pendidikan')->nullable();

            // Portofolio (JSON arrays)
            $table->json('penelitian')->nullable();
            $table->json('pengabdian')->nullable();
            $table->json('publikasi')->nullable();
            $table->json('hki')->nullable();

            // Statistik
            $table->integer('jumlah_penelitian')->default(0);
            $table->integer('jumlah_publikasi')->default(0);
            $table->integer('jumlah_pengabdian')->default(0);

            // Sertifikasi
            $table->boolean('sertifikat_pendidik')->default(false);
            $table->year('tahun_sertifikasi')->nullable();

            // Data tambahan (input manual)
            $table->string('email_institusi', 100)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->text('bio')->nullable();
            $table->string('bidang_keahlian', 500)->nullable();
            $table->string('foto_profil', 255)->nullable();

            // Metadata
            $table->string('pddikti_url', 500)->nullable();
            $table->timestamp('last_scraped_at')->nullable();
            $table->boolean('is_verified')->default(false);

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
