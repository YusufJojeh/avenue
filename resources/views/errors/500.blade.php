@extends('layouts.app')

@section('title', 'Server Error - ' . ($siteName ?? 'MyStore'))

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
    background: linear-gradient(135deg, var(--accent), var(--gold));
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

  .error-details {
    margin-top: 2rem;
    padding: 1.5rem;
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 16px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    text-align: left;
  }

  .error-details h3 {
    color: var(--text);
    margin-bottom: 1rem;
    font-size: 1.25rem;
  }

  .error-details p {
    color: var(--muted);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }

  .error-details .error-id {
    font-family: var(--font-mono);
    background: var(--surface);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    color: var(--text);
  }

  /* Dark mode enhancements */
  html[data-theme="dark"] .error-details {
    background: rgba(26,26,26,0.8);
    border-color: rgba(255,255,255,0.1);
  }
</style>
@endpush

@section('content')

<div class="error-container">
  <div class="error-content">
    <div class="error-illustration">⚠️</div>

    <div class="error-number">500</div>

    <h1 class="error-title">Server Error</h1>

    <p class="error-message">
      Something went wrong on our end. We're working to fix the issue.
      Please try again in a few minutes.
    </p>

    <div class="error-actions">
      <a href="{{ route('home') }}" class="btn btn-vel-gold btn-lg px-4 py-2">
        <i class="me-2">🏠</i> Go Home
      </a>
      <button onclick="window.location.reload()" class="btn btn-vel-outline btn-lg px-4 py-2">
        <i class="me-2">🔄</i> Try Again
      </button>
    </div>

    @if(config('app.debug'))
      <div class="error-details">
        <h3>Error Details (Debug Mode)</h3>
        <p><strong>Error ID:</strong> <span class="error-id">{{ uniqid() }}</span></p>
        <p><strong>Time:</strong> {{ now()->format('Y-m-d H:i:s T') }}</p>
        <p><strong>URL:</strong> {{ request()->fullUrl() }}</p>
        <p><strong>Method:</strong> {{ request()->method() }}</p>
        <p><strong>User Agent:</strong> {{ request()->userAgent() }}</p>
      </div>
    @endif
  </div>
</div>

@endsection
