<?php

use App\Modules\Articles\Enums\ArticleStatus;
use App\Modules\Articles\Models\Article;
use App\Modules\Articles\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(Article::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(ArticleStatus::DRAFT());

            $table->unsignedBigInteger('category_id')->unsigned();
            $table->foreign('category_id')
                ->references('id')
                ->on(Category::TABLE)
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Article::TABLE);
    }
};

