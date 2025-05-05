<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class BarangModel extends Model
{
    use HasFactory;

    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id';

    protected $fillable = ['kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'image'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    public function getStok(): int
    {
        $stokMasuk = DB::table('t_stok')
            ->where('barang_id', $this->barang_id)
            ->sum('stok_jumlah');

        $stokKeluar = DB::table('t_penjualan_detail')
            ->where('barang_id', $this->barang_id)
            ->sum('jumlah');

        return $stokMasuk - $stokKeluar;
    }
}
