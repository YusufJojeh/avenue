@extends('layouts.app')

@section('title', __('common.pages.terms_of_service'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="crystal-card p-4">
                <h1 class="h2 mb-4">{{ __('common.pages.terms_of_service') }}</h1>

                <div class="content">
                    @if(isset($settings['content.terms']) && $settings['content.terms'])
                        {!! $settings['content.terms'] !!}
                    @else
                        <p class="lead">{{ __('common.pages.terms_intro') }}</p>

                        <h3>{{ __('common.pages.terms_acceptance_title') }}</h3>
                        <p>{{ __('common.pages.terms_acceptance_body') }}</p>

                        <h3>{{ __('common.pages.terms_license_title') }}</h3>
                        <p>{{ __('common.pages.terms_license_body') }}</p>

                        <h3>{{ __('common.pages.terms_disclaimer_title') }}</h3>
                        <p>{{ __('common.pages.terms_disclaimer_body') }}</p>

                        <h3>{{ __('common.pages.terms_limitations_title') }}</h3>
                        <p>{{ __('common.pages.terms_limitations_body') }}</p>

                        <h3>{{ __('common.pages.terms_governing_law_title') }}</h3>
                        <p>{{ __('common.pages.terms_governing_law_body') }}</p>

                        <h3>{{ __('common.pages.terms_contact_title') }}</h3>
                        <p>{{ __('common.pages.terms_contact_body') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
