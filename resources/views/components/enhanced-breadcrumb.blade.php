{{-- Enhanced Breadcrumb Component --}}
@props(['items' => []])

<nav class="breadcrumb-enhanced" aria-label="Breadcrumb">
  <ol class="breadcrumb-list">
    @foreach($items as $index => $item)
      <li>
        @if($index === count($items) - 1)
          <span class="active">{{ $item['label'] }}</span>
        @else
          <a href="{{ $item['url'] }}">
            @if(isset($item['icon']))
              <i class="{{ $item['icon'] }}"></i>
            @endif
            {{ $item['label'] }}
          </a>
        @endif
      </li>
    @endforeach
  </ol>
</nav>
