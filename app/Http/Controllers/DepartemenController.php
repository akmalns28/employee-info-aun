<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getAllDepartemen()
    {
        $this->checkPermission('departemen.view');

        $data = Departemen::all();

        return DataTables::of($data)
            ->addIndexColumn()
             ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('departemen.edit')) {
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

                if (auth()->user()->can('departemen.delete')) {
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
        $this->checkPermission('departemen.view');

        return view('departemen.index');
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

        try {
            $request->validate([
                'kode_departemen' => 'required|unique:departemens,kode_departemen|max:20',
                'departemen' => 'required|string|max:100',
            ]);

            Departemen::create($request->all());

            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('departemen.edit');

        try {
            $departemen = Departemen::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'kode_departemen' => 'required|max:20|unique:departemens,kode_departemen,' . $departemen->id,
                'departemen' => 'required|string|max:100',
            ]);

            $departemen->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil diperbarui',
                ]);
            }

            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        } catch (\Throwable $th) {
            if ($request->ajax()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $th->getMessage(),
                    ],
                    500,
                );
            }

            return back()->with('error', $th->getMessage());
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
