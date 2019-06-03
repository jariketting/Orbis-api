<?php
namespace Orbis;

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
        switch (Router::getAction()) {
            case 'get':
                self::getRequest();
                break;
            case 'update':
                self::updateRequest();
                break;
            case 'add':
                self::addRequest();
                break;
            default:
                JsonResponse::error('Invalid action', 'An invalid action was given', 400);
        }
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
        $user->update(['id', 'password']);
        $user->bindFields();

        JsonResponse::setData($user);
    }

    /**
     * Registration of new user
     */
    private static function addRequest() {

        //only allow register when user not logged in
        if(Post::get('session_id'))
            JsonResponse::error('Can not register when logged in', 'You can not register a new account when logged in.', 403);

        //TODO validate password
        $password = Post::get('password');
        if($password)
            Post::overwrite('password', Password::encrypt($password));

        $user = new User(0);
        $user->create();
        $user->bindFields();

        $user->session_id = Session::create($user->id);

        JsonResponse::setData($user);
    }
}