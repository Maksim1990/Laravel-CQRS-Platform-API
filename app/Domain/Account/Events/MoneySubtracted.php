<?php

namespace App\Domain\Account\Events;

use App\Events\BaseEvent;

final class MoneySubtracted extends BaseEvent
{
    /**
     * @var int 
     */
    public $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;

        parent::__construct(
            [
            'amount'=>$this->amount
            ]
        );
    }
}
