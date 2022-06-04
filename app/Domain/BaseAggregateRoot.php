<?php

namespace App\Domain;

use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class BaseAggregateRoot extends AggregateRoot
{
    // ========================
    // RESTORE AGGREGATE STATE
    // ========================
    public function restoreStateFromAggregateVersion(string $model, ?int $aggregateVersion = null)
    {
        $modelId = $model::uuid($this->uuid())->id;

        $aggregateState = new $model;
        $aggregateState->uuid = $this->uuid();
        $aggregateState->id = $modelId;

        $events = $this->getStoredEventRepository()->retrieveAll($this->uuid());

        /**
         * @var StoredEvent $storedEvent
         */
        foreach ($events as $storedEvent) {
            if ($aggregateVersion !== null && (int)$storedEvent->aggregate_version > $aggregateVersion) {
                break;
            }
            $aggregateState = ModelStateBuilder::applyEventData($storedEvent, $aggregateState);
        }

        return $aggregateState;
    }
}
