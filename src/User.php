<?php
namespace Orbis;

use PDO;

/**
 * Class User
 * @package Orbis
 */
class User extends Model
{
    private const MODEL = 'user';

    //database fields
    public  $id,
            $username,
            $email,
            $notifications,
            $private,
            $bio,
            $image_id;

    /**
     * User constructor.
     *
     * @param int $id
     */
    public function __construct(int $id) {
        parent::__construct(self::MODEL, $id);

        //only bind fields if user id is given, 0 means new user
        if($id > 0)
            $this->bindFields();
    }

    /**
     * Bind fields
     */
    protected function bindFields() : void {
        $this->id = (int)$this->_fields->id;
        $this->username = (string)$this->_fields->username;
        $this->email = (string)$this->_fields->email;
        $this->name = (string)$this->_fields->name;
        $this->notifications = (bool)$this->_fields->notifications;
        $this->private = (bool)$this->_fields->private;
        $this->bio = (string)$this->_fields->bio;
        $this->image_id = (int)$this->_fields->image_id;
    }

    /**
     * Request handler
     */
    public static function request() : void {
        $sessionId = Post::exists('session_id');

        switch (Router::getAction()) {
            case 'get':
                if(!$sessionId) self::sessionIdError();
                self::getRequest();
                break;
            case 'update':
                if(!$sessionId) self::sessionIdError();
                self::updateRequest();
                break;
            case 'add':
                if($sessionId) self::sessionIdError();
                self::addRequest();
                break;
            case 'delete':
                if(!$sessionId) JsonResponse::error('Session id required', 'Session id is missing in post data', 403);
                self::deleteRequest();
                break;
            default:
                JsonResponse::error('Invalid action', 'An invalid action was given', 400);
                break;
        }
    }

    /**
     * Error when session id missing
     */
    private static function sessionIdError() {
        JsonResponse::error('Session id required', 'Session id is missing in post data', 403);
    }

    /**
     * handler for get request
     */
    private static function getRequest() : void {
        $id = Router::getIdentifier();

        //if id not given, return current logged in user
        if(!$id)
            $user = Session::getUser();
        else
            $user = new User($id);

        JsonResponse::setData($user);
    }

    /**
     * Handler for update request
     */
    private static function updateRequest() {
        $user = Session::getUser();

        //overwrite password with encrypted
        $password = Post::get('password');
        if($password)
            Post::overwrite('password', Password::encrypt($password));

        $user->update(); //update user data
        $user->bindFields(); //rebind fields

        JsonResponse::setData($user); //send new user data back
    }

    /**
     * Registration of new user
     */
    private static function addRequest() {
        //TODO validate password
        $password = Post::get('password');
        if($password)
            Post::overwrite('password', Password::encrypt($password));

        //create new user
        $user = new User(0);
        $user->create(); //create user
        $user->bindFields(); //bind fields

        $user->session_id = Session::create($user->id); //log user in and send back session id.

        JsonResponse::setData($user);
    }

    /**
     * Delete user request
     */
    private static function deleteRequest() {
        $user = Session::getUser();
        $user->delete();
    }

    public function getLatestMemory() : Memory {
        $query = Database::get()->prepare('
            SELECT id
            FROM memory
            WHERE user_id = :user_id
            ORDER BY datetime DESC
            LIMIT 1
        ');
        $query->bindParam(':user_id', $this->id, PDO::PARAM_INT);
        $query->execute();

        //check if query is correct
        if(!$query)
            JsonResponse::error();
        else
            $id = $query->fetch(PDO::FETCH_OBJ)->id; //extract id

        if(!$id)
            JsonResponse::error('User has no memories', '', 404);

        return new Memory($id);
    }

    /**
     * Reset password
     */
    public static function resetPassword() : void {
        //check if email is given
        if(!Post::exists('email'))
            JsonResponse::error('Email missing', '', 400);

        $email = Post::get('email'); //get email

        $query = Database::get()->prepare('
            SELECT id
            FROM user
            WHERE email = :email
            LIMIT 1
        ');
        $query->bindParam(':email', $email);
        $query->execute();

        //check if query is correct
        if(!$query)
            JsonResponse::error();
        else
            $id = $query->fetch(PDO::FETCH_OBJ)->id; //extract id

        //check if id is found
        if(!$id)
            JsonResponse::error('No user found with that email', '', 404);
        else {
            //generate a password
            $newPassword = Password::generate(10);
            $encryptedPassword = Password::encrypt($newPassword);

            //build query to update password
            $query = Database::get()->prepare('
                UPDATE user
                SET password = :password
                WHERE id = :id
                LIMIT 1
            ');
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':password', $encryptedPassword, PDO::PARAM_STR);
            $result = $query->execute();

            //check if reset was successful
            if(!$result)
                JsonResponse::error('Could not update password');
            else {
                //delete any sessions
                $query = Database::get()->prepare('
                    DELETE FROM session 
                    WHERE user_id = :user_id
                ');
                $query->bindParam(':user_id', $id, PDO::PARAM_INT);
                $query->execute();

                //get user
                $user = new User($id);

                //build email
                $msg = "Your new password is:\n";
                $msg .= $newPassword;
                $msg = wordwrap($msg, 70); //wrap content for mailing

                //set headers
                $headers = "From: orbis@jariketting.com" . "\r\n";

                //send email
                mail($user->email, "Orbis: new password", $msg, $headers);
            }
        }
    }
}