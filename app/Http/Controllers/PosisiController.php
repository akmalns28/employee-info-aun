<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PosisiController extends Controller
{
    public function getAllPosisi()
    {
        $this->checkPermission('posisi.view');

        $data = Posisi::withCount('karyawans')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('posisi.edit')) {
                    $buttons .=
                        '
                        <button class="btn btn-warning btn-icon edit-button" data-uuid="' .
                        $row->uuid .
                        '">
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

                if (auth()->user()->can('posisi.delete')) {
                    $buttons .=
                        '
                        <button class="btn btn-danger btn-icon delete-button" data-uuid="' .
                        $row->uuid .
                        '">
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
        $this->checkPermission('posisi.view');

        return view('posisi.index');
    }

    public function store(Request $request)
    {
        $this->checkPermission('posisi.create');

        try {
            $validated = $request->validate([
                'kode_posisi' => 'required|unique:posisis,kode_posisi|max:20',
                'nama_posisi' => 'required|string|max:100',
            ]);

            Posisi::create($validated);

            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit(string $uuid)
    {
        $this->checkPermission('posisi.edit');

        $posisi = Posisi::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'uuid' => $posisi->uuid,
            'kode_posisi' => $posisi->kode_posisi,
            'nama_posisi' => $posisi->nama_posisi,
        ]);
    }

    public function update(Request $request, string $uuid)
    {
        $this->checkPermission('posisi.edit');

        try {
            $posisi = Posisi::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'kode_posisi' => 'required|max:20|unique:posisis,kode_posisi,' . $posisi->id,
                'nama_posisi' => 'required|string|max:100',
            ]);

            $posisi->update($validated);

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

    public function destroy(string $uuid)
    {
        $this->checkPermission('posisi.delete');

        try {
            $posisi = Posisi::withCount('karyawans')->where('uuid', $uuid)->firstOrFail();

            if ($posisi->karyawans_count > 0) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Posisi tidak dapat dihapus karena masih digunakan oleh karyawan.',
                    ],
                    422,
                );
            }

            $posisi->delete();

            return response()->json(
                [
                    'success' => 'Data Berhasil Dihapus',
                ],
                200,
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
