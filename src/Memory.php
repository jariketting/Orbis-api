<?php
namespace Orbis;

/**
 * Class Memory
 * @package Orbis
 */
class Memory extends Model {
    private const MODEL = 'memory';

    //database fields
    public  $id,
            $title,
            $description,
            $longitude,
            $latitude,
            $datetime,
            $user_id;

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
        $this->title = (string)$this->_fields->title;
        $this->description = (string)$this->_fields->description;
        $this->longitude = (string)$this->_fields->longitude;
        $this->latitude = (string)$this->_fields->latitude;
        $this->datetime = (string)$this->_fields->datetime;
        $this->user_id = (string)$this->_fields->user_id;
    }

    /**
     * Request handler
     */
    public static function request() : void {
        if(!Post::exists('session_id'))
            JsonResponse::error('Session id required', 'Session id is missing in post data', 403);

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
            case 'delete':
                self::deleteRequest();
                break;
            default:
                JsonResponse::error('Invalid action', 'An invalid action was given', 400);
                break;
        }
    }

    /**
     * Get memory request
     */
    private static function getRequest() : void {
        $id = Router::getIdentifier();

        //if id not given, return latest memory
        if(!$id) {
            $memory = Session::getUser()->getLatestMemory();
        } else
            $memory = new Memory($id);

        JsonResponse::setData($memory);
    }

    private static function updateRequest() : void {

    }

    private static function addRequest() : void {
        $memory = new Memory(0);
        $memory->create(); //create user
        $memory->bindFields(); //bind fields

        JsonResponse::setData($memory);
    }

    private static function deleteRequest() : void {
        $id = Router::getIdentifier();

        $memory = new Memory($id);

        if(!$memory->id)
            JsonResponse::error('Memory not found', 'Could not find this memory', 404);

        if(!$memory->user_id == Session::getUser()->id)
            JsonResponse::error('Can only delete own memories','You can not delete others memories!', 400);

        $memory->delete();
    }
}