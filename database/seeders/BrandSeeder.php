<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();

            $rows = [
                ['name' => 'MyStore',  'is_external' => false, 'logo' => 'logos/sample.png'],
                ['name' => 'Apple',    'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'Samsung',  'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'Sony',     'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'Lenovo',   'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'Dell',     'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'HP',       'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'Nike',     'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'Adidas',   'is_external' => true,  'logo' => 'logos/sample.png'],
                ['name' => 'Zara',     'is_external' => true,  'logo' => 'logos/sample.png'],
            ];

            $order = 0;
            foreach ($rows as $r) {
                Brand::updateOrCreate(
                    ['slug' => Str::slug($r['name'])],
                    [
                        'name'       => $r['name'],
                        'is_external'=> $r['is_external'],
                        'is_active'  => true,
                        'logo_path'  => $this->createDemoImage('brands', $r['name']),
                        'sort_order' => $order++,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        });
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
