<?php

namespace Database\Seeders;

use App\Models\Slide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SlideSeeder extends Seeder
{
    public function run(): void
    {
        Slide::updateOrCreate(
            ['position' => 'main', 'title' => 'Big Summer Sale'],
            [
                'subtitle'   => 'Up to 50% off',
                'image_path' => $this->createDemoImage('slides', 'Big Summer Sale'),
                'cta_label'  => 'Shop Now',
                'cta_url'    => '/products',
                'sort_order' => 0,
                'starts_at'  => now()->subDays(2),
                'ends_at'    => now()->addDays(30),
                'is_active'  => true,
            ]
        );

        $titles = ['New Arrivals', 'Hot Deals', 'Top Rated'];
        foreach ($titles as $i => $title) {
            Slide::updateOrCreate(
                ['position' => 'slider', 'title' => $title],
                [
                    'subtitle'   => null,
                    'image_path' => $this->createDemoImage('slides', $title),
                    'cta_label'  => 'Browse',
                    'cta_url'    => '/products',
                    'sort_order' => $i,
                    'starts_at'  => now()->subDays(2),
                    'ends_at'    => now()->addDays(60),
                    'is_active'  => true,
                ]
            );
        }
    }

    /**
     * Create a demo image file in the public disk
     */
    private function createDemoImage(string $folder, string $name): string
    {
        $filename = Str::random(16) . '.jpg';
        $path = $folder . '/' . $filename;
        
        // Create a simple demo image content (this is just a placeholder)
        $imageContent = "Demo image for: " . $name;
        
        // Store the file in the public disk
        Storage::disk('public')->put($path, $imageContent, 'public');
        
        return $path;
    }
}
