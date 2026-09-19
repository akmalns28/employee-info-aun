<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getAllDepartemen()
    {
        $this->checkPermission('departemen.view');

        $data = Departemen::with('divisis')->get();

        return DataTables::of($data)
            ->addIndexColumn()

            ->addColumn('divisi', function ($row) {
                if ($row->divisis->isEmpty()) {
                    return '-';
                }

                return $row->divisis
                    ->pluck('nama_divisi')
                    ->map(function ($divisi) {
                        return '<span class="badge bg-blue-lt me-1 mb-1">' . e($divisi) . '</span>';
                    })
                    ->implode(' ');
            })

            ->addColumn('jumlah_divisi', function ($row) {
                return $row->divisis->count();
            })

            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('departemen.edit')) {
                    $buttons .= '
                    <button
                        class="btn btn-warning btn-icon edit-button"
                    >
 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                    <path d="M13.5 6.5l4 4" />
                </svg>                    </button>
                ';
                }

                if (auth()->user()->can('departemen.delete')) {
                    $buttons .= '
                    <button
                        class="btn btn-danger btn-icon delete-button"
                    >
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
                </svg>                    </button>
                ';
                }

                return $buttons ?: '-';
            })

            ->rawColumns(['divisi', 'action'])

            ->make(true);
    }

    public function index()
    {
        $this->checkPermission('departemen.view');

        $divisi = Divisi::orderBy('nama_divisi')->get();

        return view('departemen.index', compact('divisi'));
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
        $this->checkPermission('departemen.create');

        $validated = $request->validate([
            'kode_departemen' => ['required', 'string', 'max:20', 'unique:departemens,kode_departemen'],

            'departemen' => ['required', 'string', 'max:100', 'unique:departemens,departemen'],

            'divisi' => ['required', 'array', 'min:1'],

            'divisi.*' => ['required', 'string', 'max:100', 'distinct'],
        ]);

        DB::beginTransaction();

        try {
            $departemen = Departemen::create([
                'uuid' => (string) Str::uuid(),
                'kode_departemen' => strtoupper($validated['kode_departemen']),
                'departemen' => $validated['departemen'],
            ]);

            foreach ($validated['divisi'] as $namaDivisi) {
                Divisi::create([
                    'uuid' => (string) Str::uuid(),
                    'uuid_departemen' => $departemen->uuid,
                    'nama_divisi' => trim($namaDivisi),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Departemen dan divisi berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
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
    public function edit(string $uuid)
    {
        $this->checkPermission('departemen.edit');

        $departemen = Departemen::with('divisis')->where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'uuid' => $departemen->uuid,
            'kode_departemen' => $departemen->kode_departemen,
            'departemen' => $departemen->departemen,
            'divisis' => $departemen->divisis
                ->map(function ($divisi) {
                    return [
                        'uuid' => $divisi->uuid,
                        'nama_divisi' => $divisi->nama_divisi,
                    ];
                })
                ->values(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('departemen.edit');

        $departemen = Departemen::with('divisis')->where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'kode_departemen' => ['required', 'string', 'max:20', Rule::unique('departemens', 'kode_departemen')->ignore($departemen->id)],
            'departemen' => ['required', 'string', 'max:100', Rule::unique('departemens', 'departemen')->ignore($departemen->id)],
            'divisi' => ['required', 'array', 'min:1'],
            'divisi.*.uuid' => ['nullable', 'uuid'],
            'divisi.*.nama_divisi' => ['required', 'string', 'max:100'],
        ]);

        DB::beginTransaction();

        try {
            $departemen->update([
                'kode_departemen' => strtoupper($validated['kode_departemen']),
                'departemen' => $validated['departemen'],
            ]);

            $submittedUuids = collect($validated['divisi'])->pluck('uuid')->filter()->values()->all();

            Divisi::where('uuid_departemen', $departemen->uuid)->whereNotIn('uuid', $submittedUuids)->delete();

            foreach ($validated['divisi'] as $item) {
                if (!empty($item['uuid'])) {
                    Divisi::where('uuid', $item['uuid'])
                        ->where('uuid_departemen', $departemen->uuid)
                        ->update([
                            'nama_divisi' => trim($item['nama_divisi']),
                        ]);

                    continue;
                }

                Divisi::create([
                    'uuid' => (string) Str::uuid(),
                    'uuid_departemen' => $departemen->uuid,
                    'nama_divisi' => trim($item['nama_divisi']),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Departemen dan divisi berhasil diperbarui.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $this->checkPermission('departemen.delete');

        try {
            $departemen = Departemen::where('uuid', $uuid)->firstOrFail();
            $departemen->delete();
            return response()->json(['success' => 'Data Berhasil Dihapus'], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
