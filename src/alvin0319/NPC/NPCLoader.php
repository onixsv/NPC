<?php
declare(strict_types=1);

namespace alvin0319\NPC;

use alvin0319\NPC\command\NPCCommand;
use alvin0319\NPC\entity\NPC;
use Closure;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class NPCLoader extends PluginBase{
	use SingletonTrait;

	/** @var Closure[] */
	public static array $closureFunc = [];

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		$this->getServer()->getCommandMap()->register("npc", new NPCCommand());

		EntityFactory::getInstance()->register(NPC::class, function(World $world, CompoundTag $nbt) : NPC{
			return new NPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
		}, ["NPC"]);

		$this->getServer()->getPluginManager()->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event) : void{
			$player = $event->getPlayer();
			if(isset(NPCLoader::$closureFunc[$player->getName()]))
				unset(NPCLoader::$closureFunc[$player->getName()]);
		}, EventPriority::NORMAL, $this, true);
	}

	protected function onDisable() : void{
		self::$instance = null;
	}
}
