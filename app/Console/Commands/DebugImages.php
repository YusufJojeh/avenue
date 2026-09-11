<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Slide;
use App\Models\Offer;

class DebugImages extends Command
{
    protected $signature = 'debug:images';
    protected $description = 'Verify that stored image files exist and are public';

    public function handle()
    {
        $disks = Storage::disk('public');

        $check = function ($label, $list, $getter) use ($disks) {
            $missing = 0;
            foreach ($list as $m) {
                $p = $getter($m);
                if ($p && !$disks->exists($p)) {
                    $this->warn("$label #{$m->id} missing: $p");
                    $missing++;
                }
            }
            $this->info("$label checked: ".count($list).", missing: $missing");
        };

        $check('ProductImage', ProductImage::all(), fn($m)=>$m->path);
        $check('Category', Category::all(), fn($m)=>$m->image_path);
        $check('Brand', Brand::all(), fn($m)=>$m->logo_path);
        $check('Slide', Slide::all(), fn($m)=>$m->image_path);
        $check('Offer', Offer::all(), fn($m)=>$m->banner_image);

        return 0;
    }
}
