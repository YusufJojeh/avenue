<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Clean up old Category records with old paths
        Category::where('image_path', 'like', 'categories/%')->update(['image_path' => null]);

        DB::transaction(function () {
            $now = now();
            $usedSlugs = [];

            // مصفوفة التصنيفات مع الأبناء
            $data = [
                [
                    'name' => 'Electronics',
                    'description' => 'Phones, computers, and accessories.',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Phones & Tablets'],
                        ['name' => 'Computers & Laptops'],
                        ['name' => 'Gaming'],
                        ['name' => 'Audio & Headphones'],
                        ['name' => 'Cameras & Photography'],
                        ['name' => 'Accessories'],
                    ],
                ],
                [
                    'name' => 'Fashion',
                    'description' => 'Clothing and accessories.',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Men'],
                        ['name' => 'Women'],
                        ['name' => 'Kids'],
                        ['name' => 'Shoes'],
                        ['name' => 'Bags & Accessories'],
                    ],
                ],
                [
                    'name' => 'Home & Kitchen',
                    'description' => 'Furniture, tools & appliances.',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Furniture'],
                        ['name' => 'Appliances'],
                        ['name' => 'Cookware'],
                        ['name' => 'Home Decor'],
                        ['name' => 'Tools & DIY'],
                    ],
                ],
                [
                    'name' => 'Beauty & Health',
                    'description' => 'Skincare, makeup, and healthcare.',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Skincare'],
                        ['name' => 'Makeup'],
                        ['name' => 'Hair Care'],
                        ['name' => 'Personal Care'],
                        ['name' => 'Supplements'],
                    ],
                ],
                [
                    'name' => 'Sports & Outdoors',
                    'description' => 'Equipment and outdoor gear.',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Fitness'],
                        ['name' => 'Cycling'],
                        ['name' => 'Camping & Hiking'],
                        ['name' => 'Team Sports'],
                    ],
                ],
                [
                    'name' => 'Books & Stationery',
                    'description' => 'Books, office & school supplies.',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Books'],
                        ['name' => 'Notebooks'],
                        ['name' => 'Office Supplies'],
                        ['name' => 'Art & Crafts'],
                    ],
                ],
                [
                    'name' => 'Toys & Games',
                    'description' => 'Toys for all ages and board games.',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Action Figures'],
                        ['name' => 'Puzzles'],
                        ['name' => 'Board Games'],
                        ['name' => 'STEM & Learning'],
                    ],
                ],
            ];

            $order = 0;

            foreach ($data as $parent) {
                $parentModel = Category::create([
                    'parent_id'   => null,
                    'name'        => $parent['name'],
                    'slug'        => $this->uniqueSlug($parent['name'], $usedSlugs),
                    'description' => $parent['description'] ?? null,
                    'image_path'  => $this->createDemoImage('categories', $parent['name']),
                    'is_active'   => $parent['is_active'] ?? true,
                    'sort_order'  => $order++,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                foreach ($parent['children'] as $i => $child) {
                    Category::create([
                        'parent_id'   => $parentModel->id,
                        'name'        => $child['name'],
                        'slug'        => $this->uniqueSlug($child['name'], $usedSlugs),
                        'description' => $child['description'] ?? null,
                        'image_path'  => $this->createDemoImage('categories', $child['name']),
                        'is_active'   => $child['is_active'] ?? true,
                        'sort_order'  => $i,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }
            }
        });
    }

    /**
     * توليد slug فريد على مستوى الجدول أثناء السيّد.
     */
    private function uniqueSlug(string $name, array &$used): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (in_array($slug, $used, true) || Category::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $used[] = $slug;
        return $slug;
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
