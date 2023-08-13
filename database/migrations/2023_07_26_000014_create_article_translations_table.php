<?php

use App\Modules\Articles\Models\Article;
use App\Modules\Articles\Models\ArticleTranslation;
use App\Modules\Localization\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(ArticleTranslation::TABLE,
            function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedBigInteger('row_id')->unsigned();
                $table->foreign('row_id')
                    ->references('id')
                    ->on(Article::TABLE)
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('lang', 3);
                $table->foreign('lang')
                    ->references('slug')
                    ->on(Language::TABLE)
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('title', 500);
                $table->text('text');
                $table->string('description', 2000)->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(ArticleTranslation::TABLE);
    }
};


