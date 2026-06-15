<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'ice_permissions';

    public static $types = ['read', 'write', 'import', 'export'];

    public function modules()
    {
        return $this->hasOne(Module::class, 'id', 'module_id')
            ->where('status', 1);
    }

    public function roles()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public static function checkPermission($module_id = 0, $type = 'read', $message = '')
    {
        if (!in_array($type, self::$types)) {
            return 0; // return 0 instead of false
        }

        if (
            Auth::user()->role != 'Admin'
            && Module::where('id', $module_id)->value('admin')
        ) {
            return 0;
        }

        Logs::insert([
            'user_id' => Auth::user()->id,
            'type' => $type,
            'message' => $message,
            'module_id' => $module_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $hasPermission = self::where('module_id', $module_id)
                             ->where('role_id', Auth::user()->role_id)
                             ->where('can_'.$type, 1)
                             ->exists();

        return $hasPermission ? 1 : 0; // return 1 if exists, 0 otherwise
    }



    public static function getPermissions($moduleId)
    {
        $permissions = [];
        foreach (self::$types as $type) {
            $permissions[$type] = self::checkPermission($moduleId, $type);
        }

        return $permissions;
    }
}
