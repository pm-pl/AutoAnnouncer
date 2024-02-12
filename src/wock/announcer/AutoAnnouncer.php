<?php

namespace wock\announcer;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;
use wock\announcer\Commands\AutoAnnounceCommand;
use wock\announcer\Tasks\AnnounceTask;

class AutoAnnouncer extends PluginBase {

    /** @var string[] */
    public array $messages;
    /** @var int */
    private int $currentIndex;
    /** @var string */
    private string $prefix;
    /** @var bool */
    private bool $usePrefix;

    public function onEnable(): void
    {
        $config = $this->getConfig();
        $currentVersion = $config->get("version");

        if ($currentVersion === null || $currentVersion !== "1.0.1") {
            $this->getLogger()->info($currentVersion === null ? "Updating configuration to new format" : "Updating configuration to version 1.0.1");
            $this->saveOldConfig();
            $this->saveDefaultConfig();
            $config = $this->getConfig();
        }

        $this->messages = $config->get("messages", []);
        $this->currentIndex = 0;
        $this->prefix = $config->getNested("settings.prefix", "[AA] ");
        $this->usePrefix = $config->getNested("settings.use-prefix", true);

        $interval = $config->getNested("settings.interval", 60);

        $this->getScheduler()->scheduleRepeatingTask(new AnnounceTask($this), $interval * 20);
        $this->getServer()->getCommandMap()->register("autoannouncer", new AutoAnnounceCommand($this));
    }

    public function broadcastNextMessage(): void
    {
        $config = $this->getConfig();
        $messagesConfig = $config->get("messages", []);
        $soundSetting = $config->getNested("settings.sound");
        $prefix = $this->usePrefix ? C::colorize($this->prefix) : '';
        
        if (empty($messagesConfig)) {
            return;
        }
        
        $messageConfig = $config->getNested("settings.random") === true
            ? $messagesConfig[mt_rand(0, count($messagesConfig) - 1)]['message'] ?? []
            : $messagesConfig[$this->currentIndex]['message'] ?? [];
        
        $this->currentIndex = ($this->currentIndex + 1) % count($messagesConfig);
        
        foreach ($messageConfig as $index => $messageLine) {
            $formattedLine = C::colorize($messageLine);
            $formattedMessage = $index === 0 ? $prefix . $formattedLine : $formattedLine;
            
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $player->sendMessage($formattedMessage);
                
                if ($soundSetting) {
                    $this->playSound($player, $soundSetting);
                }
            }
        }
    }

    /**
     * @param Entity $player
     * @param string $sound
     * @param int $volume
     * @param int $pitch
     * @param int $radius
     */
    public function playSound(Entity $player, string $sound, int $volume = 1, int $pitch = 1, int $radius = 5): void
    {
        foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($radius, $radius, $radius)) as $p) {
            if ($p instanceof Player) {
                $location = $p->getLocation();
                $p->getNetworkSession()->sendDataPacket(
                    PlaySoundPacket::create($sound, floatval($location->getX()), floatval($location->getY()), floatval($location->getZ()), $volume, $pitch)
                );
            }
        }
    }

    private function saveOldConfig(): void
    {
        $oldConfigPath = $this->getDataFolder() . "old_config.yml";
        $this->saveResource("config.yml", false);
        rename($this->getDataFolder() . "config.yml", $oldConfigPath);
    }
}
