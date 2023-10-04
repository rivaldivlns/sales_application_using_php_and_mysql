<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = Setting::find(1);
        return view('customer.index', compact('setting'));
    }

    public function data()
    {
        $customer = Customer::orderBy('kode_customer')->get();

        return datatables()
            ->of($customer)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_customer[]" value="'. $produk->id_customer .'">
                ';
            })
            ->addColumn('kode_customer', function ($customer) {
                return '<span class="label label-success">'. $customer->kode_customer .'<span>';
            })
            ->addColumn('aksi', function ($customer) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('customer.update', $customer->id_customer) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('customer.destroy', $customer->id_customer) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'select_all', 'kode_customer'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer = Customer::latest()->first() ?? new Customer();
        $kode_customer = (int) $customer->kode_customer +1;

        $customer = new Customer();
        $customer->kode_customer = 'CUST'. tambah_nol_didepan($kode_customer, 3);
        $customer->nama_customer = $request->nama_customer;
        $customer->telepon_customer = $request->telepon_customer;
        $customer->alamat_customer = $request->alamat_customer;
        $customer->email_customer = $request->email_customer;
        $customer->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = customer::find($id);

        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = customer::find($id)->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = customer::find($id);
        $customer->delete();

        return response(null, 204);
    }
}
