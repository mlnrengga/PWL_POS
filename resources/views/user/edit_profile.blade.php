<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="updateProfileModalLabel">Ubah Profil</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="updateProfileForm" method="POST" enctype="multipart/form-data"
        action="{{ url('user/profile_update') }}">
        @csrf
        <div class="modal-body">
            <div class="form-group">
                <!-- Preview -->
                <div class="text-center mt-2">
                    <img id="previewProfilePhoto"
                         src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('adminlte/dist/img/user2-160x160.jpg') }}"
                         alt="Preview Foto Profil"
                         class="img-thumbnail"
                         style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                </div>
            </div>
            <input type="hidden" name="user_id" value="{{ $user->user_id }}">
    
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="{{ $user->nama }}" class="form-control" required>
            </div>
    
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="{{ $user->username }}" class="form-control" required>
            </div>
    
            <div class="form-group">
                <label for="profile_photo">Foto Profil</label>
                <input type="file" name="profile_photo" id="profile_photo" class="form-control">
                <small class="text-muted">Format: jpg, jpeg, png. Maksimal 2MB</small>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
    
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#profile_photo').on('change', function(event) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#previewProfilePhoto').attr('src', e.target.result);
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        });

        $('#updateProfileForm').submit(function(e) {
            e.preventDefault(); 
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            timer: 4000,
                            showConfirmButton: false
                        }).then(function() {
                            $('#updateProfileModal').modal('hide');
                            location
                        .reload(); 
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                }
            });
        });
    });
</script>
