@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pengguna</h3>
            <div class="card-tools">
              <button onclick="modalAction('{{ url('/user/import') }}')" class="btn btn-sm btn-info mt-1">Import User</button>
                <a href="{{ url('/user/create') }}" class="btn btn-sm btn-primary m-1">Tambah Data</a>
                <button onclick="modalAction('{{ url('/user/create_ajax') }}')" class="btn btn-sm btn-success m-1">Tambah Data (Ajax)</button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter data -->
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="level_id" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="level_id" id="level_id" class="form-control form-control-sm">
                                    <option value="">- Semua -</option>
                                    @foreach($level as $item)
                                        <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Level Pengguna</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-sm table-striped table-hover" id="table-user">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Level Pengguna</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static"
        data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var tableUser;
        $(document).ready(function () {
            tableUser = $('#table-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('user/list') }}",
                    dataType: "json",
                    type: "POST",
                    data: function (d) {
                        d.filter_level = $('.filter_level').val();
                    }
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
                        data: "username",
                        width: "20%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "nama",
                        width: "25%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "level.level_nama",
                        width: "25%",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        width: "25%",
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            $('#table-user_filter input').unbind().bind().on('keyup', function (e) {
                if (e.keyCode == 13) {
                    tableUser.search(this.value).draw();
                }
            });

            $('.filter_kategori').change(function() {
                tableUser.draw();
            });
        });
    </script>
@endpush
