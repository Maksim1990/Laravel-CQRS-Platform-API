<?php

namespace App\Domain\Account;

use App\Domain\Account\Events\AccountCreated;
use App\Domain\Account\Events\AccountDeleted;
use App\Domain\Account\Events\AccountLimitHit;
use App\Domain\Account\Events\MoneyAdded;
use App\Domain\Account\Events\MoneySubtracted;
use App\Domain\Account\Events\MoreMoneyNeeded;
use App\Domain\Account\Exceptions\CouldNotSubtractMoney;
use App\Domain\Account\StateBuilder\AccountStateBuilder;
use App\Models\Account;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

final class AccountAggregateRoot extends AggregateRoot
{
    /**
     * @var int 
     */
    private $balance = 0;

    /**
     * @var int 
     */
    private $accountLimit = -5000;

    /**
     * @var int 
     */
    private $accountLimitHitInARow = 0;

    public function createAccount(string $name, string $userId)
    {
        $this->recordThat(new AccountCreated($name, $userId));

        return $this;
    }

    public function addMoney(int $amount)
    {
        $this->recordThat(new MoneyAdded($amount));

        return $this;
    }

    public function applyMoneyAdded(MoneyAdded $event)
    {
        // \Log::info('apply money added' . $event->amount);
        //var_dump('money added '.$event->amount);
        $this->accountLimitHitInARow = 0;

        $this->balance += $event->amount;

        return $this->balance;
    }

    public function subtractMoney(int $amount)
    {
        if (!$this->hasSufficientFundsToSubtractAmount($amount)) {
            $this->recordThat(new AccountLimitHit());

            if ($this->needsMoreMoney()) {
                $this->recordThat(new MoreMoneyNeeded());
            }

            $this->persist();

            throw CouldNotSubtractMoney::notEnoughFunds($amount);
        }

        $this->recordThat(new MoneySubtracted($amount));
    }

    public function applyMoneySubtracted(MoneySubtracted $event)
    {
        //var_dump('money subtracted '.$event->amount);
        $this->balance -= $event->amount;

        $this->accountLimitHitInARow = 0;
    }

    public function deleteAccount()
    {
        $this->recordThat(new AccountDeleted());

        return $this;
    }

    public function applyAccountLimitHit()
    {
        $this->accountLimitHitInARow++;
    }

    private function hasSufficientFundsToSubtractAmount(int $amount): bool
    {
        return $this->balance - $amount >= $this->accountLimit;
    }

    private function needsMoreMoney()
    {
        return $this->accountLimitHitInARow >= 3;
    }

    // ========================
    // RESTORE AGGREGATE STATE
    // ========================
    public function restoreStateFromAggregateVersion(int $aggregateVersion = 1)
    {
        $aggregateState = new Account;
        $aggregateState->uuid = $this->uuid();

        $events = $this->getStoredEventRepository()->retrieveAll($this->uuid());

        /**
 * @var StoredEvent $storedEvent 
*/
        foreach ($events as $storedEvent){
            if((int)$storedEvent->aggregate_version > $aggregateVersion) { break;
            }
            //var_dump($storedEvent->created_at);
            $aggregateState = AccountStateBuilder::applyEventData($storedEvent->event, $aggregateState);
        }

        return $aggregateState;
    }
}
