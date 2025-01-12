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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis_kelamin');
            $table->string('kantor_cabang');
            $table->string('id_kelas')->nullable();
            $table->string('batch')->nullable();
            $table->string('kendaraan');
            $table->string('no_polisi');
            $table->string('no_hp');
            $table->string('email');
            $table->date('tanggal_rencana_checkin');
            $table->date('tanggal_rencana_checkout');
            $table->date('tanggal_checkin');
            $table->date('tanggal_checkout');
            $table->foreignId('id_kamar')->constrained('rooms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
