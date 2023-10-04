<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Transaksi;
use App\Models\Setting;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $setting = Setting::find(1);
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir', 'setting'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $saldo = 0;
        $total_saldo = 0;

        $from = date($awal);
        $to = date($akhir);

        // $all_trans = Transaksi::whereBetween('created_at', [$from, $to])->get();
        $all_trans = Penjualan::whereRaw(
            "(created_at >= ? AND created_at <= ? AND diterima != ?)", 
            [
               $from ." 00:00:00", 
               $to ." 23:59:59",
               0,
            ]
          )->get();
        foreach ($all_trans as $trans) {
            $subtotal = $trans->total_harga;
            $total_saldo += $subtotal;

            $data[] = [
                'DT_RowIndex' => $no,
                'tanggal' => tanggal_indonesia($trans->created_at),
                // 'kode_produk' => '',
                // 'nama_produk' => '',
                // 'harga_satuan' => format_uang($harga_satuan),
                // 'jumlah' => '',
                'subtotal' => format_uang($trans->total_harga),
                'saldo' => format_uang($total_saldo),
                'aksi'=> '
                    <div class="btn-group">
                        <button onclick="showDetail(`'. route('laporan.show',$trans->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    </div>'
                
            ];
            $no++;
        }        

        return $data;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);
        // dd($data);

        return datatables()
            ->of($data)
            ->rawColumns(['aksi'])
            ->make(true);
    }

}
