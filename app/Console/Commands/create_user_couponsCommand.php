<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Console\Command;

class create_user_couponsCommand extends Command
{
    protected $signature = 'create:user-coupons';


    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $userInitials = substr($user->full_name, 0, 3);
            $code = strtoupper($userInitials . substr(md5(uniqid()), 0, 5));

            if (Coupon::where('code', $code)->exists()) {
                $code = strtoupper($userInitials . substr(md5(uniqid()), 0, 5)) . $user->id;
            }

            $user->coupon()->create([
                'is_active' => true,
                'code' => $code,
                'percentage_value' => 10,
                'fixed_value' => json_encode([]),
                'type' => 'percentage',
                'is_created_by_admin' => false,
            ]);
        }
    }
}
