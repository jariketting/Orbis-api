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
    public static function follow() : void {
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
            ON DUPLICATE KEY UPDATE following_id = following_id
        ');
        $query->bindParam(':user_id', $user->id, PDO::PARAM_INT);
        $query->bindParam(':following_id', $followUser->id, PDO::PARAM_INT);
        $query->execute();

        //check if query is correct
        if(!$query)
            JsonResponse::error();
    }

    /**
     * Unfollow user
     */
    public static function unfollow() : void {
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

    /**
     * Get followers
     */
    public static function getFollowers() : void {
        $id = Post::get('user_id');

        //if id not given, return current logged in user
        if(!$id)
            $user = Session::getUser();
        else
            $user = new User($id);

        $users = [];

        $query = Database::get()->prepare('
            SELECT user_id
            FROM follow
            WHERE following_id = :user_id
        ');
        $query->bindParam(':user_id', $user->id, PDO::PARAM_INT);
        $query->execute();

        if(!$query)
            JsonResponse::error();
        else
            $userIds = $query->fetchAll(PDO::FETCH_OBJ);

        if(!$userIds)
            JsonResponse::error('No followers', '', 404);

        foreach ($userIds as $userId) {
            $user = new User($userId->user_id);
            unset($user->email);
            unset($user->notifications);
            unset($user->private);
            unset($user->bio);
            $users[] = $user;
        }

        JsonResponse::setData($users);
    }

    /**
     * Get followers
     */
    public static function getFollowing() : void {
        $id = Post::get('user_id');

        //if id not given, return current logged in user
        if(!$id)
            $user = Session::getUser();
        else
            $user = new User($id);

        $users = [];

        $query = Database::get()->prepare('
            SELECT user_id
            FROM follow
            WHERE following_id = :user_id
        ');
        $query->bindParam(':user_id', $user->id, PDO::PARAM_INT);
        $query->execute();

        if(!$query)
            JsonResponse::error();
        else
            $userIds = $query->fetchAll(PDO::FETCH_OBJ);

        if(!$userIds)
            JsonResponse::error('No followers', '', 404);

        foreach ($userIds as $userId) {
            $user = new User($userId->user_id);
            unset($user->email);
            unset($user->notifications);
            unset($user->private);
            unset($user->bio);
            $users[] = $user;
        }

        JsonResponse::setData($users);
    }
}