@props([
    'data'
])

@if(is_array($data))
    @foreach($data['blocks'] as $index => $block)
        @if($block['type'] === 'paragraph')
            <p class="text margin">
                {{ $block['data']['text']  }}
            </p>
        @elseif($data['type'] === 'header')
            <p class="text margin">
                {{ "<h{$block['data']['level']}>" . $block['data']['text'] . "</h{$block['data']['level']}>"  }}
            </p>
        @endif
    @endforeach
@endif
