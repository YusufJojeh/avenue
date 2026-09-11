{{-- Unified Design System - Based on home.blade.php --}}
<style>
  /* ===== UNIFIED DESIGN SYSTEM ===== */
  
  /* Crystal and Glass Effects - Enhanced */
  .crystal-card {
    background: linear-gradient(135deg,
      rgba(255,255,255,0.1) 0%,
      rgba(255,255,255,0.05) 50%,
      rgba(255,255,255,0.02) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 24px;
    box-shadow:
      0 8px 32px rgba(0,0,0,0.1),
      inset 0 1px 0 rgba(255,255,255,0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .crystal-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
      transparent,
      rgba(255,255,255,0.1),
      transparent);
    transition: left 0.6s ease;
  }

  .crystal-card:hover::before {
    left: 100%;
  }

  .crystal-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow:
      0 20px 40px rgba(0,0,0,0.15),
      0 0 0 1px rgba(255,255,255,0.3),
      inset 0 1px 0 rgba(255,255,255,0.3);
  }

  /* 3D Floating Elements */
  .floating-element {
    position: absolute;
    opacity: 0.6;
    animation: float 6s ease-in-out infinite;
    z-index: 1;
  }

  .floating-element:nth-child(2) { animation-delay: -2s; }
  .floating-element:nth-child(3) { animation-delay: -4s; }
  .floating-element:nth-child(4) { animation-delay: -6s; }

  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
  }

  /* Enhanced Hero Sections */
  .hero-section {
    position: relative;
    min-height: 60vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: linear-gradient(135deg,
      rgba(240,194,75,0.1) 0%,
      rgba(120,119,198,0.1) 100%);
    border-radius: 24px;
    margin-bottom: 3rem;
  }

  .hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
      radial-gradient(circle at 20% 80%, rgba(240,194,75,0.15) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(120,119,198,0.1) 0%, transparent 50%),
      radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
    animation: heroFloat 20s ease-in-out infinite;
  }

  @keyframes heroFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-20px) rotate(1deg); }
    66% { transform: translateY(10px) rotate(-1deg); }
  }

  /* Enhanced Section Titles */
  .section-title {
    font-family: 'Georgia', serif;
    font-weight: 700;
    font-size: 2.5rem;
    background: linear-gradient(135deg, var(--text) 0%, var(--gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    margin-bottom: 3rem;
    text-align: center;
  }

  .section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--gold), transparent);
    border-radius: 2px;
  }

  /* Enhanced Page Headers */
  .page-header {
    background: linear-gradient(135deg, rgba(240,194,75,0.1) 0%, rgba(120,119,198,0.1) 100%);
    border-radius: 24px;
    padding: 4rem 2rem;
    margin-bottom: 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
  }

  .page-title {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text) 0%, var(--gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    position: relative;
    z-index: 2;
  }

  .page-subtitle {
    color: var(--muted);
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
  }

  /* Enhanced Content Cards */
  .content-card {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .content-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
      transparent,
      rgba(240,194,75,0.1),
      transparent);
    transition: left 0.6s ease;
  }

  .content-card:hover::before {
    left: 100%;
  }

  .content-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0,0,0,0.15);
  }

  /* Enhanced Form Elements */
  .form-control {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  .form-control:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(240,194,75,0.2);
    background: var(--surface);
    color: var(--text);
  }

  .form-control::placeholder {
    color: var(--muted);
  }

  /* Enhanced Buttons */
  .btn-enhanced {
    background: linear-gradient(135deg, var(--gold), #ffd700);
    color: #111216;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(240, 194, 75, 0.3);
  }

  .btn-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
      transparent,
      rgba(255,255,255,0.3),
      transparent);
    transition: left 0.5s ease;
  }

  .btn-enhanced:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #ffd700, var(--gold));
    box-shadow: 0 8px 24px rgba(240, 194, 75, 0.4);
    color: #111216;
  }

  .btn-enhanced:hover::before {
    left: 100%;
  }

  /* Enhanced Info Cards */
  .info-card {
    background: var(--glass);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
  }

  .info-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  }

  .info-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--gold), #ffd700);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #111216;
    margin: 0 auto 1rem;
  }

  /* Enhanced Stats Section */
  .stats-section {
    background: linear-gradient(135deg, rgba(240,194,75,0.1) 0%, rgba(120,119,198,0.1) 100%);
    border-radius: 24px;
    padding: 3rem 0;
    margin: 3rem 0;
  }

  .stat-item {
    text-align: center;
    padding: 1rem;
  }

  .stat-number {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gold), #ff6b6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
  }

  .stat-label {
    color: var(--muted);
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  /* Enhanced CTA Section */
  .cta-section {
    background: linear-gradient(135deg, rgba(240,194,75,0.15) 0%, rgba(120,119,198,0.15) 100%);
    border-radius: 24px;
    padding: 4rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
  }

  /* Enhanced Animations */
  .fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .fade-in-up.visible {
    opacity: 1;
    transform: translateY(0);
  }

  .scale-in {
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .scale-in.visible {
    opacity: 1;
    transform: scale(1);
  }

  /* Enhanced Responsive Design */
  @media (max-width: 768px) {
    .hero-section {
      min-height: 50vh;
    }

    .page-title {
      font-size: 2rem;
    }

    .section-title {
      font-size: 2rem;
    }

    .stat-number {
      font-size: 2rem;
    }

    .page-header {
      padding: 2rem 1rem;
    }
  }

  /* Dark Mode Enhancements */
  html[data-theme="dark"] .crystal-card,
  html[data-theme="dark"] .content-card,
  html[data-theme="dark"] .info-card {
    background: linear-gradient(135deg,
      rgba(240,194,75,0.15) 0%,
      rgba(120,119,198,0.15) 100%);
    border-color: rgba(255,255,255,0.1);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
  }

  html[data-theme="dark"] .crystal-card:hover,
  html[data-theme="dark"] .content-card:hover,
  html[data-theme="dark"] .info-card:hover {
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
  }

  html[data-theme="dark"] .page-header,
  html[data-theme="dark"] .hero-section,
  html[data-theme="dark"] .stats-section,
  html[data-theme="dark"] .cta-section {
    background: linear-gradient(135deg, rgba(240,194,75,0.15) 0%, rgba(120,119,198,0.15) 100%);
  }

  html[data-theme="dark"] .form-control {
    background: rgba(26,26,26,0.9);
    border-color: rgba(255,255,255,0.1);
    color: #f0f0f0;
  }

  html[data-theme="dark"] .form-control:focus {
    border-color: rgba(240,194,75,0.5);
    box-shadow: 0 0 0 3px rgba(240,194,75,0.3);
  }
</style>
