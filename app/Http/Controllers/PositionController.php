<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $positionList = Position::latest()->paginate($perPage);

        return view('pages.position.index', [
            'positionList' => $positionList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('pages.position.form', [
            'save_route' => route('position.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:positions,title',
            'grade' => 'required|unique:positions,grade',
            'publish_status' => 'required|in:1,0',
        ], [
            'title.unique'     => 'Nama jawatan telah wujud atau masih dalam rekod dipadam',
            'title.required'     => 'Sila isi nama jawatan',
            'grade.unique'     => 'Gred jawatan telah wujud atau masih dalam rekod dipadam',
            'grade.required'     => 'Sila isi gred jawatan',
            'publish_status.required' => 'Sila isi status jawatan',
        ]);

        $position = new Position();

        $position->fill($request->all());
        $position->save();

        return redirect()->route('position')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $position = Position::findOrFail($id);

        return view('pages.position.view', [
            'position' => $position,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('pages.position.form', [
            'save_route' => route('position.update', $id),
            'str_mode' => 'Kemas Kini',
            'position' => Position::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:positions,title,' . $id,
            'grade' => 'required|unique:positions,grade,' . $id,
            'publish_status' => 'required|in:1,0',
        ], [
            'title.required'     => 'Sila isi nama jawatan',
            'title.unique'       => 'Nama jawatan telah wujud atau masih dalam rekod dipadam',
            'grade.required'     => 'Sila isi gred jawatan',
            'grade.unique'       => 'Gred jawatan telah wujud atau masih dalam rekod dipadam',
            'publish_status.required' => 'Sila isi status jawatan',
        ]);

        $position = Position::findOrFail($id);
        $position->fill($request->all());
        $position->save();

        return redirect()->route('position')->with('success', 'Maklumat berjaya dikemaskini');
    }
    public function search(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        if ($search) {
            $positionList = Position::where('title', 'LIKE', "%$search%")
                ->orWhere('grade', 'LIKE', "%$search%")
                ->latest()
                ->paginate($perPage);
        } else {
            $positionList = Position::latest()->paginate($perPage);
        }

        return view('pages.position.index', [
            'positionList' => $positionList,
            'perPage' => $perPage,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $position->delete();

        return redirect()->route('position')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = Position::onlyTrashed()->latest()->paginate(10);

        return view('pages.position.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        Position::withTrashed()->where('id', $id)->restore();

        return redirect()->route('position')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $position = Position::withTrashed()->findOrFail($id);

        $position->forceDelete();

        return redirect()->route('position.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
