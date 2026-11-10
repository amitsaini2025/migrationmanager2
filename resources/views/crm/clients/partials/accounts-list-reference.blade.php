@if(!empty($pdfUrl) && $text !== '' && $text !== '-')
    <a href="{{ $pdfUrl }}" target="_blank" rel="noopener">{{ $text }}</a>
@else
    {{ $text }}
@endif
@if(!empty($docUrl))
    <a href="{{ $docUrl }}" target="_blank" rel="noopener" title="Uploaded document" class="ml-1">@icon('fa-file-pdf')</a>
@endif
