<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
            // Menampilkan halaman awal level
            public function index()
            {
                $breadcrumb = (object) [
                    'title' => 'Daftar Barang',
                    'list' => ['Home', 'Barang']
                ];
        
                $page = (object) [
                    'title' => 'Daftar Barang yang terdaftar dalam sistem'
                ];
        
                $activeMenu = 'barang'; // set menu yang sedang aktif

                $kategori = KategoriModel::all();
        
                return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
            }
        
            // Ambil data level dalam bentuk json untuk datatables
            public function list(Request $request)
            {
                $barangs = BarangModel::select('barang_id', 'kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
                ->with('kategori');

                // Filter data user berdasarkan level_id
                if ($request->kategori_id) {
                    $barangs->where('kategori_id', $request->kategori_id);
                }
                return DataTables::of($barangs)
                    ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom:DT_RowIndex)
                    ->addColumn('aksi', function ($barang) { // menambahkan kolom aksi
                        /* $btn = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btnsm">Detail</a> ';
                    $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btnwarning btn-sm">Edit</a> ';
                    $btn .= '<form class="d-inline-block" method="POST" action="'. url('/user/'.$user-
                    >user_id).'">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';*/
                        $btn = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                            '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                        $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                            '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                        $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                            '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                        return $btn;
                    })->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
                    ->make(true);
            }
        
            // Menampilkan halaman form tambah level
            public function create()
            {
                $breadcrumb = (object) [
                    'title' => 'Tambah Barang',
                    'list' => ['Home', 'Barang', 'Tambah']
                ];
        
                $page = (object) [
                    'title' => 'Tambah barang baru'
                ];
        
                $kategori = KategoriModel::all();
                $activeMenu = 'barang'; // set menu yang sedang aktif
        
                return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
            }
        
            // Menyimpan data level baru
            public function store(Request $request)
            {
                $request->validate([
                'barang_kode' => 'required|string|max:10|unique:m_barang,barang_kode',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|integer',
                'harga_jual' => 'required|integer',
                'kategori_id' => 'required|integer'
                ]);
        
                BarangModel::create([
                    'barang_kode' => $request->barang_kode,
                    'barang_nama' => $request->barang_nama,
                    'harga_beli' => $request->harga_beli,
                    'harga_jual' => $request->harga_jual,
                    'kategori_id' => $request->kategori_id
                ]);
        
                return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
            }
        
            // Menampilkan detail level
            public function show(string $id)
            {
                $barang = BarangModel::with('kategori')->find($id);
        
                $breadcrumb = (object) [
                    'title' => 'Detail Barang',
                    'list' => ['Home', 'Barang', 'Detail']
                ];
        
                $page = (object) [
                    'title' => 'Detail Barang'
                ];
        
                $activeMenu = 'barang'; // set menu yang sedang aktif
        
                return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
            }
        
            // Menampilkan halaman form edit level
            public function edit(string $id)
            {
                $barang = BarangModel::find($id);
                $kategori = KategoriModel::all();
        
                $breadcrumb = (object) [
                    'title' => 'Edit Barang',
                    'list' => ['Home', 'Barang', 'Edit']
                ];
        
                $page = (object) [
                    'title' => 'Edit Barang'
                ];
        
                $activeMenu = 'barang'; // set menu yang sedang aktif
        
                return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
            }
        
            // Menyimpan perubahan data level
            public function update(Request $request, string $id)
            {
                $request->validate([
                    'barang_kode' => 'required|string|max:10|unique:m_barang,barang_kode,' . $id . ',barang_id',
                    'barang_nama' => 'required|string|max:100',
                    'harga_beli' => 'required|integer',
                    'harga_jual' => 'required|integer',
                    'kategori_id' => 'required|integer'
                ]);
        
                BarangModel::find($id)->update([
                    'barang_nama' => $request->barang_nama,
                    'harga_beli' => $request->harga_beli,
                    'harga_jual' => $request->harga_jual,
                    'kategori_id' => $request->kategori_id
                ]);
        
                return redirect('/barang')->with('success', 'Data barang berhasil diubah');
            }
        
            // Menghapus data level
            public function destroy(string $id)
            {
                $check = BarangModel::find($id); // untuk mengecek apakah data level dengan id yang dimaksud ada atau tidak
                if (!$check) {
                    return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
                }
        
                try {
                    BarangModel::destroy($id); // Hapus data level
                    return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
                } catch (\Illuminate\Database\QueryException $e) {
                    // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
                    return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
                }
            }

            public function create_ajax()
            {
                $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
                return view('barang.create_ajax')->with('kategori', $kategori);
            }
        
            public function store_ajax(Request $request)
            {
                // cek apakah request berupa ajax
                if ($request->ajax() || $request->wantsJson()) {
                    $rules = [
                        'kategori_id' => 'required|integer',
                        'barang_kode' => 'required|string|min:3|max:10|unique:m_barang,barang_kode',
                        'barang_nama' => 'required|string|min:3|max:100',
                        'harga_beli' => 'required|integer|min:3',
                        'harga_jual' => 'required|integer|min:3'
                    ];
        
        
                    $validator = Validator::make($request->all(), $rules);
        
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false, // response status, false: error/gagal, true: berhasil
                            'message' => 'Validasi Gagal',
                            'msgField' => $validator->errors() // pesan error validasi
                        ]);
                    }
        
                    BarangModel::create($request->all());
        
                    return response()->json([
                        'status' => true,
                        'message' => 'Data barang berhasil disimpan'
                    ]);
                }
        
                redirect('/');
            }

            public function edit_ajax(string $id)
            {
                $barang = BarangModel::find($id);
                $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
                return view('barang.edit_ajax', ['barang' => $barang, 'kategori' => $kategori]);
            }

            public function update_ajax(Request $request, $id)
            {
                // cek apakah request dari ajax
                if ($request->ajax() || $request->wantsJson()) {
                    $rules = [
                        'barang_kode' => 'required|string|max:10|unique:m_barang,barang_kode,' . $id . ',barang_id',
                        'barang_nama' => 'required|string|max:100',
                        'harga_beli' => 'required|integer',
                        'harga_jual' => 'required|integer',
                        'kategori_id' => 'required|integer'
                    ];
                    // use Illuminate\Support\Facades\Validator;
                    $validator = Validator::make($request->all(), $rules);
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false, // respon json, true: berhasil, false: gagal
                            'message' => 'Validasi gagal.',
                            'msgField' => $validator->errors() // menunjukkan field mana yang error
                        ]);
                    }
                    $check = BarangModel::find($id);
                    if ($check) {
                        if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request
                            $request->request->remove('password');
                        }
                        $check->update($request->all());
                        return response()->json([
                            'status' => true,
                            'message' => 'Data berhasil diupdate'
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Data tidak ditemukan'
                        ]);
                    }
                }
                return redirect('/');
            }

            public function confirm_ajax(string $id) {
                $barang = BarangModel::find($id);
            
                return view('barang.confirm_ajax', ['barang' => $barang]);
            }

            public function delete_ajax(Request $request, $id)
            {
            // cek apakah request dari ajax
            if ($request->ajax() || $request->wantsJson()) {
                try {
                    $barang = BarangModel::find($id);
                    if ($barang) {
                        $barang->delete();
                        return response()->json([
                            'status' => true,
                            'message' => 'Data berhasil dihapus'
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Data tidak ditemukan'
                        ]);
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
                    ]);
                }
            }
        
            return redirect('/');
            }

            public function show_ajax(string $id)
            {
                $barang = BarangModel::find($id);
                $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
                return view('barang.show_ajax', ['barang' => $barang, 'kategori' => $kategori]);
            }  

            public function import()
            {
                return view('barang.import'); // file view: resources/views/barang/import.blade.php
            }

            public function import_ajax(Request $request)
            {
                if($request->ajax() || $request->wantsJson()){
                    $rules = [
                        'file_barang' => ['required', 'mimes:xlsx', 'max:1024']
                    ];
            
                    $validator = Validator::make($request->all(), $rules);
                    if($validator->fails()){
                        return response()->json([
                            'status' => false,
                            'message' => 'Validasi Gagal',
                            'msgField' => $validator->errors()
                        ]);
                    }
            
                    $file = $request->file('file_barang');
                    $reader = IOFactory::createReader('Xlsx');
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($file->getRealPath());
                    $sheet = $spreadsheet->getActiveSheet();
                    $data = $sheet->toArray(null, false, true, true);
            
                    $insert = [];
                    if(count($data) > 1){
                        foreach ($data as $baris => $value) {
                            if($baris > 1){
                                $insert[] = [
                                    'kategori_id' => $value['A'],
                                    'barang_kode' => $value['B'],
                                    'barang_nama' => $value['C'],
                                    'harga_beli' => $value['D'],
                                    'harga_jual' => $value['E'],
                                    'created_at' => now(),
                                ];
                            }
                        }
            
                        if(count($insert) > 0){
                            BarangModel::insertOrIgnore($insert);
                        }
            
                        return response()->json([
                            'status' => true,
                            'message' => 'Data berhasil diimport'
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Tidak ada data yang diimport'
                        ]);
                    }
                }
                return redirect('/');
            }            
}
