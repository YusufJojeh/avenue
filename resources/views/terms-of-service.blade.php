@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="crystal-card p-4">
                <h1 class="h2 mb-4">Terms of Service</h1>

                <div class="content">
                    @if(isset($settings['content.terms']) && $settings['content.terms'])
                        {!! $settings['content.terms'] !!}
                    @else
                        <p class="lead">Please read these terms of service carefully before using our website.</p>

                        <h3>Acceptance of Terms</h3>
                        <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>

                        <h3>Use License</h3>
                        <p>Permission is granted to temporarily download one copy of the materials on our website for personal, non-commercial transitory viewing only.</p>

                        <h3>Disclaimer</h3>
                        <p>The materials on our website are provided on an 'as is' basis. We make no warranties, expressed or implied.</p>

                        <h3>Limitations</h3>
                        <p>In no event shall our company or its suppliers be liable for any damages arising out of the use or inability to use the materials on our website.</p>

                        <h3>Governing Law</h3>
                        <p>These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which we operate.</p>

                        <h3>Contact Information</h3>
                        <p>If you have any questions about these terms of service, please contact us.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
