<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('permissions.view');

        return view('permissions.index');
    }

    public function getAllPermissions(Request $request)
    {
        $this->checkPermission('permissions.view');

        $permissions = Permission::all();
        return DataTables::of($permissions)
            ->addIndexColumn()
             ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('permissions.edit')) {
                    $buttons .= '
            <button class="btn btn-warning btn-icon edit-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                    <path d="M13.5 6.5l4 4" />
                </svg>
            </button>
        ';
                }

                if (auth()->user()->can('permissions.delete')) {
                    $buttons .= '
            <button class="btn btn-danger btn-icon delete-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 7l16 0" />
                    <path d="M10 11l0 6" />
                    <path d="M14 11l0 6" />
                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                </svg>
            </button>
        ';
                }

                return $buttons ?: '-';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('permissions.create');

        try {
            $validator = Validator::make($request->all(), [
                'group_name' => 'required|string|',
                'permissions' => 'required|array',
                'permissions.*' => 'required|string|unique:permissions,name',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Simpan group_name (opsional, sesuaikan dengan model atau kebutuhan)
            $groupName = $request->input('group_name');

            foreach ($request->permissions as $permission) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web',
                    'group_name' => $groupName,
                ]);
            }

            return redirect()->route('permissions.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            return back()->with('failed', 'Data Gagal Ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->checkPermission('permissions.edit');
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json($role);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission('permissions.edit');

        $request->validate([
            'group_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);

        $permission->update([
            'group' => $request->group_name,
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission('permissions.delete');

        try {
            $permissions = Permission::findOrFail($id);
            $permissions->delete();

            return response()->json(['success' => 'Data Berhasil Dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
}
