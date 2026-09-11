@extends('layouts.app')

@section('title', 'Page Not Found - ' . ($siteName ?? 'MyStore'))

@push('styles')
<style>
  .error-container {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem;
  }

  .error-content {
    max-width: 600px;
  }

  .error-number {
    font-size: 8rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--gold), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 1rem;
  }

  .error-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 1rem;
  }

  .error-message {
    font-size: 1.2rem;
    color: var(--muted);
    margin-bottom: 2rem;
    line-height: 1.6;
  }

  .error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
  }

  .error-illustration {
    font-size: 6rem;
    margin-bottom: 2rem;
    opacity: 0.8;
  }

  .suggested-links {
    margin-top: 3rem;
    padding: 2rem;
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 16px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
  }

  .suggested-links h3 {
    color: var(--text);
    margin-bottom: 1rem;
    font-size: 1.5rem;
  }

  .suggested-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
  }

  .suggested-links li a {
    color: var(--gold);
    text-decoration: none;
    padding: 0.5rem 1rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-block;
  }

  .suggested-links li a:hover {
    background: var(--gold);
    color: #111216;
    border-color: var(--gold);
    transform: translateY(-2px);
    text-decoration: none;
  }

  /* Dark mode enhancements */
  html[data-theme="dark"] .suggested-links {
    background: rgba(26,26,26,0.8);
    border-color: rgba(255,255,255,0.1);
  }
</style>
@endpush

@section('content')

<div class="error-container">
  <div class="error-content">
    <div class="error-illustration">🔍</div>

    <div class="error-number">404</div>

    <h1 class="error-title">Page Not Found</h1>

    <p class="error-message">
      Oops! The page you're looking for doesn't exist. It might have been moved,
      deleted, or you entered the wrong URL.
    </p>

    <div class="error-actions">
      <a href="{{ route('home') }}" class="btn btn-vel-gold btn-lg px-4 py-2">
        <i class="me-2">🏠</i> Go Home
      </a>
      <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
        <svg class="me-2 browse-icon" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
          <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
        </svg>
        Browse Products
      </a>
    </div>

    <div class="suggested-links">
      <h3>Popular Pages</h3>
      <ul>
        <li><a href="{{ route('products.index') }}">All Products</a></li>
        <li><a href="{{ route('categories.index') }}">Categories</a></li>
        <li><a href="{{ route('brands.index') }}">Brands</a></li>
        <li><a href="{{ route('about') }}">About Us</a></li>
        <li><a href="{{ route('contact') }}">Contact</a></li>
        <li><a href="{{ route('faq') }}">FAQ</a></li>
      </ul>
    </div>
  </div>
</div>

@endsection
