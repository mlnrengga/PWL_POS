<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'barang_id' => 1, 
                'kategori_id' => 1, 
                'barang_kode' => 'BRG-101', 
                'barang_nama' => 'Laptop Lenovo ThinkPad', 
                'harga_beli' => 7800000, 
                'harga_jual' => 8800000
            ],
            [
                'barang_id' => 2, 
                'kategori_id' => 1, 
                'barang_kode' => 'BRG-102', 
                'barang_nama' => 'Smartphone iPhone 12', 
                'harga_beli' => 9500000, 
                'harga_jual' => 10250000
            ],
            [
                'barang_id' => 3, 
                'kategori_id' => 4, 
                'barang_kode' => 'BRG-103', 
                'barang_nama' => 'Mixer Miyako', 
                'harga_beli' => 300000, 
                'harga_jual' => 375000
            ],
            [
                'barang_id' => 4, 
                'kategori_id' => 4, 
                'barang_kode' => 'BRG-104', 
                'barang_nama' => 'Dispenser Sharp', 
                'harga_beli' => 550000, 
                'harga_jual' => 650000
            ],
            [
                'barang_id' => 5, 
                'kategori_id' => 5, 
                'barang_kode' => 'BRG-105', 
                'barang_nama' => 'Kertas A4 PaperOne', 
                'harga_beli' => 48000, 
                'harga_jual' => 60000
            ],
            [
                'barang_id' => 6, 
                'kategori_id' => 2, 
                'barang_kode' => 'BRG-106', 
                'barang_nama' => 'Kaos Polos Uniqlo', 
                'harga_beli' => 75000, 
                'harga_jual' => 120000
            ],
            [
                'barang_id' => 7, 
                'kategori_id' => 2, 
                'barang_kode' => 'BRG-107', 
                'barang_nama' => 'Jaket Hoodie Adidas', 
                'harga_beli' => 250000, 
                'harga_jual' => 350000
            ],
            [
                'barang_id' => 8, 
                'kategori_id' => 3, 
                'barang_kode' => 'BRG-108', 
                'barang_nama' => 'Susu UHT Ultra Milk 1L', 
                'harga_beli' => 18000, 
                'harga_jual' => 25000
            ],
            [
                'barang_id' => 9, 
                'kategori_id' => 3, 
                'barang_kode' => 'BRG-109', 
                'barang_nama' => 'Kopi Bubuk Kapal Api', 
                'harga_beli' => 32000, 
                'harga_jual' => 40000
            ],
            [
                'barang_id' => 10, 
                'kategori_id' => 3, 
                'barang_kode' => 'BRG-110', 
                'barang_nama' => 'Biskuit Roma Kelapa', 
                'harga_beli' => 15000, 
                'harga_jual' => 22000
            ],
            [
                'barang_id' => 11, 
                'kategori_id' => 4, 
                'barang_kode' => 'BRG-111', 
                'barang_nama' => 'Kompor Gas Rinnai', 
                'harga_beli' => 600000, 
                'harga_jual' => 750000
            ],
            [
                'barang_id' => 12, 
                'kategori_id' => 4, 
                'barang_kode' => 'BRG-112', 
                'barang_nama' => 'Setrika Philips', 
                'harga_beli' => 250000, 
                'harga_jual' => 325000
            ],
            [
                'barang_id' => 13, 
                'kategori_id' => 5, 
                'barang_kode' => 'BRG-113', 
                'barang_nama' => 'Pulpen Pilot G2', 
                'harga_beli' => 10000, 
                'harga_jual' => 15000
            ],
            [
                'barang_id' => 14, 
                'kategori_id' => 5, 
                'barang_kode' => 'BRG-114', 
                'barang_nama' => 'Binder A5 Kokuyo', 
                'harga_beli' => 75000, 
                'harga_jual' => 100000
            ],
            [
                'barang_id' => 15, 
                'kategori_id' => 5, 
                'barang_kode' => 'BRG-115', 
                'barang_nama' => 'Spidol Snowman Permanent', 
                'harga_beli' => 12000, 
                'harga_jual' => 18000
            ],
        ];
        
        DB::table('m_barang')->insert($data);
        
    }
}
