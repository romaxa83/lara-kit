<?php

namespace Tests\Unit\Modules\Admin\Actions;

use App\Modules\Admin\Actions\AdminChangePasswordAction;
use App\Modules\Admin\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class AdminChangePasswordActionTest extends TestCase
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
    public function success_change()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $password = 'newPassword123';

        $this->assertFalse(password_verify($password, $model->password));

        /** @var $handler AdminChangePasswordAction */
        $handler = resolve(AdminChangePasswordAction::class);
        $model = $handler->exec($model, $password);

        $this->assertTrue($model instanceof Admin);

        $this->assertTrue(password_verify($password, $model->password));
    }
}
