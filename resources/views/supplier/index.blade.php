@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Supplier</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/supplier/import') }}')" class="btn btn-sm btn-info mt-1">Import Supplier</button>
                <a href="{{ url('/supplier/create') }}" class="btn btn-sm btn-primary mt-1">Tambah Data</a>
                <button onclick="modalAction('{{ url('/supplier/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Data (Ajax)</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-sm table-striped table-hover" id="table-supplier">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Alamat Supplier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false"
        data-width="75%"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var tableSupplier;
    $(document).ready(function () {
        tableSupplier = $('#table-supplier').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ url('supplier/list') }}",
                "dataType": "json",
                "type": "POST"
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    className: "text-center",
                    width: "5%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "supplier_kode",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "supplier_nama",
                    className: "",
                    width: "30%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "supplier_alamat",
                    className: "",
                    width: "35%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "aksi",
                    className: "text-center",
                    width: "15%",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#table-supplier_filter input').unbind().bind().on('keyup', function (e) {
            if (e.keyCode == 13) {
                tableSupplier.search(this.value).draw();
            }
        });
    });
</script>
@endpush
