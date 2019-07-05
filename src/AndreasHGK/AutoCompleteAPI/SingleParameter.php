<?php

declare(strict_types=1);

namespace AndreasHGK\AutoCompleteAPI;

class SingleParameter extends MagicParameter {

    protected $text;

    public function __construct(string $name, bool $optional, string $typeName, string $text){
        parent::__construct($name, $optional, $typeName);
        $this->text = $text;
    }

    public function getText() : string {
        return $this->text;
    }

}