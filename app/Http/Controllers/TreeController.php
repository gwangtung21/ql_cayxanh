<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use App\Models\TreeCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TreeController extends Controller
{
    // Hiển thị danh sách cây
    public function index(Request $request)
    {
        $trees = Tree::with(['category', 'location'])->get();

        // If accessing admin routes, return admin view with extra data for modals
        if ($request->is('admin*') || $request->route() && optional($request->route())->getName() && str_starts_with($request->route()->getName(), 'admin.')) {
            $categories = TreeCategory::all();
            $locations = Location::all();
            return view('admin.trees.index', compact('trees', 'categories', 'locations'));
        }

        // Public listing
        return view('trees.index', compact('trees'));
    }

    // Form thêm cây mới
    public function create()
    {
        // keep for direct usage if needed
        $categories = TreeCategory::all();
        $locations = Location::all();
        return view('admin.trees.create', compact('categories', 'locations'));
    }

    // Lưu cây mới
    public function store(Request $request)
    {
        Log::info('Tree store request', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:tree_categories,id',
            'location_id' => 'required|exists:locations,id',
            'planting_date' => 'nullable|date',
            'planted_at' => 'nullable|date',
            'height' => 'nullable|numeric',
            'height_m' => 'nullable',
            'diameter' => 'nullable|numeric',
            'diameter_cm' => 'nullable',
            'health_status' => 'nullable|string|in:excellent,good,fair,poor',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        try {
            // Normalize data and accept alternate input names from form
            $data = [];
            $data['name'] = $validated['name'];
            $data['category_id'] = $validated['category_id'];
            $data['location_id'] = $validated['location_id'];
            $data['planting_date'] = $validated['planting_date'] ?? $validated['planted_at'] ?? null;

            // handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('trees', 'public');
                $data['image_url'] = Storage::url($path);
            } else {
                $data['image_url'] = $validated['image_url'] ?? $request->input('image_url') ?? null;
            }

            // Height: accept 'height' or 'height_m' (may include comma)
            $rawHeight = $request->input('height', $request->input('height_m'));
            if (!is_null($rawHeight) && $rawHeight !== '') {
                $rawHeight = str_replace(',', '.', $rawHeight);
                $data['height'] = is_numeric($rawHeight) ? (float)$rawHeight : null;
            } else {
                $data['height'] = null;
            }

            // Diameter: accept 'diameter' or 'diameter_cm'
            $rawDiam = $request->input('diameter', $request->input('diameter_cm'));
            if (!is_null($rawDiam) && $rawDiam !== '') {
                $rawDiam = str_replace(',', '.', $rawDiam);
                $data['diameter'] = is_numeric($rawDiam) ? (float)$rawDiam : null;
            } else {
                $data['diameter'] = null;
            }

            // Ensure health_status is one of enum values (map common inputs)
            $hs = $validated['health_status'] ?? null;
            if (!$hs) {
                $hs = $request->input('health_status');
            }
            $map = [
                'khỏe mạnh' => 'good',
                'khoe manh' => 'good',
                'khỏe' => 'good',
                'cần chú ý' => 'fair',
                'can chu y' => 'fair',
                'cần xử lý gấp' => 'poor',
                'can xu ly gap' => 'poor',
                'excellent' => 'excellent',
                'good' => 'good',
                'fair' => 'fair',
                'poor' => 'poor',
            ];
            $normHs = null;
            if ($hs) {
                $lk = mb_strtolower(trim($hs));
                $normHs = $map[$lk] ?? (in_array($lk, ['excellent','good','fair','poor']) ? $lk : null);
            }
            $data['health_status'] = $normHs ?? null;

            $data['notes'] = $validated['notes'] ?? null;
            $data['image_url'] = $data['image_url'] ?? null;

            Log::info('Tree prepared data', $data);

            $tree = Tree::create($data);

            Log::info('Tree created', ['id' => $tree->id ?? null]);

            return redirect()->back()->with('success', 'Cây đã được thêm');
        } catch (\Exception $e) {
            Log::error('Tree store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Optionally return debug info for immediate feedback
            return redirect()->back()->withInput()->with('error', 'Lỗi khi lưu cây: ' . $e->getMessage());
        }
    }

    // Hiển thị chi tiết cây
    public function show(Tree $tree)
    {
        return view('trees.show', compact('tree'));
    }

    // Form chỉnh sửa cây
    public function edit(Tree $tree)
    {
        // not used for admin modal flow but keep available
        $categories = TreeCategory::all();
        $locations = Location::all();
        return view('admin.trees.edit', compact('tree', 'categories', 'locations'));
    }

    // Cập nhật cây
    public function update(Request $request, Tree $tree)
    {
        Log::info('Tree update request for id ' . $tree->id, $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:tree_categories,id',
            'location_id' => 'required|exists:locations,id',
            'planting_date' => 'nullable|date',
            'planted_at' => 'nullable|date',
            'height' => 'nullable',
            'height_m' => 'nullable',
            'diameter' => 'nullable',
            'diameter_cm' => 'nullable',
            'health_status' => 'nullable|string',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [];
        $data['name'] = $validated['name'];
        $data['category_id'] = $validated['category_id'];
        $data['location_id'] = $validated['location_id'];
        $data['planting_date'] = $validated['planting_date'] ?? $validated['planted_at'] ?? $tree->planting_date;

        // handle image upload on update
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('trees', 'public');
            $data['image_url'] = Storage::url($path);
        } else {
            $data['image_url'] = $request->input('image_url') ?? $tree->image_url;
        }

        $rawHeight = $request->input('height', $request->input('height_m'));
        if (!is_null($rawHeight) && $rawHeight !== '') {
            $rawHeight = str_replace(',', '.', $rawHeight);
            $data['height'] = is_numeric($rawHeight) ? (float)$rawHeight : $tree->height;
        } else {
            $data['height'] = $tree->height;
        }

        $rawDiam = $request->input('diameter', $request->input('diameter_cm'));
        if (!is_null($rawDiam) && $rawDiam !== '') {
            $rawDiam = str_replace(',', '.', $rawDiam);
            $data['diameter'] = is_numeric($rawDiam) ? (float)$rawDiam : $tree->diameter;
        } else {
            $data['diameter'] = $tree->diameter;
        }

        $data['health_status'] = $validated['health_status'] ?? $tree->health_status;
        $data['notes'] = $validated['notes'] ?? $tree->notes;
        $data['image_url'] = $data['image_url'] ?? $tree->image_url;

        $tree->update($data);

        return redirect()->back()->with('success', 'Cây đã được cập nhật');
    }

    // Xóa cây
    public function destroy(Tree $tree)
    {
        $tree->delete();
        return redirect()->back()->with('success', 'Cây đã được xóa');
    }
}