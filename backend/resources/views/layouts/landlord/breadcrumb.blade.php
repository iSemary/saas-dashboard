<div class="row">
    <div class="col-6">
        <div>
            <!-- Breadcrumb Navigation -->
            @if (isset($breadcrumbs) && is_array($breadcrumbs))
                <nav aria-label="breadcrumb bg-transparent">
                    <ol class="breadcrumb bg-transparent">
                        @foreach ($breadcrumbs as $breadcrumb)
                            @if (isset($breadcrumb['link']) && $breadcrumb['link'])
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['link'] }}">{{ $breadcrumb['text'] ?? '-' }}</a>
                                </li>
                            @else
                                <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['text'] ?? '-' }}
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endif
        </div>
    </div>
    <div class="col-6">
        <div class="text-revert p-1">
            @if (isset($actionButtons) && is_array($actionButtons))
                @foreach ($actionButtons as $button)
                    @if (!isset($button['permission']) || Gate::check($button['permission']))
                        <button type="button" class="btn m-1 {{ $button['class'] }}"
                            @if (isset($button['redirect']) && $button['redirect']) onclick="window.location.href='{{ $button['redirect'] }}'" @endif
                            @if (isset($button['attr']) && is_array($button['attr'])) @foreach ($button['attr'] as $key => $value) 
                                    {{ $key }}="{{ $value }}" 
                                @endforeach @endif>
                            @if ($language->direction == 'ltr')
                                <span class="btn-text">{{ $button['text'] }}</span>
                                {!! isset($button['icon']) ? '<span class="">' . $button['icon'] . '</span>' : '' !!}
                            @else
                                {!! isset($button['icon']) ? '<span class="">' . $button['icon'] . '</span>' : '' !!}
                                {{ $button['text'] }}
                            @endif
                        </button>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
