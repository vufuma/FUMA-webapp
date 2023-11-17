<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException as ValidationException;
use  Spatie\Permission\Exceptions\RoleDoesNotExist as RoleDoesNotExist;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Define environment variable SEED_ADMIN_EMAIL as the email address of
     * a user to be assigned as an administrator
     * 
     * e.g.
     * 
     * SEED_ADMIN_EMAIL=someone@example.com php artisan db:seed --class=RolesAndPermissionsSeeder
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = null;
        // create an admin permission for managing roles and permissions
        if(!$this->roleExists('Admin')) 
        {
            echo "Create Admin Role and permissions \n";
            // create an admin permission for managing roles and permissions
            Permission::create(['guard_name' => 'web', 'name' => 'Administer roles & permissions']);
            $adminRole = Role::create(['guard_name' => 'web', 'name' => 'Admin'])
                ->givePermissionTo('Administer roles & permissions');
            $adminRole->givePermissionTo(Permission::all());
        } 
        else {
            echo "Load existing Admin Role \n";
            $adminRole = Role::findByName('Admin')->get();
        }

        $seed_admin_email = getenv('SEED_ADMIN_EMAIL', '');
        if ($seed_admin_email == '') {
            echo "Skipping setting Admin user \n";
        }
        else {
            echo "Adding Admin to user with email: $seed_admin_email \n";
            $admin_user = User::where('email', $seed_admin_email)->first();
            if ($admin_user === null) {
                error_log("No user found corresponding to email: ".$seed_admin_email);
                error_log("Please set a valid user email in environment variable SEED_ADMIN_EMAIL");
                throw ValidationException::withMessages(["SEED_ADMIN_EMAIL" => "" . $seed_admin_email]);
            }
            $admin_user->assignRole("Admin");
        }
    }

    function roleExists($role)
    {
        try {
            return Count(Role::findByName($role)->get()) > 0;
        } catch (RoleDoesNotExist $e) {
            return FALSE;
        }
    }
}
