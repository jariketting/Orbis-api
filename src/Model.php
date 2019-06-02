<?php
namespace Orbis;


use PDO;

abstract class Model
{
    protected $_fields;

    private $_model;

    public function __construct(string $model, string $id) {
        $this->_model = $model;

        $query = Database::get()->prepare('
            SELECT * 
            FROM  '.$model.'
            WHERE id = :id
            LIMIT 1
        ');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        if(!$query) JsonResponse::error();
        else {
            $this->_fields = $query->fetch(PDO::FETCH_OBJ);

            if(!$this->_fields)
                JsonResponse::error( ucfirst($model).' not found', '', 404);
        }
    }

    /**
     * Update database fields
     */
    protected function update() : bool {
        foreach ($this->_fields as $field => $value) {
            if($field == 'id') continue; //skip id

            //check if value is given in post
            if(Post::exists($field))
                $this->_fields->$field = Post::get($field);
        }

        $query = 'UPDATE ' . $this->_model . ' SET';
        $values = [];

        foreach ($this->_fields as $field => $value) {
            if($field == 'id') continue; //skip id

            $query .= ' '.$field.' = :'.$field.','; // the :$name part is the placeholder, e.g. :zip
            $values[':'.$field] = $value; // save the placeholder
        }

        $query = substr($query, 0, -1).';'; // remove last , and add a ;

        $query .= ' WHERE id = :id LIMIT 1';
        $values[':id'] = $this->_fields->id;

        $execute = Database::get()->prepare($query);
        return $execute->execute($values);
    }
}