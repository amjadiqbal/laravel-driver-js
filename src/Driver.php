<?php

namespace AmjadIqbal\DriverJS;

class Driver
{
    protected array $config = [];
    protected array $steps = [];
    protected ?string $action = null;
    protected array $actionPayload = [];

    public static function make(array $config = []): self
    {
        return new self($config);
    }

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function allowClose(bool $value = true): self
    {
        $this->config['allowClose'] = $value;

        return $this;
    }

    public function overlayColor(string $value): self
    {
        $this->config['overlayColor'] = $value;

        return $this;
    }

    public function opacity(float $value): self
    {
        $this->config['opacity'] = $value;

        return $this;
    }

    public function onHighlighting(string $handler): self
    {
        $this->config['callbacks']['onHighlighting'] = $handler;

        return $this;
    }

    public function set(string $key, mixed $value): self
    {
        $this->config[$key] = $value;

        return $this;
    }

    public function addStep(string $selector, string $title = '', string $description = '', array $options = []): self
    {
        $step = [
            'element' => $selector,
            'popover' => [
                'title' => $title,
                'description' => $description,
            ],
        ];
        if (!empty($options)) {
            $step['options'] = $options;
        }
        $this->steps[] = $step;

        return $this;
    }

    public function defineSteps(array $steps): self
    {
        $this->steps = $steps;

        return $this;
    }

    public function highlight(string $selector): self
    {
        $this->action = 'highlight';
        $this->actionPayload = ['selector' => $selector];

        return $this;
    }

    public function drive(): self
    {
        $this->action = 'drive';

        return $this;
    }

    public function destroy(): self
    {
        $this->action = 'destroy';

        return $this;
    }

    public function moveNext(): self
    {
        $this->action = 'moveNext';

        return $this;
    }

    public function movePrevious(): self
    {
        $this->action = 'movePrevious';

        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'config' => $this->config,
            'steps' => $this->steps,
        ];
        if ($this->action) {
            $data['action'] = [
                'type' => $this->action,
                'payload' => $this->actionPayload,
            ];
        }

        return $data;
    }

    public function toJson(): string
    {

        return json_encode($this->toArray());
    }
}
