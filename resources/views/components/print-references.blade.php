@props([
    'references'
])

@php
    $i = 1;
@endphp

@foreach($references as $reference)
    <p class="text sm">
        [{{ $i++ }}] {{ $reference  }}
    </p>
@endforeach
