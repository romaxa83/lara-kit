<?php

namespace Database\Seeders;

use App\Modules\Admin\Actions\AdminCreateAction;
use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Repositories\AdminRepository;
use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Repositories\RoleRepository;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function __construct(
        protected AdminRepository $repo,
        protected RoleRepository $roleRepo,
    )
    {}

    public function run(): void
    {
        if($this->repo->getByRoleName(BaseRole::SUPER_ADMIN)->isEmpty()){

            $role = $this->roleRepo->getBy('name', BaseRole::SUPER_ADMIN);
            $data = [
                'name' => 'John Doe',
                'email' => 'super.admin@gmail.com',
                'password' => 'password1',
                'role' => $role
            ];

            /** @var $handler AdminCreateAction */
            $handler = resolve(AdminCreateAction::class);
            $handler->exec(AdminDto::byArgs($data));
        }
    }
}
