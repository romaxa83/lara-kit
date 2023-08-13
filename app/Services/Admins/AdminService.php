<?php

namespace App\Services\Admins;


use App\Services\Localizations\LocalizationService;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminService
{
    public function __construct(private LocalizationService $localizationService)
    {
    }

//    public function create(AdminDto $dto): Admin
//    {
//        $admin = new Admin();
//        $admin->name = $dto->getName();
//        $admin->email = $dto->getEmail();
//        $admin->setPassword($dto->getPassword());
//
//        $admin->lang = $this->localizationService->getDefaultSlug();
//        $admin->save();
//
//        if ($dto->hasRoleId()) {
//            $admin->assignRole($dto->getRoleId());
//        }
//
//        return $admin;
//    }


//    public function update(Admin $admin, AdminDto $dto): Admin
//    {
//        $admin->name = $dto->getName();
//        $admin->email = $dto->getEmail();
//
//        if ($dto->hasPassword()) {
//            $admin->setPassword($dto->getPassword());
//        }
//
//        if ($dto->hasRoleId()) {
//            $admin->syncRoles($dto->getRoleId());
//        }
//
//        if ($admin->isDirty()) {
//            $admin->save();
//        }
//
//        return $admin;
//    }
//
//    public function changePassword(Authenticatable|Admin $admin, string $password): bool
//    {
//        return $admin->setPassword($password)
//            ->save();
//    }
}
