@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="crystal-card p-4">
                <h1 class="h2 mb-4">Privacy Policy</h1>
                
                <div class="content">
                    @if(isset($settings['content.privacy']) && $settings['content.privacy'])
                        {!! $settings['content.privacy'] !!}
                    @else
                        <p class="lead">Your privacy is important to us. This privacy policy explains how we collect, use, and protect your information.</p>
                        
                        <h3>Information We Collect</h3>
                        <p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us.</p>
                        
                        <h3>How We Use Your Information</h3>
                        <p>We use the information we collect to provide, maintain, and improve our services.</p>
                        
                        <h3>Information Sharing</h3>
                        <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent.</p>
                        
                        <h3>Data Security</h3>
                        <p>We implement appropriate security measures to protect your personal information.</p>
                        
                        <h3>Contact Us</h3>
                        <p>If you have any questions about this privacy policy, please contact us.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
