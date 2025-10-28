<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTrees = Tree::count();
        $totalUsers = User::count();

        // defaults
        $byCategory = [];
        $healthDistribution = [];
        $avgHeight = 0;
        $avgDiameter = 0;

        // helper to normalize raw health label to Vietnamese category
        $normalizeHealth = function ($s) {
            $s = mb_strtolower(trim((string)$s));
            if ($s === '') return 'Cần chú ý';
            if (strpos($s, 'kh') !== false || strpos($s, 'khoe') !== false || strpos($s, 'good') !== false || strpos($s, 'excellent') !== false) return 'Khỏe mạnh';
            if (strpos($s, 'chu') !== false || strpos($s, 'can') !== false || strpos($s, 'fair') !== false || strpos($s, 'canh') !== false) return 'Cần chú ý';
            if (strpos($s, 'xu') !== false || strpos($s, 'gap') !== false || strpos($s, 'gấp') !== false || strpos($s, 'poor') !== false || strpos($s, 'nghiêm') !== false) return 'Cần xử lý gấp';
            return 'Cần chú ý';
        };

        if (Schema::hasTable('trees')) {
            // category distribution
            if (Schema::hasTable('tree_categories') && Schema::hasColumn('trees', 'category_id')) {
                try {
                    $byCategory = DB::table('trees')
                        ->join('tree_categories', 'trees.category_id', '=', 'tree_categories.id')
                        ->select('tree_categories.name', DB::raw('count(trees.id) as cnt'))
                        ->groupBy('tree_categories.name')
                        ->orderByDesc('cnt')
                        ->pluck('cnt', 'name')
                        ->toArray();
                } catch (\Throwable $e) {
                    $byCategory = [];
                }
            }

            // raw health distribution from DB
            if (Schema::hasColumn('trees', 'health_status')) {
                try {
                    $raw = Tree::select('health_status', DB::raw('count(*) as cnt'))
                        ->groupBy('health_status')
                        ->pluck('cnt', 'health_status')
                        ->toArray();

                    $healthDistribution = $raw; // keep raw for chart JS which also normalizes

                    // aggregate into 3 categories for KPIs
                    $aggregated = ['Khỏe mạnh' => 0, 'Cần chú ý' => 0, 'Cần xử lý gấp' => 0];
                    foreach ($raw as $k => $v) {
                        $cat = $normalizeHealth($k);
                        $aggregated[$cat] = ($aggregated[$cat] ?? 0) + (int)$v;
                    }

                    $healthyCount = $aggregated['Khỏe mạnh'];
                    $attentionCount = $aggregated['Cần chú ý'];
                    $urgentCount = $aggregated['Cần xử lý gấp'];
                } catch (\Throwable $e) {
                    $healthDistribution = [];
                    $healthyCount = $attentionCount = $urgentCount = 0;
                }
            } else {
                $healthyCount = $attentionCount = $urgentCount = 0;
            }

            // averages: prefer 'height'/'diameter', fallback to legacy names
            if (Schema::hasColumn('trees', 'height')) {
                try { $avgHeight = (float) Tree::whereNotNull('height')->avg('height') ?? 0; } catch (\Throwable $e) { $avgHeight = 0; }
            } elseif (Schema::hasColumn('trees', 'height_m')) {
                try { $avgHeight = (float) Tree::whereNotNull('height_m')->avg('height_m') ?? 0; } catch (\Throwable $e) { $avgHeight = 0; }
            }

            if (Schema::hasColumn('trees', 'diameter')) {
                try { $avgDiameter = (float) Tree::whereNotNull('diameter')->avg('diameter') ?? 0; } catch (\Throwable $e) { $avgDiameter = 0; }
            } elseif (Schema::hasColumn('trees', 'diameter_cm')) {
                try { $avgDiameter = (float) Tree::whereNotNull('diameter_cm')->avg('diameter_cm') ?? 0; } catch (\Throwable $e) { $avgDiameter = 0; }
            }
        } else {
            $healthyCount = $attentionCount = $urgentCount = 0;
        }

        // ensure variables exist for view
        $totalTrees = $totalTrees ?? 0;
        $healthyCount = $healthyCount ?? 0;
        $attentionCount = $attentionCount ?? 0;
        $urgentCount = $urgentCount ?? 0;

        return view('admin.dashboard', compact('totalTrees','healthyCount','attentionCount','urgentCount','totalUsers','byCategory','healthDistribution','avgHeight','avgDiameter'));
    }
}
