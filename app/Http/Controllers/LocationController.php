<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('trees')->get();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')->with('success', 'Vị trí đã được thêm!');
    }

    public function show(Location $location)
    {
        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')->with('success', 'Vị trí đã được cập nhật!');
    }

    public function destroy(Location $location)
    {
        if ($location->trees()->count() > 0) {
            return redirect()->route('locations.index')->with('error', 'Không thể xóa vị trí đang có cây!');
        }

        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Vị trí đã được xóa!');
    }
}