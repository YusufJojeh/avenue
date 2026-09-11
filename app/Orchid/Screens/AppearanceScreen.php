<?php

namespace App\Orchid\Screens;

use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Toast;

class AppearanceScreen extends Screen
{
    public function query(): array
    {
        return [
            // Brand Colors
            'theme_primary'    => Setting::get('theme.primary', '#F0C275'),
            'theme_grad_start' => Setting::get('theme.grad.start', '#F0C275'),
            'theme_grad_end'   => Setting::get('theme.grad.end', '#7877C6'),
            'theme_accent'     => Setting::get('theme.accent', '#FF6B6B'),
            'theme_success'    => Setting::get('theme.success', '#10B981'),
            'theme_warning'    => Setting::get('theme.warning', '#F59E0B'),
            'theme_error'      => Setting::get('theme.error', '#EF4444'),

            // Light Theme Colors
            'light_bg'         => Setting::get('theme.light.bg', '#F5F2EC'),
            'light_surface'    => Setting::get('theme.light.surface', '#FFFFFF'),
            'light_text'       => Setting::get('theme.light.text', '#1E293B'),
            'light_muted'      => Setting::get('theme.light.muted', '#64748B'),
            'light_border'     => Setting::get('theme.light.border', '#E2E8F0'),
            'light_glass'      => Setting::get('theme.light.glass', 'rgba(255,255,255,0.8)'),

            // Dark Theme Colors
            'dark_bg'          => Setting::get('theme.dark.bg', '#0F1115'),
            'dark_surface'     => Setting::get('theme.dark.surface', '#1E293B'),
            'dark_text'        => Setting::get('theme.dark.text', '#F1F5F9'),
            'dark_muted'       => Setting::get('theme.dark.muted', '#94A3B8'),
            'dark_border'      => Setting::get('theme.dark.border', '#334155'),
            'dark_glass'       => Setting::get('theme.dark.glass', 'rgba(255,255,255,0.1)'),

            // Advanced Settings
            'gradient_type'    => Setting::get('theme.gradient.type', 'linear'),
            'gradient_angle'   => Setting::get('theme.gradient.angle', '135'),
            'gradient_custom'  => Setting::get('theme.gradient.custom', ''),
            'border_radius'    => Setting::get('theme.border.radius', '12'),
            'shadow_intensity' => Setting::get('theme.shadow.intensity', 'medium'),
            'animation_speed'  => Setting::get('theme.animation.speed', '0.3'),

            // Card Styling
            'card_blur'        => Setting::get('theme.card.blur', '20'),
            'card_opacity'     => Setting::get('theme.card.opacity', '0.1'),
            'card_border'      => Setting::get('theme.card.border', '0.3'),

            // Backup Status
            'has_backup'       => Setting::get('theme.backup.working_style') ? true : false,
            'backup_date'      => Setting::get('theme.backup.last_created') ? Setting::get('theme.backup.last_created') : null,
        ];
    }

