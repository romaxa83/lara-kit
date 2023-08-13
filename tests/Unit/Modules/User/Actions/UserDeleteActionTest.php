<?php

namespace Tests\Unit\Modules\User\Actions;

use App\Modules\User\Actions\UserDeleteAction;
use App\Modules\User\Collections\UserEloquentCollection;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Models\Phone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class UserDeleteActionTest extends TestCase
{
    use DatabaseTransactions;
    protected UserBuilder $userBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->userBuilder = resolve(UserBuilder::class);
    }

    /** @test */
    public function success_delete()
    {
        /** @var $model User */
        $model = $this->userBuilder->phone()->create();
        $id = $model->id;

        $this->assertTrue(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );

        /** @var $handler UserDeleteAction */
        $handler = resolve(UserDeleteAction::class);
        $res = $handler->exec($model);

        $this->assertTrue($res);

        $this->assertFalse(User::query()->where('id', $id)->exists());
        $this->assertTrue(User::query()->withTrashed()->where('id', $id)->exists());

        $this->assertTrue(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );
    }

    /** @test */
    public function success_force_delete()
    {
        /** @var $model User */
        $model = $this->userBuilder->phone()->create();
        $id = $model->id;

        $this->assertTrue(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );

        /** @var $handler UserDeleteAction */
        $handler = resolve(UserDeleteAction::class);
        $res = $handler->exec($model, true);

        $this->assertTrue($res);

        $this->assertFalse(User::query()->where('id', $id)->exists());
        $this->assertFalse(User::query()->withTrashed()->where('id', $id)->exists());

        $this->assertFalse(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->where('model_id', $id)
                ->exists()
        );
    }

    /** @test */
    public function success_force_delete_if_not_phone()
    {
        /** @var $model User */
        $model = $this->userBuilder->create();
        $id = $model->id;

        $this->assertNull($model->phone);

        /** @var $handler UserDeleteAction */
        $handler = resolve(UserDeleteAction::class);
        $res = $handler->exec($model, true);

        $this->assertTrue($res);

        $this->assertFalse(User::query()->where('id', $id)->exists());
        $this->assertFalse(User::query()->withTrashed()->where('id', $id)->exists());
    }

    /** @test */
    public function success_delete_many()
    {
        /** @var $model User */
        $model = $this->userBuilder->phone()->create();
        $model_2 = $this->userBuilder->phone()->create();
        $id = $model->id;
        $id_2 = $model->id;

        $this->assertTrue(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );

        /** @var $models UserEloquentCollection */
        $models = User::query()->whereIn('id', [$id, $id_2])->get();

        /** @var $handler UserDeleteAction */
        $handler = resolve(UserDeleteAction::class);
        $res = $handler->exec($models);

        $this->assertTrue($res);

        $this->assertFalse(User::query()->where('id', $id)->exists());
        $this->assertTrue(User::query()->withTrashed()->where('id', $id)->exists());

        $this->assertFalse(User::query()->where('id', $id_2)->exists());
        $this->assertTrue(User::query()->withTrashed()->where('id', $id_2)->exists());

        $this->assertTrue(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );
    }

    /** @test */
    public function success_delete_many_force()
    {
        /** @var $model User */
        $model = $this->userBuilder->phone()->create();
        $model_2 = $this->userBuilder->phone()->create();
        $id = $model->id;
        $id_2 = $model_2->id;

        /** @var $models UserEloquentCollection */
        $models = User::query()->whereIn('id', [$id, $id_2])->get();

        $this->assertTrue(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );

        /** @var $handler UserDeleteAction */
        $handler = resolve(UserDeleteAction::class);
        $res = $handler->exec($models, true);

        $this->assertTrue($res);

        $this->assertFalse(User::query()->where('id', $id)->exists());
        $this->assertFalse(User::query()->withTrashed()->where('id', $id)->exists());

        $this->assertFalse(User::query()->where('id', $id_2)->exists());
        $this->assertFalse(User::query()->withTrashed()->where('id', $id_2)->exists());

        $this->assertFalse(
            Phone::query()
                ->where('model_type', User::MORPH_NAME)
                ->whereIn('model_id', [$id, $id_2])
                ->exists()
        );
    }
}
