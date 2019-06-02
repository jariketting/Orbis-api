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
    //database fields
    public  $id,
            $user_id;

    public function __construct(string $id) {
        $query = Database::get()->prepare('
            SELECT * 
            FROM session 
            WHERE id = :id
            LIMIT 1
        ');
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();

        if(!$query)
            JsonResponse::error('Session not found', 'The session id was invalid or not found.', 404);
        else {
            $session = $query->fetch(PDO::FETCH_OBJ);

            $this->id       = (string)$session->id;
            $this->user_id  = (int)$session->user_id;
        }
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
}