    public function name(): ?string
    {
        return 'Appearance Settings';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Preview')->icon('bs.eye')->method('preview'),
            Button::make('Create Backup')->icon('bs.download')->method('createBackup')->confirm('Create a backup of current working style?'),
            Button::make('Reset Options')->icon('bs.arrow-clockwise')->method('showResetOptions'),
            Button::make('Save')->icon('bs.check')->method('save'),
        ];
    }

    public function layout(): array
    {
        $imageService = app(ImageService::class);

        $logoLightHelp = Setting::get('site.logo_light')
            ? 'Current: ' . $imageService->getUrl(Setting::get('site.logo_light'))
            : 'Upload PNG/SVG (max 2MB)';

        $logoDarkHelp = Setting::get('site.logo_dark')
            ? 'Current: ' . $imageService->getUrl(Setting::get('site.logo_dark'))
            : 'Upload PNG/SVG (max 2MB)';

        $faviconHelp = Setting::get('site.favicon')
            ? 'Current: ' . $imageService->getUrl(Setting::get('site.favicon'))
            : 'Upload PNG/ICO (max 1MB)';

        return [
            // Theme Presets
            Layout::rows([
                Select::make('theme_preset')
                    ->title('Theme Preset')
                    ->options([
                        'crystal' => 'Crystal Theme (Current)',
                        'ocean' => 'Ocean Blue',
                        'sunset' => 'Sunset Orange',
                        'forest' => 'Forest Green',
                        'royal' => 'Royal Purple',
                        'minimal' => 'Minimal Gray',
                        'custom' => 'Custom Colors',
                    ])
                    ->help('Choose a preset or create custom colors')
                    ->value('crystal'),
            ])->title('🎨 Theme Presets'),

            // Brand Colors
            Layout::rows([
                Input::make('theme_primary')
                    ->title('Primary Color')
                    ->type('color')
                    ->help('Main brand color used throughout the site'),

                Input::make('theme_accent')
                    ->title('Accent Color')
                    ->type('color')
                    ->help('Secondary accent color for highlights'),

                Input::make('theme_success')
                    ->title('Success Color')
                    ->type('color')
                    ->help('Color for success states and positive actions'),

                Input::make('theme_warning')
                    ->title('Warning Color')
                    ->type('color')
                    ->help('Color for warning states and caution messages'),

                Input::make('theme_error')
                    ->title('Error Color')
                    ->type('color')
                    ->help('Color for error states and negative actions'),
            ])->title('🎯 Brand Colors'),

            // Gradient Maker
            Layout::rows([
                Select::make('gradient_type')
                    ->title('Gradient Type')
                    ->options([
                        'linear' => 'Linear Gradient',
                        'radial' => 'Radial Gradient',
                        'conic' => 'Conic Gradient',
                    ])
                    ->help('Choose the type of gradient effect'),

                Input::make('gradient_angle')
                    ->title('Gradient Angle')
                    ->type('number')
                    ->min(0)
                    ->max(360)
                    ->help('Angle in degrees (0-360)'),

                Input::make('theme_grad_start')
                    ->title('Gradient Start Color')
                    ->type('color')
                    ->help('Starting color of the gradient'),

                Input::make('theme_grad_end')
                    ->title('Gradient End Color')
                    ->type('color')
                    ->help('Ending color of the gradient'),

                TextArea::make('gradient_custom')
                    ->title('Custom Gradient CSS')
                    ->rows(3)
                    ->help('Advanced: Custom CSS gradient (e.g., linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1))')
                    ->placeholder('linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1)'),
            ])->title('🌈 Gradient Maker'),

            // Light Theme Colors
            Layout::rows([
                Input::make('light_bg')
                    ->title('Background Color')
                    ->type('color')
                    ->help('Main background color'),

                Input::make('light_surface')
                    ->title('Surface Color')
                    ->type('color')
                    ->help('Color for cards and elevated surfaces'),

                Input::make('light_text')
                    ->title('Text Color')
                    ->type('color')
                    ->help('Primary text color'),

                Input::make('light_muted')
                    ->title('Muted Text Color')
                    ->type('color')
                    ->help('Secondary and muted text color'),

                Input::make('light_border')
                    ->title('Border Color')
                    ->type('color')
                    ->help('Color for borders and dividers'),

                Input::make('light_glass')
                    ->title('Glass Effect Color')
                    ->type('text')
                    ->help('RGBA color for glass morphism effects (e.g., rgba(255,255,255,0.8))')
                    ->placeholder('rgba(255,255,255,0.8)'),
            ])->title('☀️ Light Theme Colors'),

            // Dark Theme Colors
            Layout::rows([
                Input::make('dark_bg')
                    ->title('Background Color')
                    ->type('color')
                    ->help('Main background color'),

                Input::make('dark_surface')
                    ->title('Surface Color')
                    ->type('color')
                    ->help('Color for cards and elevated surfaces'),

                Input::make('dark_text')
                    ->title('Text Color')
                    ->type('color')
                    ->help('Primary text color'),

                Input::make('dark_muted')
                    ->title('Muted Text Color')
                    ->type('color')
                    ->help('Secondary and muted text color'),

                Input::make('dark_border')
                    ->title('Border Color')
                    ->type('color')
                    ->help('Color for borders and dividers'),

                Input::make('dark_glass')
                    ->title('Glass Effect Color')
                    ->type('text')
                    ->help('RGBA color for glass morphism effects (e.g., rgba(255,255,255,0.1))')
                    ->placeholder('rgba(255,255,255,0.1)'),
            ])->title('🌙 Dark Theme Colors'),

            // Advanced Styling
            Layout::rows([
                Input::make('border_radius')
                    ->title('Border Radius')
                    ->type('number')
                    ->min(0)
                    ->max(50)
                    ->help('Border radius in pixels (0-50)'),

                Select::make('shadow_intensity')
                    ->title('Shadow Intensity')
                    ->options([
                        'none' => 'No Shadows',
                        'light' => 'Light Shadows',
                        'medium' => 'Medium Shadows',
                        'heavy' => 'Heavy Shadows',
                    ])
                    ->help('Choose shadow intensity level'),

                Input::make('animation_speed')
                    ->title('Animation Speed')
                    ->type('number')
                    ->step(0.1)
                    ->min(0.1)
                    ->max(2.0)
                    ->help('Animation duration in seconds (0.1-2.0)'),
            ])->title('⚙️ Advanced Styling'),

            // Card Styling
            Layout::rows([
                Input::make('card_blur')
                    ->title('Card Blur Effect')
                    ->type('number')
                    ->min(0)
                    ->max(50)
                    ->help('Backdrop blur in pixels (0-50)'),

                Input::make('card_opacity')
                    ->title('Card Background Opacity')
                    ->type('number')
                    ->step(0.1)
                    ->min(0)
                    ->max(1)
                    ->help('Background opacity (0-1)'),

                Input::make('card_border')
                    ->title('Card Border Opacity')
                    ->type('number')
                    ->step(0.1)
                    ->min(0)
                    ->max(1)
                    ->help('Border opacity (0-1)'),
            ])->title('🃏 Card Styling'),

            // Brand Assets
            Layout::rows([
                Input::make('logo_light_file')
                    ->type('file')
                    ->title('Logo (Light Theme)')
                    ->acceptedFiles('image/*')
                    ->help($logoLightHelp),

                Input::make('logo_dark_file')
                    ->type('file')
                    ->title('Logo (Dark Theme)')
                    ->acceptedFiles('image/*')
                    ->help($logoDarkHelp),

                Input::make('favicon_file')
                    ->type('file')
                    ->title('Favicon')
                    ->acceptedFiles('image/*')
                    ->help($faviconHelp),
            ])->title('🏷️ Brand Assets'),

            // Reset Options
            Layout::rows([
                // Empty row to create a titled section
            ])->title('🔄 Reset Options'),

            // Backup Status
            Layout::rows([
                // This will show backup status
            ])->title('💾 Backup Status'),
            Layout::rows([
                // Backup status will be displayed here
            ])->title('Backup Information'),

            Layout::rows([
                Button::make('Reset to Working Style')
                    ->icon('bs.arrow-clockwise')
                    ->method('resetToWorkingStyle')
                    ->confirm('Are you sure you want to restore your last working style? This will overwrite all current changes.')
                    ->class('btn-warning')
                    ->canSee($this->query()['has_backup']),

                Button::make('Reset to Defaults')
                    ->icon('bs.arrow-clockwise')
                    ->method('resetDefaults')
                    ->confirm('Are you sure you want to reset all appearance settings to factory defaults? This cannot be undone.')
                    ->class('btn-danger'),

                Button::make('Create Backup Now')
                    ->icon('bs.download')
                    ->method('createBackup')
                    ->confirm('Create a backup of current working style?')
                    ->class('btn-info'),
            ])->title('Reset & Backup Options'),

            // Live Preview
            Layout::rows([
                // Empty row to create a titled section
            ])->title('👁️ Live Preview'),
            Layout::view('admin.appearance-preview'),
        ];
    }

    public function save(Request $request)
    {
        // Auto-create backup before saving if no backup exists
        if (!Setting::get('theme.backup.working_style')) {
            $this->createBackup();
        }

        $imageService = app(ImageService::class);

        $data = $request->validate([
            // Brand Colors
            'theme_primary'    => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'theme_accent'     => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'theme_success'    => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'theme_warning'    => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'theme_error'      => ['required','regex:/^#[0-9A-F]{6}$/i'],

            // Gradient Settings
            'theme_grad_start' => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'theme_grad_end'   => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'gradient_type'    => ['required','in:linear,radial,conic'],
            'gradient_angle'   => ['required','integer','min:0','max:360'],
            'gradient_custom'  => ['nullable','string'],

            // Light Theme Colors
            'light_bg'         => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'light_surface'    => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'light_text'       => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'light_muted'      => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'light_border'     => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'light_glass'      => ['required','string'],

            // Dark Theme Colors
            'dark_bg'          => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'dark_surface'     => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'dark_text'        => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'dark_muted'       => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'dark_border'      => ['required','regex:/^#[0-9A-F]{6}$/i'],
            'dark_glass'       => ['required','string'],

            // Advanced Settings
            'border_radius'    => ['required','integer','min:0','max:50'],
            'shadow_intensity' => ['required','in:none,light,medium,heavy'],
            'animation_speed'  => ['required','numeric','min:0.1','max:2.0'],

            // Card Styling
            'card_blur'        => ['required','integer','min:0','max:50'],
            'card_opacity'     => ['required','numeric','min:0','max:1'],
            'card_border'      => ['required','numeric','min:0','max:1'],

            // Theme Preset
            'theme_preset'     => ['nullable','string'],

            // File Uploads
            'logo_light_file'  => ['nullable','image','max:2048'], // 2MB
            'logo_dark_file'   => ['nullable','image','max:2048'], // 2MB
            'favicon_file'     => ['nullable','image','max:1024'], // 1MB
        ]);

        // Save brand colors
        Setting::set('theme.primary', $data['theme_primary']);
        Setting::set('theme.accent', $data['theme_accent']);
        Setting::set('theme.success', $data['theme_success']);
        Setting::set('theme.warning', $data['theme_warning']);
        Setting::set('theme.error', $data['theme_error']);

        // Save gradient settings
        Setting::set('theme.grad.start', $data['theme_grad_start']);
        Setting::set('theme.grad.end', $data['theme_grad_end']);
        Setting::set('theme.gradient.type', $data['gradient_type']);
        Setting::set('theme.gradient.angle', $data['gradient_angle']);
        Setting::set('theme.gradient.custom', $data['gradient_custom']);

        // Save light theme colors
        Setting::set('theme.light.bg', $data['light_bg']);
        Setting::set('theme.light.surface', $data['light_surface']);
        Setting::set('theme.light.text', $data['light_text']);
        Setting::set('theme.light.muted', $data['light_muted']);
        Setting::set('theme.light.border', $data['light_border']);
        Setting::set('theme.light.glass', $data['light_glass']);

        // Save dark theme colors
        Setting::set('theme.dark.bg', $data['dark_bg']);
        Setting::set('theme.dark.surface', $data['dark_surface']);
        Setting::set('theme.dark.text', $data['dark_text']);
        Setting::set('theme.dark.muted', $data['dark_muted']);
        Setting::set('theme.dark.border', $data['dark_border']);
        Setting::set('theme.dark.glass', $data['dark_glass']);

        // Save advanced settings
        Setting::set('theme.border.radius', $data['border_radius']);
        Setting::set('theme.shadow.intensity', $data['shadow_intensity']);
        Setting::set('theme.animation.speed', $data['animation_speed']);

        // Save card styling
        Setting::set('theme.card.blur', $data['card_blur']);
        Setting::set('theme.card.opacity', $data['card_opacity']);
        Setting::set('theme.card.border', $data['card_border']);

        // Save theme preset
        if ($data['theme_preset']) {
            Setting::set('theme.preset', $data['theme_preset']);
        }

        // Upload and save logos and favicon
        if ($request->hasFile('logo_light_file')) {
            $file = $request->file('logo_light_file');
            $uploadOptions = $imageService->getUploadOptions('branding');

            $result = $imageService->upload($file, 'branding', $uploadOptions);

            if ($result['success']) {
                $old = Setting::get('site.logo_light');
                if ($old) {
                    $imageService->delete($old);
                }
                Setting::updateOrCreate(['key'=>'site.logo_light'], ['value'=>$result['path']]);
            } else {
                Toast::error('Logo light upload failed: ' . $result['error']);
            }
        }

        if ($request->hasFile('logo_dark_file')) {
            $file = $request->file('logo_dark_file');
            $uploadOptions = $imageService->getUploadOptions('branding');

            $result = $imageService->upload($file, 'branding', $uploadOptions);

            if ($result['success']) {
                $old = Setting::get('site.logo_dark');
                if ($old) {
                    $imageService->delete($old);
                }
                Setting::updateOrCreate(['key'=>'site.logo_dark'], ['value'=>$result['path']]);
            } else {
                Toast::error('Logo dark upload failed: ' . $result['error']);
            }
        }

        if ($request->hasFile('favicon_file')) {
            $file = $request->file('favicon_file');
            $uploadOptions = $imageService->getUploadOptions('branding');

            $result = $imageService->upload($file, 'branding', $uploadOptions);

            if ($result['success']) {
                $old = Setting::get('site.favicon');
                if ($old) {
                    $imageService->delete($old);
                }
                Setting::updateOrCreate(['key'=>'site.favicon'], ['value'=>$result['path']]);
            } else {
                Toast::error('Favicon upload failed: ' . $result['error']);
            }
        }

        // Clear CSS cache (if we implement ETag from values)
        cache()->forget('theme-css-hash');

        Toast::info('Appearance updated.');
        return back();
    }

    public function preview(Request $request)
    {
        // This method can be used to generate a preview URL
        // For now, just redirect back with a preview flag
        Toast::info('Preview mode activated. Check the live preview section below.');
        return back();
    }

    public function createBackup()
    {
        // Create a backup of current working style
        $backup = [
            'timestamp' => now()->toISOString(),
            'description' => 'Backup created on ' . now()->format('M d, Y \a\t H:i'),
            'settings' => [
                // Brand Colors
                'theme.primary' => Setting::get('theme.primary', '#F0C275'),
                'theme.accent' => Setting::get('theme.accent', '#FF6B6B'),
                'theme.success' => Setting::get('theme.success', '#10B981'),
                'theme.warning' => Setting::get('theme.warning', '#F59E0B'),
                'theme.error' => Setting::get('theme.error', '#EF4444'),

                // Gradient Settings
                'theme.grad.start' => Setting::get('theme.grad.start', '#F0C275'),
                'theme.grad.end' => Setting::get('theme.grad.end', '#7877C6'),
                'theme.gradient.type' => Setting::get('theme.gradient.type', 'linear'),
                'theme.gradient.angle' => Setting::get('theme.gradient.angle', '135'),
                'theme.gradient.custom' => Setting::get('theme.gradient.custom', ''),

                // Light Theme Colors
                'theme.light.bg' => Setting::get('theme.light.bg', '#F5F2EC'),
                'theme.light.surface' => Setting::get('theme.light.surface', '#FFFFFF'),
                'theme.light.text' => Setting::get('theme.light.text', '#1E293B'),
                'theme.light.muted' => Setting::get('theme.light.muted', '#64748B'),
                'theme.light.border' => Setting::get('theme.light.border', '#E2E8F0'),
                'theme.light.glass' => Setting::get('theme.light.glass', 'rgba(255,255,255,0.8)'),

                // Dark Theme Colors
                'theme.dark.bg' => Setting::get('theme.dark.bg', '#0F1115'),
                'theme.dark.surface' => Setting::get('theme.dark.surface', '#1E293B'),
                'theme.dark.text' => Setting::get('theme.dark.text', '#F1F5F9'),
                'theme.dark.muted' => Setting::get('theme.dark.muted', '#94A3B8'),
                'theme.dark.border' => Setting::get('theme.dark.border', '#334155'),
                'theme.dark.glass' => Setting::get('theme.dark.glass', 'rgba(255,255,255,0.1)'),

                // Advanced Settings
                'theme.border.radius' => Setting::get('theme.border.radius', '12'),
                'theme.shadow.intensity' => Setting::get('theme.shadow.intensity', 'medium'),
                'theme.animation.speed' => Setting::get('theme.animation.speed', '0.3'),

                // Card Styling
                'theme.card.blur' => Setting::get('theme.card.blur', '20'),
                'theme.card.opacity' => Setting::get('theme.card.opacity', '0.1'),
                'theme.card.border' => Setting::get('theme.card.border', '0.3'),

                // Theme Preset
                'theme.preset' => Setting::get('theme.preset', 'crystal'),
            ]
        ];

        // Store backup in database
        Setting::set('theme.backup.working_style', json_encode($backup));
        Setting::set('theme.backup.last_created', now()->toISOString());

        Toast::info('Backup of current working style created successfully!');
        return back();
    }

    public function showResetOptions()
    {
        // Show a modal or redirect to a reset options page
        // For now, we'll use a simple approach with session messages
        session()->flash('show_reset_options', true);
        Toast::info('Choose your reset option below.');
        return back();
    }

    public function resetToWorkingStyle()
    {
        $backup = Setting::get('theme.backup.working_style');

        if (!$backup) {
            Toast::error('No working style backup found. Please create a backup first.');
            return back();
        }

        $backupData = json_decode($backup, true);

        if (!$backupData || !isset($backupData['settings'])) {
            Toast::error('Backup data is corrupted. Please create a new backup.');
            return back();
        }

        // Restore all settings from backup
        foreach ($backupData['settings'] as $key => $value) {
            Setting::set($key, $value);
        }

        // Clear CSS cache
        cache()->forget('theme-css-hash');

        Toast::info('Successfully restored to your working style from ' . $backupData['description']);
        return back();
    }

    public function resetDefaults()
    {
        // Reset all appearance settings to defaults
        $defaults = [
            // Brand Colors
            'theme.primary' => '#F0C275',
            'theme.accent' => '#FF6B6B',
            'theme.success' => '#10B981',
            'theme.warning' => '#F59E0B',
            'theme.error' => '#EF4444',

            // Gradient Settings
            'theme.grad.start' => '#F0C275',
            'theme.grad.end' => '#7877C6',
            'theme.gradient.type' => 'linear',
            'theme.gradient.angle' => '135',
            'theme.gradient.custom' => '',

            // Light Theme Colors
            'theme.light.bg' => '#F5F2EC',
            'theme.light.surface' => '#FFFFFF',
            'theme.light.text' => '#1E293B',
            'theme.light.muted' => '#64748B',
            'theme.light.border' => '#E2E8F0',
            'theme.light.glass' => 'rgba(255,255,255,0.8)',

            // Dark Theme Colors
            'theme.dark.bg' => '#0F1115',
            'theme.dark.surface' => '#1E293B',
            'theme.dark.text' => '#F1F5F9',
            'theme.dark.muted' => '#94A3B8',
            'theme.dark.border' => '#334155',
            'theme.dark.glass' => 'rgba(255,255,255,0.1)',

            // Advanced Settings
            'theme.border.radius' => '12',
            'theme.shadow.intensity' => 'medium',
            'theme.animation.speed' => '0.3',

            // Card Styling
            'theme.card.blur' => '20',
            'theme.card.opacity' => '0.1',
            'theme.card.border' => '0.3',

            // Theme Preset
            'theme.preset' => 'crystal',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }

        // Clear CSS cache
        cache()->forget('theme-css-hash');

        Toast::info('All appearance settings have been reset to defaults.');
        return back();
    }
}
