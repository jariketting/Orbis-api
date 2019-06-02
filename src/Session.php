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
    /**
     * Validates session id
     */
    static function validate() : void {
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

        if($query)
            $result = $query->fetch(PDO::FETCH_OBJ)->count;
        else
            JsonResponse::error();

        if($result)
            $data['valid'] = true;

        JsonResponse::setData($data); //set data to created return data
    }
}