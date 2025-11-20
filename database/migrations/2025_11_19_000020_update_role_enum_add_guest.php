<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Mở rộng nếu hiện tại chưa có guest
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','guest') NOT NULL");
        } catch (\Throwable $e) {
            // Nếu không phải ENUM hoặc đã đúng thì bỏ qua
        }
    }

    public function down(): void
    {
        // Quay lại (loại guest) – cẩn thận trước khi rollback
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','user') NOT NULL");
        } catch (\Throwable $e) {
            // Bỏ qua
        }
    }
};
