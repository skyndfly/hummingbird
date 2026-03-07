<?php

namespace app\repositories\BotSettings;

use app\repositories\BaseRepository;

class BotSettingsRepository extends BaseRepository
{
    public const string TABLE = 'bot_settings';

    public function getCutoffHour(): int
    {
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->orderBy(['id' => SORT_ASC])
            ->one();
        if ($record === false) {
            $this->createDefault();
            return 16;
        }
        return (int) $record['cutoff_hour'];
    }

    public function updateCutoffHour(int $hour): void
    {
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->orderBy(['id' => SORT_ASC])
            ->one();
        if ($record === false) {
            $this->getCommand()->insert(
                table: self::TABLE,
                columns: [
                    'cutoff_hour' => $hour,
                    'updated_at' => $this->getCurrentDate(),
                ]
            )->execute();
            return;
        }
        $this->getCommand()->update(
            table: self::TABLE,
            columns: [
                'cutoff_hour' => $hour,
                'updated_at' => $this->getCurrentDate(),
            ],
            condition: ['id' => $record['id']]
        )->execute();
    }

    private function createDefault(): void
    {
        $this->getCommand()->insert(
            table: self::TABLE,
            columns: [
                'cutoff_hour' => 16,
                'updated_at' => $this->getCurrentDate(),
            ]
        )->execute();
    }
}
