<div class="row">
    <div class="col-md-12 mb-3">
        <div class="form-group">
            <label for="name">Nama</label>
            <input readonly type="text" name="name" id="name"
                class="form-control @error('name') is-invalid @enderror" placeholder="{{ trans('users/form.name') }}"
                value="{{ isset($user) ? $user->name : old('name') }}" required autofocus>
            @error('name')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-12 mb-3">
        <div class="form-group">
            <label for="email">Email</label>
            <input readonly type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror" placeholder="{{ trans('users/form.email') }}"
                value="{{ isset($user) ? $user->email : old('email') }}" required>
            @error('email')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    @isset($user)
        <div class="col-md-12 mb-3">
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-select js-example-basic-multiple" name="role" id="role" class="form-control"
                    required>
                    <option value="" selected disabled>-- Pilih --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ $user->getRoleNames()->toArray() !== [] && $user->getRoleNames()[0] == $role->name ? 'selected' : '-' }}>
                            {{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endisset
    @isset($user)
        <div class="col-md-4 text-center mb-3">
            <div class="avatar avatar-xl">
                @if ($user->avatar == null)
                    <img class="img-thumbnail"
                        src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}&s=450"
                        alt="avatar">
                @else
                    <img class="img-thumbnail" src="{{ asset("storage/uploads/avatars/$user->avatar") }}" style="width: 150px;height: 150px;border-radius: 5%;">



                @endif
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                    id="avatar">
                @error('avatar')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
                @if ($user->avatar == null)
                    <div id="passwordHelpBlock" class="form-text">
                        {{ trans('users/form.note_avatar') }}
                    </div>
                @endif
            </div>
        </div>
    @endisset
</div>
