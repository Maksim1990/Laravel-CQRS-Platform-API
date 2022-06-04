<?php

namespace App\Mappings;

class TagMapping
{
    public const ATTACH_ACTION = 'ATTACH';
    public const DETACH_ACTION = 'DETACH';

    public const TAGS_ACTIONS = [self::ATTACH_ACTION, self::DETACH_ACTION];
}
