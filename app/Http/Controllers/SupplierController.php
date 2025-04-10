<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
                ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom:DT_RowIndex)
                ->addColumn('aksi', function ($supplier) { // menambahkan kolom aksi
                    /* $btn = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btnsm">Detail</a> ';
                   $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btnwarning btn-sm">Edit</a> ';
                   $btn .= '<form class="d-inline-block" method="POST" action="'. url('/user/'.$user-
                   >user_id).'">'
                   . csrf_field() . method_field('DELETE') .
                   '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';*/
                    $btn = '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id .
                        '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id .
                        '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id .
                        '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                    return $btn;
                })->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
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

        public function create_ajax()
        {
            return view('supplier.create_ajax');
        }

        public function store_ajax(Request $request)
        {
            // cek apakah request berupa ajax
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'supplier_kode' => 'required|string|max:10|unique:m_supplier,supplier_kode',
                    'supplier_nama' => 'required|string|max:100',
                    'supplier_alamat' => 'required|string|max:255'
                ];
    
    
                $validator = Validator::make($request->all(), $rules);
    
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false, // response status, false: error/gagal, true: berhasil
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors() // pesan error validasi
                    ]);
                }
    
                SupplierModel::create($request->all());
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil disimpan'
                ]);
            }
    
            redirect('/');
        }

        public function edit_ajax(string $id)
        {
            $supplier = SupplierModel::find($id);
            return view('supplier.edit_ajax', ['supplier' => $supplier]);
        }

        public function update_ajax(Request $request, string $id)
        {
            // cek apakah request berupa ajax
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'supplier_kode' => 'required|string|max:10|unique:m_supplier,supplier_kode,'.$id.',supplier_id',
                    'supplier_nama' => 'required|string|max:100',
                    'supplier_alamat' => 'required|string|max:255'
                ];
    
                $validator = Validator::make($request->all(), $rules);
    
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false, // response status, false: error/gagal, true: berhasil
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors() // pesan error validasi
                    ]);
                }
    
                SupplierModel::find($id)->update($request->all());
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil diubah'
                ]);
            }
    
            redirect('/');
        }

        public function confirm_ajax(string $id) {
            $supplier = SupplierModel::find($id);
        
            return view('supplier.confirm_ajax', ['supplier' => $supplier]);
        }
        
        public function delete_ajax(Request $request, $id)
        {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $supplier = SupplierModel::find($id);
                if ($supplier) {
                    $supplier->delete();
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
            $supplier = SupplierModel::find($id);
            return view('supplier.show_ajax', ['supplier' => $supplier]);
        }

        public function import()
        {
            return view('supplier.import'); 
        }
    
        public function import_ajax(Request $request)
        {
            if($request->ajax() || $request->wantsJson()){
                $rules = [
                    'file_supplier' => ['required', 'mimes:xlsx', 'max:1024']
                ];
        
                $validator = Validator::make($request->all(), $rules);
                if($validator->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors()
                    ]);
                }
        
                $file = $request->file('file_supplier');
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
                                'supplier_kode' => $value['A'],
                                'supplier_nama' => $value['B'],
                                'supplier_alamat' => $value['C'],
                                'created_at' => now(),
                            ];
                        }
                    }
        
                    if(count($insert) > 0){
                        SupplierModel::insertOrIgnore($insert);
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

        public function export_excel()
        {
            // ambil data barang yang akan di export
            $barang = SupplierModel::select('supplier_kode', 'supplier_nama', 'supplier_alamat')
                                ->orderBy('supplier_kode')
                                ->get();
    
                // load library excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
    
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode Supplier');
            $sheet->setCellValue('C1', 'Nama Supplier');
            $sheet->setCellValue('D1', 'Alamat Supplier');
    
    
            $sheet->getStyle('A1:D1')->getFont()->setBold(true); // bold header
    
            $no = 1;         // nomor data dimulai dari 1
            $baris = 2;      // baris data dimulai dari baris ke 2
            foreach ($barang as $key => $value) {
                $sheet->setCellValue('A'.$baris, $no);
                $sheet->setCellValue('B'.$baris, $value->supplier_kode);
                $sheet->setCellValue('C'.$baris, $value->supplier_nama);
                $sheet->setCellValue('D'.$baris, $value->supplier_alamat);
                $baris++;
                $no++;
            }
            
            foreach (range('A', 'D') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
            }
    
            $sheet->setTitle('Data Supplier'); // set title sheet
    
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'Data Supplier' . date('Y-m-d_H-i-s') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            
            $writer->save('php://output');
            
            exit;
        } //end function export_excel
                 
        public function export_pdf()
        {
            $supplier = SupplierModel::select('supplier_kode', 'supplier_nama', 'supplier_alamat')
                                ->orderBy('supplier_kode')
                                ->get();
        
            // use Barryvdh\DomPDF\Facade\Pdf;
            $pdf = Pdf::loadView('supplier.export_pdf', ['supplier' => $supplier]);
            $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
            $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
            $pdf->render();
            return $pdf->stream('Data Supplier_' . date('Y-m-d_H-i-s') . '.pdf');
        }
}
