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
            
            // HANYA URL - data lain ambil dari scraping
            $table->string('pddikti_url', 500);
            
            // Data manual (tidak ada di PDDikti)
            $table->string('foto_profil', 255)->nullable();
            $table->text('bio')->nullable();
            $table->string('no_telepon', 20)->nullable();
            
            // Metadata
            $table->timestamp('last_scraped_at')->nullable();
            
            $table->timestamps();
            
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