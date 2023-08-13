<?php

use App\Modules\Localization\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(Language::TABLE,
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 3)->unique();
                $table->string('locale', 8)->unique();
                $table->boolean('default')->default(true);
                $table->boolean('active')->default(true);
                $table->integer('sort')->default(0);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(Language::TABLE);
    }
};
