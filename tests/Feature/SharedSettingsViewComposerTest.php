<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regression coverage for a performance bug: ViewServiceProvider registers a
 * View::composer('*', ...) that shares site settings with every view. Blade
 * composers fire on every individual view/partial/component render, not once
 * per HTTP response, so an unmemoized query there turns into hundreds of
 * identical `settings` table scans on pages that render many partials (e.g.
 * an Orchid admin table with dozens of rows). The fix memoizes the query per
 * request; this test fails if that memoization regresses.
 */
class SharedSettingsViewComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_settings_query_runs_once_per_request_even_across_many_view_renders(): void
    {
        DB::enableQueryLog();

        // Render several independent view instances, simulating a page that
        // includes many partials/components in a single response.
        for ($i = 0; $i < 10; $i++) {
            view('welcome')->render();
        }

        $settingsQueries = collect(DB::getQueryLog())
            ->filter(fn (array $q) => preg_match('/from ["`]settings["`]/', $q['query']) === 1)
            ->count();

        $this->assertLessThanOrEqual(
            1,
            $settingsQueries,
            'Expected the shared settings lookup in ViewServiceProvider to be memoized per request, but it ran '.$settingsQueries.' times.'
        );
    }
}
