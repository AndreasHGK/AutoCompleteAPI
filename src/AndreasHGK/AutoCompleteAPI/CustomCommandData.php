<?php

declare(strict_types=1);

namespace AndreasHGK\AutoCompleteAPI;

use Composer\Command\SelfUpdateCommand;
use pocketmine\command\Command;

class CustomCommandData {

    public const ARG_TYPE_INT             = 0;
    public const ARG_TYPE_FLOAT           = 1;
    public const ARG_TYPE_VALUE           = 2;
    public const ARG_TYPE_WILDCARD_INT    = 3;
    public const ARG_TYPE_OPERATOR        = 4;
    public const ARG_TYPE_TARGET          = 5;
    public const ARG_TYPE_FILEPATH = 6;
    public const ARG_TYPE_STRING   = 7;
    public const ARG_TYPE_POSITION = 8;
    public const ARG_TYPE_MESSAGE  = 9;
    public const ARG_TYPE_RAWTEXT  = 10;
    public const ARG_TYPE_JSON     = 11;
    public const ARG_TYPE_COMMAND  = 12;

    //don't use this
    public const ARG_TYPE_ARRAY = 13;

    //custom types
    public const MAGIC_TYPE_ITEM = "Item";
    public const MAGIC_TYPE_BLOCK = "Block";


    /** @var Command */
    protected $command;

    protected $name;

    /** @var CustomCommandParameter[][] */
    protected $parameters = [];

    // The $x value allows setting multiple different parameters (multiple lines). Set this to 0 if you don't need this.
    // The $y value is the position of the parameter. if it is 0 it will be the first parameter, 2 the second one ...
    public function addParameter(int $x, int $y, CustomCommandParameter $param) : void{
        $this->parameters[$x][$y] = $param;
    }

    //anything from 0 - 12 in the types above
    public function normalParameter(int $x, int $y, int $type, string $name, bool $optional = false) : void{
        if($type > 12 || $type < 0){
            throw new \TypeError("Unknown parameter type");
        }
        $param = new CustomCommandParameter($type, $name, $optional);
        $this->addParameter($x, $y, $param);
    }

    public function arrayParameter(int $x, int $y, string $name, array $contents, bool $optional = false, string $typeName = null) : void{
        if($typeName === null){
            $typeName = (string)mt_rand(); //somehow, if the typeName of parameters are the same, the game will only use the values of the last registered parameter with that name. This is a workaround.
        }
        $param = new ArrayParameter($name, $optional, $typeName, $contents);
        $this->addParameter($x, $y, $param);
    }

    public function magicParameter(int $x, int $y, string $customType, string $name, bool $optional = false) : void{
        switch ($customType){
            case self::MAGIC_TYPE_BLOCK:
                break;
            case self::MAGIC_TYPE_ITEM:
                break;
            default:
                throw new \TypeError("Unknown custom parameter type");
                break;
        }
        $param = new MagicParameter($name, $optional, $customType);
        $this->addParameter($x, $y, $param);
    }

    public function removeParameter(int $x, int $y) : void{
        unset($this->parameters[$x][$y]);
    }




    /**
     * @return CustomCommandParameter[][]
     */
    public function getParameters() : array {
        return $this->parameters;
    }

    public function __construct(Command $cmd){
        $this->command = $cmd;
        $this->name = $this->command->getName();
    }

    public function getName() : string {
        return $this->name;
    }

    public function getCommand() : Command{
        return $this->command;
    }

}