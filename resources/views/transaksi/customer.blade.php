<div class="modal fade" id="modal-customer" tabindex="-1" role="dialog" aria-labelledby="modal-customer">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Customer</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-customer">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode Customer</th>
                        <th>Nama Customer</th>
                        <th>Telepon Customer</th>
                        <th>Alamat Customer</th>
                        <th>Email Customer</th>
                        <th><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @foreach ($customer as $key => $item)
                            <tr>
                                <td width="5%">{{ $key+1 }}</td>
                                <td>{{ $item->kode_customer }}</td>
                                <td>{{ $item->nama_customer }}</td>
                                <td>{{ $item->telepon_customer }}</td>
                                <td>{{ $item->alamat_customer }}</td>
                                <td>{{ $item->email_customer }}</td>

                                <td>
                                    <a href="#" class="btn btn-primary btn-xs btn-flat"
                                        onclick="pilihCustomer('{{ $item->id_customer }}', '{{ $item->kode_customer }}')">
                                        <i class="fa fa-check-circle"></i>
                                        Pilih Customer
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>