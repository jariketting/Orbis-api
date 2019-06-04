<?php
namespace Orbis;

use Exception;
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

        if($id > 0) {

            //build query
            /** @noinspection SqlResolve */
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
    }

    /**
     * Bind fields
     */
    abstract protected function bindFields() : void;

    /**
     * Gets fields from table
     */
    protected function getFields() : void {
        //get table structure from database
        $query = Database::get()->query('DESCRIBE '.$this->_model);
        $structure = $query->fetchAll(PDO::FETCH_OBJ);

        $this->_fields = new \stdClass(); //prevents error

        //assign each item to the fields
        foreach($structure as $item) {
            $field = $item->Field;
            $this->_fields->{$field} = null; //default value is null
        }
    }

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
                $this->_fields->$field = Post::get($field); //assign new value
        }

        //build query
        $query = 'UPDATE ' . $this->_model . ' SET';

        $values = []; //store values to update

        //go trough each field and build query and set values
        foreach ($this->_fields as $field => $value) {
            if(in_array($field, $blacklist)) continue; //skip items in blacklist

            $query .= ' '.$field.' = :'.$field.','; // the :$name part is the placeholder, e.g. :zip
            $values[':'.$field] = $value; // save the placeholder
        }

        $query = substr($query, 0, -1); // remove last , and add a ;

        $query .= ' WHERE id = :id LIMIT 1;';
        $values[':id'] = $this->_fields->id;

        $execute = Database::get()->prepare($query);

        try {
            $result = $execute->execute($values);
        } catch (Exception $e) {
            JsonResponse::error('Could not create '.$this->_model, $e->getMessage(), 500);
        }

        return $result;
    }

    /**
     * @param array $blacklist
     *
     * @return bool
     */
    protected function create(array $blacklist = ['id']) {
        $this->getFields();

        foreach ($this->_fields as $field => $value) {
            if(in_array($field, $blacklist)) continue; //skip items in blacklist

            //check if value is given in post
            if(Post::exists($field))
                $this->_fields->$field = Post::get($field);
            else
                JsonResponse::error('Missing field '.$field, '', 400);
        }

        $query = 'INSERT INTO '.$this->_model. ' SET';
        $values = [];

        foreach ($this->_fields as $field => $value) {
            if(in_array($field, $blacklist)) continue; //skip items in blacklist

            $query .= ' '.$field.' = :'.$field.','; // the :$name part is the placeholder, e.g. :zip
            $values[':'.$field] = $value; // save the placeholder
        }

        $query = substr($query, 0, -1); // remove last , and add a ;

        $execute = Database::get()->prepare($query);

        try {
            $result = $execute->execute($values);
        } catch (Exception $e) {
            JsonResponse::error('Could not create '.$this->_model, $e->getMessage(), 500);
        }

        $this->_fields->id = Database::get()->lastInsertId();

        return $result;
    }

    /**
     * Delete from database
     */
    protected function delete() {
        $id = $this->_fields->id;

        if(!$id)
            JsonResponse::error('Id not found', '', 400);

        //build query
        /** @noinspection SqlResolve */$query = Database::get()->prepare('
                DELETE 
                FROM  '.$this->_model.'
                WHERE id = :id
                LIMIT 1
            ');
        $query->bindParam(':id', $id, PDO::PARAM_INT); //bind id of model
        $query->execute(); //execute query
    }
}