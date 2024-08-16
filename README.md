# VDoPlayer
- Processing returns to players is useful for plugins that need player rewards or something like that! PocketMine-PMMP5

# Virion Required
- [LibVapmPMMP](https://github.com/VennDev/LibVapmPMMP)

# Example
```php
/**
 * Example data:
 * "rewards" => {
 *     "reward" => "give %player% diamond 1",
 *     "reward" => ".\\quests\\quest1",
 *      ...
 */
$this->plugin->getDoPlayer()->doPlayer(
    player: $player,
    path: $pathQuest,
    data: $rewards
);
```
