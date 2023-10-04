<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;
use PDF;

class PenjualanController extends Controller
{
    public function index()
    {
        $setting = Setting::find(1);
        return view('penjualan.index',compact('setting'));
    }

    public function data()
    {
        $penjualan = Penjualan::orderBy('id_penjualan')->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->bayar);
            })
            ->addColumn('diterima', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->diterima);
            })
            ->addColumn('nama_produk', function ($penjualan) {
                $produk = $penjualan->produk->nama_produk ?? '';
                return $penjualan->nama_produk ;
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('kode_produk', function ($penjualan) {
                $produk = $penjualan->produk->kode_produk ?? '';
                return $penjualan->kode_produk ;

            })
            ->addColumn('aksi', function ($penjualan) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk'])
            ->make(true);
    }

    public function create()
    {
        $penjualan = new Penjualan();
        $penjualan->id_produk = 0;
        $penjualan->nama_produk = 0;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        // dd($request->request);
        
        $penjualan = Penjualan::findOrFail($request->id_penjualan);
        $penjualan->id_produk = 0;
        $penjualan->kode_produk = '-';
        $penjualan->nama_produk = '-';
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->bayar = $request->bayar;
        $penjualan->diterima = $request->diterima;
        $penjualan->update();

        // $detail = Transaksi::where('id_penjualan', $penjualan->id_penjualan)->get();
        // foreach ($request->id_penjualan as $item) {
        $produks = [];
        for ($i=0; $i < count($request->det_kode_produk); $i++) { 
            // Proses update stok produk
            $produk = Produk::find($request->det_id_produk[$i]);
            $produk->jumlah -= $request->det_jumlah[$i];
            $produk->update();
            $produks[] = $produk;

            // proses simpan detail transaksi
            $trans = new Transaksi();
            $trans->id_penjualan = $request->id_penjualan;
            $trans->kode_produk = $produk->kode_produk;
            $trans->nama_produk = $produk->nama_produk;
            $trans->harga_satuan = $produk->harga_satuan;
            $trans->jumlah = $request->det_jumlah[$i];
            $trans->subtotal = $request->det_subtotal[$i];
            $trans->save();
        }
        // dd($produks);
        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        // $detail = Transaksi::with('produk')->where('id_penjualan', $id)->get();
        $detail = Transaksi::join('produk', 'transaksi.kode_produk','=','produk.kode_produk')->where('id_penjualan', $id)->get(
            ['transaksi.*', 'produk.kode_produk', 'produk.nama_produk']
        );
        // echo json_encode($detail); die;
        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return $detail->kode_produk;
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->nama_produk;
            })
            ->addColumn('harga_satuan', function ($detail) {
                return 'Rp. '. format_uang($detail->harga_satuan);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. '. format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        $detail    = Transaksi::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }

    public function notapenjualan()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail    = Transaksi::where('id_penjualan', session('id_penjualan'))->get();
        return view('penjualan.nota_penjualan', compact('setting', 'penjualan', 'detail'));
    }
}
