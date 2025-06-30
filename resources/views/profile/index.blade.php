@include('layout.head', ['title' => 'Profile'])
@include('layout.sidebar')
@include('layout.header')

<div class="page-container">
    <div class="page-title-box">
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Profile</h4>
            </div>
        </div>
    </div>

    <div class="container mt-4">
    @include('notification.alert')
    <div class="row">
        {{-- Form Ganti Avatar 1 --}}
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <div class="card-header">Avatar</div>
                <div class="card-body">
                    <img src="{{ asset('avatar') }}/{{ Auth::user()->avatar }}"
                        alt="user-image"
                        width="200"
                        class="img-fluid rounded border border-2 mb-3"
                        style="object-fit: cover; max-height: 200px;">
                </div>
            </div>
        </div>


        {{-- Form Ganti Avatar 2 --}}
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Ubah Avatar</div>
                <div class="card-body">
                    <form action="{{ route('profile.changeAvatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Upload Foto Profil</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Form Ganti Password --}}
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Ubah Password</div>
                <div class="card-body">
                    <form action="{{ route('profile.changePassword') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

@include('layout.footer')
