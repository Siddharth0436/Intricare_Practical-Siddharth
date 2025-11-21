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
        DB::statement("ALTER TABLE custom_fields MODIFY COLUMN type ENUM('text','textarea','date','number','select','checkbox','radio','file','email','phone') NOT NULL");
        Schema::table('custom_fields', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            //
        });
    }
};
