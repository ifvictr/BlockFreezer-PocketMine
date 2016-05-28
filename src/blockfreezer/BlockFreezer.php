<?php

namespace blockfreezer;

use blockfreezer\command\BlockFreezerCommand;
use blockfreezer\event\BlockFreezerListener;
use pocketmine\block\Block;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class BlockFreezer extends PluginBase{
    /** @var Config[] */
    private $configs = [];
    public function onEnable(){
        @mkdir($this->getDataFolder());
        foreach($this->getServer()->getLevels() as $level){
            $ilevel = strtolower($level->getName());
            $this->configs[$ilevel] = new Config($this->getDataFolder().$ilevel.".txt", Config::ENUM);
        }
    	$this->getServer()->getCommandMap()->register("blockfreezer", new BlockFreezerCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new BlockFreezerListener($this), $this);
    }
    /**
     * @param int $id
     * @param int $damage
     * @param string $level
     * @return bool
     */
    public function addBlock($id, $damage, $level){
        if(isset($this->configs[$ilevel = strtolower($level)])){
            $this->configs[$ilevel]->set($id.":".$damage);
            $this->configs[$ilevel]->save();
            return true;
        }
        return false;
    }
    /**
     * @param int $id
     * @param int $damage
     * @param string $level
     * @return bool
     */
    public function removeBlock($id, $damage, $level){
        if(isset($this->configs[$ilevel = strtolower($level)])){
            if($this->configs[$ilevel]->exists($key = $id.":".$damage)){
                $this->configs[$ilevel]->remove($key);
                $this->configs[$ilevel]->save();
                return true;
            }
        }
        return false;
    }
    /**
     * @param Block $block
     * @return bool
     */
    public function isFreezable(Block $block){
        if(isset($this->configs[$ilevel = strtolower($block->getLevel()->getName())])){
            return $this->configs[$ilevel]->exists($block->getId().":".$block->getDamage(), true);
        }
        return false;
    }
}
