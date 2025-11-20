<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tăng ENUM tạm thời để đảm bảo ALTER không lỗi (bỏ qua nếu không phải ENUM)
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','user','guest') NOT NULL");
        } catch (\Throwable $e) {}

        // Chuyển dữ liệu user -> guest
        DB::table('users')->where('role', 'user')->update(['role' => 'guest']);

        // Thu hẹp ENUM, loại 'user'
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','guest') NOT NULL");
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // Phục hồi: thêm lại 'user' và chuyển guest -> user
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','user','guest') NOT NULL");
        } catch (\Throwable $e) {}
        DB::table('users')->where('role', 'guest')->update(['role' => 'user']);
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','user') NOT NULL");
        } catch (\Throwable $e) {}
    }
};
