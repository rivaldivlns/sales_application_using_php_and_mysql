@extends('layouts.master')

@section('title')
    Data Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Data Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <!-- <th>Kode Produk</th>
                        <th>Nama Produk</th> -->
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        <th>Total Bayar</th>
                        <th>Diterima</th>
                        
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan.detail')
@endsection

@push('scripts')
<script>
    let table, table1;

    $(function () {
        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route("penjualan.data") }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                // {data: 'kode_produk'},
                // {data: 'nama_produk'},
                {data: 'total_item'},
                {data: 'total_harga'},
                {data: 'bayar'},
                {data: 'diterima'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
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
    });

    function showDetail(url) {
        $('#modal-detail').modal('show');
        console.log(url);
        table1.ajax.url(url);
        table1.ajax.reload();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Ubah Data Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=kode_produk]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=kode_produk]').val(response.kode_produk);
                $('#modal-form [name=nama_produk]').val(response.nama_produk);
                $('#modal-form [name=harga_satuan]').val(response.harga_satuan);
                $('#modal-form [name=jumlah]').val(response.jumlah);
                $('#modal-form [name=subtotal]').val(response.subtotal);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data penjualan');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data penjualan?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data penjualan');
                    return;
                });
        }
    }
</script>
@endpush