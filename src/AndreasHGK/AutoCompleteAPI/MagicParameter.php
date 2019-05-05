<?php

declare(strict_types=1);

namespace AndreasHGK\AutoCompleteAPI;

class MagicParameter extends CustomCommandParameter {

    protected $typeName = "";

    public function __construct(string $name, bool $optional, string $typeName){
        parent::__construct(CustomCommandData::ARG_TYPE_ARRAY, $name, $optional);
        $this->typeName = $typeName;
    }

    public function getTypeName() : string {
        return $this->typeName;
    }

}