@element([
    'componentElement' => 'template',
    'attributeList' => [
        'data-js-search-hit-template-municipal-event' => true
    ]
])
    @card([
        'heading' => '{SEARCH_HIT_ADMINISTRATION}',
        'link' => '{SEARCH_HIT_LINK}',
        'classList' => ['u-height--100', 'c-municipal-event-card'],
        'context' => ['archive', 'archive.list', 'archive.list.card'],
        'containerAware' => true,
        'attributeList' => ['aria-label' => '{SEARCH_HIT_ARIA_LABEL}']
    ])
        @slot('content')
            <ul class="c-municipal-event-card__meta unlist">
                <li class="c-municipal-event-card__meta-item">
                    <wa-icon name="calendar"></wa-icon>
                    <span>{SEARCH_HIT_TIME_RANGE}</span>
                </li>
                <li class="c-municipal-event-card__meta-item">
                    <wa-icon name="{SEARCH_HIT_PLACE_ICON}"></wa-icon>
                    <span>{SEARCH_HIT_PLACE}</span>
                </li>
                <li class="c-municipal-event-card__meta-item">
                    <wa-icon name="{SEARCH_HIT_TYPE_ICON}"></wa-icon>
                    <span>{SEARCH_HIT_TYPE}</span>
                </li>
            </ul>
        @endslot
    @endcard
@endelement
