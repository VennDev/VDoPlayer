<?php

declare(strict_types=1);

namespace venndev\vdoplayer;

use Generator;
use RuntimeException;
use Throwable;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use vennv\vapm\CoroutineGen;
use vennv\vapm\VapmPMMP;

final readonly class VDoPlayer
{

    public function __construct(private PluginBase $plugin)
    {
        VapmPMMP::init($plugin);
    }

    /**
     * @param Player $player - The player
     * @param string $path - The path to the directory
     * @param array $data - The array of data
     * @throws Throwable
     *
     * Example we have $data is:
     * [
     *      "path/to/file.php",
     *      "path/to/directory"
     *      "say %player% Hello"
     * ]
     */
    public function doPlayer(
        Player $player, string $path, array $data
    ): void
    {
        // Run the coroutine to prevent blocking the main thread
        CoroutineGen::runBlocking(function () use ($player, $path, $data): Generator {
            try {
                $fncDoPlayer = function ($player, $value) {
                    if (isset($main)) {
                        $main($player);
                    } else {
                        throw new RuntimeException("Function `main` not found in $value");
                    }
                };
                foreach ($data as $key => $value) {
                    if (is_dir($path . $value)) {
                        $dir = scandir($path . $value);
                        foreach ($dir as $file) {
                            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                                require_once $path . $value . DIRECTORY_SEPARATOR . $file;
                                $fncDoPlayer($player, $file);
                            }
                            yield;
                        }
                    } elseif (is_file($value)) {
                        require_once $value;
                        $fncDoPlayer($player, $value);
                    } else {
                        $server = $this->plugin->getServer();
                        $server->dispatchCommand(
                            new ConsoleCommandSender($server, $server->getLanguage()),
                            str_replace("%player%", $player->getName(), $value)
                        );
                    }
                    yield;
                }
            } catch (Throwable $e) {
                $this->plugin->getLogger()->error($e->getMessage());
            }
        });
    }

    public function getPlugin(): PluginBase
    {
        return $this->plugin;
    }

}