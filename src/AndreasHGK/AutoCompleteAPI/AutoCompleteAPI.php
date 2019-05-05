<?php

declare(strict_types=1);

namespace AndreasHGK\AutoCompleteAPI;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\CommandData;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\network\mcpe\protocol\types\CommandParameter;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;

class AutoCompleteAPI extends PluginBase implements Listener {

    /** @var AutoCompleteAPI */
    protected static $instance;

    protected static $enumIndex = 1;

    /** @var CustomCommandData[] */
    protected $commands = [];

    protected $lastPacket = null;

    public static function getInstance() : ?AutoCompleteAPI {
        return self::$instance;
    }

    public function registerCommandData(Command $command, bool $overwrite = false) : ?CustomCommandData{
        $data = new CustomCommandData($command);
        $name = $command->getName();
        if(isset($this->commands[$name]) && $overwrite){
            $this->commands[$name] = $data;
        }elseif(isset($this->commands[$name]) && !$overwrite){
            return null;
        }else{
            $this->commands[$name] = $data;
        }
        return $data;
    }

    public function getCommandData(string $name) : ?CustomCommandData{
        return $this->commands[$name] ?? null;
    }



    public function onLoad() : void {
        self::$instance = $this;
    }

    public function onEnable() : void{
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

    public function onJoin(PlayerJoinEvent $ev) : void{
        $this->sendCommandData($ev->getPlayer());
    }

    public function onPacket(DataPacketSendEvent $ev){
        if ($ev->getPacket() !== $this->lastPacket && $ev->getPacket() instanceof AvailableCommandsPacket){
            $this->sendCommandData($ev->getPlayer());
        }
    }

    public function sendCommandData(Player $player){
        $pk = new AvailableCommandsPacket();
        foreach ($this->commands as $name => $commandData){
            $data = new CommandData();
            $data->commandName = strtolower($commandData->getName());
            $data->commandDescription = $this->getServer()->getLanguage()->translateString($commandData->getCommand()->getDescription());
            $data->flags = 0;
            $data->permission = 0;

            $aliases = $commandData->getCommand()->getAliases();
            if(!empty($aliases)){
                if(!in_array($data->commandName, $aliases, true)){
                    //work around a client bug which makes the original name not show when aliases are used
                    $aliases[] = $data->commandName;
                }
                $data->aliases = new CommandEnum();
                $data->aliases->enumName = ucfirst($commandData->getName()) . "Aliases";
                $data->aliases->enumValues = $aliases;
            }

            foreach ($commandData->getParameters() as $x => $y){
                foreach ($y as $key => $customParameter){
                    $parameter = new CommandParameter();
                    $parameter->paramName = $customParameter->getName();
                    $parameter->isOptional = $customParameter->isOptional();
                    if($customParameter instanceof MagicParameter){
                        $parameter->paramType = AvailableCommandsPacket::ARG_FLAG_ENUM | AvailableCommandsPacket::ARG_FLAG_VALID | self::$enumIndex;
                        self::$enumIndex++;
                        $parameter->enum = new CommandEnum();
                        $parameter->enum->enumName = $customParameter->getTypeName();
                        if($customParameter instanceof ArrayParameter){
                            foreach($customParameter->getContents() as $content){
                                array_push($pk->enumValues, $content);
                            }
                            $parameter->enum->enumValues = $customParameter->getContents();
                        }
                        array_push($pk->enums, $parameter->enum);
                    }else{
                        switch ($customParameter->getType()){
                            case 0:
                                $type = 0x01;
                                break;
                            case 1:
                                $type = 0x02;
                                break;
                            case 2:
                                $type = 0x03;
                                break;
                            case 3:
                                $type = 0x04;
                                break;
                            case 4:
                                $type = 0x05;
                                break;
                            case 5:
                                $type = 0x06;
                                break;
                            case 6:
                                $type = 0x0e;
                                break;
                            case 7:
                                $type = 0x1b;
                                break;
                            case 8:
                                $type = 0x1d;
                                break;
                            case 9:
                                $type = 0x20;
                                break;
                            case 10:
                                $type = 0x22;
                                break;
                            case 11:
                                $type = 0x25;
                                break;
                            case 12:
                                $type = 0x2c;
                                break;
                            default:
                                throw new \TypeError("Unknown parameter type");
                                break;
                        }
                        $parameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | $type;
                    }
                    $data->overloads[$x][$key] = $parameter;
                }
            }

            $pk->commandData[$commandData->getName()] = $data;
        }

        foreach($this->getServer()->getCommandMap()->getCommands() as $name => $command){
            if(isset($pk->commandData[$command->getName()]) or $command->getName() === "help" or !$command->testPermissionSilent($player)){
                continue;
            }

            $data = new CommandData();
            $data->commandName = strtolower($command->getName());
            $data->commandDescription = $this->getServer()->getLanguage()->translateString($command->getDescription());
            $data->flags = 0;
            $data->permission = 0;

            $parameter = new CommandParameter();
            $parameter->paramName = "args";
            $parameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_TYPE_RAWTEXT;
            $parameter->isOptional = true;
            $data->overloads[0][0] = $parameter;

            $aliases = $command->getAliases();
            if(!empty($aliases)){
                if(!in_array($data->commandName, $aliases, true)){
                    //work around a client bug which makes the original name not show when aliases are used
                    $aliases[] = $data->commandName;
                }
                $data->aliases = new CommandEnum();
                $data->aliases->enumName = ucfirst($command->getName()) . "Aliases";
                $data->aliases->enumValues = $aliases;
            }

            $pk->commandData[$command->getName()] = $data;
        }

        $this->lastPacket = $pk;
        $player->dataPacket($pk);
    }

    public function broadcastCommandData() : void{
	    foreach ($this->getServer()->getOnlinePlayers() as $player){
	        $this->sendCommandData($player);
        }
    }

	public function onDisable() : void{
	}
}
