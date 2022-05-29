<?php
declare(strict_types=1);

namespace alvin0319\NPC\entity;

use alvin0319\NPC\NPCLoader;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use function trim;

class NPC extends Human{
	/** @var string */
	protected string $command;
	/** @var string */
	protected string $message;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->command = $nbt->getString("command", "");
		$this->message = $nbt->getString("message", "");
	}

	public function interact(Player $player) : void{
		$this->lookAt($player->getPosition());
		if(trim($this->message) !== ""){
			$player->sendMessage($this->message);
		}
		if(trim($this->command) !== ""){
			$this->server->dispatchCommand($player, $this->command);
		}
	}

	public function onInteract(Player $player, Vector3 $clickPos) : bool{
		if(isset(NPCLoader::$closureFunc[$player->getName()])){
			(NPCLoader::$closureFunc[$player->getName()])($this, $player);
			unset(NPCLoader::$closureFunc[$player->getName()]);
			return true;
		}
		$this->interact($player);
		return true;
	}

	public function getCommand() : string{
		return $this->command;
	}

	public function setCommand(string $command) : void{
		$this->command = $command;
	}

	public function getMessage() : string{
		return $this->message;
	}

	public function setMessage(string $message) : void{
		$this->message = $message;
	}

	public function attack(EntityDamageEvent $source) : void{
		$source->cancel();
	}
}
