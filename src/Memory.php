<?php
namespace Orbis;

use PDO;

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
            $user_id,
            $images;

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
        $this->images = [];
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

        $query = Database::get()->prepare('
            SELECT file_id
            FROM memory_file
            WHERE memory_id = :memory_id
        ');
        $query->bindParam(':memory_id', $memory->id, PDO::PARAM_INT);
        $query->execute();

        if(!$query)
            JsonResponse::error('Could not get images');

        $images = $query->fetchAll(PDO::FETCH_OBJ);

        if($images) {
            foreach($images as $image) {
                $memory->images[] = new File($image->file_id);
            }
        }

        JsonResponse::setData($memory);
    }

    private static function updateRequest() : void {
        $id = Router::getIdentifier();

        $memory = new Memory($id);

        if(!$memory->id)
            JsonResponse::error('Memory not found', 'Could not find this memory', 404);

        if(!$memory->user_id == Session::getUser()->id)
            JsonResponse::error('Can only delete own memories','You can not delete others memories!', 400);

        $memory->update();
        $memory->bindFields(); //rebind fields

        if(Post::exists('images'))
            $memory->setImages(Post::get('images'));

        JsonResponse::setData($memory); //send new memory data back
    }

    private static function addRequest() : void {
        $memory = new Memory(0);

        Post::overwrite('user_id', Session::getUser()->id);

        $memory->create(); //create user
        $memory->bindFields(); //bind fields

        if(Post::exists('images'))
            $memory->setImages(Post::get('images'));

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

    private function setImages(Array $ids) : void {
        $delete = Database::get()->prepare('
        DELETE FROM memory_file
        WHERE memory_id = :memory_id
        ');
        $delete->bindParam(':memory_id', $this->id, PDO::PARAM_INT);
        $delete->execute();

        if($ids) {
            $query = Database::get()->prepare('
                INSERT INTO memory_file
                (memory_id, file_id) VALUES (:memory_id, :file_id)
            ');

            foreach ($ids as $id) {
                $query->bindParam(':memory_id', $this->id, PDO::PARAM_STR);
                $query->bindParam(':file_id', $id, PDO::PARAM_STR);
                $query->execute();

                $this->images[] = new File($id);
            }
        }
    }
}