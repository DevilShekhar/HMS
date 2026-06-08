<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomPack;
use Illuminate\Support\Facades\Storage;

class RoomPackController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomPack::where('designer_id', auth()->id());

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roomPacks = $query->paginate(9);

        return view('designer.room_packs.index', compact('roomPacks'));
    }

    public function create()
    {
        return view('designer.room_packs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cover_render' => 'required|image',
            'optional_renders.*' => 'nullable|image',
            'pdf_2d_drawing' => 'required|mimes:pdf',
            'decor_material_chart' => 'required|file',
        ]);

        $renders = [];
        if ($request->hasFile('optional_renders')) {
            foreach ($request->file('optional_renders') as $file) {
                $renders[] = $file->store('room_packs', 'public');
            }
        }

        RoomPack::create([
            'designer_id' => auth()->id(),
            'name' => $request->name,
            'cover_render' => $request->file('cover_render')->store('room_packs', 'public'),
            'optional_renders' => $renders, // Direct array assignment, Laravel will handle JSON conversion
            'pdf_2d_drawing' => $request->file('pdf_2d_drawing')->store('room_packs', 'public'),
            'decor_material_chart' => $request->file('decor_material_chart')->store('room_packs', 'public'),
        ]);

        return redirect()->route('room_packs.index')->with('success', 'Room Pack Uploaded Successfully');
    }

    public function show($id)
    {
        $roomPack = RoomPack::where('designer_id', auth()->id())->findOrFail($id);
        return view('designer.room_packs.show', compact('roomPack'));
    }

    public function edit($id)
    {
        $roomPack = RoomPack::where('designer_id', auth()->id())->findOrFail($id);
        return view('designer.room_packs.edit', compact('roomPack'));
    }

    public function update(Request $request, $id)
    {
        $roomPack = RoomPack::where('designer_id', auth()->id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'cover_render' => 'nullable|image',
            'optional_renders.*' => 'nullable|image',
            'pdf_2d_drawing' => 'nullable|mimes:pdf',
            'decor_material_chart' => 'nullable|file',
        ]);

        if ($request->hasFile('cover_render')) {
            Storage::disk('public')->delete($roomPack->cover_render);
            $roomPack->cover_render = $request->file('cover_render')->store('room_packs', 'public');
        }

        if ($request->hasFile('optional_renders')) {
            if (is_array($roomPack->optional_renders)) {
                foreach ($roomPack->optional_renders as $file) {
                    Storage::disk('public')->delete($file);
                }
            }

            $renders = [];
            foreach ($request->file('optional_renders') as $file) {
                $renders[] = $file->store('room_packs', 'public');
            }

            $roomPack->optional_renders = $renders; // Assign array directly
        }

        if ($request->hasFile('pdf_2d_drawing')) {
            Storage::disk('public')->delete($roomPack->pdf_2d_drawing);
            $roomPack->pdf_2d_drawing = $request->file('pdf_2d_drawing')->store('room_packs', 'public');
        }

        if ($request->hasFile('decor_material_chart')) {
            Storage::disk('public')->delete($roomPack->decor_material_chart);
            $roomPack->decor_material_chart = $request->file('decor_material_chart')->store('room_packs', 'public');
        }

        $roomPack->name = $request->name;
        $roomPack->save();

        return redirect()->route('room_packs.index')->with('success', 'Room Pack Updated Successfully');
    }

    public function destroy($id)
    {
        $roomPack = RoomPack::where('designer_id', auth()->id())->findOrFail($id);

        Storage::disk('public')->delete([
            $roomPack->cover_render,
            $roomPack->pdf_2d_drawing,
            $roomPack->decor_material_chart,
        ]);

        if (is_array($roomPack->optional_renders)) {
            foreach ($roomPack->optional_renders as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $roomPack->delete();

        return redirect()->route('room_packs.index')->with('success', 'Room Pack Deleted Successfully');
    }
}
