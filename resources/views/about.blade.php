@extends('layouts.app')

@section('title', 'About Us - ' . ($siteName ?? 'MyStore'))

@push('styles')
@include('partials.unified-styles')
<style>
  /* About Page Specific Styles */

  .story-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
  }

  .story-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text);
    margin-bottom: 2rem;
  }

  .values-section {
    margin-bottom: 3rem;
  }

  .value-card {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    height: 100%;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .value-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  }

  .value-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--gold), #ffd700);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #111216;
    margin: 0 auto 1.5rem;
  }

  .value-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text);
  }

  .value-description {
    color: var(--muted);
    line-height: 1.6;
  }

  .team-section {
    margin-bottom: 3rem;
  }

  .team-card {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    height: 100%;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .team-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  }

  .team-avatar {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--gold), #ffd700);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #111216;
    margin: 0 auto 1.5rem;
  }

  .team-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text);
  }

  .team-role {
    color: var(--gold);
    font-weight: 500;
    margin-bottom: 1rem;
  }

  .team-bio {
    color: var(--muted);
    font-size: 0.9rem;
    line-height: 1.6;
  }

  .cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--text);
  }

  .cta-text {
    color: var(--muted);
    font-size: 1.1rem;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }
</style>
@endpush

@section('content')

{{-- About Header --}}
<div class="container py-4">
  <div class="page-header text-center mb-5">
    <h1 class="page-title">About {{ $siteName ?? 'MyStore' }}</h1>
    <p class="page-subtitle text-muted">
      {{ __('common.messages.passionate_about_delivering') }}
    </p>
  </div>

{{-- Our Story --}}
<section class="py-5 reveal">
  <div class="container">
    <div class="content-card">
      <div class="story-content">
        <h2 class="section-title">{{ __('common.pages.our_story') }}</h2>
        @if(isset($settings['content.about']) && $settings['content.about'])
          <div class="story-text">
            {!! $settings['content.about'] !!}
          </div>
        @else
          <p class="story-text">
            {{ __('common.messages.founded_with_vision') }}
            What started as a small local business has grown into a trusted destination for quality products and exceptional service.
          </p>
          <p class="story-text">
            Our journey began with a simple belief: that every customer deserves access to premium products at fair prices,
            backed by outstanding customer support. Today, we continue to uphold these values while embracing new technologies
            and expanding our product offerings to meet the evolving needs of our community.
          </p>
          <p class="story-text">
            We're not just selling products; we're building relationships, fostering trust, and creating experiences that
            make a difference in our customers' lives. Every decision we make is guided by our commitment to excellence
            and our passion for customer satisfaction.
          </p>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- Our Values --}}
<section class="py-5 reveal">
  <div class="container">
    <h2 class="section-title">Our Values</h2>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon">
            <svg class="value-svg" viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <h3 class="value-title">Quality</h3>
          <p class="value-description">
            We never compromise on quality. Every product in our catalog meets our high standards for excellence.
          </p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon">
            <svg class="value-svg" viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <h3 class="value-title">Trust</h3>
          <p class="value-description">
            Building lasting relationships through transparency, honesty, and reliable service.
          </p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon">
            <svg class="value-svg" viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <h3 class="value-title">Innovation</h3>
          <p class="value-description">
            Continuously improving our platform and services to provide the best possible experience.
          </p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon">
            <svg class="value-svg" viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <h3 class="value-title">Customer First</h3>
          <p class="value-description">
            Every decision we make is guided by what's best for our customers.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Stats Section --}}
<section class="stats-section reveal">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">1000+</div>
          <div class="stat-label">Happy Customers</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">500+</div>
          <div class="stat-label">Products</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">50+</div>
          <div class="stat-label">Brands</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">24/7</div>
          <div class="stat-label">Support</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Our Team --}}
<section class="py-5 reveal">
  <div class="container">
    <h2 class="section-title">Meet Our Team</h2>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="team-card">
          <div class="team-avatar">👨‍💼</div>
          <h3 class="team-name">John Smith</h3>
          <div class="team-role">Founder & CEO</div>
          <p class="team-bio">
            Visionary leader with over 15 years of experience in e-commerce and digital innovation.
          </p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="team-card">
          <div class="team-avatar">👩‍💻</div>
          <h3 class="team-name">Sarah Johnson</h3>
          <div class="team-role">Head of Technology</div>
          <p class="team-bio">
            Tech enthusiast driving our digital transformation and platform development.
          </p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="team-card">
          <div class="team-avatar">👨‍🎨</div>
          <h3 class="team-name">Mike Chen</h3>
          <div class="team-role">Creative Director</div>
          <p class="team-bio">
            Creative mind behind our brand identity and customer experience design.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Call to Action --}}
<section class="cta-section reveal">
  <div class="container position-relative">
    <h2 class="cta-title">Ready to Experience the Difference?</h2>
    <p class="cta-text">
      {{ __('common.messages.join_thousands_satisfied') }}
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{ route('products.index') }}" class="btn btn-enhanced btn-lg px-5 py-3">
        Shop Now
        <i class="ms-2">→</i>
      </a>
      <a href="{{ route('contact') }}" class="btn btn-vel-outline btn-lg px-5 py-3">
        {{ __('common.actions.contact_us') }}
      </a>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  // Enhanced reveal animations
  (function(){
    if (window.__velRevealBound) return;
    window.__velRevealBound = true;

    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Observe all reveal elements
    document.querySelectorAll('.reveal').forEach(el => {
      observer.observe(el);
    });
  })();
</script>
@endpush
