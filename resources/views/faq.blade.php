@extends('layouts.app')

@section('title', __('common.nav.faq') . ' - ' . ($siteName ?? 'MyStore'))

@push('styles')
@include('partials.unified-styles')
<style>
  /* FAQ Page Specific Styles */

  .faq-section {
    margin-bottom: 3rem;
  }

  .faq-item {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 16px;
    margin-bottom: 1rem;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .faq-item:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    transform: translateY(-2px);
  }

  .faq-question {
    background: var(--surface);
    padding: 1.5rem;
    cursor: pointer;
    border: none;
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: var(--text);
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
  }

  .faq-question:hover {
    background: var(--glass);
  }

  .faq-question:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(240,194,75,0.2);
  }

  .faq-icon {
    width: 24px;
    height: 24px;
    background: var(--gold);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #111216;
    font-size: 0.875rem;
    font-weight: bold;
    transition: transform 0.3s ease;
    flex-shrink: 0;
  }

  .faq-item.active .faq-icon {
    transform: rotate(45deg);
  }

  .faq-answer {
    padding: 0 1.5rem;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    background: var(--glass);
  }

  .faq-item.active .faq-answer {
    padding: 1.5rem;
    max-height: 500px;
  }

  .faq-answer p {
    margin: 0;
    color: var(--text);
    line-height: 1.6;
  }

  .faq-answer ul {
    margin: 1rem 0;
    padding-left: 1.5rem;
    color: var(--text);
  }

  .faq-answer li {
    margin-bottom: 0.5rem;
  }

  .contact-title {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text);
  }

  .contact-text {
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

{{-- FAQ Header --}}
<div class="container py-4">
  <div class="page-header text-center mb-5">
    <h1 class="page-title">{{ __('common.messages.frequently_asked_questions') }}</h1>
    <p class="page-subtitle text-muted">
      {{ __('common.pages.find_answers_common') }}
    </p>
  </div>

{{-- FAQ Content --}}
<section class="py-5 reveal">
  <div class="container">
    {{-- General Questions --}}
    <div class="faq-section">
      <h2 class="section-title">{{ __('common.pages.general_questions') }}</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.messages.what_is_mystore') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.mystore_description') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.how_create_account') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.creating_account_easy') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.personal_information_secure') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.absolutely_secure') }}
          </p>
        </div>
      </div>
    </div>

    {{-- Shopping & Orders --}}
    <div class="faq-section">
      <h2 class="section-title">{{ __('common.pages.shopping_orders') }}</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.how_place_order') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.place_order_description') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.payment_methods_accept') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.payment_methods_description') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.shipping_times') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.shipping_times_description') }}
          </p>
          <ul>
            <li>{{ __('common.pages.standard_shipping') }}</li>
            <li>{{ __('common.pages.express_shipping') }}</li>
            <li>{{ __('common.pages.international_shipping') }}</li>
          </ul>
          <p>
            {{ __('common.pages.receive_tracking_info') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.cancel_modify_order') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.cancel_modify_description') }}
          </p>
        </div>
      </div>
    </div>

    {{-- Returns & Refunds --}}
    <div class="faq-section">
      <h2 class="section-title">{{ __('common.pages.returns_refunds') }}</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.return_policy') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.return_policy_description') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.how_return_item') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.return_item_description') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.refund_time') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.refund_time_description') }}
          </p>
        </div>
      </div>
    </div>

    {{-- Product Information --}}
    <div class="faq-section">
      <h2 class="section-title">{{ __('common.pages.product_information') }}</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.products_authentic') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.products_authentic_description') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.product_warranties') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.product_warranties_description') }}
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.product_reviews') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.product_reviews_description') }}
          </p>
        </div>
      </div>
    </div>

    {{-- Customer Service --}}
    <div class="faq-section">
      <h2 class="section-title">{{ __('common.pages.customer_service') }}</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.contact_customer_service') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.contact_customer_service_description') }}
          </p>
          <ul>
            <li>{{ __('common.pages.live_chat') }}</li>
            <li>{{ __('common.fields.email') }}: {{ 'support@' . str_replace(['http://', 'https://', 'www.'], '', config('app.url', 'mystore.com')) }}</li>
            <li>{{ __('common.pages.phone_support') }}</li>
            <li>{{ __('common.pages.contact_form') }}</li>
          </ul>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          {{ __('common.pages.what_are_business_hours') }}
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.pages.business_hours_description') }}
          </p>
          <ul>
            <li>{{ __('common.pages.monday_friday_est') }}</li>
            <li>{{ __('common.pages.saturday_est') }}</li>
            <li>{{ __('common.pages.sunday_closed') }}</li>
          </ul>
          <p>
            {{ __('common.pages.urgent_matters') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Contact Section --}}
<section class="cta-section reveal">
  <div class="container position-relative">
    <h3 class="contact-title">{{ __('common.pages.still_have_questions') }}</h3>
    <p class="contact-text">
      {{ __('common.pages.cant_find_answer') }}
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{ route('contact') }}" class="btn btn-enhanced btn-lg px-4 py-2">
        {{ __('common.actions.contact_us') }}
        <i class="ms-2">→</i>
      </a>
      <a href="{{ route('products.index') }}" class="btn btn-vel-outline btn-lg px-4 py-2">
        {{ __('common.actions.browse_products') }}
      </a>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  function toggleFAQ(button) {
    const faqItem = button.closest('.faq-item');
    const isActive = faqItem.classList.contains('active');

    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
      item.classList.remove('active');
    });

    // Open clicked item if it wasn't already open
    if (!isActive) {
      faqItem.classList.add('active');
    }
  }

  // Close FAQ when clicking outside
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.faq-item')) {
      document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
      });
    }
  });

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
      });
    }
  });
</script>
@endpush
