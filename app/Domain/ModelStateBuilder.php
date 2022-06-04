<?php
namespace App\Domain;

use App\Events\BaseEvent;
use Illuminate\Database\Eloquent\Model;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class ModelStateBuilder
{
    /**
     * @param  StoredEvent $storedEvent
     * @param  Model       $aggregateState
     * @return mixed
     */
    public static function applyEventData(StoredEvent $storedEvent,Model $aggregateState)
    {
        // Merge current and event attributes
        $attributes = array_merge($aggregateState->getAttributes(), $storedEvent->event->getPayload());

        $aggregateState->setRawAttributes($attributes);

        if((int)$storedEvent->aggregate_version === 1) {
            $aggregateState->setCreatedAt($storedEvent->created_at);
        }
        $aggregateState->setUpdatedAt($storedEvent->created_at);
        return $aggregateState;
    }
}
