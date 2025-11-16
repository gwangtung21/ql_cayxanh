<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tree;
use App\Models\TreeCategory;
use App\Models\Location;
use Illuminate\Support\Carbon;

class TreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 sample trees (no images). Safe to run multiple times using firstOrCreate for categories/locations,
        // but trees are created anew if not present by unique name.

        $samples = [
            ['name'=>'Cây Phượng Vỹ 01','category'=>'Cây Trang Trí Ngoài Trời','location'=>'Sân Trước','planting'=>'2018-03-12','height'=>6.2,'diameter'=>30,'health'=>'good','notes'=>'Cây cổ điển trước sân.'],
            ['name'=>'Cây Bàng 02','category'=>'Cây Trang Trí Ngoài Trời','location'=>'Sân Sau','planting'=>'2016-06-20','height'=>5.0,'diameter'=>28,'health'=>'fair','notes'=>'Cần tỉa cành nhẹ.' ],
            ['name'=>'Cây Sầu Riêng 01','category'=>'Cây Ăn Quả','location'=>'Vườn Sau','planting'=>'2020-05-10','height'=>4.5,'diameter'=>25,'health'=>'good','notes'=>'Đang cho trái.' ],
            ['name'=>'Cây Xoài 03','category'=>'Cây Ăn Quả','location'=>'Vườn Trước','planting'=>'2015-04-18','height'=>7.0,'diameter'=>40,'health'=>'poor','notes'=>'Rễ bị mục, cần xử lý.' ],
            ['name'=>'Cây Bơ 05','category'=>'Cây Ăn Quả','location'=>'Sân Sau','planting'=>'2019-09-01','height'=>3.8,'diameter'=>22,'health'=>'good','notes'=>'Mới được bón phân.' ],
            ['name'=>'Cây Dừa 01','category'=>'Cây Công Trình','location'=>'Sân Trước','planting'=>'2010-11-11','height'=>12.5,'diameter'=>55,'health'=>'fair','notes'=>'Cần kiểm tra an toàn.' ],
            ['name'=>'Cây Sao Đen 07','category'=>'Cây Trồng Hàng Rào','location'=>'Hàng Rào Tây','planting'=>'2017-02-02','height'=>2.5,'diameter'=>12,'health'=>'good','notes'=>'Tốt, phát triển đều.' ],
            ['name'=>'Cây Lòng Bò 08','category'=>'Cây Trang Trí Ngoài Trời','location'=>'Sân Thượng','planting'=>'2021-07-07','height'=>1.8,'diameter'=>8,'health'=>'fair','notes'=>'Cần che nắng buổi trưa.' ],
            ['name'=>'Cây Bồ Đề 09','category'=>'Cây Cổ Thụ','location'=>'Công Viên','planting'=>'2001-01-01','height'=>15.0,'diameter'=>90,'health'=>'poor','notes'=>'Cần đánh giá nguy cơ gãy.' ],
            ['name'=>'Cây Hoa Sữa 10','category'=>'Cây Cảnh','location'=>'Lối Vào','planting'=>'2014-08-15','height'=>6.0,'diameter'=>35,'health'=>'good','notes'=>'Tỏa mùi thơm vào buổi tối.' ],
        ];

        foreach ($samples as $s) {
            $cat = TreeCategory::firstOrCreate(['name' => $s['category']]);
            $loc = Location::firstOrCreate(['name' => $s['location']]);

            // avoid duplicate tree names
            $exists = Tree::where('name', $s['name'])->first();
            if ($exists) continue;

            Tree::create([
                'name' => $s['name'],
                'category_id' => $cat->id,
                'location_id' => $loc->id,
                'planting_date' => $s['planting'],
                'height' => $s['height'],
                'diameter' => $s['diameter'],
                'health_status' => $s['health'],
                'notes' => $s['notes'],
                'image_url' => null,
            ]);
        }
    }
}
