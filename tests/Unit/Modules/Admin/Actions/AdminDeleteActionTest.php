<?php

namespace Tests\Unit\Modules\Admin\Actions;

use App\Modules\Admin\Actions\AdminDeleteAction;
use App\Modules\Admin\Collections\AdminEloquentCollection;
use App\Modules\Admin\Models\Admin;
use App\Modules\Utils\Phones\Models\Phone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class AdminDeleteActionTest extends TestCase
{
    use DatabaseTransactions;
    protected AdminBuilder $adminBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
    }

    /** @test */
    public function success_delete()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->phone()->create();
        $id = $model->id;

        $this->assertTrue(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );

        /** @var $handler AdminDeleteAction */
        $handler = resolve(AdminDeleteAction::class);
        $res = $handler->exec($model);

        $this->assertTrue($res);

        $this->assertFalse(Admin::query()->where('id', $id)->exists());
        $this->assertTrue(Admin::query()->withTrashed()->where('id', $id)->exists());

        $this->assertTrue(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );
    }

    /** @test */
    public function success_force_delete()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->phone()->create();
        $id = $model->id;

        $this->assertTrue(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );

        /** @var $handler AdminDeleteAction */
        $handler = resolve(AdminDeleteAction::class);
        $res = $handler->exec($model, true);

        $this->assertTrue($res);

        $this->assertFalse(Admin::query()->where('id', $id)->exists());
        $this->assertFalse(Admin::query()->withTrashed()->where('id', $id)->exists());

        $this->assertFalse(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );
    }

    /** @test */
    public function success_delete_many()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->phone()->create();
        $model_2 = $this->adminBuilder->phone()->create();
        $id = $model->id;
        $id_2 = $model->id;

        $this->assertTrue(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );

        /** @var $models AdminEloquentCollection */
        $models = Admin::query()->whereIn('id', [$id, $id_2])->get();

        /** @var $handler AdminDeleteAction */
        $handler = resolve(AdminDeleteAction::class);
        $res = $handler->exec($models);

        $this->assertTrue($res);

        $this->assertFalse(Admin::query()->where('id', $id)->exists());
        $this->assertTrue(Admin::query()->withTrashed()->where('id', $id)->exists());

        $this->assertFalse(Admin::query()->where('id', $id_2)->exists());
        $this->assertTrue(Admin::query()->withTrashed()->where('id', $id_2)->exists());

        $this->assertTrue(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );
    }

    /** @test */
    public function success_delete_many_force()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->phone()->create();
        $model_2 = $this->adminBuilder->phone()->create();
        $id = $model->id;
        $id_2 = $model_2->id;

        /** @var $models AdminEloquentCollection */
        $models = Admin::query()->whereIn('id', [$id, $id_2])->get();

        $this->assertTrue(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );

        /** @var $handler AdminDeleteAction */
        $handler = resolve(AdminDeleteAction::class);
        $res = $handler->exec($models, true);

        $this->assertTrue($res);

        $this->assertFalse(Admin::query()->where('id', $id)->exists());
        $this->assertFalse(Admin::query()->withTrashed()->where('id', $id)->exists());

        $this->assertFalse(Admin::query()->where('id', $id_2)->exists());
        $this->assertFalse(Admin::query()->withTrashed()->where('id', $id_2)->exists());

        $this->assertFalse(
            Phone::query()
                ->where('model_type', Admin::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );
    }
}
