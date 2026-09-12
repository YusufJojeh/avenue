@extends('layouts.app')

@section('title', __('common.pages.privacy_policy'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="crystal-card p-4">
                <h1 class="h2 mb-4">{{ __('common.pages.privacy_policy') }}</h1>

                <div class="content">
                    @if(isset($settings['content.privacy']) && $settings['content.privacy'])
                        {!! $settings['content.privacy'] !!}
                    @else
                        <p class="lead">{{ __('common.pages.privacy_intro') }}</p>

                        <h3>{{ __('common.pages.privacy_info_collect_title') }}</h3>
                        <p>{{ __('common.pages.privacy_info_collect_body') }}</p>

                        <h3>{{ __('common.pages.privacy_info_use_title') }}</h3>
                        <p>{{ __('common.pages.privacy_info_use_body') }}</p>

                        <h3>{{ __('common.pages.privacy_info_sharing_title') }}</h3>
                        <p>{{ __('common.pages.privacy_info_sharing_body') }}</p>

                        <h3>{{ __('common.pages.privacy_data_security_title') }}</h3>
                        <p>{{ __('common.pages.privacy_data_security_body') }}</p>

                        <h3>{{ __('common.pages.privacy_contact_title') }}</h3>
                        <p>{{ __('common.pages.privacy_contact_body') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
