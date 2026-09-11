<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SearchRegressionTest extends TestCase
{
    public function test_search_view_contract_and_accessor_columns(): void
    {
        $controller = file_get_contents(__DIR__.'/../../app/Http/Controllers/HomeController.php');
        $service = file_get_contents(__DIR__.'/../../app/Services/EnhancedPerformanceService.php');

        $this->assertStringContainsString("'q' => \$query", $controller);
        $this->assertStringContainsString("select(['id', 'name', 'slug', 'image_path'])", $service);
        $this->assertStringContainsString("select(['id', 'name', 'slug', 'logo_path'])", $service);
        $this->assertStringNotContainsString("select(['id', 'name', 'slug', 'image_url'])", $service);
        $this->assertStringNotContainsString("select(['id', 'name', 'slug', 'logo_url'])", $service);
    }
}
