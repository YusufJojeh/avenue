<div class="mb-3 d-flex align-items-center gap-3">
    <div class="thumb-sm avatar">
        <img src="{{ $lockUser->presenter()->image() }}" class="b bg-light" alt="{{ $lockUser->presenter()->title() }}">
    </div>
    <div class="d-flex flex-column overflow-hidden small" style="color: rgba(255,255,255,.78);">
        <span class="text-ellipsis">{{ $lockUser->presenter()->title() }}</span>
        <span class="text-muted d-block text-ellipsis">{{ $lockUser->presenter()->subTitle() }}</span>
    </div>
    <input type="hidden" name="email" required value="{{ $lockUser->email }}">
</div>

<div class="mb-3">
    <input type="hidden" name="remember" value="true">
    <label class="form-label" for="adminLockPassword">
        {{ __('user.fields.password') }}
    </label>
    <input
        id="adminLockPassword"
        name="password"
        type="password"
        class="form-control @error('email') is-invalid @enderror"
        required
        autocomplete="current-password"
        autofocus
        placeholder="{{ __('common.messages.enter_password') }}"
        tabindex="1"
    >
    @error('email')
        <div class="invalid-feedback d-block">
            {{ $errors->first('email') }}
        </div>
    @enderror
</div>

<div class="row align-items-center g-2">
    <div class="col-12 col-sm-6">
        <a href="{{ route('platform.login.lock') }}" class="small" style="color: rgba(240, 194, 75, .92); text-decoration: none;">
            {{ __('common.messages.sign_in_another_user') }}
        </a>
    </div>
    <div class="col-12 col-sm-6">
        <button id="button-login" type="submit" class="btn-admin-auth" tabindex="2">
            {{ __('common.nav.login') }}
        </button>
    </div>
</div>

