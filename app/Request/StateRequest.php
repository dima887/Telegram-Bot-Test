<?php

namespace App\Request;

/**
 * Работа с состоянием переписки.
 * Состояние переписки сохраняется в базе данных
 */
class StateRequest extends Request
{
    /**
     * Добавить id пользователя в базу данных
     * Сбрасывает состояние переписки в NULL.
     * state = NULL
     */
     public function setStateNull(object $updates): bool
    {
        $id = $updates['callback_query']['message']['chat']['id'] ?? $updates["message"]["chat"]["id"];

        $data = $this->getState($updates);

        if (!empty($data)) {
            $stmt = $this->pdo->pdo->prepare("UPDATE states SET state = ? WHERE user_id = ?");
            return $stmt->execute([null, $id]);
        }

        $stmt = $this->pdo->pdo->prepare("INSERT INTO states (state, user_id) VALUES (?, ?)");
        return $stmt->execute([null, $id]);
    }

    /**
     * Получить статус состояния переписки
     */
    public function getState(object $updates): array|bool
    {
        $id = $updates['callback_query']['message']['chat']['id'] ?? $updates["message"]["chat"]["id"];

        $stmt = $this->pdo->pdo->prepare("SELECT * FROM states WHERE user_id = ?");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    /**
     * Определить статус состояния переписки.
     * state = value
     */
    function setState(string $state, object $updates): string
    {
        $id = $updates['callback_query']['message']['chat']['id'] ?? $updates["message"]["chat"]["id"];

        $stmt = $this->pdo->pdo->prepare("UPDATE states SET state = ? WHERE user_id = ?");

        return $stmt->execute([$state, $id]);
    }
}

