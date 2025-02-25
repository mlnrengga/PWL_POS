<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id' => 1, 
                'supplier_kode' => 'SPL-101', 
                'supplier_nama' => 'PT Sentosa Jaya', 
                'supplier_alamat' => 'Jl. Merdeka No. 10, Jakarta Selatan'
            ],
            [
                'supplier_id' => 2, 
                'supplier_kode' => 'SPL-102', 
                'supplier_nama' => 'CV Sukses Makmur', 
                'supplier_alamat' => 'Jl. Asia Afrika No. 22, Bandung'
            ],
            [
                'supplier_id' => 3, 
                'supplier_kode' => 'SPL-103', 
                'supplier_nama' => 'UD Lancar Sejahtera', 
                'supplier_alamat' => 'Jl. Soekarno-Hatta No. 56, Surabaya'
            ],
        ];
        
        DB::table('m_supplier')->insert($data);
        
    }
}
