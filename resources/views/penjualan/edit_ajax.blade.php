@empty($barang)
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/barang') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ url('/penjualan/'.$penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Penjualan Beserta Detailnya</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Pegawai</label>
                        <input type="text" class="form-control" value="{{ $penjualan->user->nama }}" readonly>
                        <input type="hidden" name="user_id" value="{{ $penjualan->user_id }}">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Penjualan</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->format('d-m-Y H:i:s') }}" readonly>
                        <input type="hidden" name="penjualan_tanggal" value="{{ $penjualan->penjualan_tanggal }}">
                    </div>

                    <div class="form-group">
                        <label>Kode Penjualan</label>
                        <input type="text" class="form-control" value="{{ $penjualan->penjualan_kode }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Pembeli</label>
                        <input type="text" name="pembeli" class="form-control" value="{{ $penjualan->pembeli }}" required>
                    </div>

                    <h5>Detail Penjualan</h5>
                    <table class="table" id="detailTable">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Harga (Otomatis)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualan->penjualanDetail as $detail)
                            <tr>
                                <td>
                                    <select name="barang_id[]" class="form-control barang-select" required>
                                        <option value="">- Pilih Barang -</option>
                                        @foreach($barang as $b)
                                        <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}" {{ $b->barang_id == $detail->barang_id ? 'selected' : '' }}>
                                            {{ $b->barang_nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="jumlah[]" class="form-control jumlah-input" value="{{ $detail->jumlah }}" required>
                                </td>
                                <td>
                                    <input type="text" name="harga[]" class="form-control harga-input" value="{{ $detail->harga }}" readonly required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger removeDetail">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" id="addDetail" class="btn btn-info">Tambah Barang</button>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function () {
            var barangs = @json($barang);

            function addDetailRow() {
                var row = '<tr>' +
                          '<td><select name="barang_id[]" class="form-control barang-select" required>' +
                          '<option value="">- Pilih Barang -</option>';
                $.each(barangs, function (i, barang) {
                    row += '<option value="' + barang.barang_id + '" data-harga="' + barang.harga_jual + '">' + barang.barang_nama + '</option>';
                });
                row += '</select></td>' +
                       '<td><input type="number" name="jumlah[]" class="form-control jumlah-input" required></td>' +
                       '<td><input type="text" name="harga[]" class="form-control harga-input" readonly required></td>' +
                       '<td><button type="button" class="btn btn-danger removeDetail">Hapus</button></td>' +
                       '</tr>';
                $('#detailTable tbody').append(row);
            }

            $('#addDetail').click(addDetailRow);

            $('#detailTable')
                .on('click', '.removeDetail', function () {
                    $(this).closest('tr').remove();
                })
                .on('change', '.barang-select', function () {
                    var harga = parseFloat($(this).find(':selected').data('harga')) || 0;
                    var row = $(this).closest('tr');
                    var jumlah = parseFloat(row.find('.jumlah-input').val()) || 0;
                    row.find('.harga-input').val(harga * jumlah);
                })
                .on('input', '.jumlah-input', function () {
                    var jumlah = parseFloat($(this).val()) || 0;
                    var row = $(this).closest('tr');
                    var harga = parseFloat(row.find('.barang-select').find(':selected').data('harga')) || 0;
                    row.find('.harga-input').val(harga * jumlah);
                });

            // CSRF setup
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });

            // jQuery Validation + AJAX submit
            $('#form-edit').validate({
                submitHandler: function (form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function (res) {
                            if (res.status) {
                                // tutup modal yang di index
                                $('#myModal').modal('hide');
                                Swal.fire('Berhasil', res.message, 'success');
                                if (typeof dataPenjualan !== 'undefined') {
                                    dataPenjualan.ajax.reload();
                                }
                            } else {
                                // tampilkan error field
                                $.each(res.msgField || {}, function (key, msgs) {
                                    $('#error-' + key).text(msgs[0]);
                                });
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan saat mengirim data.', 'error');
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty