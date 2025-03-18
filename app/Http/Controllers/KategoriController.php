<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
        // Menampilkan halaman awal level
        public function index()
        {
            $breadcrumb = (object) [
                'title' => 'Daftar Kategori',
                'list' => ['Home', 'Kategori']
            ];
    
            $page = (object) [
                'title' => 'Daftar Kategori yang terdaftar dalam sistem'
            ];
    
            $activeMenu = 'kategori'; // set menu yang sedang aktif
    
    
            return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
        }
    
        // Ambil data level dalam bentuk json untuk datatables
        public function list(Request $request)
        {
            $kategoris = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

            return DataTables::of($kategoris)
                ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom:DT_RowIndex)
                ->addColumn('aksi', function ($kategori) { // menambahkan kolom aksi
                    /* $btn = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btnsm">Detail</a> ';
                   $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btnwarning btn-sm">Edit</a> ';
                   $btn .= '<form class="d-inline-block" method="POST" action="'. url('/user/'.$user-
                   >user_id).'">'
                   . csrf_field() . method_field('DELETE') .
                   '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';*/
                    $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                        '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                        '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                        '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                    return $btn;
                })->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
                ->make(true);
        }
    
        // Menampilkan halaman form tambah level
        public function create()
        {
            $breadcrumb = (object) [
                'title' => 'Tambah Kategori',
                'list' => ['Home', 'Kategori', 'Tambah']
            ];
    
            $page = (object) [
                'title' => 'Tambah kategori baru'
            ];
    
            $kategori = KategoriModel::all(); // ambil data level untuk ditampilkan di form
            $activeMenu = 'kategori'; // set menu yang sedang aktif
    
            return view('kategori.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
        }
    
        // Menyimpan data level baru
        public function store(Request $request)
        {
            $request->validate([
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100'
            ]);
    
            KategoriModel::create([
                'kategori_kode' => $request->kategori_kode,
                'kategori_nama' => $request->kategori_nama
            ]);
    
            return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
        }
    
        // Menampilkan detail level
        public function show(string $id)
        {
            $kategori = KategoriModel::find($id);
    
            $breadcrumb = (object) [
                'title' => 'Detail Kategori',
                'list' => ['Home', 'Kategori', 'Detail']
            ];
    
            $page = (object) [
                'title' => 'Detail Kategori'
            ];
    
            $activeMenu = 'kategori'; // set menu yang sedang aktif
    
            return view('kategori.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
        }
    
        // Menampilkan halaman form edit level
        public function edit(string $id)
        {
            $kategori = KategoriModel::find($id);
    
            $breadcrumb = (object) [
                'title' => 'Edit Kategori',
                'list' => ['Home', 'Kategori', 'Edit']
            ];
    
            $page = (object) [
                'title' => 'Edit Kategori'
            ];
    
            $activeMenu = 'kategori'; // set menu yang sedang aktif
    
            return view('kategori.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
        }
    
        // Menyimpan perubahan data level
        public function update(Request $request, string $id)
        {
            $request->validate([
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100'
            ]);
    
            KategoriModel::find($id)->update([
                'kategori_kode' => $request->kategori_kode,
                'kategori_nama' => $request->kategori_nama
            ]);
    
            return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
        }
    
        // Menghapus data level
        public function destroy(string $id)
        {
            $check = KategoriModel::find($id); // untuk mengecek apakah data level dengan id yang dimaksud ada atau tidak
            if (!$check) {
                return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
            }
    
            try {
                KategoriModel::destroy($id); // Hapus data level
                return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
            } catch (\Illuminate\Database\QueryException $e) {
                // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
                return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
            }
        }

        public function create_ajax()
        {
            return view('kategori.create_ajax');
        }
        
        public function store_ajax(Request $request)
        {
            // cek apakah request berupa ajax
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100'
                ];
    
    
                $validator = Validator::make($request->all(), $rules);
    
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false, // response status, false: error/gagal, true: berhasil
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors() // pesan error validasi
                    ]);
                }
    
                KategoriModel::create($request->all());
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data kategori berhasil disimpan'
                ]);
            }
    
            redirect('/');
        }

        public function edit_ajax(string $id)
        {
            $kategori = KategoriModel::find($id);
            return view('kategori.edit_ajax', ['kategori' => $kategori]);
        }

        public function update_ajax(Request $request, string $id)
        {
            // cek apakah request berupa ajax
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode,'.$id.',kategori_id',
                'kategori_nama' => 'required|string|max:100'
                ];
    
                $validator = Validator::make($request->all(), $rules);
    
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false, // response status, false: error/gagal, true: berhasil
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors() // pesan error validasi
                    ]);
                }
    
                KategoriModel::find($id)->update($request->all());
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data kategori berhasil diubah'
                ]);
            }
    
            redirect('/');
        }

        public function confirm_ajax(string $id) {
            $kategori = KategoriModel::find($id);
        
            return view('kategori.confirm_ajax', ['kategori' => $kategori]);
        }

        public function delete_ajax(Request $request, $id)
        {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $kategori = KategoriModel::find($id);
                if ($kategori) {
                    $kategori->delete();
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
            $kategori = KategoriModel::find($id);
            return view('kategori.show_ajax', ['kategori' => $kategori]);
        }
}
