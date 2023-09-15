<?php

use App\Modules\Utils\Phones\Models\Phone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create(Phone::TABLE,
            function (Blueprint $table) {
                $table->id();
                $table->morphs('model');
                $table->string('phone', 24);
                $table->timestamp('phone_verified_at')->nullable();
                $table->boolean('default');
                $table->string('code', 10)->nullable();
                $table->timestamp('code_expired_at')->nullable();
                $table->string('desc', 1000)->nullable();
                $table->integer('sort')->default(1);

                $table->unique(['model_id', 'model_type', 'phone']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(Phone::TABLE);
    }
};
