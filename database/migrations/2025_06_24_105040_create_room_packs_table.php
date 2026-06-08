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
        Schema::create('room_packs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designer_id'); // references users table
            $table->string('name');                   // Room Pack Name
            $table->string('cover_render');           // Required
            $table->json('optional_renders')->nullable(); // Array of optional renders
            $table->string('pdf_2d_drawing');         // Required PDF
            $table->string('decor_material_chart');   // Required chart file
            $table->timestamps();

            $table->foreign('designer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_packs');
    }
};
