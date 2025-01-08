<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\ComputerLabHistory;
use App\Models\Software;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ComputerLabController extends Controller
{
    use SoftDeletes;

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $user = User::find(auth()->id());

        $computerLabQuery = ComputerLab::with(['pemilik', 'campus'])
            ->where('publish_status', 1);

        // Filter based on user role
        if ($user->hasAnyRole(['Admin', 'Superadmin'])) {
        } else {
            $userCampusIds = $user->campus->pluck('id')->toArray();
            $computerLabQuery->whereIn('campus_id', $userCampusIds);
        }

        $computerLabList = $computerLabQuery->latest()->paginate($perPage);

        foreach ($computerLabList as $computerLab) {
            // Check if the 'user_credentials' is a valid JSON string before decoding
            if (is_string($computerLab->user_credentials)) {
                $computerLab->user_credentials = json_decode($computerLab->user_credentials, true);
            }
        }

        return view('pages.computer-lab.index', [
            'computerLabList' => $computerLabList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $categories = ['makmal_komputer', 'sudut_it', 'pusat_data'];
        // Get list of Pemilik and Kampus
        $pemilikList = User::role('Pemilik')->where('publish_status', 1)->get();
        $campusList = Campus::where('publish_status', 1)->get();
        $softwareList = Software::where('publish_status', 1)
            ->orderBy('title', 'asc')
            ->get();

        return view('pages.computer-lab.create', [
            'save_route' => route('computer-lab.store'),
            'campusList' => $campusList,
            'pemilikList' => $pemilikList,
            'softwareList' => $softwareList,
            'categories' => $categories,
            'str_mode' => 'Tambah',
        ]);
    }

    public function getPemilikByCampus($campusId)
    {
        $pemilikList = User::role('Pemilik')
            ->where('publish_status', 1)
            ->whereHas('campus', function ($query) use ($campusId) {
                $query->where('campuses.id', $campusId);
            })
            ->get();

        return response()->json($pemilikList);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'nullable|in:makmal_komputer,sudut_it,pusat_data',
            'code' => 'nullable|string',
            'name' => [
                'required',
                Rule::unique('computer_labs')->where(function ($query) use ($request) {
                    return $query->where('campus_id', $request->input('campus_id'));
                }),
            ],
            'campus_id' => 'required|exists:campuses,id',
            'pemilik_id' => 'required',
            'software_id' => 'nullable|array',
            'location' => 'required',
            'user_credentials' => 'nullable|array',
            'user_credentials.*.username' => 'nullable|string',
            'user_credentials.*.password' => 'nullable|string',
            'no_of_computer' => 'required|integer',
            'jadual_kuliah' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required' => 'Sila isi nama makmal komputer',
            'name.unique' => 'Nama makmal komputer telah wujud',
            'campus_id.required' => 'Sila pilih kampus',
            'campus_id.exists' => 'Kampus yang dipilih tidak sah',
            'pemilik_id.required' => 'Sila pilih pemilik',
            'location.required' => 'Sila isi nama lokasi',
            'no_of_computer.required' => 'Sila isi bilangan komputer',
            'publish_status.required'     => 'Sila pilih status',
        ]);

        $computerLab = new ComputerLab();
        $computerLab->fill($request->except('software_id', 'user_credentials', 'jadual_kuliah'));
        $computerLab->user_credentials = json_encode($request->input('user_credentials'));

        // Handle jadual_kuliah image upload
        if ($request->hasFile('jadual_kuliah')) {
            // Store the image in the 'public' disk under a structured folder
            $path = $request->file('jadual_kuliah')->store("makmal_komputer/temp/jadual_kuliah", 'public');
            $computerLab->jadual_kuliah = $path;
        }

        $computerLab->save();

        $computerLab->software()->attach($request->input('software_id'));

        $this->logHistory($computerLab, 'Tambah');

        return redirect()->route('computer-lab')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $computerLab = ComputerLab::findOrFail($id);
        $softwareList = Software::where('publish_status', 1)->get();
        $userCredentials = is_string($computerLab->user_credentials)
            ? json_decode($computerLab->user_credentials, true)
            : $computerLab->user_credentials;


        return view('pages.computer-lab.view', [
            'computerLab' => $computerLab,
            'softwareList' => $softwareList,
            'userCredentials' => $userCredentials,
        ]);
    }

    public function edit($id)
    {
        $computerLab = ComputerLab::findOrFail($id);
        $categories = ['makmal_komputer', 'sudut_it', 'pusat_data'];
        $user = User::find(auth()->id());
        // Allow editing only if the user is the owner (Pemilik) of the lab
        if ($user->hasRole('Pemilik') && $computerLab->pemilik_id !== $user->id) {
            abort(403, 'Anda tidak mempunyai akses untuk mengedit makmal ini.');
        }

        $pemilikList = User::role('Pemilik')->where('publish_status', 1)->get();
        $campusList = Campus::where('publish_status', 1)->get();
        $softwareList = Software::where('publish_status', 1)
            ->orderBy('title', 'asc')
            ->get();
        $userCredentials = null;

        // Check if user_credentials is a string, and if so, decode it
        if (!is_null($computerLab->user_credentials)) {
            // If it's a string, decode it, otherwise leave it as is
            if (is_string($computerLab->user_credentials)) {
                $userCredentials = json_decode($computerLab->user_credentials, true);
            } else {
                $userCredentials = $computerLab->user_credentials; // Already an array
            }
        }

        return view('pages.computer-lab.edit', [
            'save_route' => route('computer-lab.update', $computerLab->id),
            'campusList' => $campusList,
            'softwareList' => $softwareList,
            'pemilikList' => $pemilikList,
            'computerLab' => $computerLab,
            'userCredentials' => $userCredentials,
            'categories' => $categories,
            'str_mode' => 'Kemaskini',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'nullable|in:makmal_komputer,sudut_it,pusat_data',
            'code' => 'nullable|string',
            'name' => [
                'required',
                Rule::unique('computer_labs')->where(function ($query) use ($request, $id) {
                    return $query->where('campus_id', $request->input('campus_id'))
                        ->where('id', '!=', $id);
                }),
            ],
            'campus_id' => 'required|exists:campuses,id',
            'pemilik_id' => 'required',
            'software_id' => 'nullable|array',
            'location' => 'required',
            'user_credentials' => 'nullable|array',
            'user_credentials.*.username' => 'nullable|string',
            'user_credentials.*.password' => 'nullable|string',
            'jadual_kuliah' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'no_of_computer' => 'required',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required' => 'Sila isi nama makmal komputer',
            'name.unique' => 'Nama makmal komputer telah wujud',
            'campus_id.required' => 'Sila pilih kampus',
            'campus_id.exists' => 'Kampus yang dipilih tidak sah',
            'pemilik_id.required' => 'Sila pilih pemilik',
            'location.required' => 'Sila isi nama lokasi',
            'no_of_computer.required' => 'Sila isi bilangan komputer',
            'publish_status.required'     => 'Sila pilih status',
        ]);

        $computerLab = ComputerLab::findOrFail($id);
        $computerLab->fill($request->except('software_id', 'user_credentials', 'jadual_kuliah'));
        $computerLab->user_credentials = json_encode($request->input('user_credentials'));

        // Handle jadual_kuliah image upload (if a new image is uploaded)
        if ($request->hasFile('jadual_kuliah')) {
            // Delete the old image if it exists
            if ($computerLab->jadual_kuliah && file_exists(public_path('storage/' . $computerLab->jadual_kuliah))) {
                unlink(public_path('storage/' . $computerLab->jadual_kuliah));
            }

            // Store the new image in the 'public' disk under a structured folder
            $path = $request->file('jadual_kuliah')->store("makmal_komputer/temp/jadual_kuliah", 'public');
            $computerLab->jadual_kuliah = $path;
        }
        $computerLab->save();

        $computerLab->software()->sync($request->input('software_id'));

        $this->logHistory($computerLab, 'Kemaskini');

        return redirect()->route('computer-lab')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        $user = User::find(auth()->id());

        $computerLabQuery = ComputerLab::with(['pemilik', 'campus'])
            ->where('publish_status', 1);

        if (!$user->hasAnyRole(['Admin', 'Superadmin'])) {
            $userCampusIds = $user->campus->pluck('id')->toArray();
            $computerLabQuery->whereIn('campus_id', $userCampusIds);
        }

        if ($search) {
            $computerLabQuery->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('code', 'LIKE', "%$search%");
            });
        }

        $computerLabList = $computerLabQuery->latest()->paginate($perPage);

        foreach ($computerLabList as $computerLab) {
            // Check if the 'user_credentials' is a valid JSON string before decoding
            if (is_string($computerLab->user_credentials)) {
                $computerLab->user_credentials = json_decode($computerLab->user_credentials, true);
            }
        }

        return view('pages.computer-lab.index', [
            'computerLabList' => $computerLabList,
            'perPage' => $perPage,
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
