<div class="appearance-preview">
    <style>
        .appearance-preview {
            padding: 20px;
            background: var(--bg, #f5f5f5);
            border-radius: 12px;
            margin: 20px 0;
        }
        
        .preview-section {
            margin-bottom: 30px;
        }
        
        .preview-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text, #333);
        }
        
        .color-palette {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .color-swatch {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            background: var(--surface, #fff);
            border: 1px solid var(--border, #e0e0e0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .color-preview {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid var(--border, #e0e0e0);
        }
        
        .color-name {
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            color: var(--text, #333);
        }
        
        .color-value {
            font-size: 0.8rem;
            color: var(--muted, #666);
            margin-top: 5px;
        }
        
        .gradient-preview {
            height: 100px;
            border-radius: 12px;
            margin: 15px 0;
            border: 1px solid var(--border, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        
        .card-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 15px 0;
        }
        
        .preview-card {
            padding: 20px;
            border-radius: var(--radius, 12px);
            background: var(--glass, rgba(255,255,255,0.8));
            backdrop-filter: blur(var(--blur, 20px));
            border: 1px solid var(--border, rgba(0,0,0,0.1));
            box-shadow: var(--shadow, 0 4px 6px rgba(0,0,0,0.1));
            transition: all var(--speed, 0.3s) ease;
        }
        
        .preview-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover, 0 8px 15px rgba(0,0,0,0.15));
        }
        
        .theme-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .theme-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .theme-btn.active {
            background: var(--primary, #3b82f6);
            color: white;
        }
        
        .theme-btn:not(.active) {
            background: var(--surface, #fff);
            color: var(--text, #333);
            border: 1px solid var(--border, #e0e0e0);
        }
    </style>

    <div class="preview-section">
        <div class="preview-title">🎨 Color Palette Preview</div>
        <div class="color-palette">
            <div class="color-swatch">
                <div class="color-preview" style="background: {{ $theme_primary ?? '#F0C275' }}"></div>
                <div class="color-name">Primary</div>
                <div class="color-value">{{ $theme_primary ?? '#F0C275' }}</div>
            </div>
            <div class="color-swatch">
                <div class="color-preview" style="background: {{ $theme_accent ?? '#FF6B6B' }}"></div>
                <div class="color-name">Accent</div>
                <div class="color-value">{{ $theme_accent ?? '#FF6B6B' }}</div>
            </div>
            <div class="color-swatch">
                <div class="color-preview" style="background: {{ $theme_success ?? '#10B981' }}"></div>
                <div class="color-name">Success</div>
                <div class="color-value">{{ $theme_success ?? '#10B981' }}</div>
            </div>
            <div class="color-swatch">
                <div class="color-preview" style="background: {{ $theme_warning ?? '#F59E0B' }}"></div>
                <div class="color-name">Warning</div>
                <div class="color-value">{{ $theme_warning ?? '#F59E0B' }}</div>
            </div>
            <div class="color-swatch">
                <div class="color-preview" style="background: {{ $theme_error ?? '#EF4444' }}"></div>
                <div class="color-name">Error</div>
                <div class="color-value">{{ $theme_error ?? '#EF4444' }}</div>
            </div>
        </div>
    </div>

    <div class="preview-section">
        <div class="preview-title">🌈 Gradient Preview</div>
        <div class="gradient-preview" style="background: {{ $gradient_custom ?: 'linear-gradient(' . ($gradient_angle ?? 135) . 'deg, ' . ($theme_grad_start ?? '#F0C275') . ', ' . ($theme_grad_end ?? '#7877C6') . ')' }}">
            {{ $gradient_type ?? 'linear' }} gradient
        </div>
        <div style="font-size: 0.9rem; color: var(--muted, #666);">
            Type: {{ ucfirst($gradient_type ?? 'linear') }} | 
            Angle: {{ $gradient_angle ?? 135 }}° | 
            Colors: {{ $theme_grad_start ?? '#F0C275' }} → {{ $theme_grad_end ?? '#7877C6' }}
        </div>
    </div>

    <div class="preview-section">
        <div class="preview-title">🃏 Card Styling Preview</div>
        <div class="card-preview">
            <div class="preview-card">
                <h4 style="margin: 0 0 10px 0; color: var(--text, #333);">Sample Card</h4>
                <p style="margin: 0; color: var(--muted, #666);">This is how your cards will look with the current settings.</p>
                <div style="margin-top: 15px;">
                    <button style="background: var(--primary, #F0C275); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                        Sample Button
                    </button>
                </div>
            </div>
            <div class="preview-card">
                <h4 style="margin: 0 0 10px 0; color: var(--text, #333);">Product Card</h4>
                <div style="width: 100%; height: 80px; background: var(--surface, #fff); border-radius: 8px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; color: var(--muted, #666);">
                    Product Image
                </div>
                <p style="margin: 0; color: var(--text, #333); font-weight: 500;">Product Name</p>
                <p style="margin: 5px 0 0 0; color: var(--primary, #F0C275); font-weight: 600;">$99.99</p>
            </div>
        </div>
    </div>

    <div class="preview-section">
        <div class="preview-title">⚙️ Advanced Settings Preview</div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div style="padding: 15px; background: var(--surface, #fff); border-radius: {{ $border_radius ?? 12 }}px; border: 1px solid var(--border, #e0e0e0);">
                <div style="font-weight: 600; margin-bottom: 5px;">Border Radius</div>
                <div style="color: var(--muted, #666);">{{ $border_radius ?? 12 }}px</div>
            </div>
            <div style="padding: 15px; background: var(--surface, #fff); border-radius: {{ $border_radius ?? 12 }}px; border: 1px solid var(--border, #e0e0e0);">
                <div style="font-weight: 600; margin-bottom: 5px;">Card Blur</div>
                <div style="color: var(--muted, #666);">{{ $card_blur ?? 20 }}px</div>
            </div>
            <div style="padding: 15px; background: var(--surface, #fff); border-radius: {{ $border_radius ?? 12 }}px; border: 1px solid var(--border, #e0e0e0);">
                <div style="font-weight: 600; margin-bottom: 5px;">Animation Speed</div>
                <div style="color: var(--muted, #666);">{{ $animation_speed ?? 0.3 }}s</div>
            </div>
            <div style="padding: 15px; background: var(--surface, #fff); border-radius: {{ $border_radius ?? 12 }}px; border: 1px solid var(--border, #e0e0e0);">
                <div style="font-weight: 600; margin-bottom: 5px;">Shadow Intensity</div>
                <div style="color: var(--muted, #666);">{{ ucfirst($shadow_intensity ?? 'medium') }}</div>
            </div>
        </div>
    </div>

    <script>
        // Live preview updates
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="color"], input[type="number"], select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Update preview in real-time
                    updatePreview();
                });
            });
            
            function updatePreview() {
                // This would update the preview based on form values
                // For now, it's static but can be enhanced with AJAX
                console.log('Preview updated');
            }
        });
    </script>
</div>
