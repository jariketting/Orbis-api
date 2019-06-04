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

        if(!$query)
            JsonResponse::error('Current user not found', '', 404); //something went wrong

        $user = (object)$query->fetch(PDO::FETCH_OBJ);

        if(!isset($user->user_id))
            JsonResponse::error('User not found or logged in', '', 404);

        return new User($user->user_id);
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
        $id = session_create_id();

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

    /**
     * Login with username and password
     */
    public static function login() : void {
        //check if email and password where set
        if(!Post::exists('email') || !Post::exists('password'))
            JsonResponse::error('Email or password missing', '', 400);

        //get email and password
        $email = Post::get('email');
        $password = Post::get('password');

        //build query to get password and user id by email
        $query = Database::get()->prepare('
            SELECT id, password
            FROM user
            WHERE email = :email
            LIMIT 1
        ');
        $query->bindParam(':email', $email); //bind email
        $query->execute();

        //check if query was successful
        if(!$query)
            JsonResponse::error();
        else
            $user = $query->fetch(PDO::FETCH_OBJ);

        //check if user was found
        if(!$user)
            JsonResponse::error('No user found with that email', '', 404);
        else {
            //verify password
            if(!Password::verify($password, $user->password))
                JsonResponse::error('Wrong password', '', 401); //wrong password
            else {
                //get user with logged in user id
                $user = new User($user->id);
                $user->session_id = self::create($user->id); //create new sessions and add to data

                //send logged in user back
                JsonResponse::setData($user);
            }
        }
    }

    /**
     * Logout
     */
    public static function logout() : void {
        //check if session id is given
        if(!Post::exists('session_id'))
            JsonResponse::error('Session id not given', '', 400);

        //get session id
        $id = Post::get('session_id');

        //build query to delete any session
        $query = Database::get()->prepare('
            DELETE FROM session 
            WHERE id = :id 
            LIMIT 1
        ');
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $result = $query->execute();

        //check if successful logout
        if(!$result)
            JsonResponse::error('Could not log out', '', 500);
    }
}