<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('badge')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('logo')->nullable();
            $table->string('category_label');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('rating', 2, 1)->default(4.5);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('delivery_time')->default('30-45 min');
            $table->decimal('delivery_fee', 8, 2)->default(5.99);
            $table->decimal('min_order', 8, 2)->default(20.00);
            $table->boolean('is_free_delivery')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_open')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image');
            $table->decimal('price', 8, 2);
            $table->decimal('original_price', 8, 2)->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_promo')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['restaurant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('categories');
    }
};
