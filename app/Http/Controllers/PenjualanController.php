<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use App\Models\PenjualanModel;
use Illuminate\Support\Facades\DB;
use App\Models\PenjualanDetailModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list'  => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar penjualan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';


        $users = UserModel::all();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'users'      => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select(
            'penjualan_id',
            'user_id',
            'pembeli',
            'penjualan_kode',
            'penjualan_tanggal'
        )
        ->with('user'); 

        // Filter data berdasarkan user_id
        $user_id = $request->input('user_id');
        if (!empty($user_id)) {
            $penjualans->where('user_id', $user_id);
        }

        return DataTables::of($penjualans)
            ->addIndexColumn() 
            ->addColumn('aksi', function ($penjualans) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualans->penjualan_id . 
                '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualans->penjualan_id . 
                '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualans->penjualan_id .
                '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Menampilkan halaman form tambah penjualan
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Penjualan',
            'list'  => ['Home', 'Penjualan', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah penjualan baru'
        ];

        $activeMenu = 'penjualan';

        $users = UserModel::all();

        return view('penjualan.create', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'activeMenu' => $activeMenu,
            'users'      => $users
        ]);
    }

    // Menyimpan data penjualan baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|integer',
            'pembeli'          => 'required|string|max:100',
            'penjualan_kode'   => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal'=> 'required|date',
        ]);

        PenjualanModel::create([
            'user_id'          => $request->user_id,
            'pembeli'          => $request->pembeli,
            'penjualan_kode'   => $request->penjualan_kode,
            'penjualan_tanggal'=> $request->penjualan_tanggal,
        ]);

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil disimpan');
    }

    // Menampilkan detail penjualan
    public function show(string $id)
    {
        // Gunakan with('user') agar info user (kasir) dapat ditampilkan
        $penjualan = PenjualanModel::with('user')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Penjualan',
            'list'  => ['Home', 'Penjualan', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail penjualan'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.show', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'penjualan'  => $penjualan,
            'activeMenu' => $activeMenu
        ]);
    }

    // Menampilkan halaman form edit penjualan
    public function edit(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Penjualan',
            'list'  => ['Home', 'Penjualan', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit penjualan'
        ];

        $activeMenu = 'penjualan';

        $users = UserModel::all();

        return view('penjualan.edit', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'penjualan'  => $penjualan,
            'users'      => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    // Menyimpan perubahan data penjualan
    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_id'          => 'required|integer',
            'pembeli'          => 'required|string|max:100',
            'penjualan_kode'   => 'required|string|max:20|unique:t_penjualan,penjualan_kode,'.$id.',penjualan_id',
            'penjualan_tanggal'=> 'required|date',
        ]);

        $penjualan = PenjualanModel::find($id);
        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        $penjualan->update([
            'user_id'          => $request->user_id,
            'pembeli'          => $request->pembeli,
            'penjualan_kode'   => $request->penjualan_kode,
            'penjualan_tanggal'=> $request->penjualan_tanggal,
        ]);

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil diubah');
    }

    // Menghapus data penjualan
    public function destroy(string $id)
    {
        $check = PenjualanModel::find($id);
        if (!$check) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        try {
            PenjualanModel::destroy($id);
            return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/penjualan')->with(
                'error',
                'Data penjualan gagal dihapus karena masih ada data lain yang terkait'
            );
        }
    }

    public function create_ajax()
    {
        $barangs = BarangModel::all()->map(function ($barang) {
            return [
                'barang_id' => $barang->barang_id,
                'barang_nama' => $barang->barang_nama,
                'harga_jual' => $barang->harga_jual,
                'get_stok' => $barang->getStok(), // Pastikan stok dihitung di sini
            ];
        });

        return view('penjualan.create_ajax', ['barangs' => $barangs]);
    }

    public function store_ajax(Request $request)
    {
        $rules = [
            'pembeli'           => ['required', 'string', 'max:100'],
            'penjualan_kode'    => ['required', 'string', 'max:20', 'unique:t_penjualan,penjualan_kode'],
            'details'           => ['required', 'array', 'min:1'],
            'details.*.barang_id' => ['required', 'integer'],
            'details.*.jumlah'    => ['required', 'integer', 'min:1'],
            'details.*.harga'     => ['required', 'numeric'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {

            $dataPenjualan = $request->only([
                'pembeli', 'penjualan_kode'
            ]);
            $dataPenjualan['user_id'] = auth()->id();
            $dataPenjualan['penjualan_tanggal'] =  now();

            $penjualan = PenjualanModel::create($dataPenjualan);

            foreach ($request->details as $index => $detail) {
                $barang = BarangModel::find($detail['barang_id']);
            
                if (!$barang || $barang->getStok() < 1) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => false,
                        'message' => 'Stok barang tidak tersedia atau habis pada baris ke-' . ($index + 1)
                    ]);
                }
            
                if ($detail['jumlah'] > $barang->getStok()) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => false,
                        'message' => 'Jumlah yang diminta melebihi stok yang tersedia pada baris ke-' . ($index + 1)
                    ]);
                }
            
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id'    => $detail['barang_id'],
                    'jumlah'       => $detail['jumlah'],
                    'harga'        => $detail['harga'],
                ]);
            }
            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Data penjualan beserta detail berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage()
            ]);
        }
    }

    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['penjualanDetail.barang', 'user'])->find($id);
        $user = UserModel::all();
        $barang = BarangModel::all();

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ]);
        }

        return view('penjualan.edit_ajax', compact('penjualan', 'user', 'barang'));
    }

    public function update_ajax(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:50',
            'penjualan_tanggal' => 'required|date',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'harga' => 'required|array|min:1',
            'harga.*' => 'required|numeric|min:0'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }
    
        try {
            DB::beginTransaction();
    
            $penjualan = PenjualanModel::find($id);
            if (!$penjualan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }
    
            // Ambil detail lama untuk rollback perhitungan stok
            $detailsLama = PenjualanDetailModel::where('penjualan_id', $id)->get();
            $rollback = [];
    
            foreach ($detailsLama as $d) {
                if (!isset($rollback[$d->barang_id])) {
                    $rollback[$d->barang_id] = 0;
                }
                $rollback[$d->barang_id] += $d->jumlah;
            }
    
            // Validasi stok untuk data baru
            foreach ($request->barang_id as $index => $barang_id) {
                $jumlahBaru = $request->jumlah[$index];
                $stokBarang = BarangModel::find($barang_id)->getStok();
    
                // Tambahkan rollback jika barang ini sudah pernah ada di detail sebelumnya
                $stokBarang += $rollback[$barang_id] ?? 0;
    
                if ($stokBarang < $jumlahBaru) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Stok barang tidak mencukupi untuk barang ID {$barang_id}. Sisa stok (termasuk rollback): {$stokBarang}"
                    ]);
                }
            }
    
            // Update data penjualan
            $penjualan->update([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_tanggal' => $request->penjualan_tanggal
            ]);
    
            // Hapus semua detail lama
            PenjualanDetailModel::where('penjualan_id', $id)->delete();
    
            // Insert detail yang baru
            foreach ($request->barang_id as $index => $barang_id) {
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barang_id,
                    'harga' => $request->harga[$index],
                    'jumlah' => $request->jumlah[$index]
                ]);
            }
    
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil diperbarui',
                'data' => $penjualan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function show_ajax($id)
    {
        $penjualan = PenjualanModel::with(['user', 'penjualanDetail.barang'])->find($id);

        $penjualanDetail = $penjualan->penjualanDetail;

        return view('penjualan.show_ajax', ['penjualanDetail' => $penjualanDetail]);
    }

    public function confirm_ajax($id)
    {
        $penjualan = PenjualanModel::with(['penjualanDetail.barang', 'user'])->find($id);
        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::with('penjualanDetail')->find($id);

            if (!$penjualan) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }

            DB::beginTransaction();
            try {
                // Hapus semua detail penjualan terkait
                foreach ($penjualan->penjualanDetail as $detail) {
                    $detail->delete();
                }

                // Hapus data penjualan
                $penjualan->delete();

                DB::commit();
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function import()
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if (! $request->ajax() && ! $request->wantsJson()) {
            return redirect()->back();
        }

        // 1) validasi file
        $validator = Validator::make($request->all(), [
            'file_penjualan' => ['required','mimes:xlsx','max:2048'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        // 2) load spreadsheet
        $path        = $request->file('file_penjualan')->getPathname();
        $reader      = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        // Sheet pertama = header penjualan, sheet kedua = detail
        $sheetH = $spreadsheet->getSheet(0)->toArray(null, true, true, true);
        $sheetD = $spreadsheet->getSheet(1)->toArray(null, true, true, true);

        DB::beginTransaction();
        try {
            // Mengimport penjualan
            $mapKode = []; // [ penjualan_kode => penjualan_id ]
            foreach ($sheetH as $rowNum => $row) {
                if ($rowNum === 1) {
                    // anggap baris 1 adalah header kolom: skip
                    continue;
                }

                // baca kolom A:D sesuai template:
                $userId  = intval($row['A'] ?? 0);
                $pembeli = trim($row['B']  ?? '');
                $kode    = trim($row['C']  ?? '');
                $tgl     = trim($row['D']  ?? '');

                // jika salah satu field wajib kosong, skip baris ini
                if (! $userId || $kode === '' || ! $tgl) {
                    continue;
                }

                // insert penjualan baru
                $p = PenjualanModel::create([
                    'user_id'           => $userId,
                    'pembeli'           => $pembeli,
                    'penjualan_kode'    => $kode,
                    'penjualan_tanggal' => date('Y-m-d H:i:s', strtotime($tgl)),
                ]);
            }

            foreach ($sheetD as $rowNum => $row) {
                if ($rowNum === 1) {
                    // skip header kolom
                    continue;
                }

                $kode      = trim($row['A'] ?? '');
                $barangId  = intval($row['B'] ?? 0);
                $jumlah    = intval($row['C'] ?? 0);
                $harga     = floatval($row['D'] ?? 0);

                // pastikan header dengan kode ini sudah di‐import
                if (! isset($mapKode[$kode])) {
                    throw new \Exception("Header penjualan kode “{$kode}” tidak ditemukan (baris {$rowNum}).");
                }
                $penjualanId = $mapKode[$kode];

                // cek & kurangi stok di BarangModel
                $barang = BarangModel::find($barangId);
                if (! $barang) {
                    throw new \Exception("Barang dengan ID {$barangId} tidak ditemukan (baris {$rowNum}).");
                }
                if ($barang->barang_stok < $jumlah) {
                    throw new \Exception("Stok tidak mencukupi untuk barang “{$barang->barang_nama}” (baris {$rowNum}).");
                }
                // kurangi stok
                $barang->decrement('barang_stok', $jumlah);

                // simpan detail
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualanId,
                    'barang_id'    => $barangId,
                    'jumlah'       => $jumlah,
                    'harga'        => $harga,
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Import berhasil: data penjualan & detail tersimpan, stok terupdate.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Import gagal: ' . $e->getMessage()
            ]);
        }
    }

    public function export_excel()
    {
        $penjualan = PenjualanModel::with(['user', 'penjualanDetail.barang'])
            ->orderBy('penjualan_tanggal', 'asc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); 

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Pembeli');
        $sheet->setCellValue('E1', 'User');
        $sheet->setCellValue('F1', 'Kode Barang');
        $sheet->setCellValue('G1', 'Nama Barang');
        $sheet->setCellValue('H1', 'Harga');
        $sheet->setCellValue('I1', 'Jumlah');
        $sheet->setCellValue('J1', 'Subtotal');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $no = 1; 
        $baris = 2; 

        foreach ($penjualan as $p) {
            $firstRow = true;
        
            foreach ($p->penjualanDetail as $detail) {
                $unitPrice = $detail->jumlah
                    ? ($detail->harga / $detail->jumlah)
                    : 0;
        
                $subTotal = $unitPrice * $detail->jumlah;
        
                $sheet->setCellValue('A' . $baris, $firstRow ? $no : '');
                $sheet->setCellValue('B' . $baris, $firstRow ? $p->penjualan_kode : '');
                $sheet->setCellValue('C' . $baris, $firstRow ? $p->penjualan_tanggal : '');
                $sheet->setCellValue('D' . $baris, $firstRow ? $p->pembeli : '');
                $sheet->setCellValue('E' . $baris, $firstRow ? $p->user->username : '');
                $sheet->setCellValue('F' . $baris, $detail->barang->barang_kode);
                $sheet->setCellValue('G' . $baris, $detail->barang->barang_nama);
                
                $sheet->setCellValue('H' . $baris, $unitPrice);
                $sheet->setCellValue('I' . $baris, $detail->jumlah);
                $sheet->setCellValue('J' . $baris, $subTotal);
        
                $baris++;
                $firstRow = false;
            }
        
            $no++;
        }

        foreach(range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan'); 
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf(){
        $penjualan = PenjualanModel::with(['user','penjualanDetail'])
            ->orderBy('penjualan_id')
            ->orderBy('penjualan_kode')
            ->get();

        $pdf = PDF::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('A4', 'portrait'); 
        $pdf->setOption("isRemoteEnabled", true); 
        $pdf->render();

        return $pdf->stream('Data Supplier '.date('Y-m-d H-i-s').'.pdf');
    }
}