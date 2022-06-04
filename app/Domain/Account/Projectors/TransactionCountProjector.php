<?php

namespace App\Domain\Account\Projectors;

use App\Domain\Account\Events\AccountCreated;
use App\Domain\Account\Events\LessonDeleted;
use App\Domain\Account\Events\MoneyAdded;
use App\Domain\Account\Events\MoneySubtracted;
use App\Models\TransactionCount;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class TransactionCountProjector extends Projector
{
    public function onAccountCreated(AccountCreated $event, string $aggregateUuid)
    {
        TransactionCount::create(
            [
            'uuid' => $aggregateUuid,
            'user_id' => $event->userId,
            ]
        );
    }

    public function onMoneyAdded(MoneyAdded $event, string $aggregateUuid)
    {
        var_dump('money added transaction '.$event->amount);
        TransactionCount::uuid($aggregateUuid)->incrementCount();
    }

    public function onMoneySubtracted(MoneySubtracted $event, string $aggregateUuid)
    {
        var_dump('money subtracted transaction '.$event->amount);
        TransactionCount::uuid($aggregateUuid)->incrementCount();
    }

    public function onAccountDeleted(LessonDeleted $event, string $aggregateUuid)
    {
        var_dump('money deleted transaction');
        TransactionCount::uuid($aggregateUuid)->delete();
    }
}
