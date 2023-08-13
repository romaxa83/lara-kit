<?php

namespace Tests\Builders\Users;

use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Models\Role;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\PhoneService;
use App\Modules\Utils\Phones\ValueObject\Phone;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Tests\Builders\BaseBuilder;
use Tests\Builders\Permissions\RoleBuilder;

class UserBuilder extends BaseBuilder
{
    protected ?string $phone = null;
    protected bool $phoneVerify = false;

    function modelClass(): string
    {
        return User::class;
    }

    public function password(string $value): self
    {
        $this->data['password'] = Hash::make($value);
        return $this;
    }

    public function phone(?string $value = null, bool $verify = false): self
    {
        if(!$value){
            $value = $this->faker->phoneNumber();
        }
        $this->phone = $value;
        $this->phoneVerify = $verify;
        return $this;
    }

    public function lang(Language $model): self
    {
        $this->data['lang'] = $model->slug;
        return $this;
    }

    public function email(?string $value = null, bool $verify = false): self
    {
        if(!$value){
            $value = $this->faker->email();
        }
        if($verify){
            $this->data['email_verified_at'] = CarbonImmutable::now();
        }
        $this->data['email'] = $value;
        return $this;
    }

    protected function afterSave($model): void
    {
        if(!$role = Role::query()->where('name', BaseRole::USER)->first()){
            /** @var $builder RoleBuilder */
            $builder = resolve(RoleBuilder::class);
            $role = $builder->asUser()->create();
        }

        $model->assignRole($role);

        if($this->phone){
            /** @var $phoneService PhoneService */
            $phoneService = resolve(PhoneService::class);
            $phoneService->create(
                $model,
                new Phone($this->phone),
                $this->phoneVerify
            );

            $model->refresh();
        }
    }

    protected function afterClear(): void
    {
        $this->phone = null;
        $this->phoneVerify = false;
    }
}
