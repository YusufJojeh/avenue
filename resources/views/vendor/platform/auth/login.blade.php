@extends('platform::auth')
@section('title', __('common.messages.sign_in_to_account'))

@section('content')
    <div class="mb-3">
        <h1 class="h4 mb-1">{{ __('common.messages.sign_in_to_account') }}</h1>
        <div class="small" style="color: rgba(255,255,255,.65);">
            {{ __('common.nav.admin') }}
        </div>
    </div>

    <form
        role="form"
        method="POST"
        data-controller="form"
        data-form-need-prevents-form-abandonment-value="false"
        data-action="form#submit"
        action="{{ route('platform.login.auth') }}"
    >
        @csrf

        @includeWhen($isLockUser,'platform::auth.lockme')
        @includeWhen(!$isLockUser,'platform::auth.signin')
    </form>
@endsection

