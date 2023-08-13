<?php

use App\Modules\Articles\Models\Category;
use App\Modules\Articles\Models\CategoryTranslation;
use App\Modules\Localization\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(CategoryTranslation::TABLE,
            function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedBigInteger('row_id')->unsigned();
                $table->foreign('row_id')
                    ->references('id')
                    ->on(Category::TABLE)
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('lang', 3);
                $table->foreign('lang')
                    ->references('slug')
                    ->on(Language::TABLE)
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('title', 500);
                $table->string('description', 2000)->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(CategoryTranslation::TABLE);
    }
};

