<?php

namespace Tests\Unit\Modules\Admin\Actions;

use App\Modules\Admin\Actions\AdminCreateAction;
use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Events\AdminCreatedEvent;
use App\Modules\Admin\Listeners\SendAdminCredentialsListener;
use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\Exceptions\PhoneServiceException;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\ValueObject\Phone as PhoneObj;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;

class AdminCreateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;
    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->data = [
            'name' => 'admin',
            'email' => 'test@test.com',
            'password' => 'password',
            'phone' => '+38(095)451-49-49',
        ];

        $this->langInit();
    }

    /** @test */
    public function success_create()
    {
        Event::fake([AdminCreatedEvent::class]);

        $lang_1 = $this->langBuilder->default()->active()->create();
        $lang_2 = $this->langBuilder->active()->create();
        Lang::setLocale($lang_1->slug);

        $perm_1 = $this->permissionBuilder->create();
        $perm_2 = $this->permissionBuilder->create();

        /** @var $role Role */
        $role = $this->roleBuilder->permissions($perm_2, $perm_1)->create();

        $data = $this->data;
        $data['role'] = $role;
        $data['lang'] = $lang_2->slug;

        $this->assertNull(Admin::query()->where('email', $data['email'])->first());

        /** @var $handler AdminCreateAction */
        $handler = resolve(AdminCreateAction::class);
        $model = $handler->exec(AdminDto::byArgs($data));

        $this->assertTrue($model instanceof Admin);

        $this->assertEquals($model->name, $data['name']);
        $this->assertEquals($model->email, $data['email']);
        $this->assertTrue(password_verify($data['password'], $model->password));
        $this->assertEquals($model->lang, $data['lang']);

        $this->assertCount(1, $model->phones);
        $this->assertEquals(
            $model->phone->phone,
            phone_clear($data['phone'])
        );
        $this->assertFalse($model->isPhoneVerified());

        $this->assertCount(1, $model->roles);
        $this->assertEquals($model->role->id, $role->id);

        $this->assertCount(2, $model->role->permissions);

        Event::assertDispatched(fn (AdminCreatedEvent $event) =>
            $event->getModel()->id === $model->id
            && $event->getDto()->password === data_get($this->data, 'password')
        );
        Event::assertListening(AdminCreatedEvent::class, SendAdminCredentialsListener::class);
    }

    /** @test */
    public function success_create_verified_phone()
    {
        $lang_1 = $this->langBuilder->default()->active()->create();
        Lang::setLocale($lang_1->slug);

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role;

        /** @var $handler AdminCreateAction */
        $handler = resolve(AdminCreateAction::class);
        $model = $handler->exec(AdminDto::byArgs($data), verifyPhone: true);

        $this->assertTrue($model instanceof Admin);

        $this->assertCount(1, $model->phones);
        $this->assertTrue($model->phone instanceof Phone);
        $this->assertTrue($model->phone->phone instanceof PhoneObj);
        $this->assertEquals(
            $model->phone->phone,
            phone_clear($data['phone'])
        );

        $this->assertTrue($model->isPhoneVerified());
    }

    /** @test */
    public function success_create_without_phone()
    {
        $lang_1 = $this->langBuilder->default()->active()->create();
        Lang::setLocale($lang_1->slug);

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role->id;
        unset($data['phone']);

        /** @var $handler AdminCreateAction */
        $handler = resolve(AdminCreateAction::class);
        $model = $handler->exec(AdminDto::byArgs($data));

        $this->assertEquals($model->lang, default_lang()->slug);

        $this->assertCount(1, $model->roles);
        $this->assertEquals($model->role->id, $role->id);

        $this->assertCount(0, $model->phones);
        $this->assertNull($model->phone);
    }

    /** @test */
    public function success_create_many_phone()
    {
        Event::fake([AdminCreatedEvent::class]);

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role;
        unset($data['phone']);
        $data['phones'] = [
            [
                'phone' => '380954444444',
                'default' => true,
                'desc' => 'some desc phone 1'
            ],
            [
                'phone' => '380954444445',
                'default' => false,
                'desc' => 'some desc phone 2'
            ]
        ];

        $this->assertFalse(Admin::query()->where('email', $data['email'])->exists());

        /** @var $handler AdminCreateAction */
        $handler = resolve(AdminCreateAction::class);
        $model = $handler->exec(AdminDto::byArgs($data));

        foreach ($data['phones'] as $item){
            /** @var $p Phone */
            $p = $model->phones->where('phone', $item['phone'])->first();
            $this->assertEquals($p->default, $item['default']);
            $this->assertEquals($p->desc, $item['desc']);
            $this->assertFalse($p->isVerify());
        }

        $this->assertEquals($model->phone->phone->asString(), $data['phones'][0]['phone']);
    }

    /** @test */
    public function success_create_many_phone_as_verify()
    {
        Event::fake([AdminCreatedEvent::class]);

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role;
        unset($data['phone']);
        $data['phones'] = [
            [
                'phone' => '380954444444',
                'default' => false,
                'desc' => 'some desc phone 1'
            ],
            [
                'phone' => '380954444445',
                'default' => false,
                'desc' => null
            ]
        ];

        $this->assertFalse(Admin::query()->where('email', $data['email'])->exists());

        /** @var $handler AdminCreateAction */
        $handler = resolve(AdminCreateAction::class);
        $model = $handler->exec(AdminDto::byArgs($data), true);

        foreach ($data['phones'] as $item){
            /** @var $p Phone */
            $p = $model->phones->where('phone', $item['phone'])->first();
            $this->assertEquals($p->default, $item['default']);
            $this->assertEquals($p->desc, $item['desc']);
            $this->assertTrue($p->isVerify());
        }

        $this->assertNull($model->phone);
    }

    /** @test */
    public function fail_create_many_phone_duplicate()
    {
        Event::fake([AdminCreatedEvent::class]);

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role;
        unset($data['phone']);
        $data['phones'] = [
            [
                'phone' => '380954444444',
                'default' => false,
                'desc' => 'some desc phone 1',
            ],
            [
                'phone' => '380954444444',
                'default' => false,
                'desc' => null
            ]
        ];

        $this->assertFalse(Admin::query()->where('email', $data['email'])->exists());

        $this->expectException(PhoneServiceException::class);
        $this->expectExceptionMessage(__("exceptions.phone.duplicate_phone"));

        /** @var $handler AdminCreateAction */
        $handler = resolve(AdminCreateAction::class);
        $handler->exec(AdminDto::byArgs($data), true);
    }
}
