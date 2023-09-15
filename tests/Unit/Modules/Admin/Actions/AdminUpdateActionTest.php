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
use Tests\Builders\Utils\PhoneBuilder;
use Tests\TestCase;

class AdminUpdateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;
    protected RoleBuilder $roleBuilder;
    protected AdminBuilder $adminBuilder;
    protected PhoneBuilder $phoneBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->phoneBuilder = resolve(PhoneBuilder::class);

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

    /** @test */
    public function success_update_and_create_phones()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $phone_1 = $this->phoneBuilder->model($model)
            ->verify()
            ->sort(1)
            ->default()
            ->desc('some desc')
            ->create();
        $phone_2 = $this->phoneBuilder->model($model)
            ->default(false)
            ->sort(2)
            ->create();

        $data = $this->data;
        $data['role'] = $model->role->id;
        unset($data['phone']);
        $data['phones'] = [
            [
                'phone' => $phone_1->phone->asString(),
                'default' => false,
                'desc' => 'office'
            ],
            [
                'phone' => $phone_2->phone->asString(),
                'default' => false,
                'desc' => 'office 2'
            ],
            [
                'phone' => '380934444444',
                'default' => true,
                'desc' => null
            ]
        ];

        $model->refresh();

        $this->assertEquals($model->phone->phone->asString(), $phone_1->phone->asString());

        $this->assertCount(2, $model->phones);

        $this->assertEquals(
            $model->phones[0]->phone->asString(),
            $data['phones'][0]['phone']
        );
        $this->assertNotEquals(
            $model->phones[0]->desc,
            $data['phones'][0]['desc']
        );

        $this->assertEquals(
            $model->phones[1]->phone->asString(),
            $data['phones'][1]['phone']
        );
        $this->assertNotEquals(
            $model->phones[1]->desc,
            $data['phones'][1]['desc']
        );

        /** @var $handler AdminUpdateAction */
        $handler = resolve(AdminUpdateAction::class);
        $model = $handler->exec($model, AdminDto::byArgs($data));

        $model->refresh();

        $this->assertEquals($model->phone->phone->asString(), $data['phones'][2]['phone']);

        $this->assertCount(3, $model->phones);

//        dd($model->phones);

        $this->assertEquals(
            $model->phones[0]->desc,
            $data['phones'][0]['desc']
        );
        $this->assertEquals(
            $model->phones[1]->desc,
            $data['phones'][1]['desc']
        );
        $this->assertEquals(
            $model->phones[2]->desc,
            $data['phones'][2]['desc']
        );
        $this->assertEquals(
            $model->phones[2]->phone->asString(),
            $data['phones'][2]['phone']
        );
    }
}

