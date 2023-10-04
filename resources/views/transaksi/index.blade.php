@extends('layouts.master')

@section('title')
    Transaksi
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    /* .table-penjualan tbody tr:last-child {
        display: none;
    } */

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">

                <form class="form-produk">
                    @csrf
                    <div class="form-group row">
                        <label for="kode_produk" class="col-lg-2">Kode Produk</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_penjualan" id="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="id_produk" id="id_produk">
                                <input type="text" class="form-control" name="kode_produk" id="kode_produk">
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>                                
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th width="15%">Jumlah</th>
                        <th>Total</th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('transaksi.simpan') }}" class="form-penjualan" method="post">
                            @csrf
                            <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="bayar" id="bayar">
                            <input type="hidden" name="id_customer" id="id_customer" value="{{ $customerSelected->id_customer }}">

                            <div id="det_trans">                                
                            </div>

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kode_customer" class="col-lg-2 control-label">Customer</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="kode_customer" value="{{ $customerSelected->kode_customer }}">
                                        <span class="input-group-btn">
                                            <button onclick="tampilCustomer()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bayar" class="col-lg-2 control-label">Bayar</label>
                                <div class="col-lg-8">
                                    <input type="text" id="bayarrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diterima" class="col-lg-2 control-label">Diterima</label>
                                <div class="col-lg-8">
                                    <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $penjualan->diterima ?? 0 }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembali" class="col-lg-2 control-label">Kembali</label>
                                <div class="col-lg-8">
                                    <input type="text" id="kembali" name="kembali" class="form-control" value="0" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

@includeIf('transaksi.produk')
@includeIf('transaksi.customer')
@endsection

@push('scripts')
<script>
    let table, table2, id_produk_="", count = 0, total = 0;

    function tampilProduk() {
        $('#modal-produk').modal('show');
    }

    function pilihProduk(id_produk, kode_produk, nama_produk, harga_satuan, jumlah) {
        ++count;
        $('#id_produk').val(id_produk);
        $('#kode_produk').val(kode_produk);
        hideProduk();
        var produks = [
            count, kode_produk, nama_produk, harga_satuan, jumlah, (harga_satuan * jumlah), id_produk
        ];
        tambahProduk(produks);
    }    

    function tambahProduk(add_produk) {
        table.row.add(add_produk).draw();
        // menjumlah Total dgn subtotal dari barang yg baru ditambah.
        total += add_produk[5];
        $('#det_trans').append(
            '<input type="hidden" id="det_kode_produk'+count+'" name="det_kode_produk[]" value="'+add_produk[1]+'">' +
            '<input type="hidden" id="det_nama_produk'+count+'" name="det_nama_produk[]" value="'+add_produk[2]+'">' + 
            '<input type="hidden" id="det_harga_satuan'+count+'" name="det_harga_satuan[]" value="'+add_produk[3]+'">' + 
            '<input type="hidden" id="det_jumlah'+count+'" name="det_jumlah[]" value="'+add_produk[4]+'">' + 
            '<input type="hidden" id="det_subtotal'+count+'" name="det_subtotal[]" value="'+add_produk[5]+'">'+
            '<input type="hidden" id="det_id_produk'+count+'" name="det_id_produk[]" value="'+add_produk[6]+'">'
        );

        // set tampilan di ui
        $('#totalrp').val(total);
        $('#bayarrp').val(total);
        $('.tampil-bayar').html(total);
        $('#kode_produk').focus();

        // set variabel u/ form 
        $('#total').val(total);
        $('#total_item').val(count);
        $('#bayar').val(total);
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function tampilCustomer() {
        $('#modal-customer').modal('show');
    }

    function pilihCustomer(id_customer, kode_customer) {
        $('#id_customer').val(id_customer);
        $('#kode_customer').val(kode_customer);
        $('#diterima').val(0).focus().select();
        hideCustomer();
    }

    function hideCustomer() {
        $('#modal-customer').modal('hide');
    }

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-penjualan').DataTable({
            columnDefs: [ 
                {
                    title: 'No.',
                    data: '0',
                    targets: 0
                },
                {
                    title: 'Kode',
                    data: '1',
                    targets: 1
                },
                {
                    title: 'Nama',
                    data: '2',
                    targets: 2
                },
                {
                    title: 'Harga',
                    data: '3',
                    targets: 3
                },
                {
                    title: 'Jumlah',
                    data: '4',
                    targets: 4,
                    render: function (data){
                        return '<input type="number" class="form-control quantity" value="'+data+'">'
                    }
                },
                {
                    title: 'Total',
                    data: '5',
                    targets: 5
                },
                {
                    title: 'id_produk',
                    visible: false,
                    data: '6',
                    targets: 6
                },
        ]
        })

        table2 = $('.table-produk').DataTable();

        table.on('change', '.quantity', function () {
            let tr = this.closest("tr");
            let trdata = table.row(tr).data();
            let rowindex = table.row(tr).index() + 1;
            console.log(trdata);
            console.log(rowindex);            
            console.log(this.value);            

            // Mengurangi nilai total, dikurangi value subtotal yg lama pada row ini
            total -= trdata[5];

            var subtotal = this.value * trdata[3];

            // set new value and redraw datatable
            trdata[4] = this.value;
            trdata[5] = subtotal;
            table.row(tr).data( trdata ).draw();

            // Value total dijumlah dengan subtotal baru.
            total += subtotal;
            // set tampilan di ui
            $('#totalrp').val(total);
            $('#bayarrp').val(total);
            $('.tampil-bayar').html(total);
            $('#kode_produk').focus();

            // set variabel u/ form 
            $('#total').val(total);
            $('#total_item').val(count);
            $('#bayar').val(total);

            // update variabel detail untuk dikirim ke controller
            $('#det_jumlah'+rowindex).val(this.value);
            $('#det_subtotal'+rowindex).val(subtotal);
        });
        // $(document).on('input', '.quantity', function () {
        //     let id = $(this).data('id');
        //     let jumlah = parseInt($(this).val());

        //     if (jumlah < 1) {
        //         $(this).val(1);
        //         alert('Jumlah tidak boleh kurang dari 1');
        //         return;
        //     }
        //     if (jumlah > 10000) {
        //         $(this).val(10000);
        //         alert('Jumlah tidak boleh lebih dari 10000');
        //         return;
        //     }

        //     $.post(`{{ url('/transaksi') }}/${id}`, {
        //             '_token': $('[name=csrf-token]').attr('content'),
        //             '_method': 'put',
        //             'jumlah': jumlah
        //         })
        //         .fail(errors => {
        //             alert('Tidak dapat menyimpan data');
        //             return;
        //         });
        // });

        $('#diterima').on('keyup', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }else{
                var kembalian = $(this).val() - $('#totalrp').val()
                if (kembalian >= 0) {
                    $('#kembali').val(kembalian);                    
                } else {
                    $('#kembali').val(0);                                        
                }
            }
        });               

        $('.btn-simpan').on('click', function () {
            $('.form-penjualan').submit();
        });

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })

                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
});
</script>
@endpush
