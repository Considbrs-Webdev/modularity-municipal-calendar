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
                                    @if(!empty($post->municipalEventData->formattedDate))
                                        <li class="c-municipal-event-card__meta-item">
                                            @icon(['icon' => 'fa-solid fa-calendar-days'])@endicon
                                            <span>{{ $post->municipalEventData->formattedDate }}</span>
                                        </li>
                                    @endif
                                    
                                    {{-- Place --}}
                                    @if(!empty($post->municipalEventData->place))
                                        <li class="c-municipal-event-card__meta-item">
                                            @if($post->municipalEventData->placeIcon)
                                                @icon(['icon' => $post->municipalEventData->placeIcon])@endicon
                                            @endif
                                            <span>{{ $post->municipalEventData->place->name }}</span>
                                        </li>
                                    @endif
                                    
                                    {{-- Type of event/meeting --}}
                                    @if(!empty($post->municipalEventData->type))
                                        <li class="c-municipal-event-card__meta-item">
                                            @if($post->municipalEventData->typeIcon)
                                                @icon(['icon' => $post->municipalEventData->typeIcon])@endicon
                                            @endif
                                            <span>{{ $post->municipalEventData->type->name }}</span>
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

