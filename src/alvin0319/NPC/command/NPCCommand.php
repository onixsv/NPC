<?php
declare(strict_types=1);

namespace alvin0319\NPC\command;

use alvin0319\NPC\entity\NPC;
use alvin0319\NPC\NPCLoader;
use OnixUtils\OnixUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\EntityDataHelper;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use function trim;

class NPCCommand extends Command{

	public function __construct(){
		parent::__construct("npc", "엔피시를 관리합니다.");
		$this->setPermission("npc.command");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$this->testPermission($sender))
			return false;
		if($sender instanceof Player){
			switch($args[0] ?? "x"){
				case "생성":
					if(trim($args[1] ?? "") !== ""){
						$nbt = CompoundTag::create();
						$nbt->setTag("Skin", CompoundTag::create()
							->setString("Name", $sender->getSkin()->getSkinId())
							->setByteArray("Data", $sender->getSkin()->getSkinData())
							->setByteArray("CapeData", $sender->getSkin()->getCapeData())
							->setString("GeometryName", $sender->getSkin()->getGeometryName())
							->setString("GeometryData", $sender->getSkin()->getGeometryData())
						);

						$npc = new NPC($sender->getLocation(), $sender->getSkin(), $nbt);
						$npc->setNameTag($args[1]);
						$npc->setNameTagAlwaysVisible(true);
						$npc->spawnToAll();
					}else{
						OnixUtils::message($sender, "엔피시의 네임태그를 입력해주세요.");
					}
					break;
				case "명령어":
					NPCLoader::$closureFunc[$sender->getName()] = function(NPC $npc, Player $player) use ($args) : void{
						$npc->setCommand($args[1]);
						OnixUtils::message($player, "{$npc->getNameTag()} 엔피시의 명령어를 \"{$args[1]}\"(으)로 설정했습니다.");
					};
					OnixUtils::message($sender, "이제 명령어를 설정할 엔피시를 터치해주세요.");
					break;
				case "메시지":
					NPCLoader::$closureFunc[$sender->getName()] = function(NPC $npc, Player $player) use ($args) : void{
						$npc->setMessage($args[1]);
						OnixUtils::message($player, "{$npc->getNameTag()} 엔피시의 메시지를 \"{$args[1]}\"(으)로 설정했습니다.");
					};
					OnixUtils::message($sender, "이제 메시지를 설정할 엔피시를 터치해주세요.");
					break;
				case "제거":
					NPCLoader::$closureFunc[$sender->getName()] = function(NPC $npc, Player $player) : void{
						$npc->close();
						OnixUtils::message($player, "엔피시를 성공적으로 제거했습니다.");
					};
					OnixUtils::message($sender, "제거할 엔피시를 터치해주세요.");
					break;
				default:
					foreach([
						["생성 [네임태그]", "엔피시를 생성합니다."],
						["명령어 [명령어]", "엔피시에 명령어를 설정합니다."],
						["메시지 [메시지]", "엔피시에 메시지를 설정합니다."],
						["제거", "엔피시를 제거합니다."]
					] as $usage){
						OnixUtils::message($sender, "/npc {$usage[0]} - {$usage[1]}");
					}
			}
		}
		return true;
	}
}
