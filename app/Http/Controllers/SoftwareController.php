<?php

namespace App\Http\Controllers;

use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoftwareController extends Controller
{
    use SoftDeletes;

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $softwareList = Software::latest()->paginate($perPage);

        return view('pages.software.index', [
            'softwareList' => $softwareList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('pages.software.form', [
            'save_route' => route('software.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'version' => 'nullable|string',
            'publish_status' => 'required|in:1,0',
        ], [
            'title.required'     => 'Sila isi nama perisian',
            'publish_status.required' => 'Sila pilih status',
        ]);

        // Ensure the combination of title and version is unique
        $existingSoftware = Software::where('title', $request->title)
            ->where('version', $request->version)
            ->first();

        if ($existingSoftware) {
            return back()->withErrors([
                'title' => 'Nama perisian dan versi telah wujud atau masih dalam rekod dipadam.',
            ]);
        }

        $software = new Software();
        $software->fill($request->all());
        $software->save();

        return redirect()->route('software')
            ->with('success', 'Maklumat berjaya disimpan');
    }


    public function show($id)
    {
        $software = Software::findOrFail($id);

        return view('pages.software.view', [
            'software' => $software,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('pages.software.form', [
            'save_route' => route('software.update', $id),
            'str_mode' => 'Kemas Kini',
            'software' => Software::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'version' => 'nullable|string',
            'publish_status' => 'required|in:1,0'
        ], [
            'title.required' => 'Sila isi nama perisian',
            'publish_status.required' => 'Sila pilih status',
        ]);
    
        // Ensure the combination of title and version is unique, excluding the current record
        $existingSoftware = Software::where('title', $request->title)
            ->where('version', $request->version)
            ->where('id', '!=', $id)  // Exclude current record by ID
            ->first();
    
        if ($existingSoftware) {
            return back()->withErrors([
                'title' => 'Nama perisian dan versi telah wujud atau masih dalam rekod dipadam.',
            ]);
        }
    
        $software = Software::findOrFail($id);
    
        $software->fill($request->all());
        $software->save();
    
        return redirect()->route('software')->with('success', 'Maklumat berjaya dikemaskini');
    }
    

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $softwareList = Software::where('title', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $softwareList = Software::latest()->paginate(10);
        }

        return view('pages.software.index', [
            'softwareList' => $softwareList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $software = Software::findOrFail($id);

        $software->delete();

        return redirect()->route('software')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = Software::onlyTrashed()->latest()->paginate(10);

        return view('pages.software.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        Software::withTrashed()->where('id', $id)->restore();

        return redirect()->route('software')->with('success', 'Maklumat berjaya dikembalikan');
    }

    public function forceDelete($id)
    {
        // Permanently delete the record
        Software::withTrashed()->where('id', $id)->forceDelete();

        return redirect()->route('software.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
