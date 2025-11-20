<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Mở rộng ENUM tạm thời nếu đang dùng ENUM
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','user','guest') NOT NULL");
        } catch (\Throwable $e) {
            // Bỏ qua nếu cột không phải ENUM
        }

        // Chuyển dữ liệu user -> guest
        DB::table('users')->where('role', 'user')->update(['role' => 'guest']);

        // Thu hẹp ENUM, loại 'user'
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','guest') NOT NULL");
        } catch (\Throwable $e) {
            // Bỏ qua nếu không phải ENUM
        }
    }

    public function down(): void
    {
        // Cho phép lại 'user' trong ENUM
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','user','guest') NOT NULL");
        } catch (\Throwable $e) {}

        // Chuyển dữ liệu guest -> user
        DB::table('users')->where('role', 'guest')->update(['role' => 'user']);

        // Thu hẹp ENUM về admin,staff,user
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','user') NOT NULL");
        } catch (\Throwable $e) {}
    }
};
