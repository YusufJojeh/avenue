<?php

namespace App\Orchid\Screens;

use App\Models\Slide;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

class SlideListScreen extends Screen
{
    public function name(): ?string { return 'Slides'; }
    public function description(): ?string { return 'Hero & slider slides'; }

    public function query(): array
    {
        return [
            'slides' => Slide::orderBy('position')->orderBy('sort_order')->paginate(20),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Create')->icon('bs.plus')->route('platform.slides.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('slides', [
                TD::make('position')->sort(),
                TD::make('title')->sort()->filter(TD::FILTER_TEXT),
                TD::make('window','Window')->render(fn(Slide $s) =>
                    ($s->starts_at? $s->starts_at->format('Y-m-d') : '—').' → '.($s->ends_at? $s->ends_at->format('Y-m-d') : '—')
                ),
                TD::make('sort_order','Order')->sort(),
                TD::make('is_active','Active')->render(fn(Slide $s) => $s->is_active ? 'Yes' : 'No')->align(TD::ALIGN_CENTER)->sort(),
                TD::make(__('Actions'))->align(TD::ALIGN_RIGHT)->render(function (Slide $s) {
                    return
                        Link::make('Edit')->icon('bs.pencil')->route('platform.slides.edit', $s).' '.
                        Button::make('Delete')->icon('bs.trash')
                            ->confirm('Delete this slide?')
                            ->method('remove', ['id' => $s->id]);
                }),
            ]),
        ];
    }

    public function remove(Request $request)
    {
        $id = $request->get('id');
        $s  = $id ? Slide::find($id) : null;
        if (! $s) {
            Toast::warning('Slide not found.');
            return back();
        }
        $s->delete();
        Toast::info('Deleted.');
        return back();
    }
}
