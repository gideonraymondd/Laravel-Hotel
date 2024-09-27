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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('created_by')->constrained('users'); // Foreign key ke tabel 'users'
            $table->foreignId('checked_in_by')->constrained('users'); // Foreign key ke tabel 'users'
            $table->timestamp('checked_in_time'); // Tetap sebagai timestamp
            $table->foreignId('checked_out_by')->constrained('users'); // Foreign key ke tabel 'users'
            $table->timestamp('checked_out_time'); // Tetap sebagai timestamp
            $table->foreignId('cleaned_by')->constrained('users'); // Foreign key ke tabel 'users'
            $table->timestamp('cleaned_time'); // Tetap sebagai timestamp
            $table->string('room_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

             // Drop foreign key constraints terlebih dahulu
            $table->dropForeign(['created_by']); // Hapus foreign key 'created_by'
            $table->dropForeign(['checked_in_by']); // Hapus foreign key 'checked_in_by'
            $table->dropForeign(['checked_out_by']); // Hapus foreign key 'checked_out_by'
            $table->dropForeign(['cleaned_by']); // Hapus foreign key 'cleaned_by'

            // Drop columns
            $table->dropColumn('created_by'); // Hapus kolom 'created_by'
            $table->dropColumn('checked_in_by'); // Hapus kolom 'checked_in_by'
            $table->dropColumn('checked_in_time'); // Hapus kolom 'checked_in_time'
            $table->dropColumn('checked_out_by'); // Hapus kolom 'checked_out_by'
            $table->dropColumn('checked_out_time'); // Hapus kolom 'checked_out_time'
            $table->dropColumn('cleaned_by'); // Hapus kolom 'cleaned_by'
            $table->dropColumn('cleaned_time'); // Hapus kolom 'cleaned_time'
            $table->dropColumn('room_status');

        });
    }
};
