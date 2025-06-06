<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

        public function import()
        {
            return view('kategori.import'); 
        }
    
        public function import_ajax(Request $request)
        {
            if($request->ajax() || $request->wantsJson()){
                $rules = [
                    'file_kategori' => ['required', 'mimes:xlsx', 'max:1024']
                ];
        
                $validator = Validator::make($request->all(), $rules);
                if($validator->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors()
                    ]);
                }
        
                $file = $request->file('file_kategori');
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
                                'kategori_kode' => $value['A'],
                                'kategori_nama' => $value['B'],
                                'created_at' => now(),
                            ];
                        }
                    }
        
                    if(count($insert) > 0){
                        KategoriModel::insertOrIgnore($insert);
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
            $barang = KategoriModel::select('kategori_kode', 'kategori_nama')
                                ->orderBy('kategori_kode')
                                ->get();
    
                // load library excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
    
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode Kategori');
            $sheet->setCellValue('C1', 'Nama Kategori');
    
    
            $sheet->getStyle('A1:C1')->getFont()->setBold(true); // bold header
    
            $no = 1;         // nomor data dimulai dari 1
            $baris = 2;      // baris data dimulai dari baris ke 2
            foreach ($barang as $key => $value) {
                $sheet->setCellValue('A'.$baris, $no);
                $sheet->setCellValue('B'.$baris, $value->kategori_kode);
                $sheet->setCellValue('C'.$baris, $value->kategori_nama);
                $baris++;
                $no++;
            }
            
            foreach (range('A', 'C') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
            }
    
            $sheet->setTitle('Data Kategori'); // set title sheet
    
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'Data Kategori_' . date('Y-m-d_H-i-s') . '.xlsx';
            
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
            $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')
                                ->orderBy('kategori_kode')
                                ->get();
        
            // use Barryvdh\DomPDF\Facade\Pdf;
            $pdf = Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
            $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
            $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
            $pdf->render();
            return $pdf->stream('Data Kategori' . date('Y-m-d_H-i-s') . '.pdf');
        }
}
