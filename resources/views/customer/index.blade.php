@extends('layouts.master')

@section('title')
    Data Customer
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Data Customer</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('customer.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah Data Customer</button>
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-customer">
                    @csrf
                    <table class="table table-stiped table-bordered">
                        <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th width="5%">No</th>
                            <th>Kode Customer</th>
                            <th>Nama Customer</th>
                            <th>Telepon Customer</th>
                            <th>Alamat Customer</th>
                            <th>Email Customer</th>


                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('customer.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('customer.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_customer'},
                {data: 'nama_customer'},
                {data: 'telepon_customer'},
                {data: 'alamat_customer'},
                {data: 'email_customer'},


                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        console.log(errors);
                        alert('Tidak dapat menyimpan data customer');
                        return;
                    });
            }
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Data Customer');
        console.log(url);
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=kode_customer]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Ubah Data Customer');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=kode_customer]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=kode_customer]').val(response.kode_customer);
                $('#modal-form [name=nama_customer]').val(response.nama_customer);
                $('#modal-form [name=telepon_customer]').val(response.telepon_customer);
                $('#modal-form [name=alamat_customer]').val(response.alamat_customer);
                $('#modal-form [name=email_customer]').val(response.email_customer);

            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data customer');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data customer?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data customer');
                    return;
                });
        }
    }
</script>
@endpush