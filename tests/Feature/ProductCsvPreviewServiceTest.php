<?php

namespace Tests\Feature;

use App\Services\ProductCsvContract;
use App\Services\ProductCsvPreviewService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductCsvPreviewServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name'); $table->string('name_ar')->nullable(); $table->string('name_en')->nullable();
            $table->string('slug'); $table->string('slug_ar')->nullable(); $table->string('slug_en')->nullable();
            $table->timestamps();
        });
        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->string('name'); $table->string('name_ar')->nullable(); $table->string('name_en')->nullable();
            $table->string('slug'); $table->string('slug_ar')->nullable(); $table->string('slug_en')->nullable();
            $table->timestamps();
        });

        \DB::table('categories')->insert([
            ['name' => 'Men', 'slug' => 'men'], ['name' => 'Shoes', 'slug' => 'shoes'],
        ]);
        \DB::table('brands')->insert(['name' => 'Avenue', 'slug' => 'avenue']);
    }

    public function test_valid_csv_parsing(): void
    {
        $preview = app(ProductCsvPreviewService::class)->preview(
            new UploadedFile(base_path('tests/fixtures/product-import-example.csv'), 'products.csv', 'text/csv', null, true)
        );

        $this->assertSame(['total' => 2, 'valid' => 2, 'warnings' => 0, 'invalid' => 0], $preview['summary']);
        $this->assertSame(3, $preview['rows'][0]['image_count']);
    }

    public function test_utf8_bom_is_accepted(): void
    {
        $preview = $this->preview("\xEF\xBB\xBFname_ar,name_en,category,price\nمنتج,Product,Men,10\n");
        $this->assertSame('READY', $preview['rows'][0]['status']);
    }

    public function test_seo_fields_parse_and_trim(): void
    {
        $preview = $this->preview("name_ar,name_en,category,price,seo_title_ar,seo_title_en,seo_description_ar,seo_description_en\nمنتج,Product,Men,10, عنوان عربي , English title , وصف عربي , English description \n");

        $normalized = $preview['rows'][0]['normalized'];
        $this->assertSame('عنوان عربي', $normalized['seo_title_ar']);
        $this->assertSame('English description', $normalized['seo_description_en']);
    }

    public function test_blank_seo_fields_are_allowed(): void
    {
        $preview = $this->preview("name_ar,name_en,category,price,seo_title_ar,seo_description_en\nمنتج,Product,Men,10,,\n");
        $this->assertSame('READY', $preview['rows'][0]['status']);
    }

    public function test_oversized_seo_is_invalid(): void
    {
        $title = str_repeat('x', ProductCsvPreviewService::SEO_TITLE_MAX + 1);
        $preview = $this->preview("name_ar,name_en,category,price,seo_title_en\nمنتج,Product,Men,10,{$title}\n");
        $this->assertSame('INVALID', $preview['rows'][0]['status']);
    }

    public function test_malformed_price_is_invalid(): void
    {
        $preview = $this->preview("name_ar,name_en,category,price\nمنتج,Product,Men,nope\n");
        $this->assertSame('INVALID', $preview['rows'][0]['status']);
    }

    public function test_invalid_image_url_is_invalid(): void
    {
        $preview = $this->preview("name_ar,name_en,category,price,primary_image_url\nمنتج,Product,Men,10,file:///tmp/image.jpg\n");
        $this->assertSame('INVALID', $preview['rows'][0]['status']);
    }

    public function test_duplicate_slug_inside_csv_is_warning(): void
    {
        $preview = $this->preview("name_ar,name_en,slug_en,category,price\nأول,First,same,Men,10\nثان,Second,same,Men,20\n");
        $this->assertSame('WARNING', $preview['rows'][1]['status']);
    }

    public function test_max_row_limit_is_enforced(): void
    {
        $rows = array_fill(0, ProductCsvPreviewService::MAX_ROWS + 1, "منتج,Product,Men,10");
        $this->expectException(ValidationException::class);
        $this->preview("name_ar,name_en,category,price\n".implode("\n", $rows));
    }

    public function test_missing_required_column_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->preview("name_ar,name_en,category\nمنتج,Product,Men\n");
    }

    public function test_unsupported_column_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->preview("name_ar,name_en,category,price,not_a_real_column\nمنتج,Product,Men,10,x\n");
    }

    public function test_spreadsheet_formula_prefixed_value_is_invalid(): void
    {
        $preview = $this->preview("name_ar,name_en,category,price\n=cmd|'/c calc'!A1,Product,Men,10\n");
        $this->assertSame('INVALID', $preview['rows'][0]['status']);
        $this->assertStringContainsString('spreadsheet formula', implode(' ', $preview['rows'][0]['messages']));
    }

    public function test_generated_template_is_utf8_and_cannot_drift_from_parser_contract(): void
    {
        $csv = ProductCsvContract::template();
        $this->assertTrue(mb_check_encoding($csv, 'UTF-8'));
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);

        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $csv);
        rewind($stream);
        $headers = fgetcsv($stream, escape: '');
        fclose($stream);
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $this->assertSame(ProductCsvContract::HEADERS, $headers);
        $defined = array_merge(ProductCsvContract::REQUIRED_HEADERS, ProductCsvContract::OPTIONAL_HEADERS);
        sort($defined);
        $supported = ProductCsvContract::HEADERS;
        sort($supported);
        $this->assertSame($supported, $defined);
        $this->assertSame('|', ProductCsvContract::LIST_SEPARATOR);

        \DB::table('categories')->insert(['name' => 'Template Category', 'slug' => 'replace-with-existing-category']);
        $preview = $this->preview($csv);
        $this->assertSame('READY', $preview['rows'][0]['status']);
        $this->assertSame(['S', 'M', 'L'], $preview['rows'][0]['normalized']['sizes']);
    }

    private function preview(string $csv): array
    {
        $path = tempnam(sys_get_temp_dir(), 'avenue-csv-');
        file_put_contents($path, $csv);

        try {
            return app(ProductCsvPreviewService::class)->preview(new UploadedFile($path, 'products.csv', 'text/csv', null, true));
        } finally {
            @unlink($path);
        }
    }
}
