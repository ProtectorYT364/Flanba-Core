<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore;


use alemiz\sga\StarGateAtlantis;
use muqsit\invmenu\InvMenuHandler;
use muqsit\simplepackethandler\SimplePacketHandler;
use paroxity\portal\Portal;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\ServerException;
use pocketmine\utils\SingletonTrait;
use sergittos\flanbacore\arena\ArenaFactory;
use sergittos\flanbacore\command\party\PartyCommand;
use sergittos\flanbacore\command\tempc\PingCommand;
use sergittos\flanbacore\listener\ClaimListener;
use sergittos\flanbacore\listener\FlanbaListener;
use sergittos\flanbacore\listener\ItemListener;
use sergittos\flanbacore\listener\LobbyListener;
use sergittos\flanbacore\listener\MatchListener;
use sergittos\flanbacore\listener\PartyListener;
use sergittos\flanbacore\listener\ScoreboardListener;
use sergittos\flanbacore\listener\SessionListener;
use sergittos\flanbacore\listener\SlotsListener;
use sergittos\flanbacore\listener\TeamListener;
use sergittos\flanbacore\map\MapFactory;
use sergittos\flanbacore\match\MatchManager;
use sergittos\flanbacore\provider\presets\JsonProvider;
use sergittos\flanbacore\provider\presets\SqliteProvider;
use sergittos\flanbacore\provider\presets\YamlProvider;
use sergittos\flanbacore\provider\Provider;
use sergittos\flanbacore\queue\QueueManager;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ConfigGetter;
use sergittos\flanbacore\command\tempc\HubCommand;
use thebarii\partyengine\PartyEngine;


class FlanbaCore extends PluginBase {
    use SingletonTrait;

    private Provider $provider;

    private MatchManager $match_manager;
    private QueueManager $queue_manager;
    private PartyEngine $partyEngine;
    public array $dcommands = ["kill", "suicide", "me", "about", "ver", "version", "clear"];


    protected function onLoad(): void {
        self::setInstance($this);

        $data_folder = $this->getDataFolder();
        $players_dir = $data_folder . "players";
        if(!is_dir($players_dir)) {
            mkdir($players_dir);
        }
        $maps_dir = $data_folder . "maps";
        if(!is_dir($maps_dir)) {
            mkdir($maps_dir);
        }
        $this->saveDefaultConfig();
    }

    protected function onEnable(): void {
	    
	     foreach ($this->dcommands as $command) {
            $commandMap = $this->getServer()->getCommandMap();
            $cmd = $commandMap->getCommand($command);
            if ($cmd == null) {
                $this->getLogger()->error("Command /" . $command . " not found");
            }else{
                $commandMap->unregister($cmd);
            }
        }
	    
	    
        ConfigGetter::init();
        $this->getServer()->getWorldManager()->loadWorld(ConfigGetter::getLobbyWorldName(), true);

        MapFactory::init();
        ArenaFactory::init();
        $this->initProvider();
        $this->match_manager = new MatchManager();
        $this->queue_manager = new QueueManager();
        $partyEngine = $this->getServer()->getPluginManager()->getPlugin("Party Engine");

        if($partyEngine instanceof PartyEngine)
            $this->partyEngine = $partyEngine;
        $this->registerListener(new ClaimListener());
        $this->registerListener(new FlanbaListener($this));
		$this->registerListener(new ScoreboardListener());
        $this->registerListener(new ItemListener());
        $this->registerListener(new LobbyListener());
        $this->registerListener(new MatchListener());
        $this->registerListener(new PartyListener());
        $this->registerListener(new SessionListener());
        $this->registerListener(new SlotsListener());
        $this->registerListener(new TeamListener());

        $this->registerCommand(new HubCommand());
        $this->registerCommand(new PartyCommand());
		$this->registerCommand(new PingCommand());

		$scheduler = $this->getScheduler();
		$scheduler->scheduleRepeatingTask(new FlanbaHeartbeat(), 20); // 1 second
        $this->doMuqsitThings();
    }

    protected function onDisable(): void {
        foreach(SessionFactory::getSessions() as $session) {
            $session->save();
        }

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            StarGateAtlantis::getInstance()->transferPlayer($player, "Hub-1");
        }
    }

    private function doMuqsitThings(): void {
        if(!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        static $send = false;
        SimplePacketHandler::createInterceptor($this)
            ->interceptIncoming(function(ContainerClosePacket $packet, NetworkSession $session) use (&$send): bool {
                $send = false;
                $session->sendDataPacket($packet);
                $send = true;
                return true;
            })
            ->interceptOutgoing(function(ContainerClosePacket $packet, NetworkSession $session) use (&$send): bool {
                return $send;
            });

        $this->getScheduler()->scheduleRepeatingTask(new class extends Task {

            public function onRun(): void {
                foreach(Server::getInstance()->getNetwork()->getInterfaces() as $interface) {
                    if($interface instanceof RakLibInterface) {
                        $interface->setPacketLimit(PHP_INT_MAX);
                        $this->getHandler()?->cancel();
                    }
                }
            }

        }, 20);
    }

    private function initProvider(): void {
        $provider = strtolower(ConfigGetter::getProvider());

        $this->provider = match($provider) {
            "sqlite", "sqlite3" => new SqliteProvider(),
            "yml", "yaml" => new YamlProvider(),
            "json" => new JsonProvider(),
            default => throw new ServerException("Unknown provider")
        };
    }

    private function registerListener(Listener $listener): void {
        $this->getServer()->getPluginManager()->registerEvents($listener, $this);
    }

    private function registerCommand(Command $command): void {
        $this->getServer()->getCommandMap()->register("flanbacore", $command);
    }

    public function getProvider(): Provider {
        return $this->provider;
    }

    public function getMatchManager(): MatchManager {
        return $this->match_manager;
    }

    public function getQueueManager(): QueueManager {
        return $this->queue_manager;
    }

}
