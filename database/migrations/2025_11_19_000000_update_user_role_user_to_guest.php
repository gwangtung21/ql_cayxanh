<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateUserRoleUserToGuest extends Migration
{
    /**
     * Run the migrations.
     *
     * Chuyển tất cả users.role = 'user' -> 'guest'
     */
    public function up()
    {
        DB::table('users')->where('role', 'user')->update(['role' => 'guest']);
    }

    /**
     * Reverse the migrations.
     *
     * Chuyển ngược 'guest' -> 'user' (cẩn trọng nếu có người trước đó đã là guest)
     */
    public function down()
    {
        DB::table('users')->where('role', 'guest')->update(['role' => 'user']);
    }
}
