<?php

declare(strict_types=1);

namespace AndreasHGK\AutoCompleteAPI;

class CustomCommandParameter {

    protected $type = 0;
    protected $name = "";
    protected $optional = false;

    public function __construct(int $type, string $name, bool $optional){
        $this->type = $type;
        $this->name = $name;
        $this->optional = $optional;
    }

    public function getType() : int{
        return $this->type;
    }

    public function getName() : string{
        return $this->name;
    }

    public function isOptional() : bool{
        return $this->optional;
    }

}