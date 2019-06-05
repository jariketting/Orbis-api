<?php
namespace Orbis;

use PDO;

/**
 * Class Follow
 * @package Orbis
 */
class Follow
{
    /**
     * Follow a user
     */
    public static function follow() {
        $userId = Post::get('user_id');

        if(!$userId)
            JsonResponse::error('Missing user id to follow', '', 400);

        $followUser = new User($userId);
        $user = Session::getUser();

        if($followUser->id == $user->id)
            JsonResponse::error('Cannot follow yourself', '', 400);

        $query = Database::get()->prepare('
            INSERT INTO follow
            (user_id, following_id) 
            VALUES 
            (:user_id, :following_id)
        ');
        $query->bindParam(':user_id', $user->id, PDO::PARAM_INT);
        $query->bindParam(':following_id', $followUser->id, PDO::PARAM_INT);
        $query->execute();

        //check if query is correct
        if(!$query)
            JsonResponse::error();
    }

    public static function unfollow() {
        $userId = Post::get('user_id');

        if(!$userId)
            JsonResponse::error('Missing user id to follow', '', 400);

        $user = new User($userId);

        $query = Database::get()->prepare('
            DELETE FROM follow
            WHERE following_id = :following_id
            LIMIT 1
        ');
        $query->bindParam(':following_id', $user->id, PDO::PARAM_INT);
        $query->execute();

        //check if query is correct
        if(!$query)
            JsonResponse::error();
    }
}