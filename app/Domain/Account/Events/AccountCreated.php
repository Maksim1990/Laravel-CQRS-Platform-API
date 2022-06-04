<?php

namespace App\Domain\Account\Events;

use App\Events\BaseEvent;

final class AccountCreated extends BaseEvent
{
    /**
     * @var string 
     */
    public $name;

    /**
     * @var int 
     */
    public $userId;

    public function __construct(string $name, int $userId)
    {
        $this->name = $name;

        $this->userId = $userId;

        parent::__construct(
            [
            'name'=>$this->name,
            'userId'=>$this->userId
            ]
        );
    }
}
