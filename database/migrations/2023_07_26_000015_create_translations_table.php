<?php

use App\Modules\Localization\Models\Language;
use App\Modules\Localization\Models\Translation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create(Translation::TABLE,
            function (Blueprint $table) {
                $table->id();

                $table->string('place', 50);
                $table->string('key', 500);
                $table->string('text', 1000)->nullable();

                $table->string('lang', 3);
                $table->foreign('lang')
                    ->references('slug')
                    ->on(Language::TABLE)
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->timestamps();
                $table->unique(['place', 'key', 'lang']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(Translation::TABLE);
    }
};
