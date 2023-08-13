<?php

namespace Tests\Unit\Modules\User\Actions;

use App\Modules\User\Actions\UserChangePasswordAction;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class UserChangePasswordActionTest extends TestCase
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
    public function success_change()
    {
        /** @var $model User */
        $model = $this->userBuilder->create();

        $password = 'newPassword123';

        $this->assertFalse(password_verify($password, $model->password));

        /** @var $handler UserChangePasswordAction */
        $handler = resolve(UserChangePasswordAction::class);
        $model = $handler->exec($model, $password);

        $this->assertTrue($model instanceof User);

        $this->assertTrue(password_verify($password, $model->password));
    }
}
