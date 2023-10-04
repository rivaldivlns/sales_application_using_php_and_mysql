@extends('layouts.master')

@section('title')
    Laporan Penjualan {{ tanggal_indonesia($tanggalAwal, false) }} s/d {{ tanggal_indonesia($tanggalAkhir, false) }}
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Laporan Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="updatePeriode()" class="btn btn-info btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Ubah Periode</button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered" id="myDataTable">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <!-- <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th> -->
                        <th>Total Penerimaan</th>
                        <th>Saldo Akhir</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan.detail')
@includeIf('laporan.form')
@endsection

@push('scripts')
<script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    let table, table1;
    console.log('{{ route("laporan.data", [$tanggalAwal, $tanggalAkhir]) }}');

    $(function () {
        table = $('#myDataTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route("laporan.data", [$tanggalAwal, $tanggalAkhir]) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                // {data: 'kode_produk'},
                // {data: 'nama_produk'},
                // {data: 'harga_satuan'},
                // {data: 'jumlah'},
                {data: 'subtotal'},
                // {data: 'kode_produk'},
                // {data: 'total_saldo'},
                {data: 'saldo'},
                {
                    title: 'Action',
                    data: 'aksi'
                },
                
            ],
            dom: 'Brt',
            bSort: false,
            bPaginate: false,
        });

        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_satuan'},
                {data: 'jumlah'},
                {data: 'subtotal'},
            ]
        })

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    function updatePeriode() {
        $('#modal-form').modal('show');
    }

    function showDetail(url) {
        $('#modal-detail').modal('show');
        console.log(url);
        table1.ajax.url(url);
        table1.ajax.reload();
    }
</script>
@endpush
