<?php

use App\Modules\Admin\Models\Admin;
use App\Modules\Localization\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create(Admin::TABLE,
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('email_verification_code', 16)->nullable();
                $table->string('password');

                $table->string('lang', 3);
                $table->foreign('lang')
                    ->references('slug')
                    ->on(Language::TABLE)
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->softDeletes();
                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(Admin::TABLE);
    }
};
