<?php

namespace AmjadIqbal\DriverJS;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Fluent builder for one Driver.js tour (or a single highlight).
 *
 * Option names match Driver.js 1.x exactly, so anything this class does not
 * wrap can still be passed through with set().
 *
 * @implements Arrayable<string, mixed>
 */
class Driver implements Arrayable, Jsonable, JsonSerializable
{
    /** Hooks Driver.js 1.x accepts on the tour config. */
    public const HOOKS = [
        'onPopoverRender',
        'onHighlightStarted',
        'onHighlighted',
        'onDeselected',
        'onDestroyStarted',
        'onDestroyed',
        'onNextClick',
        'onPrevClick',
        'onCloseClick',
        'onDoneClick',
    ];

    public const SIDES = ['top', 'right', 'bottom', 'left'];

    public const ALIGNMENTS = ['start', 'center', 'end'];

    public const BUTTONS = ['next', 'previous', 'close'];

    /** @var array<string, mixed> */
    protected array $config = [];

    /** @var array<string, string> */
    protected array $hooks = [];

    /** @var list<array<string, mixed>> */
    protected array $steps = [];

    /** @var array<string, mixed>|null */
    protected ?array $highlight = null;

    protected string $name = 'default';

    protected ?string $once = null;

    protected bool $autoStart = true;

