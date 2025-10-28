<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TreeCategory;

class TreeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Cây ăn quả', 'description' => 'Cây cho trái ăn được', 'care_frequency_days' => 30],
            ['name' => 'Cây cổ thụ', 'description' => 'Cây lâu năm, thân gỗ lớn', 'care_frequency_days' => 90],
            ['name' => 'Cây Bóng Mát', 'description' => 'Cây tạo bóng mát', 'care_frequency_days' => 60],
            ['name' => 'Cây Trang Trí Ngoài Trời', 'description' => 'Cây trang trí sân vườn, công viên', 'care_frequency_days' => 45],
            ['name' => 'Cây Lá Màu', 'description' => 'Cây có lá màu sắc nổi bật', 'care_frequency_days' => 30],
            ['name' => 'Cây Dây Leo', 'description' => 'Cây leo/tường rào', 'care_frequency_days' => 30],
            ['name' => 'Cây Hoa', 'description' => 'Cây chủ yếu để trưng bông hoa', 'care_frequency_days' => 20],
        ];

        foreach ($categories as $cat) {
            TreeCategory::updateOrCreate([
                'name' => $cat['name']
            ], [
                'description' => $cat['description'],
                'care_frequency_days' => $cat['care_frequency_days']
            ]);
        }
    }
}