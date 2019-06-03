<?php
namespace Orbis;

use PDO;

/**
 * Class Model
 * @package Orbis
 */
abstract class Model
{
    protected $_fields; //store model fields

    private $_model; //store model name

    /**
     * Model constructor.
     *
     * @param string $model
     * @param string $id
     */
    public function __construct(string $model, string $id) {
        $this->_model = $model; //store model

        //build query
        $query = Database::get()->prepare('
            SELECT * 
            FROM  '.$model.'
            WHERE id = :id
            LIMIT 1
        ');
        $query->bindParam(':id', $id, PDO::PARAM_INT); //bind id of model
        $query->execute(); //execute query

        //if query was unsuccessful return error
        if(!$query) JsonResponse::error();
        else {
            $this->_fields = $query->fetch(PDO::FETCH_OBJ); //store db fields in fields

            //if field are empty give an error
            if(!$this->_fields)
                JsonResponse::error( ucfirst($model).' not found', '', 404);
        }
    }

    /**
     * Bind fields
     */
    abstract protected function bindFields() : void;

    /**
     * Update database fields
     *
     * @param array $blacklist
     *
     * @return bool
     */
    protected function update(array $blacklist = ['id']) : bool {
        foreach ($this->_fields as $field => $value) {
            if(in_array($field, $blacklist)) continue; //skip items in blacklist

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

        $query = substr($query, 0, -1); // remove last , and add a ;

        $query .= ' WHERE id = :id LIMIT 1;';
        $values[':id'] = $this->_fields->id;

        $execute = Database::get()->prepare($query);
        return $execute->execute($values);
    }
}