    /**
     * @param  array<string, mixed>  $config
     */
    public static function make(array $config = []): self
    {
        return new self($config);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * The name used to start this tour from a button (data-driver-start="name"),
     * Alpine ($dispatch) or Livewire ($this->dispatch).
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Auto-start only the first time a visitor sees the tour, per browser.
     * Remembered in the browser's localStorage under this key as soon as the tour starts;
     * change the key (e.g. "welcome-v2") to show an updated tour again.
     * Buttons and events can still start it at any time.
     */
    public function once(string $key): self
    {
        $this->once = $key;

        return $this;
    }

    /** Start on page load (default) or only when triggered. */
    public function autoStart(bool $value = true): self
    {
        $this->autoStart = $value;

        return $this;
    }

    /** Register the tour without starting it; start it with a trigger. */
    public function manual(): self
    {
        return $this->autoStart(false);
    }

    public function animate(bool $value = true): self
    {
        return $this->set('animate', $value);
    }

    public function overlayColor(string $value): self
    {
        return $this->set('overlayColor', $value);
    }

    public function overlayOpacity(float $value): self
    {
        return $this->set('overlayOpacity', $value);
    }

    /** @deprecated Use overlayOpacity(); Driver.js 1.x has no "opacity" option. */
    public function opacity(float $value): self
    {
        return $this->overlayOpacity($value);
    }

    public function smoothScroll(bool $value = true): self
    {
        return $this->set('smoothScroll', $value);
    }

    public function allowClose(bool $value = true): self
    {
        return $this->set('allowClose', $value);
    }

    public function allowKeyboardControl(bool $value = true): self
    {
        return $this->set('allowKeyboardControl', $value);
    }

    /** What a click on the dimmed overlay does: "close", "nextStep" or "none". */
    public function overlayClickBehavior(string $value): self
    {
        if (! in_array($value, ['close', 'nextStep', 'none'], true)) {
            throw new InvalidArgumentException("overlayClickBehavior must be close, nextStep or none, got [{$value}].");
        }

        return $this->set('overlayClickBehavior', $value);
    }

    public function stagePadding(int $value): self
    {
        return $this->set('stagePadding', $value);
    }

    public function stageRadius(int $value): self
    {
        return $this->set('stageRadius', $value);
    }

    public function disableActiveInteraction(bool $value = true): self
    {
        return $this->set('disableActiveInteraction', $value);
    }

    public function popoverClass(string $value): self
    {
        return $this->set('popoverClass', $value);
    }

    public function popoverOffset(int $value): self
    {
        return $this->set('popoverOffset', $value);
    }

    /** Show "1 of 4"; the text may use {{current}} and {{total}}. */
    public function showProgress(bool $value = true, ?string $text = null): self
    {
        $this->set('showProgress', $value);

        return $text === null ? $this : $this->set('progressText', $text);
    }

    /** @param  list<string>  $buttons  any of next, previous, close */
    public function showButtons(array $buttons): self
    {
        return $this->set('showButtons', $this->buttons($buttons));
    }

    /** @param  list<string>  $buttons  any of next, previous, close */
    public function disableButtons(array $buttons): self
    {
        return $this->set('disableButtons', $this->buttons($buttons));
    }

    public function buttonLabels(?string $next = null, ?string $previous = null, ?string $done = null): self
    {
        foreach (['nextBtnText' => $next, 'prevBtnText' => $previous, 'doneBtnText' => $done] as $key => $text) {
            if ($text !== null) {
                $this->set($key, $text);
            }
        }

        return $this;
    }

    /**
     * Wire a Driver.js hook to a global JavaScript function, by name or dot path
     * ("tourFinished", "App.tours.finished"). The function is looked up on
     * window when the tour starts.
     */
    public function on(string $hook, string $function): self
    {
        if (! in_array($hook, self::HOOKS, true)) {
            throw new InvalidArgumentException("Unknown Driver.js hook [{$hook}]. Expected one of: ".implode(', ', self::HOOKS).'.');
        }

        $this->hooks[$hook] = $function;

        return $this;
    }

    /** @deprecated Driver.js 1.x has no onHighlighting hook; this maps to onHighlightStarted. */
    public function onHighlighting(string $function): self
    {
        return $this->on('onHighlightStarted', $function);
    }

    /** Pass any Driver.js config option through unchanged. */
    public function set(string $key, mixed $value): self
    {
        $this->config[$key] = $value;

        return $this;
    }

    /**
     * Add a step. Leave $element null for a centred popover with no highlight.
     *
     * @param  array<string, mixed>  $popover  extra popover options (side, align, showButtons, ...)
     * @param  array<string, mixed>  $options  extra step options (disableActiveInteraction, ...)
     */
    public function step(?string $element, string $title = '', string $description = '', array $popover = [], array $options = []): self
    {
        $this->steps[] = $this->buildStep($element, $title, $description, $popover, $options);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @deprecated Use step(). Kept from v0.1; $options are now popover options.
     */
    public function addStep(string $selector, string $title = '', string $description = '', array $options = []): self
    {
        return $this->step($selector, $title, $description, $options);
    }

    /**
     * Replace all steps with raw Driver.js step arrays.
     *
     * @param  array<int, array<string, mixed>>  $steps
     */
    public function steps(array $steps): self
    {
        $this->steps = array_values($steps);

        return $this;
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     *
     * @deprecated Use steps().
     */
    public function defineSteps(array $steps): self
    {
        return $this->steps($steps);
    }

    /**
     * Highlight one element instead of running a multi-step tour.
     *
     * @param  array<string, mixed>  $popover
     */
    public function highlight(string $element, string $title = '', string $description = '', array $popover = []): self
    {
        $this->highlight = $this->buildStep($element, $title, $description, $popover, []);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'config' => $this->config,
            'hooks' => $this->hooks,
            'steps' => $this->steps,
            'highlight' => $this->highlight,
            'once' => $this->once,
            'autoStart' => $this->autoStart,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param  int  $options
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    /**
     * @param  array<string, mixed>  $popover
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    protected function buildStep(?string $element, string $title, string $description, array $popover, array $options): array
    {
        if (isset($popover['side']) && ! in_array($popover['side'], self::SIDES, true)) {
            throw new InvalidArgumentException('Popover side must be one of: '.implode(', ', self::SIDES).'.');
        }

        if (isset($popover['align']) && ! in_array($popover['align'], self::ALIGNMENTS, true)) {
            throw new InvalidArgumentException('Popover align must be one of: '.implode(', ', self::ALIGNMENTS).'.');
        }

        $step = $options;

        if ($element !== null) {
            $step['element'] = $element;
        }

        $step['popover'] = array_merge(array_filter([
            'title' => $title,
            'description' => $description,
        ], fn (string $value) => $value !== ''), $popover);

        return $step;
    }

    /**
     * @param  array<int, string>  $buttons
     * @return list<string>
     */
    protected function buttons(array $buttons): array
    {
        foreach ($buttons as $button) {
            if (! in_array($button, self::BUTTONS, true)) {
                throw new InvalidArgumentException("Unknown Driver.js button [{$button}]. Expected next, previous or close.");
            }
        }

        return array_values($buttons);
    }
}
