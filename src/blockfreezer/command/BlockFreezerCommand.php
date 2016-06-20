<?php

namespace blockfreezer\command;

use blockfreezer\BlockFreezer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BlockFreezerCommand extends Command{
    /** @var BlockFreezer */
    private $plugin;
    /**
     * @param BlockFreezer $plugin
     */
    public function __construct(BlockFreezer $plugin){
        parent::__construct("blockfreezer", "Shows all BlockFreezer commands", null, ["bf"]);
        $this->setPermission("blockfreezer.command.blockfreezer");
        $this->plugin = $plugin;
    }
    /**
     * @param CommandSender $sender
     */
    private function sendCommandHelp(CommandSender $sender){
        $commands = [
            "addblock" => "Adds a block type to the specified world",
            "blocks" => "Lists all the freezable block types for the specified world",
            "delblock" => "Removes a block type to the specified world",
            "help" => "Shows all BlockFreezer commands"
        ];
        $sender->sendMessage("BlockFreezer commands:");
        foreach($commands as $name => $description){
            $sender->sendMessage("/blockfreezer $name: $description");
        }
    }
    /**
     * @param CommandSender $sender
     * @param string $label
     * @param string[] $args
     * @return bool
     */
    public function execute(CommandSender $sender, $label, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(isset($args[0])){
            switch(strtolower($args[0])){
                case "a";
                case "addblock":
                    if(isset($args[1]) and isset($args[2]) and isset($args[3])){
                        if($this->plugin->addBlock($args[1], $args[2], $args[3])){
                            $sender->sendMessage(TextFormat::GREEN."Successfully added $args[1]:$args[2] to $args[3].");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Failed to add.");
                        }
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify an id, damage value, and world name.");
                    }
                    return true;
                case "b":
                case "blocks":
                    if(isset($args[1])){
                        if(file_exists($path = $this->plugin->getDataFolder().($name = strtolower($args[1])).".txt")){
                            $count = 0;
                            $values = "";
                            foreach(file($path, FILE_SKIP_EMPTY_LINES) as $id){
                                $values .= trim($id).", ";
                                $count++;
                            }
                            $sender->sendMessage(TextFormat::AQUA."Found $count block type(s) for $name: $values");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."That world file couldn't be found.");
                        }
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify a world name.");
                    }
                    return true;
                case "d":
                case "delblock":
                    if(isset($args[1]) and isset($args[2]) and isset($args[3])){
                        if($this->plugin->removeBlock($args[1], $args[2], $args[3])){
                            $sender->sendMessage(TextFormat::GREEN."Successfully removed $args[1]:$args[2] from $args[3].");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Failed to remove.");
                        }
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify an id, damage value, and world name.");
                    }
                    return true;
                case "help":
                    $this->sendCommandHelp($sender);
                    return true;
                default:
                    $sender->sendMessage("Usage: /blockfreezer <sub-command> [parameters]");
                    return false;
            }
        }
        else{
            $this->sendCommandHelp($sender);
            return false;
        }
    }
}