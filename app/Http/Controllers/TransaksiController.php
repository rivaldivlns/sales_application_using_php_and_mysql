<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Penjualan;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $setting = Setting::find(1);
        $produk = Produk::orderBy('nama_produk')->get();
        $customer = Customer::orderBy('nama_customer')->get();

        // Cek apakah ada transaksi yang sedang berjalan
        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = Penjualan::find($id_penjualan);
            $customerSelected = $penjualan->customer ?? new Customer();

            return view('transaksi.index', compact('produk', 'customer','id_penjualan', 'penjualan', 'customerSelected', 'setting'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaksi.baru');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function data($id)
    {
        $detail = Transaksi::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">'. $item->produk['kode_produk'] .'</span';
            $row['nama_produk'] = $item->produk['nama_produk'];
            $row['harga_satuan']  = 'Rp. '. format_uang($item->harga_satuan);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_transaksi .'" value="'. $item->jumlah .'">';
            $row['subtotal']    = 'Rp. '. format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('penjualan.destroy', $item->id_transaksi) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->harga_satuan * $item->jumlah;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'kode_produk' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'nama_produk' => '',
            'harga_satuan'  => '',
            'jumlah'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::where('id_produk', $request->id_produk)->first();
        if (! $produk) {
            return response()->json('Data gagal disimpan', 400);
        }

        $detail = new Transaksi();
        $detail->id_penjualan = $request->id_penjualan;
        $detail->id_produk = $produk->id_produk;
        $detail->harga_satuan = $produk->harga_satuan;
        $detail->jumlah = 1;
        $detail->subtotal = $produk->harga_satuan;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = Transaksi::find($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_satuan * $request->jumlah;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = Transaksi::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($total = 0, $diterima = 0)
    {
        $bayar   = $total;
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
        $data    = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar). ' Rupiah'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali). ' Rupiah'),
        ];

        return response()->json($data);
    }
}
