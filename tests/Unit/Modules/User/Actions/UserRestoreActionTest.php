<?php

namespace Tests\Unit\Modules\User\Actions;

use App\Modules\Permissions\Models\Role;
use App\Modules\User\Actions\UserRestoreAction;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class UserRestoreActionTest extends TestCase
{
    use DatabaseTransactions;

    protected AdminBuilder $adminBuilder;
    protected UserBuilder $userBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);

        $this->langInit();

    }

    /** @test */
    public function success_delete_as_model()
    {
        /** @var $model User */
        $model = $this->userBuilder->trashed()->create();

        $modelId = $model->id;

        $this->assertFalse(User::query()->where('id', $modelId)->exists());
        $this->assertTrue(User::query()->onlyTrashed()->where('id', $modelId)->exists());

        /** @var $handler UserRestoreAction */
        $handler = resolve(UserRestoreAction::class);
        $this->assertTrue($handler->exec($model));

        $this->assertTrue(User::query()->where('id', $modelId)->exists());
        $this->assertFalse(User::query()->onlyTrashed()->where('id', $modelId)->exists());
    }

    /** @test */
    public function success_delete_as_id()
    {
        /** @var $model User */
        $model = $this->userBuilder->trashed()->create();

        $modelId = $model->id;

        $this->assertFalse(User::query()->where('id', $modelId)->exists());
        $this->assertTrue(User::query()->onlyTrashed()->where('id', $modelId)->exists());

        /** @var $handler UserRestoreAction */
        $handler = resolve(UserRestoreAction::class);
        $this->assertTrue($handler->exec($modelId));

        $this->assertTrue(User::query()->where('id', $modelId)->exists());
        $this->assertFalse(User::query()->onlyTrashed()->where('id', $modelId)->exists());
    }

    /** @test */
    public function success_delete_as_ids()
    {
        /** @var $model User */
        $model = $this->userBuilder->trashed()->create();
        $model_2 = $this->userBuilder->trashed()->create();

        $modelId = $model->id;
        $modelId_2 = $model_2->id;

        $this->assertFalse(User::query()->where('id', $modelId)->exists());
        $this->assertFalse(User::query()->where('id', $modelId_2)->exists());

        /** @var $handler UserRestoreAction */
        $handler = resolve(UserRestoreAction::class);
        $this->assertTrue($handler->exec([$model->id, $model_2->id]));

        $this->assertTrue(User::query()->where('id', $modelId)->exists());
        $this->assertTrue(User::query()->where('id', $modelId_2)->exists());
    }

    /** @test */
    public function success_delete_as_collection()
    {
        /** @var $model User */
        $model = $this->userBuilder->trashed()->create();
        $model_2 = $this->userBuilder->trashed()->create();

        $modelId = $model->id;
        $modelId_2 = $model_2->id;

        $collections = User::query()->whereIn('id', [$modelId, $modelId_2])->get();

        /** @var $handler UserRestoreAction */
        $handler = resolve(UserRestoreAction::class);
        $this->assertTrue($handler->exec($collections));

        $this->assertFalse(Role::query()->where('id', $modelId)->exists());
        $this->assertFalse(Role::query()->where('id', $modelId_2)->exists());
    }

    /** @test */
    public function success_delete_as_collection_if_one_not_trashed()
    {
        /** @var $model User */
        $model = $this->userBuilder->create();
        $model_2 = $this->userBuilder->trashed()->create();

        $modelId = $model->id;
        $modelId_2 = $model_2->id;

        $collections = User::query()->whereIn('id', [$modelId, $modelId_2])->get();

        /** @var $handler UserRestoreAction */
        $handler = resolve(UserRestoreAction::class);
        $this->assertTrue($handler->exec($collections));

        $this->assertFalse(Role::query()->where('id', $modelId)->exists());
        $this->assertFalse(Role::query()->where('id', $modelId_2)->exists());
    }
}
