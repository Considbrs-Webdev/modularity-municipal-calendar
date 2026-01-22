@card([
    'link' => $post->getPermalink(),
    'heading' => $post->municipalEventData->administration->name,
    'classList' => ['u-height--100', 'c-municipal-event-card'],
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'attributeList' => [
        'aria-label' => $post->municipalEventData->ariaLabel ?? ''
    ]
])
    @slot('content')
        <ul class="c-municipal-event-card__meta unlist">
            {{-- Date/Time Range --}}
            @if (!empty($post->municipalEventData->timeRange))
                <li class="c-municipal-event-card__meta-item">
                    @icon(['icon' => 'fa-solid fa-calendar-days'])
                    @endicon
                    <span>{{ $post->municipalEventData->timeRange }}</span>
                </li>
            @endif

            {{-- Place --}}
            @if (!empty($post->municipalEventData->place))
                <li class="c-municipal-event-card__meta-item">
                    @if ($post->municipalEventData->placeIcon)
                        @icon(['icon' => $post->municipalEventData->placeIcon])
                        @endicon
                    @endif
                    <span>{{ $post->municipalEventData->place->name }}</span>
                </li>
            @endif

            {{-- Type of event/meeting --}}
            @if (!empty($post->municipalEventData->type))
                <li class="c-municipal-event-card__meta-item">
                    @if ($post->municipalEventData->typeIcon)
                        @icon(['icon' => $post->municipalEventData->typeIcon])
                        @endicon
                    @endif
                    <span>{{ $post->municipalEventData->type->name }}</span>
                </li>
            @endif
        </ul>
    @endslot
@endcard
