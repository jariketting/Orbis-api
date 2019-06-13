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
        $order = Post::get('order');
        $search = Post::get('search');

        if(!$page) $page = 1;

        $user = Session::getUser();
        $memoryIds = self::getMemoryIds($user->id, $page, $order, $search);

        $memories = [];

        if(!$memoryIds)
            JsonResponse::error('User has no memories or last page', '', 400);

        foreach ($memoryIds as $memoryId) {
            $memory = new Memory($memoryId->id);
            unset($memory->user_id);
            unset($memory->longitude);
            unset($memory->latitude);

            $memory->getImages($memory->id);

            $memory->image = null;

            if(isset($memory->images[0]))
                $memory->image = $memory->images[0];

            $memories[] = $memory;
        }

        JsonResponse::setData($memories);
    }

    private static function getMemoryIds(int $userId, int $page, string $order, string $search) : array {
        $memoryIds = [];

        $offset = (($page*10)-10);

        switch ($order) {
            case 'old':
                $order = 'ASC';
                break;
            case 'new':
                //fall trough
            default:
                $order = 'DESC';
                break;
        }

        $query = Database::get()->prepare('
            SELECT id
            FROM memory
            WHERE user_id = :user_id
            '.(($search != '') ? 'AND title LIKE \'%'.$search.'%\'' : '').'
            ORDER BY datetime '.$order.'
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