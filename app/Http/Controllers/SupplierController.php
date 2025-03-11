<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
        // Menampilkan halaman awal level
        public function index()
        {
            $breadcrumb = (object) [
                'title' => 'Daftar Supplier',
                'list' => ['Home', 'Supplier']
            ];
    
            $page = (object) [
                'title' => 'Daftar Supplier yang terdaftar dalam sistem'
            ];
    
            $activeMenu = 'supplier'; // set menu yang sedang aktif
    
    
            return view('supplier.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
        }
    
        // Ambil data level dalam bentuk json untuk datatables
        public function list(Request $request)
        {
            $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');
    
            return DataTables::of($suppliers)
                // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
                ->addIndexColumn()
                ->addColumn('aksi', function ($supplier) { // menambahkan kolom aksi
                    $btn = '<a href="'.url('/supplier/' . $supplier->supplier_id).'" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="'.url('/supplier/' . $supplier->supplier_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                    $btn .= '<form class="d-inline-block" method="POST" action="'.url('/supplier/' . $supplier->supplier_id).'">'
                        . csrf_field() . method_field('DELETE') .
                        '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                    return $btn;
                })
                ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
                ->make(true);
        }
    
        // Menampilkan halaman form tambah level
        public function create()
        {
            $breadcrumb = (object) [
                'title' => 'Tambah Supplier',
                'list' => ['Home', 'Supplier', 'Tambah']
            ];
    
            $page = (object) [
                'title' => 'Tambah supplier baru'
            ];
    
            $supplier = SupplierModel::all(); // ambil data level untuk ditampilkan di form
            $activeMenu = 'supplier'; // set menu yang sedang aktif
    
            return view('supplier.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
        }
    
        // Menyimpan data level baru
        public function store(Request $request)
        {
            $request->validate([
            'supplier_kode' => 'required|string|max:10|unique:m_supplier,supplier_kode',
            'supplier_nama' => 'required|string|max:100',
            'supplier_alamat' => 'required|string|max:255'
            ]);
    
            SupplierModel::create([
                'supplier_kode' => $request->supplier_kode,
                'supplier_nama' => $request->supplier_nama,
                'supplier_alamat' => $request->supplier_alamat
            ]);
    
            return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
        }
    
        // Menampilkan detail level
        public function show(string $id)
        {
            $supplier = SupplierModel::find($id);
    
            $breadcrumb = (object) [
                'title' => 'Detail Supplier',
                'list' => ['Home', 'Supplier', 'Detail']
            ];
    
            $page = (object) [
                'title' => 'Detail Supplier'
            ];
    
            $activeMenu = 'supplier'; // set menu yang sedang aktif
    
            return view('supplier.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
        }
    
        // Menampilkan halaman form edit level
        public function edit(string $id)
        {
            $supplier = SupplierModel::find($id);
    
            $breadcrumb = (object) [
                'title' => 'Edit Supplier',
                'list' => ['Home', 'Supplier', 'Edit']
            ];
    
            $page = (object) [
                'title' => 'Edit supplier'
            ];
    
            $activeMenu = 'supplier'; // set menu yang sedang aktif
    
            return view('supplier.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
        }
    
        // Menyimpan perubahan data level
        public function update(Request $request, string $id)
        {
            $request->validate([
                'supplier_kode' => 'required|string|max:10|unique:m_supplier,supplier_kode',
                'supplier_nama' => 'required|string|max:100',
                'supplier_alamat' => 'required|string|max:255'
            ]);
    
            SupplierModel::find($id)->update([
                'supplier_kode' => $request->supplier_kode,
                'supplier_nama' => $request->supplier_nama,
                'supplier_alamat' => $request->supplier_alamat
            ]);
    
            return redirect('/supplier')->with('success', 'Data supplier berhasil diubah');
        }
    
        // Menghapus data level
        public function destroy(string $id)
        {
            $check = SupplierModel::find($id); // untuk mengecek apakah data level dengan id yang dimaksud ada atau tidak
            if (!$check) {
                return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
            }
    
            try {
                SupplierModel::destroy($id); // Hapus data level
                return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
            } catch (\Illuminate\Database\QueryException $e) {
                // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
                return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
            }
        }
}
