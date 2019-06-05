<?php
namespace Orbis;

use PDO;

/**
 * Class Map
 * @package Orbis
 */
class Map
{
    /**
     * Get all users memories for the map
     */
    public static function get() {
        $user = Session::getUser();
        $memoryIds = self::getMemoryIds($user->id);

        $memories = [];

        if(!$memoryIds)
            JsonResponse::error('User has no memories', '', 400);

        foreach ($memoryIds as $memoryId) {
            $memory = new Memory($memoryId->id);
            unset($memory->description);
            unset($memory->datetime);
            unset($memory->user_id);
            $memories[] = $memory;
        }

        JsonResponse::setData($memories);
    }

    private static function getMemoryIds(int $userId) : array {
        $memoryIds = [];

        $query = Database::get()->prepare('
            SELECT id
            FROM memory
            WHERE user_id = :user_id
        ');
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();

        if(!$query)
            JsonResponse::error();
        else
            $memoryIds = $query->fetchAll(PDO::FETCH_OBJ);

        return $memoryIds;
    }
}