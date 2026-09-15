<?php

namespace App\Orchid\Screens;

use App\Services\ProductCsvPreviewService;
use App\Services\ProductCsvImportService;
use App\Services\ProductCsvContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class ProductImportScreen extends Screen
{
    public function name(): ?string { return 'Product Imports'; }
    public function description(): ?string { return 'Upload and validate a CSV without changing products.'; }

    public function permission(): ?iterable
    {
        return ['platform.index'];
    }

    public function query(): array
    {
        $preview = session()->pull('product_import_preview', ['summary' => null, 'rows' => []]);
        $preview['rows'] = collect($preview['rows'])->map(fn ($row) => new Repository($row));
        $result = session()->pull('product_import_result', ['summary' => null, 'rows' => []]);
        $preview['result'] = $result['summary'];
        $preview['result_rows'] = collect($result['rows'])->map(fn ($row) => new Repository($row));

        return $preview;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('csv')->type('file')->accept('.csv,text/csv')->required()->title('CSV file'),
                Button::make('Preview')->icon('bs.eye')->method('preview'),
                Button::make('Download CSV Template')->icon('bs.download')->method('downloadTemplate')->download(),
                Button::make('Download ChatGPT Prompt')->icon('bs.download')->method('downloadChatGptPrompt')->download(),
                Button::make('Confirm Import')->icon('bs.check-circle')->method('confirmImport')
                    ->confirm('Create all eligible products? Existing products will be skipped.')
                    ->canSee((bool) session('product_import_can_confirm')),
            ]),
            Layout::view('platform.product-import-summary'),
            Layout::table('rows', [
                TD::make('row', 'Row'), TD::make('name', 'Name'), TD::make('category', 'Category'),
                TD::make('brand', 'Brand'), TD::make('price', 'Price'), TD::make('image_count', 'Images'),
                TD::make('status', 'Status'),
                TD::make('messages', 'Messages')->render(fn ($row) => implode(' ', $row['messages'])),
            ]),
            Layout::table('result_rows', [
                TD::make('row', 'Row'), TD::make('name', 'Name'), TD::make('result', 'Result'), TD::make('reason', 'Reason'),
            ]),
        ];
    }

    public function preview(Request $request, ProductCsvPreviewService $parser)
    {
        $request->validate([
            'csv' => ['required', 'file', 'extensions:csv', 'mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel', 'max:2048'],
        ]);

        $preview = $parser->preview($request->file('csv'));
        $oldToken = session('product_import_token');
        if (is_string($oldToken)) Storage::disk('local')->delete($this->temporaryPath($request, $oldToken));

        $token = (string) Str::uuid();
        $path = $this->temporaryPath($request, $token);
        abort_unless(Storage::disk('local')->put($path, file_get_contents($request->file('csv')->getRealPath())), 500);

        session([
            'product_import_token' => $token,
            'product_import_can_confirm' => $preview['summary']['invalid'] === 0,
        ]);

        return redirect()->route('platform.products.import')->with('product_import_preview', $preview);
    }

    public function confirmImport(Request $request, ProductCsvPreviewService $parser, ProductCsvImportService $importer)
    {
        $token = session('product_import_token');
        abort_unless(is_string($token), 422, 'No CSV preview is available.');

        $path = $this->temporaryPath($request, $token);
        abort_unless(Storage::disk('local')->exists($path), 422, 'The temporary CSV has expired.');

        $result = $importer->import($parser->preview(Storage::disk('local')->path($path)));

        Storage::disk('local')->delete($path);
        session()->forget(['product_import_token', 'product_import_can_confirm']);

        return redirect()->route('platform.products.import')->with('product_import_result', $result);
    }

    public function downloadTemplate(ProductCsvContract $contract)
    {
        return response($contract->template(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="avenue-product-import-template.csv"',
        ]);
    }

    public function downloadChatGptPrompt(ProductCsvContract $contract)
    {
        return response($contract->chatGptPrompt(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="avenue-product-links-to-csv-prompt.txt"',
        ]);
    }

    private function temporaryPath(Request $request, string $token): string
    {
        abort_unless(Str::isUuid($token), 422, 'Invalid import token.');
        return 'product-imports/'.$request->user()->getAuthIdentifier().'/'.$token.'.csv';
    }
}
