<div class="mb-3">
    <label class="form-label" for="adminEmail">
        {{ __('user.fields.email') }}
    </label>
    <input
        id="adminEmail"
        name="email"
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email') }}"
        required
        autofocus
        autocomplete="email"
        inputmode="email"
        placeholder="{{ __('common.messages.enter_email') }}"
        tabindex="1"
    >
    @error('email')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="adminPassword">
        {{ __('user.fields.password') }}
    </label>
    <input
        id="adminPassword"
        name="password"
        type="password"
        class="form-control @error('password') is-invalid @enderror"
        required
        autocomplete="current-password"
        placeholder="{{ __('common.messages.enter_password') }}"
        tabindex="2"
    >
    @error('password')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="row align-items-center g-2">
    <div class="col-12 col-sm-6">
        <label class="form-check">
            <input type="hidden" name="remember" value="false">
            <input type="checkbox" name="remember" value="true"
                   class="form-check-input" {{ !old('remember') || old('remember') === 'true'  ? 'checked' : '' }}>
            <span class="form-check-label">{{ __('common.messages.remember_me') }}</span>
        </label>
    </div>
    <div class="col-12 col-sm-6">
        <button id="button-login" type="submit" class="btn-admin-auth" tabindex="3">
            {{ __('common.nav.login') }}
        </button>
    </div>
</div>

