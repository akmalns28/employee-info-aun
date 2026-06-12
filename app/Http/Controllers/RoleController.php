<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public static function getpermissionGroups()
    {
        $permission_groups = DB::table('permissions')->select('group_name as name')->groupBy('group_name')->get();
        return $permission_groups;
    }

    public static function getpermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')->select('name', 'id')->where('group_name', $group_name)->orderBy('name', 'desc')->get();
        return $permissions;
    }

    public static function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
                return $hasPermission;
            }
        }
        return $hasPermission;
    }

    public function getAllRole(Request $request)
    {
        $this->checkPermission('role.view');

        $role = Role::all();
        return DataTables::of($role)
            ->addIndexColumn()
             ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('role.edit')) {
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

                if (auth()->user()->can('role.delete')) {
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

    public function index()
    {
        $this->checkPermission('role.view');

        $data['all_permissions'] = Permission::all();
        $data['permission_groups'] = $this->getpermissionGroups();

        return view('role.index', $data);
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
        $this->checkPermission('role.create');

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100|unique:roles',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Buat role baru
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

            // Filter izin untuk menghapus grup
            $permissions = $request->input('permissions', []);

            // Sinkronisasi permissions
            $role->givePermissionTo($permissions);

            return redirect()->route('role.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            return back()->with('failed', 'Data Gagal Ditambahkan: ' . $e->getMessage());
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
    public function edit(Role $role)
    {
        $this->checkPermission('role.edit');
        $role->load('permissions');

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
            ]);

            $role->update([
                'name' => $validated['name'],
            ]);

            $permissions = $validated['permissions'] ?? [];

            $role->syncPermissions($permissions);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diperbarui',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Update gagal',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->checkPermission('role.delete');
        $role = Role::findOrFail($id);

        try {
            DB::beginTransaction();

            $role->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menghapus role',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
