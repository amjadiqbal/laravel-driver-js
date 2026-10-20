{!! $assets() !!}
<script @if ($nonce = \Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ $nonce }}" @endif>
window.LaravelDriverJs && window.LaravelDriverJs.register({{ \Illuminate\Support\Js::from($definition) }});
</script>
