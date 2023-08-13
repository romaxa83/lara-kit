<?php

namespace Tests\Unit\Modules\Admin\Actions;

use App\Modules\Admin\Actions\AdminUpdateAction;
use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Models\Admin;
use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\ValueObject\Phone;
use App\Modules\Utils\Phones\Models\Phone as PhoneModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;

class AdminUpdateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;
    protected RoleBuilder $roleBuilder;
    protected AdminBuilder $adminBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);

        $this->data = [
            'name' => 'admin',
            'email' => 'test@test.com',
            'password' => 'password1',
            'phone' => '380954514949',
        ];
    }

    /** @test */
    public function success_update()
    {
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->active()->create();
        /** @var $role Role */
        $role = $this->roleBuilder->create();
        /** @var $model Admin */
        $model = $this->adminBuilder
            ->phone('380954514950', true)
            ->email(verify: true)
            ->create();

        $data = $this->data;
        $data['role'] = $role;
        $data['lang'] = $lang->slug;

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertNotEquals($model->email, $data['email']);
        $this->assertTrue($model->isEmailVerified());
        $this->assertNotEquals($model->role->id, $role->id);
        $this->assertNotEquals($model->lang, $data['lang']);
        $this->assertFalse(password_verify($data['password'], $model->password));
        $this->assertFalse($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());

        /** @var $handler AdminUpdateAction */
        $handler = resolve(AdminUpdateAction::class);
        $model = $handler->exec($model, AdminDto::byArgs($data));

        $this->assertTrue($model instanceof Admin);
        $this->assertTrue($model->phone instanceof PhoneModel);
        $this->assertTrue($model->phone->phone instanceof Phone);

        $this->assertEquals($model->name, $data['name']);
        $this->assertEquals($model->email, $data['email']);
        $this->assertFalse($model->isEmailVerified());
        $this->assertEquals($model->role->id, $role->id);
        $this->assertEquals($model->lang, $data['lang']);
        $this->assertTrue(password_verify($data['password'], $model->password));
        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertFalse($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_only_name()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->phone('380954514950', true)
            ->email(verify: true)
            ->create();

        $data = [
            'name' => 'admin',
            'email' => $model->email,
            'phone' => $model->phone->phone,
            'lang' => $model->lang,
            'role' => $model->role->id,
        ];

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertTrue($model->isPhoneVerified());
        $this->assertTrue($model->isEmailVerified());

        /** @var $handler AdminUpdateAction */
        $handler = resolve(AdminUpdateAction::class);
        $model = $handler->exec($model, AdminDto::byArgs($data));

        $this->assertEquals($model->name, $data['name']);
        $this->assertEquals($model->email, $data['email']);
        $this->assertTrue($model->isEmailVerified());
        $this->assertEquals($model->role->id, $data['role']);
        $this->assertEquals($model->lang, $data['lang']);
        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_verify_phone()
    {
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->active()->create();

        /** @var $model Admin */
        $model = $this->adminBuilder->phone('380954514950', true)
            ->lang($lang)
            ->email(verify: true)
            ->create();

        $data = $this->data;
        $data['role'] = $model->role->id;

        $this->assertFalse($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());

        /** @var $handler AdminUpdateAction */
        $handler = resolve(AdminUpdateAction::class);
        $model = $handler->exec($model, AdminDto::byArgs($data), verifyPhone: true);

        $this->assertEquals($model->lang, $lang->slug);
        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_and_create_phone()
    {
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->active()->create();

        /** @var $model Admin */
        $model = $this->adminBuilder
            ->lang($lang)
            ->email(verify: true)
            ->create();

        $data = $this->data;
        $data['role'] = $model->role->id;

        $this->assertNull($model->phone);

        /** @var $handler AdminUpdateAction */
        $handler = resolve(AdminUpdateAction::class);
        $model = $handler->exec($model, AdminDto::byArgs($data));

        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertFalse($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_and_create_phone_verify()
    {
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->active()->create();

        /** @var $model Admin */
        $model = $this->adminBuilder
            ->lang($lang)
            ->email(verify: true)
            ->create();

        $data = $this->data;
        $data['role'] = $model->role->id;

        $this->assertNull($model->phone);

        /** @var $handler AdminUpdateAction */
        $handler = resolve(AdminUpdateAction::class);
        $model = $handler->exec($model, AdminDto::byArgs($data), verifyPhone: true);

        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());
    }
}

