<?php

declare(strict_types=1);

namespace AndreasHGK\AutoCompleteAPI;

class ArrayParameter extends MagicParameter {

    protected $contents = [];

    public function __construct(string $name, bool $optional, string $typeName, array $contents){
        parent::__construct($name, $optional, $typeName);
        $this->typeName = $typeName;
        $this->contents = $contents;
    }

    public function getContents() : array {
        return $this->contents;
    }

}