@extends('layouts.template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Data Profil</h3>
                        </div>
                        <div class="card-body box-profile">
                            <div class="text-center mb-4">
                                <img class="profile-user-img img-fluid img-circle"
                                    src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('adminlte/dist/img/user8-128x128.jpg') }}"
                                    alt="Foto Profil" style="width: 150px; height: 150px; object-fit: cover;">
                            </div>

                            <h3 class="profile-username text-center">{{ $user->nama }}</h3>

                            <hr>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="fas fa-user mb-2"></i>
                                        <h5>Username</h5>
                                        <p>{{ $user->username }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="fas fa-shield-alt mb-2"></i>
                                        <h5>Role</h5>
                                        <p>{{ $user->level->level_nama ?? 'Tidak ada' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="fas fa-clock mb-2"></i>
                                        <h5>Terdaftar Sejak</h5>
                                        <p>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button id="btnEditProfile" class="btn btn-primary">
                                    <i class="fas fa-edit mr-2"></i> Ubah Profil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-hidden="true">

    </div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#updateProfileModal').load(url, function() {
                $('#updateProfileModal').modal('show');
            });
        }

        $(document).ready(function() {
            $('#btnEditProfile').click(function() {
                modalAction("{{ url('user/profile_ajax') }}");
            });
        });
    </script>
@endpush
