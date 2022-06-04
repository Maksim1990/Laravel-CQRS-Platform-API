<?php
namespace App\Mappings;

class MorphableMapping
{
    public const DEFAULT_MORPHABLE_ID = 'model_id';
    public const DEFAULT_MORPHABLE_TYPE = 'type';

    public const COMMENTABLE_ID = 'commentable_id';
    public const COMMENTABLE_TYPE = 'commentable_type';

    public const COMMENTABLE_ID_DISPLAYED = self::DEFAULT_MORPHABLE_ID;
    public const COMMENTABLE_TYPE_DISPLAYED = self::DEFAULT_MORPHABLE_TYPE;

    public const MORHABLE_MAPPING_LIST =[
        self::COMMENTABLE_ID => self::COMMENTABLE_ID_DISPLAYED,
        self::COMMENTABLE_TYPE => self::COMMENTABLE_TYPE_DISPLAYED,
    ];

    public const MORHABLE_FILEDS =[
        self::COMMENTABLE_ID,
        self::COMMENTABLE_TYPE,
    ];
}
