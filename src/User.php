<?php
namespace Orbis;

use PDO;

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

    public function __construct(string $id) {
        parent::__construct(self::MODEL, $id);

        $this->id = (int)$this->_fields->id;
        $this->username = (string)$this->_fields->username;
        $this->email = (string)$this->_fields->email;
        $this->notifications = (bool)$this->_fields->notifications;
        $this->private = (bool)$this->_fields->private;
        $this->bio = (string)$this->_fields->bio;
        $this->image_id = (int)$this->_fields->image_id;
    }

    public static function request() {
        switch (Router::getAction()) {
            case 'get':
                self::getRequest();
                break;
            case 'update':
                self::updateRequest();
                break;
            default:
                JsonResponse::error('Invalid action', 'An invalid action was given', 400);
        }
    }

    private static function getRequest() {
        $id = Router::getIdentifier();

        if(!$id) JsonResponse::error('No identifier given', '', 400);
        else JsonResponse::setData(new User($id));
    }

    private static function updateRequest() {
        $id = Router::getIdentifier();
        $response = ['success' => false];

        $user = new User($id);
        if($user->update())
            $response['success'] = true;

        if(!$id)
            JsonResponse::error('No identifier given', '', 400);
        else
            JsonResponse::setData((object)$response);
    }
}