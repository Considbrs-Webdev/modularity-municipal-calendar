@element([
    'componentElement' => 'template',
    'attributeList' => [
        'data-js-search-hit-template-municipal-event' => true
    ]
])
    <div class="c-card c-card--size-md c-card--action c-municipal-event-card ts-search-hit-card">
        <div class="c-card__paint-container">
            <div class="c-card__body">
                <div class="c-group c-group--vertical c-group--gap-1">
                    {{-- Metadata row: time range, separator dot, place --}}
                    <div class="c-group c-group--horizontal c-group--align-items-center c-group--gap-1">
                        <span class="c-badge c-badge--primary">{SEARCH_HIT_SUBHEADING}</span>
                        <span class="c-typography">
                            {SEARCH_HIT_TIME_RANGE}
                        </span>
                    </div>
                    <h2 class="c-typography c-card__heading u-margin__y--0 c-typography__variant--h3">
                        <a class="ts-search-hit-card__link" href="{SEARCH_HIT_LINK}">{SEARCH_HIT_ADMINISTRATION}</a>
                    </h2>
                    <span class="c-typography c-typography__variant--meta u-margin__y--0" data-js-hide-if-empty>
                        <i class="{SEARCH_HIT_PLACE_ICON}" aria-hidden="true"></i>
                        {SEARCH_HIT_PLACE}
                    </span>
                    <span class="c-typography c-typography__variant--meta u-margin__y--0" data-js-hide-if-empty>
                        <i class="{SEARCH_HIT_TYPE_ICON}" aria-hidden="true"></i>
                        {SEARCH_HIT_TYPE}
                    </span>
                    <p class="c-typography c-card__content c-typography__variant--p u-margin__y--0">
                        {SEARCH_HIT_EXCERPT}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endelement
