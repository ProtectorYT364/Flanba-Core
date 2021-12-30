<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore;


use flanbacore\listener\ItemListener;
use flanbacore\listener\MatchListener;
use flanbacore\listener\PartyListener;
use flanbacore\listener\QueueListener;
use flanbacore\listener\SessionListener;
use flanbacore\match\MatchHeartbeat;
use flanbacore\match\MatchManager;
use flanbacore\provider\presets\JsonProvider;
use flanbacore\provider\presets\SqliteProvider;
use flanbacore\provider\presets\YamlProvider;
use flanbacore\provider\Provider;
use flanbacore\queue\QueueManager;
use flanbacore\session\SessionFactory;
use flanbacore\utils\ConfigGetter;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\ServerException;
use pocketmine\utils\SingletonTrait;

class FlanbaCore extends PluginBase {
    use SingletonTrait;

    private Provider $provider;

    private MatchManager $match_manager;
    private QueueManager $queue_manager;

    protected function onLoad(): void {
        self::setInstance($this);

        $this->saveDefaultConfig();
        $this->saveResource("scoreboard.yml");
    }

    protected function onEnable(): void {
        if(!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        ConfigGetter::init();

        $this->initProvider();

        $this->match_manager = new MatchManager();
        $this->queue_manager = new QueueManager();

        $this->registerListener(new ItemListener());
        $this->registerListener(new MatchListener());
        $this->registerListener(new PartyListener());
        $this->registerListener(new QueueListener());
        $this->registerListener(new SessionListener());

        $this->getScheduler()->scheduleRepeatingTask(new MatchHeartbeat(), 20); // 1 second
    }

    protected function onDisable(): void {
        foreach(SessionFactory::getSessions() as $session) {
            $session->save();
        }
    }

    private function initProvider(): void {
        $provider = strtolower($this->getConfig()->get("provider"));

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