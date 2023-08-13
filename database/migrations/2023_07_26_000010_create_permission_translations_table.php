<?php

use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionTranslation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            PermissionTranslation::TABLE,
            function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedBigInteger('row_id')->unsigned();
                $table->foreign('row_id')
                    ->references('id')
                    ->on(Permission::TABLE)
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('lang', 3);
                $table->foreign('lang')
                    ->references('slug')
                    ->on(Language::TABLE)
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('title');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(PermissionTranslation::TABLE);
    }
};
