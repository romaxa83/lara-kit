<?php

namespace Tests\Builders\Admins;

use App\Modules\Admin\Models\Admin;
use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\Dto\PhoneDto;
use App\Modules\Utils\Phones\Services\PhoneService;
use App\Modules\Utils\Phones\ValueObject\Phone;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Tests\Builders\BaseBuilder;
use Tests\Builders\Permissions\RoleBuilder;

class AdminBuilder extends BaseBuilder
{
    protected ?Role $role = null;

    protected ?string $phone = null;
    protected bool $phoneVerify = false;
    protected array $perms = [];

    function modelClass(): string
    {
        return Admin::class;
    }

    public function password(string $value): self
    {
        $this->data['password'] = Hash::make($value);
        return $this;
    }

    public function role(Role $model): self
    {
        $this->role = $model;
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

    public function permissions(Permission ...$values): self
    {
        foreach ($values as $value) {
            $this->perms[] = $value->id;
        }
        return $this;
    }

    public function asSuperAdmin(): self
    {
        /** @var $builder RoleBuilder */
        $builder = resolve(RoleBuilder::class);
        $this->role = $builder->asSuperAdmin()->create();

        return $this;
    }

    protected function afterSave($model): void
    {
        /** @var $model Admin */
        if(!$this->role){
            /** @var $builder RoleBuilder */
            $builder = resolve(RoleBuilder::class);
            $this->role = $builder->asAdmin()->create();

            if($this->role->permissions->isEmpty() && !empty($this->perms)){
                $this->role->permissions()->attach($this->perms);
            }
        }
        $model->assignRole($this->role);

        if($this->phone){
            /** @var $phoneService PhoneService */
            $phoneService = resolve(PhoneService::class);
            $phoneService->create(
                $model,
                PhoneDto::byArgs([
                    'phone' => new Phone($this->phone),
                    'default' => true,
                    'verify' => $this->phoneVerify
                ]),
            );

            $model->refresh();
        }
    }

    protected function afterClear(): void
    {
        $this->role = null;
        $this->phone = null;
        $this->phoneVerify = false;
        $this->perms = [];
    }
}
