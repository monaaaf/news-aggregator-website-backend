<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(
            'api_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., NewsAPI
            $table->string('url');            // e.g., https://newsapi.org
            $table->timestamps();
        }
        );

        Schema::create(
            'sources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., BBC News
            $table->string('url');            // e.g., https://www.bbc.com/news
            $table->foreignId('api_provider_id')->constrained('api_providers')->cascadeOnDelete();
            $table->timestamps();
        }
        );

        Schema::create(
            'categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., Technology
            $table->string('slug')->unique(); // e.g., technology
            $table->timestamps();
        }
        );

        Schema::create(
            'authors', function (Blueprint $table) {
            $table->id();
            $table->text('name');              // e.g., John Doe
            $table->string('email')->nullable(); // Optional: Author email
            $table->timestamps();
        }
        );

        Schema::create(
            'articles', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('trail_text')->nullable();
            $table->text('stand_first')->nullable();
            $table->longText('main')->nullable(); // for example, the guardian have this
            $table->longText('content');
            $table->text('url')->unique();  // URL of the article
            $table->longText('featured_image')->nullable();
            $table->timestamp('published_at');
            $table->foreignId('source_id')->nullable()->constrained('sources')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('authors')->cascadeOnDelete();
            $table->timestamps();
        }
        );
    }

    public function down(): void {
        Schema::table(
            'articles', function (Blueprint $table) {
            $table->dropForeign('articles_source_id_foreign');
            $table->dropForeign('articles_category_id_foreign');
            $table->dropForeign('articles_author_id_foreign');
        }
        );

        Schema::table(
            'sources', function (Blueprint $table) {
            $table->dropForeign('sources_api_provider_id_foreign');
        }
        );

        Schema::dropIfExists('articles');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sources');
        Schema::dropIfExists('api_providers');
    }
};
