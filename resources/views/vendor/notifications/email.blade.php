<x-mail::message>

<!-- Basic header with default image -->
<div style="text-align: center; padding: 20px 0; border-bottom: 1px solid #eaeaea;">
    <img src="https://via.placeholder.com/100x100?text=Logo" alt="Logo" style="width: 100px; height: 100px;">
</div>

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
<p style="font-size: 16px; color: #333333; line-height: 1.5;">
    {{ $line }}
</p>
@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
    {{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
<p style="font-size: 16px; color: #333333; line-height: 1.5;">
    {{ $line }}
</p>
@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
<p style="font-size: 16px; color: #333333;">
    {{ $salutation }}
</p>
@else
<p style="font-size: 16px; color: #333333;">
    @lang('Regards,')<br>
    {{ config('app.name') }}
</p>
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
<p style="font-size: 12px; color: #666666; text-align: center;">
    @lang(
        "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
        'into your web browser:',
        [
            'actionText' => $actionText,
        ]
    )
</p>
<p style="word-break: break-all; text-align: center;">
    [{{ $displayableActionUrl }}]({{ $actionUrl }})
</p>
</x-slot:subcopy>
@endisset

</x-mail::message>
