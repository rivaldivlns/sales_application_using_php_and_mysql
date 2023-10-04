<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $setting = Setting::find(1);
        return view('user.index',compact('setting'));
    }

    public function show()
    {
        return User::all(); //kalo mau show all user
    }

    public function data()
    {
        $user = User::all()->except(Auth::id());

        return datatables()
            ->of($user)
            ->addIndexColumn()
            ->addColumn('name', function ($user) {
                return $user->name;
            })
            ->addColumn('email', function ($user) {
                return $user->email;
            })
            ->addColumn('aksi', function ($user) {
                return '
                <div class="btn-group">
                    <button onclick="deleteData(`'. route('user.delete', $user->id_user) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
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
        $user = User::latest()->first() ?? new user();
        $request['id_user'] = 'User'. tambah_nol_didepan((int)$user->id_user +1, 6);

        $user = user::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }


    public function update(Request $request)
    {
        $setting = Setting::first();
        $setting->nama_perusahaan = $request->nama_perusahaan;
        $setting->telepon_perusahaan = $request->telepon_perusahaan;
        $setting->alamat_perusahaan = $request->alamat_perusahaan;
        $setting->email_perusahaan = $request->email_perusahaan;

        if ($request->hasFile('path_logo')) {
            $file = $request->file('path_logo');
            $nama = 'logo-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);

            $setting->path_logo = "/img/$nama";
        }

        $setting->update();

        return response()->json('Data pengaturan berhasil disimpan', 200);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id as $id) {
            $user = User::find($id);
            $user->delete();
        }

        return response(null, 204);
    }
}
