<?php
namespace Orbis;


class File extends Model {
    private const MODEL = 'file';

    private CONST IMAGE_DIR = '../public/images/';

    private $_publicImageDir;

    //database fields
    public  $id,
            $filename,
            $file_extension,
            $uri;

    /**
     * File constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->_publicImageDir = Config::getConfig()['WEBSITE']['url'].'images/';

        parent::__construct(self::MODEL, $id);

        //only bind fields if user id is given, 0 means new user
        if($id > 0)
            $this->bindFields();
    }

    /**
     * Bind fields
     */
    protected function bindFields(): void {
        $this->id = (int)$this->_fields->id;
        $this->filename = (String)$this->_fields->filename;
        $this->file_extension = (String)$this->_fields->file_extension;

        $this->uri = $this->_publicImageDir.$this->filename.$this->file_extension;
    }

    /**
     * Add new image
     */
    public static function addImage() : void {
        if(!Post::exists('session_id'))
            JsonResponse::error('Missing session id', '', 400);

        if(!Post::exists('bitmap'))
            JsonResponse::error('Missing bitmap', '', 400);

        $bitmap = Post::get('bitmap');

        $filename =  uniqid();
        $extension = '.jpg';

        $file = self::IMAGE_DIR;
        $file .= $filename;
        $file .= $extension;

        self::_saveImage($file, $bitmap);

        $memory = new File(0);

        Post::overwrite('filename', $filename);
        Post::overwrite('file_extension', $extension);

        $memory->create(); //create user
        $memory->bindFields(); //bind fields

        JsonResponse::setData($memory);
    }

    /**
     * Saves image to disk
     *
     * @param String $bitmap
     */
    private static function _saveImage(String $filename, String $bitmap) {
        $ifp = fopen($filename, 'w');
        fwrite($ifp, base64_decode($bitmap));
        fclose($ifp);
    }
}