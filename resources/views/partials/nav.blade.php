@php $items = \App\Support\Navigation::for(auth()->user()); @endphp

<nav class="app-nav mb-4" aria-label="Main navigation">
    <ul class="app-nav-list">
        @foreach ($items as $item)
            @if (! empty($item['separator']))
                <li class="app-nav-sep" aria-hidden="true"></li>
            @else
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="app-nav-link text-decoration-none {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        <i class="fa-solid {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</nav>