<?php
namespace Orbis;

use PDO;

/**
 * Class Map
 * @package Orbis
 */
class Diary
{
    /**
     * Get all users memories for the diary
     */
    public static function get() {
        $page = Post::get('page');

        if(!$page) $page = 1;

        $user = Session::getUser();
        $memoryIds = self::getMemoryIds($user->id, $page);

        $memories = [];

        if(!$memoryIds)
            JsonResponse::error('User has no memories or last page', '', 400);

        foreach ($memoryIds as $memoryId) {
            $memory = new Memory($memoryId->id);
            unset($memory->user_id);
            unset($memory->longitude);
            unset($memory->latitude);
            $memories[] = $memory;
        }

        JsonResponse::setData($memories);
    }

    private static function getMemoryIds(int $userId, int $page) : array {
        $memoryIds = [];

        $offset = (($page*10)-10);

        $query = Database::get()->prepare('
            SELECT id
            FROM memory
            WHERE user_id = :user_id
            LIMIT :offset, 10
        ');
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->execute();

        if(!$query)
            JsonResponse::error();
        else
            $memoryIds = $query->fetchAll(PDO::FETCH_OBJ);

        return $memoryIds;
    }
}