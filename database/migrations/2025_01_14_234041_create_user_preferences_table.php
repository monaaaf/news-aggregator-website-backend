<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(
            'user_source', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
            $table->timestamps();
        }
        );

        Schema::create(
            'user_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
        }
        );

        Schema::create(
            'user_author', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->timestamps();
        }
        );
    }

    public function down(): void {
        Schema::table(
            'user_author', function (Blueprint $table) {
            $table->dropForeign('user_author_user_id_foreign');
            $table->dropForeign('user_author_author_id_foreign');
        }
        );

        Schema::table(
            'user_category', function (Blueprint $table) {
            $table->dropForeign('user_category_user_id_foreign');
            $table->dropForeign('user_category_category_id_foreign');
        }
        );

        Schema::table(
            'user_source', function (Blueprint $table) {
            $table->dropForeign('user_source_user_id_foreign');
            $table->dropForeign('user_source_source_id_foreign');
        }
        );

        Schema::dropIfExists('user_author');
        Schema::dropIfExists('user_category');
        Schema::dropIfExists('user_source');
    }
};
