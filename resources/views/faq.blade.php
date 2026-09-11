@extends('layouts.app')

@section('title', 'FAQ - ' . ($siteName ?? 'MyStore'))

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
      Find answers to common questions about our products, services, and policies.
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
            {{ $siteName ?? 'MyStore' }} is an online marketplace that offers a wide variety of high-quality products
            from trusted brands. We focus on providing exceptional customer service, competitive prices,
            and a seamless shopping experience.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          How do I create an account?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            {{ __('common.messages.creating_account_easy') }}
            fill in your details, and verify your email address. You'll then have access to your
            personalized dashboard, order history, and exclusive member benefits.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          Is my personal information secure?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Absolutely! We take your privacy and security very seriously. All personal information
            is encrypted using industry-standard SSL technology, and we never share your data with
            third parties without your explicit consent.
          </p>
        </div>
      </div>
    </div>

    {{-- Shopping & Orders --}}
    <div class="faq-section">
      <h2 class="section-title">Shopping & Orders</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          How do I place an order?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            To place an order, simply browse our products, add items to your cart, and proceed to checkout.
            You can choose from various payment methods including credit cards, PayPal, and other secure
            payment options. You'll receive an order confirmation email once your order is placed.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          What payment methods do you accept?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            We accept all major credit cards (Visa, MasterCard, American Express), PayPal, Apple Pay,
            Google Pay, and bank transfers. All payments are processed securely through our trusted
            payment partners.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          How long does shipping take?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Shipping times vary depending on your location and the shipping method you choose:
          </p>
          <ul>
            <li>Standard Shipping: 3-5 business days</li>
            <li>Express Shipping: 1-2 business days</li>
            <li>International Shipping: 7-14 business days</li>
          </ul>
          <p>
            You'll receive tracking information once your order ships.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          Can I cancel or modify my order?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Orders can be cancelled or modified within 1 hour of placement, as long as they haven't
            been processed for shipping. Please contact our customer service team immediately if you
            need to make changes to your order.
          </p>
        </div>
      </div>
    </div>

    {{-- Returns & Refunds --}}
    <div class="faq-section">
      <h2 class="section-title">Returns & Refunds</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          What is your return policy?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            We offer a 30-day return policy for most items. Products must be in their original
            condition with all packaging intact. Some items may have different return policies
            due to their nature (e.g., digital products, personalized items).
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          How do I return an item?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            To return an item, log into your account and go to "Order History." Select the order
            containing the item you want to return and follow the return process. You'll receive
            a return shipping label and instructions. Once we receive the item, we'll process
            your refund within 5-7 business days.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          How long does it take to get a refund?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Refunds are typically processed within 5-7 business days after we receive your returned item.
            The time it takes for the refund to appear in your account depends on your bank or
            payment provider, but usually takes 3-5 business days.
          </p>
        </div>
      </div>
    </div>

    {{-- Product Information --}}
    <div class="faq-section">
      <h2 class="section-title">{{ __('common.pages.product_information') }}</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          Are your products authentic?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Yes, all our products are 100% authentic. We work directly with authorized distributors
            and manufacturers to ensure the authenticity and quality of every item in our catalog.
            We never sell counterfeit or replica products.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          Do you offer product warranties?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Most products come with manufacturer warranties. The warranty period and coverage vary
            by product and brand. You can find warranty information on each product page.
            Additionally, we offer extended warranty options for many items.
          </p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          Can I see product reviews?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Yes! We display customer reviews and ratings for all products. These reviews are
            from verified customers who have purchased and used the products. You can filter
            reviews by rating, date, and helpfulness to find the information most relevant to you.
          </p>
        </div>
      </div>
    </div>

    {{-- Customer Service --}}
    <div class="faq-section">
      <h2 class="section-title">{{ __('common.pages.customer_service') }}</h2>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          How can I contact customer service?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Our customer service team is available 24/7 to help you. You can reach us through:
          </p>
          <ul>
            <li>Live Chat: Available on our website</li>
            <li>Email: support@{{ str_replace(['http://', 'https://', 'www.'], '', config('app.url', 'mystore.com')) }}</li>
            <li>Phone: +1 (555) 123-4567</li>
            <li>Contact Form: Available on our Contact page</li>
          </ul>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" onclick="toggleFAQ(this)">
          What are your business hours?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>
            Our online store is open 24/7, so you can shop anytime! Our customer service team
            is available:
          </p>
          <ul>
            <li>Monday - Friday: 9:00 AM - 6:00 PM (EST)</li>
            <li>Saturday: 10:00 AM - 4:00 PM (EST)</li>
            <li>Sunday: Closed</li>
          </ul>
          <p>
            For urgent matters outside these hours, you can still reach us via email.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Contact Section --}}
<section class="cta-section reveal">
  <div class="container position-relative">
    <h3 class="contact-title">Still Have Questions?</h3>
    <p class="contact-text">
      Can't find the answer you're looking for? Our customer service team is here to help!
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
