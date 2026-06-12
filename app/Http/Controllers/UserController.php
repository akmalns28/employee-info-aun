<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
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

    public function getAllUser()
    {
        $this->checkPermission('user.view');

        $users = User::with('departemen')->get(); //

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('user.edit')) {
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

                if (auth()->user()->can('user.delete')) {
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
        $this->checkPermission('user.view');

        $data['departemen'] = Departemen::all();
        $data['all_permissions'] = Permission::all();
        $data['permission_groups'] = $this->getpermissionGroups();
        $data['roles'] = Role::orderBy('name')->get();

        return view('user.index', $data);
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
        //
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
    public function edit(string $id)
    {
        $this->checkPermission('user.edit');
        $user = User::with('roles.permissions', 'permissions')->where('uuid', $id)->firstOrFail();

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $this->checkPermission('user.edit');
        $user = User::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'nama_depan' => 'required|string|max:100',
            'nama_belakang' => 'nullable|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'role' => 'nullable|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        DB::beginTransaction();

        try {
            $payload = [
                'nama_depan' => $validated['nama_depan'],
                'nama_belakang' => $validated['nama_belakang'] ?? null,
                'email' => $validated['email'],
            ];

            if ($request->hasFile('avatar')) {
                if ($user->avatar && !str_contains($user->avatar, 'googleusercontent.com') && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $payload['avatar'] = $request->file('avatar')->store('user/avatar', 'public');
            }

            $payload['slug'] = Str::slug(trim($validated['nama_depan'] . ' ' . ($validated['nama_belakang'] ?? '')));

            $user->update($payload);
            if (!empty($validated['role'])) {
                $user->syncRoles([$validated['role']]);
            } else {
                $user->syncRoles([]);
            }

            $user->syncPermissions($validated['permissions'] ?? []);
            DB::commit();

            return response()->json([
                'message' => 'Data user berhasil diperbarui.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(
                [
                    'message' => 'Terjadi kesalahan saat memperbarui data.',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission('user.delete');
        
        $user = User::where('uuid', $id)->firstOrFail();
        $user->delete();
        return response()->json(['success' => 'Data Berhasil Dihapus'], 200);
    }

    public function editProfile()
    {
        $this->checkPermission('user.profile');
        $data['departemen'] = Departemen::all();
        return view('auth.lengkapi-data', $data);
    }

    public function updateProfile(Request $request)
    {
        $this->checkPermission('user.profile');
        $request->validate([
            'nip' => 'required|unique:users,nip,' . auth()->id(),
            'no_hp' => 'required|unique:users,no_hp,' . auth()->id(),
            'departemen_uuid' => 'required|exists:departemens,uuid',
        ]);

        $user = auth()->user();
        $user->update([
            'nip' => $request->nip,
            'no_hp' => $request->no_hp,
            'departemen_uuid' => $request->departemen_uuid,
            'status' => 'aktif',
        ]);

        return redirect()->route('dashboard')->with('success', 'Profil berhasil dilengkapi!');
    }
}
