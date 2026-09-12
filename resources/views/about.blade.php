@extends('layouts.app')

@section('title', __('common.pages.about_us') . ' - ' . ($siteName ?? 'MyStore'))

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
    <h1 class="page-title">{{ __('common.nav.about') }} {{ $siteName ?? 'MyStore' }}</h1>
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
            {{ __('common.pages.founded_with_vision') }}
          </p>
          <p class="story-text">
            {{ __('common.pages.journey_began') }}
          </p>
          <p class="story-text">
            {{ __('common.pages.not_just_selling') }}
          </p>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- Our Values --}}
<section class="py-5 reveal">
  <div class="container">
    <h2 class="section-title">{{ __('common.pages.our_values') }}</h2>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon">
            <svg class="value-svg" viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <h3 class="value-title">{{ __('common.pages.quality') }}</h3>
          <p class="value-description">
            {{ __('common.pages.we_never_compromise') }}
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
          <h3 class="value-title">{{ __('common.pages.trust') }}</h3>
          <p class="value-description">
            {{ __('common.pages.building_lasting_relationships') }}
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
          <h3 class="value-title">{{ __('common.pages.innovation') }}</h3>
          <p class="value-description">
            {{ __('common.pages.continuously_improving') }}
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
          <h3 class="value-title">{{ __('common.pages.customer_first') }}</h3>
          <p class="value-description">
            {{ __('common.pages.every_decision_guided') }}
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
          <div class="stat-label">{{ __('common.pages.happy_customers') }}</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">500+</div>
          <div class="stat-label">{{ __('common.nav.products') }}</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">50+</div>
          <div class="stat-label">{{ __('common.pages.brands') }}</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">24/7</div>
          <div class="stat-label">{{ __('common.pages.support') }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Our Team --}}
<section class="py-5 reveal">
  <div class="container">
    <h2 class="section-title">{{ __('common.pages.meet_our_team') }}</h2>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="team-card">
          <div class="team-avatar">👨‍💼</div>
          <h3 class="team-name">{{ __('common.pages.john_smith') }}</h3>
          <div class="team-role">{{ __('common.pages.founder_ceo') }}</div>
          <p class="team-bio">
            {{ __('common.pages.visionary_leader') }}
          </p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="team-card">
          <div class="team-avatar">👩‍💻</div>
          <h3 class="team-name">{{ __('common.pages.sarah_johnson') }}</h3>
          <div class="team-role">{{ __('common.pages.head_technology') }}</div>
          <p class="team-bio">
            {{ __('common.pages.tech_enthusiast') }}
          </p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="team-card">
          <div class="team-avatar">👨‍🎨</div>
          <h3 class="team-name">{{ __('common.pages.mike_chen') }}</h3>
          <div class="team-role">{{ __('common.pages.creative_director') }}</div>
          <p class="team-bio">
            {{ __('common.pages.creative_mind') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Call to Action --}}
<section class="cta-section reveal">
  <div class="container position-relative">
    <h2 class="cta-title">{{ __('common.pages.ready_to_experience') }}</h2>
    <p class="cta-text">
      {{ __('common.messages.join_thousands_satisfied') }}
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{ route('products.index') }}" class="btn btn-enhanced btn-lg px-5 py-3">
        {{ __('common.actions.shop_now') }}
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
