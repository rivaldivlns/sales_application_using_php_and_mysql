@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard Manager </li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body text-center">
                <h1>Selamat Datang Bapak Tarman</h1>
                <h2>Manager Marketing PT Nusa Indah Jaya Utama</h2>
                <br><br>
                <a href="{{ route('laporan.index') }}" class="btn btn-success btn-lg">Laporan Penjualan</a>
                <br><br><br>
            </div>
        </div>
    </div>
</div>
<!-- /.row (main row) -->
@endsection