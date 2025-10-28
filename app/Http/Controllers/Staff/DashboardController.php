<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // determine which column (if any) represents assignment in trees table
        $possibleCols = ['assigned_to','assigned_user_id','user_id','staff_id','caretaker_id','owner_id'];
        $assignColumn = null;
        foreach($possibleCols as $c){
            if(Schema::hasColumn('trees', $c)){
                $assignColumn = $c;
                break;
            }
        }

        if($assignColumn){
            $assignedTrees = Tree::where($assignColumn, $userId)->with('category')->get();
            // if nothing assigned to the user, fallback to all trees so dashboard isn't empty
            if($assignedTrees->isEmpty()){
                $assignedTrees = Tree::with('category')->get();
            }
        } else {
            // fallback: no assignment column found — use all trees
            $assignedTrees = Tree::with('category')->get();
        }

        // categoryCounts: associative array ['Cây ăn quả' => 12, ...]
        $categoryCounts = $assignedTrees->groupBy(function($t){
            if(isset($t->category) && !empty($t->category->name)) return $t->category->name;
            if(!empty($t->category)) return (string) $t->category;
            return 'Khác';
        })->map(function($group){
            return $group->count();
        })->toArray();

        // health distribution counts
        // normalize health distribution from assignedTrees or DB
        $healthDistribution = ['good' => 0, 'fair' => 0, 'poor' => 0];

        // possible health/status column names
        $possibleHealthCols = ['health','status','condition','health_status'];

        // function to map raw label to bucket
        $mapHealthLabel = function($label){
            $s = mb_strtolower(trim((string)$label));
            if($s === '') return 'fair';
            // keywords for good/fair/poor
            $goodKeys = ['good','healthy','khoe','khỏe','khỏe mạnh','ok','healthy'];
            $poorKeys = ['poor','danger','nguy','nguy cấp','cần xử lý','can xu ly','critical','critical'];
            $fairKeys = ['fair','warning','chú ý','chu y','cần chú ý','can chu y','moderate'];
            foreach($goodKeys as $k) if(mb_strpos($s, $k) !== false) return 'good';
            foreach($poorKeys as $k) if(mb_strpos($s, $k) !== false) return 'poor';
            foreach($fairKeys as $k) if(mb_strpos($s, $k) !== false) return 'fair';
            // fallback: if numeric value (e.g., 3=good), try to map
            if(is_numeric($s)){
                $n = (int)$s;
                if($n >= 3) return 'good';
                if($n == 2) return 'fair';
                return 'poor';
            }
            return 'fair';
        };

        // get raw counts
        $rawCounts = [];
        if($assignedTrees->isNotEmpty()){
            $first = $assignedTrees->first();
            $healthField = null;
            foreach($possibleHealthCols as $c){
                if(array_key_exists($c, $first->getAttributes())){ $healthField = $c; break; }
            }
            if($healthField){
                $rawCounts = $assignedTrees->groupBy(function($t) use ($healthField){ return $t->$healthField ?? 'unknown'; })->map(function($g){ return $g->count(); })->toArray();
            } else {
                // try grouping by 'health' as default even if attribute missing
                $rawCounts = $assignedTrees->groupBy(function($t){ return $t->health ?? 'unknown'; })->map(function($g){ return $g->count(); })->toArray();
            }
        } else {
            // assignedTrees empty, try to get counts directly from DB using any available column
            foreach($possibleHealthCols as $c){
                if(Schema::hasColumn('trees', $c)){
                    $dbCounts = Tree::select($c, \DB::raw('count(*) as cnt'))->groupBy($c)->pluck('cnt', $c)->toArray();
                    $rawCounts = $dbCounts; break;
                }
            }
        }

        foreach($rawCounts as $label => $cnt){
            $bucket = $mapHealthLabel($label);
            $healthDistribution[$bucket] += (int)$cnt;
        }

        $totalAssigned = $assignedTrees->count();

        // averages (assumes numeric fields 'height' in meters and 'diameter' in cm)
        $avgHeight = null;
        if($assignedTrees->pluck('height')->filter()->count()){
            $avgHeightVal = $assignedTrees->pluck('height')->filter()->avg();
            $avgHeight = number_format($avgHeightVal, 1) . 'm';
        }

        $avgDiameter = null;
        if($assignedTrees->pluck('diameter')->filter()->count()){
            $avgDiameterVal = $assignedTrees->pluck('diameter')->filter()->avg();
            $avgDiameter = number_format($avgDiameterVal, 1) . 'cm';
        }

        return view('staff.dashboard', compact('assignedTrees','categoryCounts','healthDistribution','totalAssigned','avgHeight','avgDiameter'));
    }
}
