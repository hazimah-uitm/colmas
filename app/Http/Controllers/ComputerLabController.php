<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\ComputerLabHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComputerLabController extends Controller
{
    use SoftDeletes;

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $computerLabList = ComputerLab::latest()->paginate($perPage);

        return view('pages.computer-lab.index', [
            'computerLabList' => $computerLabList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $pemilikList = User::role('Pemilik')->get();
        $campusList = Campus::where('publish_status', 1)->get();

        return view('pages.computer-lab.create', [
            'save_route' => route('computer-lab.store'),
            'campusList' => $campusList,
            'pemilikList' => $pemilikList,
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'name' => [
                'required',
                Rule::unique('computer_labs')
                    ->where(function ($query) use ($request) {
                        return $query->where('campus_id', $request->input('campus_id'));
                    }),
            ],
            'campus_id' => 'required|exists:campuses,id',
            'pemilik_id' => 'required',
            'username' => 'required',
            'password' => 'required',
            'no_of_computer' => 'required',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required' => 'Sila isi nama kampus',
            'name.unique' => 'Nama kampus telah wujud',
            'campus_id.required' => 'Sila pilih kampus',
            'campus_id.exists' => 'Kampus yang dipilih tidak sah',
            'pemilik_id.required' => 'Sila pilih pemilik',
            'username.required' => 'Sila isi nama pengguna',
            'password.required' => 'Sila isi kata laluan',
            'no_of_computer.required' => 'Sila isi bilangan komputer',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $computerLab = new ComputerLab();
        $computerLab->fill($request->all());
        $computerLab->save();

        $this->logHistory($computerLab, 'Tambah');

        return redirect()->route('computer-lab')->with('success', 'Maklumat berjaya disimpan');
    }


    public function show($id)
    {
        $computerLab = ComputerLab::findOrFail($id);

        return view('pages.computer-lab.view', [
            'computerLab' => $computerLab,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $computerLab = ComputerLab::findOrFail($id);
        $pemilikList = User::role('Pemilik')->get();
        $campusList = Campus::where('id', $computerLab->campus_id)->where('publish_status', 1)->get();

        return view('pages.computer-lab.edit', [
            'save_route' => route('computer-lab.update', $id),
            'str_mode' => 'Kemas Kini',
            'campusList' => $campusList,
            'pemilikList' => $pemilikList,
            'computerLab' => $computerLab,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'nullable|string',
            'name' => [
                'required',
                Rule::unique('computer_labs')->where(function ($query) use ($request, $id) {
                    return $query->where('campus_id', $request->input('campus_id'))
                        ->where('id', '!=', $id); // Exclude the current record by ID
                }),
            ],
            'campus_id' => 'required|exists:campuses,id',
            'pemilik_id' => 'required',
            'username' => 'required',
            'password' => 'required',
            'no_of_computer' => 'required',
            'publish_status' => 'required|in:1,0',
        ],[
            'name.required' => 'Sila isi nama kampus',
            'name.unique' => 'Nama kampus telah wujud',
            'campus_id.required' => 'Sila pilih kampus',
            'campus_id.exists' => 'Kampus yang dipilih tidak sah',
            'pemilik_id.required' => 'Sila pilih pemilik',
            'username.required' => 'Sila isi nama pengguna',
            'password.required' => 'Sila isi kata laluan',
            'no_of_computer.required' => 'Sila isi bilangan komputer',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $computerLab = ComputerLab::findOrFail($id);
        $computerLab->fill($request->all());
        $computerLab->save();

        $this->logHistory($computerLab, 'Kemaskini');

        return redirect()->route('computer-lab')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $computerLabList = ComputerLab::where('name', 'LIKE', "%$search%")
                ->orWhere('code', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $computerLabList = ComputerLab::latest()->paginate(10);
        }

        return view('pages.computer-lab.index', [
            'computerLabList' => $computerLabList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $computerLab = ComputerLab::findOrFail($id);

        $computerLab->delete();

        $this->logHistory($computerLab, 'Padam');

        return redirect()->route('computer-lab')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = ComputerLab::onlyTrashed()->latest()->paginate(10);

        return view('pages.computer-lab.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        ComputerLab::withTrashed()->where('id', $id)->restore();

        return redirect()->route('computer-lab')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $computerLab = ComputerLab::withTrashed()->findOrFail($id);

        $computerLab->forceDelete();

        return redirect()->route('computer-lab.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }

    protected function logHistory($computerLab, $action)
    {
        $publishStatusMapping = [
            'Aktif' => 1,
            'Tidak Aktif' => 0,
        ];

        $publishStatus = isset($publishStatusMapping[$computerLab->publish_status])
            ? $publishStatusMapping[$computerLab->publish_status]
            : $computerLab->publish_status;

        ComputerLabHistory::create([
            'computer_lab_id' => $computerLab->id,
            'code' => $computerLab->code,
            'name' => $computerLab->name,
            'pc_no' => $computerLab->no_of_computer,
            'owner' => $computerLab->pemilik_id,
            'month_year' => now(),
            'action' => $action,
            'publish_status' => $publishStatus,
        ]);
    }

    public function history($id)
    {
        $computerLab = ComputerLab::findOrFail($id);
        $historyList = $computerLab->histories()->latest()->paginate(10);

        return view('pages.computer-lab.history', [
            'computerLab' => $computerLab,
            'historyList' => $historyList,
        ]);
    }
}
