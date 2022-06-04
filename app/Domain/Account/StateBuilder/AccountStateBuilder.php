<?php
namespace App\Domain\Account\StateBuilder;

use App\Events\BaseEvent;
use Illuminate\Database\Eloquent\Model;

class AccountStateBuilder
{
    /**
     * @param  BaseEvent $event
     * @param  Model     $aggregateState
     * @return mixed
     */
    public static function applyEventData(BaseEvent $event, Model $aggregateState)
    {
        // Merge current and event attributes
        $attributes = array_merge($aggregateState->getAttributes(), $event->getPayload());
        $attributes = self::filterAttributeData($attributes, $event);

        $aggregateState->setRawAttributes($attributes);

        return $aggregateState;
    }

    private static function filterAttributeData(array $attributes, BaseEvent $event): array
    {
        if (!isset($attributes['amount'])) {
            $attributes['balance'] = 0;
        }

        if (isset($attributes['amount'])) {
            $attributes['balance'] += $event->amount;
        }

        unset($attributes['amount']);

        return $attributes;
    }
}
