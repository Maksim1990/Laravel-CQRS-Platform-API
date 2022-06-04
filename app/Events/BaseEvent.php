<?php


namespace App\Events;


use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class BaseEvent extends ShouldBeStored
{
    private array $payload;

    public bool $isSaveModel;

    public function __construct(array $payload = [], bool $isSaveModel = true)
    {
        $this->payload = $payload;
        $this->isSaveModel = $isSaveModel;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function isSaveModel(): bool
    {
        return $this->isSaveModel;
    }
}
