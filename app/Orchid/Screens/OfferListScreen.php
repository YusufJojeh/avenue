<?php

namespace App\Orchid\Screens;

use App\Models\Offer;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

class OfferListScreen extends Screen
{
    public function name(): ?string { return 'Offers'; }
    public function description(): ?string { return 'Browse and manage offers'; }

    public function query(): array
    {
        return [
            'offers' => Offer::withCount('products')->orderByDesc('starts_at')->paginate(20),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Create')->icon('bs.plus')->route('platform.offers.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('offers', [
                TD::make('title')->sort()->filter(TD::FILTER_TEXT),
                TD::make('type')->sort(),
                TD::make('value')->render(fn(Offer $o) => $o->type === 'free_shipping' ? '—' : ($o->type === 'percent' ? ($o->value.' %') : number_format($o->value,2))),
                TD::make('window','Window')->render(fn(Offer $o) => ($o->starts_at? $o->starts_at->format('Y-m-d') : '—').' → '.($o->ends_at? $o->ends_at->format('Y-m-d') : '—')),
                TD::make('is_active','Active')->render(fn(Offer $o) => $o->is_active ? 'Yes' : 'No')->align(TD::ALIGN_CENTER)->sort(),
                TD::make('products_count','Products')->align(TD::ALIGN_CENTER),
                TD::make(__('Actions'))->align(TD::ALIGN_RIGHT)->render(function (Offer $o) {
                    return
                        Link::make('Edit')->icon('bs.pencil')->route('platform.offers.edit', $o).' '.
                        Button::make('Delete')
                            ->icon('bs.trash')
                            ->confirm('Delete this offer?')
                            ->method('remove', ['id' => $o->id]);
                }),
            ]),
        ];
    }

    public function remove(Request $request)
    {
        $id = $request->get('id');
        $offer = $id ? Offer::find($id) : null;

        if (! $offer) {
            Toast::warning('Offer not found.');
            return back();
        }

        try {
            $offer->products()->detach();
            $offer->delete();
            Toast::info('Deleted.');
        } catch (\Throwable $e) {
            Toast::error('Cannot delete: '.$e->getMessage());
        }

        return back();
    }
}
