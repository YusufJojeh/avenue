<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class ThemeCssController
{
    public function __invoke()
    {
        // اجلب القيم
        $gold       = Setting::get('theme.primary', '#F0C24B');
        $gradStart  = Setting::get('theme.grad_start', $gold);
        $gradEnd    = Setting::get('theme.grad_end', '#D9A92F');

        $l_bg       = Setting::get('theme.light.bg', '#F5F2EC');
        $l_surface  = Setting::get('theme.light.surface', '#FFF7ED');
        $l_text     = Setting::get('theme.light.text', '#111216');
        $l_muted    = Setting::get('theme.light.muted', '#6F7480');

        $d_bg       = Setting::get('theme.dark.bg', '#0F1115');
        $d_surface  = Setting::get('theme.dark.surface', '#161A20');
        $d_text     = Setting::get('theme.dark.text', '#ECEEF2');
        $d_muted    = Setting::get('theme.dark.muted', '#A9B0B8');

        $css = <<<CSS
:root{
  --gold: {$gold};
  --grad-start: {$gradStart};
  --grad-end: {$gradEnd};

  --bg: {$l_bg};
  --bg-grad: radial-gradient(1000px 500px at 10% -10%, color-mix(in oklab, {$gold} 10%, transparent) 0%, transparent 60%),
             radial-gradient(900px 600px at 120% 0%, color-mix(in oklab, {$gold} 8%, transparent) 0%, transparent 55%);
  --surface: {$l_surface};
  --text: {$l_text};
  --muted: {$l_muted};
  --border: rgba(0,0,0,0.1);
  --glass: rgba(255,255,255,0.8);
  --glass-strong: rgba(255,255,255,0.95);
  --ring: rgba(0,0,0,0.1);
  --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
  --radius: 12px;
  --speed: 0.3s;
  --accent-grad: linear-gradient(135deg, {$gradStart}, {$gradEnd});
}

html[data-theme="dark"]{
  --bg: {$d_bg};
  --bg-grad: radial-gradient(1000px 500px at 10% -10%, color-mix(in oklab, {$gold} 15%, transparent) 0%, transparent 60%),
             radial-gradient(900px 600px at 120% 0%, color-mix(in oklab, {$gold} 12%, transparent) 0%, transparent 55%);
  --surface: {$d_surface};
  --text: {$d_text};
  --muted: {$d_muted};
  --border: rgba(255,255,255,0.1);
  --glass: rgba(255,255,255,0.1);
  --glass-strong: rgba(255,255,255,0.15);
  --ring: rgba(255,255,255,0.1);
  --shadow: 0 4px 6px -1px rgba(0,0,0,0.3), 0 2px 4px -1px rgba(0,0,0,0.2);
}

/* Optional helpers */
.btn-gold{
  background: linear-gradient(180deg, var(--grad-start), var(--grad-end));
  color:#111216; border:none; font-weight:600;
}
.bg-hero-gradient{
  background: radial-gradient(1000px 500px at 10% -10%, color-mix(in oklab, var(--gold) 10%, transparent) 0%, transparent 60%),
              radial-gradient(900px 600px at 120% 0%, color-mix(in oklab, var(--gold) 8%, transparent) 0%, transparent 55%),
              var(--bg);
}
CSS;

        $etag = md5($css);
        if (request()->getETags() && in_array($etag, request()->getETags(), true)) {
            return response('', 304)->setEtag($etag);
        }

        return response($css, 200)
            ->header('Content-Type', 'text/css')
            ->setSharedMaxAge(600)
            ->setEtag($etag);
    }
}
