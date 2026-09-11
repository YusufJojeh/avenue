<div class="current-logos-display">
    <style>
        .current-logos-display {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .logo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        
        .logo-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .logo-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #495057;
        }
        
        .logo-preview {
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        
        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .logo-preview.no-logo {
            color: #6c757d;
            font-style: italic;
        }
        
        .logo-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .logo-path {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.8rem;
            word-break: break-all;
            margin-top: 10px;
        }
    </style>

    <div class="logo-grid">
        <div class="logo-item">
            <div class="logo-title">Light Theme Logo</div>
            <div class="logo-preview {{ $logo_light_url ? '' : 'no-logo' }}">
                @if($logo_light_url)
                    <img src="{{ $logo_light_url }}" alt="Light Logo" loading="lazy">
                @else
                    No logo uploaded
                @endif
            </div>
            <div class="logo-info">
                @if($logo_light)
                    <div class="logo-path">{{ $logo_light }}</div>
                @else
                    No logo file set
                @endif
            </div>
        </div>

        <div class="logo-item">
            <div class="logo-title">Dark Theme Logo</div>
            <div class="logo-preview {{ $logo_dark_url ? '' : 'no-logo' }}">
                @if($logo_dark_url)
                    <img src="{{ $logo_dark_url }}" alt="Dark Logo" loading="lazy">
                @else
                    No logo uploaded
                @endif
            </div>
            <div class="logo-info">
                @if($logo_dark)
                    <div class="logo-path">{{ $logo_dark }}</div>
                @else
                    No logo file set
                @endif
            </div>
        </div>
    </div>

    <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 6px; border-left: 4px solid #007bff;">
        <strong>💡 Tips:</strong>
        <ul style="margin: 10px 0 0 20px; color: #495057;">
            <li>Upload PNG or SVG files for best quality</li>
            <li>Recommended size: 200-400px width, transparent background</li>
            <li>Light logo should work well on dark backgrounds</li>
            <li>Dark logo should work well on light backgrounds</li>
            <li>Maximum file size: 2MB</li>
        </ul>
    </div>
</div>
