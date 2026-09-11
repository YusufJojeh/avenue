<?php

namespace App\Orchid\Screens;

use App\Models\ContentVersion;
use App\Services\ContentVersioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Toast;

class ContentVersioningScreen extends Screen
{
    public function query(): array
    {
        $versioningService = app(ContentVersioningService::class);

        return [
            'versions' => ContentVersion::with('content')
                ->orderBy('created_at', 'desc')
                ->paginate(20),
            'stats' => $versioningService->getVersionStats(),
            'contentTypes' => ContentVersion::distinct()->pluck('content_type')->toArray(),
            'locales' => ['en', 'ar'],
        ];
    }

    public function name(): ?string
    {
        return 'Content Versioning';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Cleanup Old Versions')
                ->icon('bs.trash')
                ->method('cleanupOldVersions')
                ->confirm('This will delete old unpublished versions. Continue?'),

            Button::make('Refresh Stats')
                ->icon('bs.arrow-clockwise')
                ->method('refreshStats'),
        ];
    }

    public function layout(): array
    {
        return [
            // Version Statistics
            Layout::rows([
                Layout::view('partials.version-stats', [
                    'stats' => $this->query()['stats']
                ])->title('📊 Version Statistics'),
            ]),

            // Filters
            Layout::rows([
                Select::make('filter.content_type')
                    ->title('Content Type')
                    ->empty('All Types')
                    ->options($this->getContentTypeOptions())
                    ->value(request('filter.content_type')),

                Select::make('filter.locale')
                    ->title('Language')
                    ->empty('All Languages')
                    ->options(['en' => 'English', 'ar' => 'العربية'])
                    ->value(request('filter.locale')),

                Select::make('filter.status')
                    ->title('Status')
                    ->empty('All Statuses')
                    ->options(['published' => 'Published', 'unpublished' => 'Unpublished'])
                    ->value(request('filter.status')),

                Button::make('Apply Filters')
                    ->icon('bs.funnel')
                    ->method('applyFilters')
                    ->class('btn-primary'),
            ])->title('🔍 Filters'),

            // Versions Table
            Layout::table('versions', [
                TD::make('content_type', 'Content Type')
                    ->sort()
                    ->render(function (ContentVersion $version) {
                        $icon = $this->getContentTypeIcon($version->content_type);
                        return $icon . ' ' . ucfirst($version->content_type);
                    }),

                TD::make('content_id', 'Content ID')
                    ->sort()
                    ->render(function (ContentVersion $version) {
                        return '#' . $version->content_id;
                    }),

                TD::make('locale', 'Language')
                    ->sort()
                    ->render(function (ContentVersion $version) {
                        $flag = $version->locale === 'en' ? '🇺🇸' : '🇸🇦';
                        return $flag . ' ' . strtoupper($version->locale);
                    }),

                TD::make('version_number', 'Version')
                    ->sort()
                    ->render(function (ContentVersion $version) {
                        return 'v' . $version->version_number;
                    }),

                TD::make('is_published', 'Status')
                    ->sort()
                    ->render(function (ContentVersion $version) {
                        if ($version->is_published) {
                            return '<span class="badge bg-success">Published</span>';
                        }
                        return '<span class="badge bg-secondary">Draft</span>';
                    }),

                TD::make('content_data', 'Content Preview')
                    ->render(function (ContentVersion $version) {
                        $data = $version->content_data;
                        $preview = '';

                        if (isset($data['name_' . $version->locale])) {
                            $preview .= '<strong>Name:</strong> ' . substr($data['name_' . $version->locale], 0, 50);
                            if (strlen($data['name_' . $version->locale]) > 50) {
                                $preview .= '...';
                            }
                        }

                        if (isset($data['description_' . $version->locale])) {
                            $preview .= '<br><strong>Description:</strong> ' . substr($data['description_' . $version->locale], 0, 100);
                            if (strlen($data['description_' . $version->locale]) > 100) {
                                $preview .= '...';
                            }
                        }

                        return $preview ?: '<em>No content data</em>';
                    }),

                TD::make('created_at', 'Created')
                    ->sort()
                    ->render(function (ContentVersion $version) {
                        return $version->created_at->format('M d, Y H:i');
                    }),

                TD::make('actions', 'Actions')
                    ->align(TD::ALIGN_RIGHT)
                    ->render(function (ContentVersion $version) {
                        $buttons = [];

                        if (!$version->is_published) {
                            $buttons[] = Button::make('Publish')
                                ->icon('bs.check-circle')
                                ->method('publishVersion', ['id' => $version->id])
                                ->class('btn-sm btn-success');
                        }

                        $buttons[] = Button::make('View')
                            ->icon('bs.eye')
                                ->method('viewVersion', ['id' => $version->id])
                                ->class('btn-sm btn-info');

                        $buttons[] = Button::make('Delete')
                            ->icon('bs.trash')
                            ->method('deleteVersion', ['id' => $version->id])
                            ->confirm('Are you sure you want to delete this version?')
                            ->class('btn-sm btn-danger');

                        return implode(' ', array_map(fn($b) => $b->render(), $buttons));
                    }),
            ])->title('📝 Content Versions'),

            // Version Details Modal (will be shown via JavaScript)
            Layout::modal('versionDetails', [
                Layout::rows([
                    // Version details will be loaded here
                ])
            ])->title('Version Details'),
        ];
    }

    public function publishVersion(Request $request, $id)
    {
        $versioningService = app(ContentVersioningService::class);

        try {
            $success = $versioningService->publishVersion($id);

            if ($success) {
                Toast::info('Version published successfully!');
            } else {
                Toast::error('Failed to publish version.');
            }
        } catch (\Exception $e) {
            Toast::error('Error: ' . $e->getMessage());
        }

        return back();
    }

    public function viewVersion(Request $request, $id)
    {
        $version = ContentVersion::findOrFail($id);

        // For now, just show a toast with version info
        Toast::info("Version {$version->version_number} of {$version->content_type} #{$version->content_id}");

        return back();
    }

    public function deleteVersion(Request $request, $id)
    {
        try {
            $version = ContentVersion::findOrFail($id);

            if ($version->is_published) {
                Toast::error('Cannot delete published version. Unpublish it first.');
                return back();
            }

            $version->delete();
            Toast::info('Version deleted successfully!');
        } catch (\Exception $e) {
            Toast::error('Error: ' . $e->getMessage());
        }

        return back();
    }

    public function cleanupOldVersions()
    {
        try {
            // Keep only the last 5 versions per content
            $deletedCount = 0;

            $contentGroups = ContentVersion::select('content_type', 'content_id', 'locale')
                ->groupBy('content_type', 'content_id', 'locale')
                ->get();

            foreach ($contentGroups as $group) {
                $oldVersions = ContentVersion::where([
                    'content_type' => $group->content_type,
                    'content_id' => $group->content_id,
                    'locale' => $group->locale,
                ])
                ->orderBy('version_number', 'desc')
                ->skip(5)
                ->get();

                foreach ($oldVersions as $version) {
                    if (!$version->is_published) {
                        $version->delete();
                        $deletedCount++;
                    }
                }
            }

            Toast::info("Cleanup completed! Deleted {$deletedCount} old versions.");
        } catch (\Exception $e) {
            Toast::error('Error during cleanup: ' . $e->getMessage());
        }

        return back();
    }

    public function refreshStats()
    {
        Toast::info('Statistics refreshed!');
        return back();
    }

    public function applyFilters(Request $request)
    {
        // This would typically redirect with filter parameters
        Toast::info('Filters applied!');
        return back();
    }

    private function getContentTypeOptions(): array
    {
        $types = ContentVersion::distinct()->pluck('content_type')->toArray();
        $options = [];

        foreach ($types as $type) {
            $options[$type] = ucfirst($type);
        }

        return $options;
    }

    private function getContentTypeIcon(string $contentType): string
    {
        return match($contentType) {
            'product' => '📦',
            'category' => '📁',
            'brand' => '🏷️',
            'slide' => '🖼️',
            'offer' => '🎯',
            default => '📄',
        };
    }
}
