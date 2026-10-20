<?php

namespace AmjadIqbal\DriverJS;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;

/**
 * Bound as a scoped singleton, so "assets already printed" resets per request
 * (and per Octane request).
 */
class Manager
{
    protected bool $assetsRendered = false;

    /**
     * @param  array<string, mixed>  $config
     */
    public function make(array $config = []): Driver
    {
        return Driver::make($config);
    }

    /** Start a named tour builder: Driver::tour('welcome')->step(...). */
    public function tour(string $name): Driver
    {
        return Driver::make()->name($name);
    }

    /**
     * The Driver.js stylesheet, script and the small runtime, printed once per
     * request. Every <x-driver-js> calls this, so you only need @driverJsAssets
     * when you want the tags somewhere specific (e.g. the <head>).
     */
    public function assets(): HtmlString
    {
        if ($this->assetsRendered) {
            return new HtmlString('');
        }

        $this->assetsRendered = true;

        $assets = (array) config('driver-js.assets', []);
        $nonce = Vite::cspNonce();
        $nonceAttr = $nonce ? ' nonce="'.e($nonce).'"' : '';
        $html = '';

        if (($assets['cdn'] ?? true) && ! empty($assets['css'])) {
            $html .= '<link rel="stylesheet" href="'.e($assets['css']).'"'.$this->integrity($assets['css_integrity'] ?? null).'>'."\n";
        }

        if (($assets['cdn'] ?? true) && ! empty($assets['js'])) {
            $html .= '<script src="'.e($assets['js']).'"'.$this->integrity($assets['js_integrity'] ?? null).$nonceAttr.'></script>'."\n";
        }

        $runtime = (string) file_get_contents(__DIR__.'/../resources/js/runtime.js');

        $html .= '<script'.$nonceAttr.'>'.$runtime.'</script>'."\n";

        return new HtmlString($html);
    }

    public function assetsRendered(): bool
    {
        return $this->assetsRendered;
    }

    protected function integrity(mixed $hash): string
    {
        return is_string($hash) && $hash !== ''
            ? ' integrity="'.e($hash).'" crossorigin="anonymous"'
            : '';
    }
}
