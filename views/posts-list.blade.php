@element([ 'classList' => $getParentColumnClasses() ])
    @if($filterConfig->isEnabled())
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @include('parts.filters')
        @endelement
    @endif
    @if(empty($posts))
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @notice([
                'type' => 'info',
                'message' => [
                    'text' => $lang->noResult,
                    'size' => 'md'
                ]
            ])
            @endnotice
        @endelement
    @else
        @if($appearanceConfig->getDesign() === \Municipio\PostsList\Config\AppearanceConfig\PostDesign::TABLE)
            @element(['classList' => ['o-layout-grid--col-span-12']])
                @include('parts.table')
            @endelement
        @else
            @foreach($posts as $post)
                @element(['classList' => $getPostColumnClasses()])
                    @if($post->getPostType() === 'municipal_event')
                        {{-- Custom Municipal Event Card --}}
                        @card([
                            'link' => $post->getPermalink(),
                            'heading' => $post->getTitle(),
                            'classList' => ['u-height--100', 'c-municipal-event-card'],
                            'context' => ['archive', 'archive.list', 'archive.list.card'],
                            'containerAware' => true,
                        ])
                            @slot('content')
                                <ul class="c-municipal-event-card__meta unlist">
                                    {{-- Date/Time --}}
                                    @php
                                        $startDate = get_field('start_date', $post->getId());
                                    @endphp
                                    @if($startDate)
                                        <li class="c-municipal-event-card__meta-item">
                                            @icon(['icon' => 'fa-solid fa-calendar-days'])@endicon
                                            <span>{{ date_i18n('j M Y, H:i', strtotime($startDate)) }}</span>
                                        </li>
                                    @endif
                                    
                                    {{-- Place --}}
                                    @php
                                        $places = get_the_terms($post->getId(), 'event_place');
                                        $place = $places && !is_wp_error($places) ? $places[0] : null;
                                        $placeIcon = isset($getEventPlaceIcon) ? $getEventPlaceIcon($post) : null;
                                    @endphp
                                    @if($place)
                                        <li class="c-municipal-event-card__meta-item">
                                            @if($placeIcon)
                                                @icon(['icon' => $placeIcon])@endicon
                                            @endif
                                            <span>{{ $place->name }}</span>
                                        </li>
                                    @endif
                                    
                                    {{-- Type of event/meeting --}}
                                    @php
                                        $types = get_the_terms($post->getId(), 'event_type');
                                        $type = $types && !is_wp_error($types) ? $types[0] : null;
                                        $typeIcon = isset($getEventTypeIcon) ? $getEventTypeIcon($post) : null;
                                    @endphp
                                    @if($type)
                                        <li class="c-municipal-event-card__meta-item">
                                            @if($typeIcon)
                                                @icon(['icon' => $typeIcon])@endicon
                                            @endif
                                            <span>{{ $type->name }}</span>
                                        </li>
                                    @endif
                                </ul>
                            @endslot
                        @endcard
                    @else
                        {{-- Original behavior for other post types --}}
                        @includeFirst(['post.' . $appearanceConfig->getDesign()->value, 'post.card'])
                    @endif
                @endelement
            @endforeach
        @endif
        @if(!empty($getPaginationComponentArguments()))
            @element(['classList' => ['o-layout-grid--col-span-12']])
                @include('parts.pagination')
            @endelement
        @endif
    @endif
@endelement

