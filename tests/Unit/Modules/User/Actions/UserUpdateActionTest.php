<?php

namespace Tests\Unit\Modules\User\Actions;

use App\Modules\Localization\Models\Language;
use App\Modules\User\Actions\UserUpdateAction;
use App\Modules\User\Dto\UserDto;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\ValueObject\Phone;
use App\Modules\Utils\Phones\Models\Phone as PhoneModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class UserUpdateActionTest extends TestCase
{
    use DatabaseTransactions;

    protected LanguageBuilder $langBuilder;
    protected UserBuilder $userBuilder;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);

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

        /** @var $model User */
        $model = $this->userBuilder
            ->phone('380954514950', true)
            ->email(verify: true)
            ->create();

        $data = $this->data;
        $data['lang'] = $lang->slug;

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertNotEquals($model->email, $data['email']);
        $this->assertTrue($model->isEmailVerified());
        $this->assertNotEquals($model->lang, $data['lang']);
        $this->assertFalse(password_verify($data['password'], $model->password));
        $this->assertFalse($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());

        /** @var $handler UserUpdateAction */
        $handler = resolve(UserUpdateAction::class);
        $model = $handler->exec($model, UserDto::byArgs($data));

        $this->assertTrue($model instanceof User);
        $this->assertTrue($model->phone instanceof PhoneModel);
        $this->assertTrue($model->phone->phone instanceof Phone);

        $this->assertEquals($model->name, $data['name']);
        $this->assertEquals($model->email, $data['email']);
        $this->assertFalse($model->isEmailVerified());
        $this->assertEquals($model->lang, $data['lang']);
        $this->assertTrue(password_verify($data['password'], $model->password));
        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertFalse($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_only_name()
    {
        /** @var $model User */
        $model = $this->userBuilder->phone('380954514950', true)
            ->email(verify: true)
            ->create();

        $data = [
            'name' => 'user',
            'email' => $model->email,
            'phone' => $model->phone->phone,
            'lang' => $model->lang,
        ];

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertTrue($model->isPhoneVerified());
        $this->assertTrue($model->isEmailVerified());

        /** @var $handler UserUpdateAction */
        $handler = resolve(UserUpdateAction::class);
        $model = $handler->exec($model, UserDto::byArgs($data));

        $this->assertEquals($model->name, $data['name']);
        $this->assertEquals($model->email, $data['email']);
        $this->assertTrue($model->isEmailVerified());
        $this->assertEquals($model->lang, $data['lang']);
        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_verify_phone()
    {
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->active()->create();

        /** @var $model User */
        $model = $this->userBuilder->phone('380954514950', true)
            ->lang($lang)
            ->email(verify: true)
            ->create();

        $data = $this->data;

        $this->assertFalse($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());

        /** @var $handler UserUpdateAction */
        $handler = resolve(UserUpdateAction::class);
        $model = $handler->exec($model, UserDto::byArgs($data), verifyPhone: true);

        $this->assertEquals($model->lang, $lang->slug);
        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_and_create_phone()
    {
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->active()->create();

        /** @var $model User */
        $model = $this->userBuilder
            ->lang($lang)
            ->email(verify: true)
            ->create();

        $data = $this->data;
        $data['role'] = $model->role->id;

        $this->assertNull($model->phone);

        /** @var $handler UserUpdateAction */
        $handler = resolve(UserUpdateAction::class);
        $model = $handler->exec($model, UserDto::byArgs($data));

        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertFalse($model->isPhoneVerified());
    }

    /** @test */
    public function success_update_and_create_phone_verify()
    {
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->active()->create();

        /** @var $model User */
        $model = $this->userBuilder
            ->lang($lang)
            ->email(verify: true)
            ->create();

        $data = $this->data;

        $this->assertNull($model->phone);

        /** @var $handler UserUpdateAction */
        $handler = resolve(UserUpdateAction::class);
        $model = $handler->exec($model, UserDto::byArgs($data), verifyPhone: true);

        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());
    }
}
