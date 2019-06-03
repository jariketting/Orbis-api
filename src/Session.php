<?php
namespace Orbis;

use PDO;

/**
 * Session model class
 *
 * Class Session
 * @package Orbis
 */
class Session
{
    public static function getUser() : User {
        $sessionId = Post::get('session_id'); //get session id from post

        //give error when no session id is given
        if(!$sessionId)
            JsonResponse::error('Session id not given.', '"session_id" is missing in post data.', 400);

        $query = Database::get()->prepare('
            SELECT user_id
            FROM session
            WHERE id = :id
            LIMIT 1;
        ');
        $query->bindParam(':id', $sessionId);
        $query->execute();

        //check if query was executed successful
        if($query)
            $userId = $query->fetch(PDO::FETCH_OBJ)->user_id; //get count from query data
        else
            JsonResponse::error('Current user not found', '', 404); //something went wrong

        return new User($userId);
    }

    /**
     * Validates session id
     */
    public static function validate() : void {
        $db = Database::get(); //get database connection
        $data = ['valid' => false]; //store return data

        $sessionId = Post::get('session_id'); //get session id from post

        //give error when no session id is given
        if(!$sessionId)
            JsonResponse::error('Session id not given.', '"session_id" is missing in post data.', 400);

        //TODO add session id validation

        //return session id (for debugging)
        $data['session_id'] = $sessionId;

        /**
         * Check if session id exists in database
         */
        $query = $db->prepare('
            SELECT COUNT(*) AS count
            FROM session 
            WHERE id = :id 
            LIMIT 1
        ');
        $query->bindParam(':id', $sessionId, PDO::PARAM_STR);
        $query->execute();

        //check if query was executed successful
        if($query)
            $result = $query->fetch(PDO::FETCH_OBJ)->count; //get count from query data
        else
            JsonResponse::error(); //something went wrong

        //if result is 1, set valid to true
        if($result)
            $data['valid'] = true; //set new value

        JsonResponse::setData($data); //set data to created return data
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    public static function create(int $userId) : string {
        session_start();
        $id = session_id();

        $query = Database::get()->prepare('
            INSERT INTO session 
            SET id = :session_id, 
                user_id = :user_id
        ');
        $query->bindParam(':session_id', $id, PDO::PARAM_STR);
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if($query->execute())
            return $id;
        else {
            JsonResponse::error('Could not create session', '');
            return '';
        }
    }
}