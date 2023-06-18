<?php

namespace wock\announcer;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;

class AutoAnnouncer extends PluginBase {

    /** @var string[] */
    private array $messages;
    /** @var int */
    private int $currentIndex;
    /** @var string */
    private string $prefix;
    /** @var bool */
    private bool $usePrefix;

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $config = $this->getConfig();
        $this->messages = $config->get("messages", []);
        $this->currentIndex = 0;
        $this->prefix = $config->get("prefix", "[AA]");
        $this->usePrefix = $config->get("use-prefix", true);

        $interval = $config->get("interval", 1200); // Default interval: 60 seconds (20 ticks per second)

        $this->getScheduler()->scheduleRepeatingTask(new AnnounceTask($this), $interval);
    }

    public function broadcastNextMessage(): void {
        if (count($this->messages) > 0) {
            $message = $this->messages[$this->currentIndex];
            if ($this->usePrefix) {
                $message = $this->prefix . $message;
            }
            $formattedMessage = $this->formatMessage($message);

            $players = $this->getServer()->getOnlinePlayers();
            foreach ($players as $player) {
                $player->sendMessage($formattedMessage);
            }

            $this->currentIndex = ($this->currentIndex + 1) % count($this->messages);
        }
    }

    public function formatMessage(string $message): string {
        $message = str_replace("&", "§", $message);
        $message = str_replace("\\n", PHP_EOL, $message);
        return $message;
    }
}

