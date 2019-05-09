<?php

declare(strict_types=1);

namespace AndreasHGK\AutoCompleteAPI;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class CustomCommandPacket extends AvailableCommandsPacket {

    //this exists so the plugin can tell the difference between it's own AvailableCommandPacket and other ones.

}