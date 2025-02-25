<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tanggal = Carbon::now()->format('Y-m-d H:i:s');

        $data = [
            [
                'penjualan_id' => 1, 
                'user_id' => 1, 
                'pembeli' => 'Andi Pratama', 
                'penjualan_kode' => 'PNJ-1001', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 2, 
                'user_id' => 1, 
                'pembeli' => 'Siti Rahma', 
                'penjualan_kode' => 'PN-1002', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 3, 
                'user_id' => 1, 
                'pembeli' => 'Budi Santoso', 
                'penjualan_kode' => 'PNJ-1003', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 4, 
                'user_id' => 1, 
                'pembeli' => 'Lina Sari', 
                'penjualan_kode' => 'PNJ-1004', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 5, 
                'user_id' => 1, 
                'pembeli' => 'Dewi Kartika', 
                'penjualan_kode' => 'PNJ-1005', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 6, 
                'user_id' => 1, 
                'pembeli' => 'Rahmat Hidayat', 
                'penjualan_kode' => 'PNJ-1006', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 7, 
                'user_id' => 1, 
                'pembeli' => 'Nina Wulandari', 
                'penjualan_kode' => 'PNJ-1007', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 8, 
                'user_id' => 1, 
                'pembeli' => 'Hadi Wijaya', 
                'penjualan_kode' => 'PNJ-1008', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 9, 
                'user_id' => 1, 
                'pembeli' => 'Sari Melati', 
                'penjualan_kode' => 'PNJ-1009', 
                'penjualan_tanggal' => $tanggal
            ],
            [
                'penjualan_id' => 10, 
                'user_id' => 1, 
                'pembeli' => 'Fajar Setiawan', 
                'penjualan_kode' => 'PNJ-1010', 
                'penjualan_tanggal' => $tanggal
            ],
        ];
        
        DB::table('t_penjualan')->insert($data);
        
    }
}
