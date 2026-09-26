<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('header_title')->default('Sewa Pacar');
            $table->string('subtitle')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_back_button')->default(false);
            $table->string('logo_path')->nullable();
            // background
            $table->boolean('use_global_background')->default(true);
            $table->string('background_type')->default('image'); // image, color, gradient
            $table->string('background_image')->nullable();
            $table->string('background_color')->nullable();
            $table->string('background_gradient')->nullable();
            // SEO per halaman (dynamic)
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_author')->nullable();
            $table->string('og_image')->nullable();
            $table->string('footer_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
