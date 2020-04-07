<?php

namespace LegitDev\CreditSystem;

use DateTime;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\UUID;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\TextFormat as color;
use pocketmine\command\CommandExecutor;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityLevelChangeEvent;

class Main extends PluginBase implements Listener{

    public $prefix = "§7{§bCredit-System§7} §7> §7";

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        date_default_timezone_set("Europe/Berlin");
        if(!file_exists($this->getDataFolder()."Transaktionen.yml")){
            $config = new Config($this->getDataFolder() ."Transaktionen.yml", Config::YAML);
            $config->set("Transaktionen", ".-.-.-.-.-.-.-.-.-.-.-.-.-.");
            $config->save();
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if (!file_exists($this->getDataFolder() . $event->getPlayer()->getName() . ".yml")) {
            $config = new Config($this->getDataFolder() . $event->getPlayer()->getName() . ".yml", Config::YAML);
            $config->set("Credits", 1000);
            $config->save();
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        $cmd = strtolower($command);

#| onCommand
#|
#|---------------------------------------------------------------------------------------------------------------------
#|
#| Command = credits

        if ($cmd == "credits") {
#-----------------------------------------------------------------------------------------------------------------------
            if($sender instanceof Player){
#-----------------------------------------------------------------------------------------------------------------------
                if($sender->hasPermission("credits.cmd")){
#-----------------------------------------------------------------------------------------------------------------------
                    if (isset($args[0])) {
                        $arged_player = $this->getServer()->getPlayerExact($args[0]);
                        if(file_exists($this->getDataFolder() .$arged_player. ".yml")) {
#-----------------------------------------------------------------------------------------------------------------------
                            if($sender->getName() == $arged_player->getName()) {
                                $config = new Config($this->getDataFolder() . $arged_player->getName() . ".yml", Config::YAML);
                                $sender->sendMessage($this->prefix . "Du hast". $config->get("Credits")." Credits");
                            }else{
                                $config = new Config($this->getDataFolder() . $arged_player->getName() . ".yml", Config::YAML);
                                $sender->sendMessage($this->prefix . $arged_player->getName() . "hat " . $config->get("Credits") . " Credits");
                            }
                        }else{
                            $sender->sendMessage($this->prefix.color::RED."Der angegebende Spieler hat noch nie auf dem Netzwerk gespielt");
                        }
                    }else{
                        $sender->sendMessage($this->prefix.color::RED."Du musst hier einen Spieler angeben");
                    }
#-----------------------------------------------------------------------------------------------------------------------
                }else{
                    $sender->sendMessage($this->prefix.color::RED . "Du hast keine Rechte für diesen Befehl");
                }
#-----------------------------------------------------------------------------------------------------------------------
            }else{
                $sender->sendMessage($this->prefix.color::RED . "Du must ein Spieler sein um diesen Befehl zu nutze");
            }

#| Command Ende(credits)
#|
#|---------------------------------------------------------------------------------------------------------------------
#|
#| Command = pay

        }elseif ($cmd == "pay"){
#-----------------------------------------------------------------------------------------------------------------------
            if($sender instanceof Player) {
#-----------------------------------------------------------------------------------------------------------------------
                if ($sender->hasPermission("pay.cmd")) {
#-----------------------------------------------------------------------------------------------------------------------
                    if (isset($args[0])) {
#-----------------------------------------------------------------------------------------------------------------------
                        if (isset($args[1])) {
                            $arged_player = $this->getServer()->getPlayerExact($args[0]);
#-----------------------------------------------------------------------------------------------------------------------
                            if ($arged_player != null AND $arged_player->isOnline()) {
#-----------------------------------------------------------------------------------------------------------------------
                                if (is_numeric($args[1])) {
                                    $config = new Config($this->getDataFolder() . $sender->getName() . ".yml", Config::YAML);
                                    $credits = $config->get("Credits");
#-----------------------------------------------------------------------------------------------------------------------
                                    if ($credits >= (int) $args[1]) {
#-----------------------------------------------------------------------------------------------------------------------
                                        if (!$arged_player->getName() === $sender->getName()) {
#-----------------------------------------------------------------------------------------------------------------------

                                            $config = new Config($this->getDataFolder() . $sender->getName() . ".yml", Config::YAML);
                                            $config->set("Credits", $config->get("Credits") - $args[1]);
                                            $config->save();
                                            $config_player = new Config($this->getDataFolder() . $arged_player->getName() . ".yml", Config::YAML);
                                            $config_player->set("Credits", $config->get("Credits") + $args[0]);
                                            $config_player->save();
                                            $jetzt = new DateTime("now");
                                            $tra_config = new Config($this->getDataFolder() ."Transaktionen.yml", Config::YAML);
                                            $old = $tra_config->get("Transaktionen");
                                            $tra_config->set("Transaktionen", $old ."\n".$jetzt->format("d.m.Y | H:i:s")." ".$sender.getName()." [->] ". $arged_player->getName(). " Credits: ".$args[1]);
                                            $tra_config->save();
                                            $sender->sendMessage($this->prefix . "Du hast " . $arged_player->getName() . " " . $args[1] . " Credits gegeben");
                                            $arged_player->sendMessage($this->prefix . "Dir wurden von " . $sender->getName() . " " . $args[1] . " Credits gegeben");

#-----------------------------------------------------------------------------------------------------------------------
                                        }else{
                                            $sender->sendMessage($this->prefix . color::RED . "Du kannst dir selber keine Credits geben");
                                        }
#-----------------------------------------------------------------------------------------------------------------------
                                    }else{
                                        $sender->sendMessage($this->prefix . color::RED . "Du hast zu wenig Credits");
                                    }
#-----------------------------------------------------------------------------------------------------------------------
                                }else{
                                    $sender->sendMessage($this->prefix . color::RED . "Du musst hier Zahlen angeben");
                                }
#-----------------------------------------------------------------------------------------------------------------------
                            }else{
                                $sender->sendMessage($this->prefix . color::RED . "Der Spieler dem du Credits geben willst muss online sein");
                            }
#-----------------------------------------------------------------------------------------------------------------------
                        }else{
                            $sender->sendMessage($this->prefix . color::RED . "Du musst angeben wie viel Credits du beazahlen willst");
                        }
#-----------------------------------------------------------------------------------------------------------------------
                    }else{
                        $sender->sendMessage($this->prefix . color::RED . "Du musst angeben wem du Credits beazahlen willst");
                    }
#-----------------------------------------------------------------------------------------------------------------------
                }else{
                    $sender->sendMessage($this->prefix . color::RED . "Du hast keine Rechte für diesen Befehl");
                }
#-----------------------------------------------------------------------------------------------------------------------
            }else{
                    $sender->sendMessage($this->prefix . color::RED . "Du musst ein Spieler sein um diesen Befehl zu nutzen");
                }

#| Command Ende(pay)
#|
#|---------------------------------------------------------------------------------------------------------------------
#|
#| Command = givecredits

        }elseif ($cmd == "givecredits"){
#-----------------------------------------------------------------------------------------------------------------------
            if($sender instanceof Player){
#-----------------------------------------------------------------------------------------------------------------------
                if($sender->hasPermission("givecredits.cmd")){
#-----------------------------------------------------------------------------------------------------------------------
                    if(isset($args[0])){
#-----------------------------------------------------------------------------------------------------------------------
                        if(isset($args[1])){
                            $arged_player = $this->getServer()->getPlayerExact($args[0]);
#-----------------------------------------------------------------------------------------------------------------------
                            if(file_exists($this->getDataFolder() . $arged_player->getName() . ".yml")){
#-----------------------------------------------------------------------------------------------------------------------
                                if(is_numeric($args[1])){
#-----------------------------------------------------------------------------------------------------------------------
                                        if($sender->getName() == $arged_player->getName()){
#-----------------------------------------------------------------------------------------------------------------------

                                            $config = new Config($this->getDataFolder().$arged_player->getName().".yml", Config::YAML);
                                            $config->set("Credits", $config->get("Credits") + $args[1]);
                                            $config->save();
                                            $jetzt = new DateTime("now");
                                            $tra_config = new Config($this->getDataFolder() ."Transaktionen.yml", Config::YAML);
                                            $old = $tra_config->get("Transaktionen");
                                            $tra_config->set("Transaktionen",$old ."\n".$jetzt->format("d.m.Y | H:i:s")." Console [->] ".$arged_player->getName(). " Credits: ".$args[1]);
                                            $tra_config->save();
                                            $sender->sendMessage($this->prefix."Du hast dir ".$args[1]." Credits gegeben");

#-----------------------------------------------------------------------------------------------------------------------
                                        }else{
                                        $sender->sendMessage($this->prefix."Du kannst nur dir Credits geben");
                                        }
#-----------------------------------------------------------------------------------------------------------------------
                                }else{
                                    $sender->sendMessage($this->prefix. color::RED. "Du musst hier Zahlen angeben");
                                }
#-----------------------------------------------------------------------------------------------------------------------
                            }else{
                                $sender->sendMessage($this->prefix. color::RED. "Der Spieler dem du Credits geben willst muss online sein");
                            }
#-----------------------------------------------------------------------------------------------------------------------
                        }else{
                            $sender->sendMessage($this->prefix. color::RED. "Du musst angeben wie viel Credits du beazahlen willst");
                        }
#-----------------------------------------------------------------------------------------------------------------------
                    }else{
                        $sender->sendMessage($this->prefix. color::RED. "Der Spieler dem du Credits geben willst muss online sein");
                    }
#-----------------------------------------------------------------------------------------------------------------------
                }else{
                    $sender->sendMessage($this->prefix. color::RED ."Du hast keine Rechte für diesen Befehl");
                }
#-----------------------------------------------------------------------------------------------------------------------
            }else{
                $sender->sendMessage($this->prefix. color::RED ."Du musst ein Spieler sein um diesen Befehl zu nutzen");
            }

#| Command Ende(givecredits)
#|
#|---------------------------------------------------------------------------------------------------------------------
#|
#| Command = removecredits

        }elseif ($cmd == "removecredits") {
#-----------------------------------------------------------------------------------------------------------------------
            if ($sender instanceof Player) {
#-----------------------------------------------------------------------------------------------------------------------
                if ($sender->hasPermission("removecredits.cmd")) {
#-----------------------------------------------------------------------------------------------------------------------
                    if (isset($args[0])) {
#-----------------------------------------------------------------------------------------------------------------------
                        if (isset($args[1])) {
                            $arged_player = $this->getServer()->getPlayerExact($args[0]);
#-----------------------------------------------------------------------------------------------------------------------
                            if ($arged_player != null AND ($this->getDataFolder().$arged_player->getName().".yml". Config::YAML)) {
#-----------------------------------------------------------------------------------------------------------------------
                                if (is_numeric($args[1])) {
#-----------------------------------------------------------------------------------------------------------------------
                                    if($sender->getName() == $arged_player->getName()){
                                        $t_config = new Config($this->getDataFolder().$arged_player->getName().".yml", Config::YAML);
                                        $credits = $t_config->get("Credits");
#-----------------------------------------------------------------------------------------------------------------------
                                        if($credits >= (int) $args[1]) {
#-----------------------------------------------------------------------------------------------------------------------
                                            $config = new Config($this->getDataFolder().$arged_player->getName().".yml", Config::YAML);
                                            $config->set("Credits", $config->get("Credits") - $args[1]);
                                            $config->save();
                                            $sender->sendMessage($this->prefix . "Du hast dir " . $args[1] . " Credits entzogen");

#-----------------------------------------------------------------------------------------------------------------------
                                        }else{
                                        $sender->sendMessage($this->prefix. color::RED."Du wolltest dir mehr Credits entziehen als du hast");
                                        }
#-----------------------------------------------------------------------------------------------------------------------
                                    }else {
                                        $config = new Config($this->getDataFolder() . $arged_player->getName() . ".yml", Config::YAML);
                                        $credits = $config->get("Credits");
#-----------------------------------------------------------------------------------------------------------------------
                                        if ($credits >= (int)$args[1]) {
#-----------------------------------------------------------------------------------------------------------------------

                                            $config = new Config($this->getDataFolder() . $arged_player->getName() . ".yml", Config::YAML);
                                            $config->set("Credits", $config->get("Credits") - $args[1]);
                                            $config->save();
                                            $sender->sendMessage($this->prefix . "Du hast " . $arged_player->getName() . " " . $args[1] . "Credits entzogen");
                                            if($arged_player->isOnline()) {
                                                $arged_player->sendMessage($this->prefix . "Dir wurden von " . $sender->getName() . " " . $args[1] . " Credits entzogen");
                                                return true;
                                            }
#-----------------------------------------------------------------------------------------------------------------------
                                        }else{
                                            $sender->sendMessage($this->prefix. color::RED."Du wolltest ".$arged_player->getName()." mehr Credits entziehen als er hat");
                                        }
                                    }
#-----------------------------------------------------------------------------------------------------------------------
                                }elseif ($args[1] == "all"){
#-----------------------------------------------------------------------------------------------------------------------
                                    if($sender->getName() == $arged_player->getName()){
#-----------------------------------------------------------------------------------------------------------------------

                                        $config = new Config($this->getDataFolder().$arged_player->getName().".yml", Config::YAML);
                                        $all = $config->get("Credits");
                                        $config->set("Credits", $config->get("Credits") - $all);
                                        $config->save();
                                        $sender->sendMessage($this->prefix."Du hast dir ".$args[1]. " Credits entzogen");

#-----------------------------------------------------------------------------------------------------------------------
                                    }else{
#-----------------------------------------------------------------------------------------------------------------------

                                        $config = new Config($this->getDataFolder() . $arged_player->getName() . ".yml", Config::YAML);
                                        $all = $config->get("Credits");
                                        $config->set("Credits", $config->get("Credits") - $all);
                                        $config->save();
                                        $sender->sendMessage($this->prefix . "Du hast " . $arged_player->getName() . " alle Credits entzogen");
                                        $arged_player->sendMessage($this->prefix . "Dir wurden von " . $sender->getName() . " alle Credits entzogen");
                                    }

#-----------------------------------------------------------------------------------------------------------------------
                                }else{
                                    $sender->sendMessage($this->prefix .color::RED. "Du musst hier Zahlen oder all angeben");
                                }
#-----------------------------------------------------------------------------------------------------------------------
                            }else{
                                $sender->sendMessage($this->prefix. color::RED. "Der Spieler dem du Credits entziehen willst muss online sein");
                            }
#-----------------------------------------------------------------------------------------------------------------------
                        }else{
                            $sender->sendMessage($this->prefix . color::RED . "Du musst angeben wie viel Credits du entziehen willst");
                        }
#-----------------------------------------------------------------------------------------------------------------------
                    }else{
                        $sender->sendMessage($this->prefix . color::RED . "Du musst angeben wem du Credits entzeihen willst");
                    }
#-----------------------------------------------------------------------------------------------------------------------
                }else{
                    $sender->sendMessage($this->prefix. color::RED ."Du hast keine Rechte für diesen Befehl");
                }
            }else{
                $sender->sendMessage($this->prefix. color::RED ."Du musst ein Spieler sein um diesen Befehl zu nutzen");
            }
#-----------------------------------------------------------------------------------------------------------------------

        }elseif ($cmd == "setcredits") {
#-----------------------------------------------------------------------------------------------------------------------
            if ($sender instanceof Player) {
#-----------------------------------------------------------------------------------------------------------------------
                if ($sender->hasPermission("setcredits.cmd")) {
#-----------------------------------------------------------------------------------------------------------------------
                    if (isset($args[0])) {
#-----------------------------------------------------------------------------------------------------------------------
                        if (isset($args[1])) {
                            $arged_player = $this->getServer()->getPlayerExact($args[0]);
#-----------------------------------------------------------------------------------------------------------------------
                            if ($arged_player != null AND ($this->getDataFolder().$arged_player->getName().".yml". Config::YAML)) {
#-----------------------------------------------------------------------------------------------------------------------
                                if (is_numeric($args[1])) {
#-----------------------------------------------------------------------------------------------------------------------
                                    if($sender->getName() == $arged_player->getName()) {
                                        $config = new Config($this->getDataFolder().$sender->getName().".yml", Config::YAML);
                                        $config->set("Credits", $args[1]);
                                        $config->save();
                                        $jetzt = new DateTime("now");
                                        $tra_config = new Config($this->getDataFolder()."Transaktionen.yml", Config::YAML);
                                        $old = $tra_config->get("Transaktionen");
                                        $tra_config->set("Transaktionen",$old ."\n".$jetzt->format("d.m.Y | H:i:s")." ".$sender->getName()." set ".$arged_player->getName(). " Credits: ".$args[1]);
                                        $tra_config->save();
                                        $sender->sendMessage($this->prefix."Du hast deine Credits auf ".$args[1]." gesetzt");
                                    }else{
                                        $config = new Config($this->getDataFolder().$arged_player->getName().".yml", Config::YAML);
                                        $config->set("Credits", $args[1]);
                                        $config->save();
                                        $jetzt = new DateTime("now");
                                        $tra_config = new Config($this->getDataFolder()."Transaktionen.yml", Config::YAML);
                                        $old = $tra_config->get("Transaktionen");
                                        $tra_config->set("Transaktionen",$old ."\n".$jetzt->format("d.m.Y | H:i:s")." ".$sender->getName()." set ".$arged_player->getName(). " Credits: ".$args[1]);
                                        $tra_config->save();
                                        $sender->sendMessage($this->prefix."Du hast die Credits von ".$arged_player->getName()." auf ".$args[1]." gesetzt");
                                        if ($arged_player->isOnline()){
                                            $sender->sendMessage($this->prefix."Deine Credits wurden dir von ".$sender->getName()." deine Credits auf ".$args[1]." gesetzt");
                                            return true;
                                        }
#-----------------------------------------------------------------------------------------------------------------------
                                    }
#-----------------------------------------------------------------------------------------------------------------------
                                }else{
                                    $sender->sendMessage($this->prefix .color::RED. "Du musst hier Zahlen oder angeben");
                                }
#-----------------------------------------------------------------------------------------------------------------------
                            }else{
                                $sender->sendMessage($this->prefix .color::RED. "Der angegebende Spieler hat noch nie auf dem Netzwerk gespielt");
                            }
#-----------------------------------------------------------------------------------------------------------------------
                        }else{
                            $sender->sendMessage($this->prefix . color::RED . "Du musst angeben wie viel Credits du setzen willst");
                        }
#-----------------------------------------------------------------------------------------------------------------------
                    }else{
                        $sender->sendMessage($this->prefix . color::RED . "Du musst angeben von wem du Credits setzten willst");
                    }
#-----------------------------------------------------------------------------------------------------------------------
                }else{
                    $sender->sendMessage($this->prefix. color::RED ."Du hast keine Rechte für diesen Befehl");
                }
#-----------------------------------------------------------------------------------------------------------------------
            }else{
                $sender->sendMessage($this->prefix. color::RED ."Du musst ein Spieler sein um diesen Befehl zu nutzen");
            }
#-----------------------------------------------------------------------------------------------------------------------
        }
        return true;
    }
}
