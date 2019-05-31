<?php


namespace Orbis;


class JsonResponse
{
    /*
     * Storing error stuff
     */
    private $_error = false,     //true if error occurred
            $_errorTitle = '',   //title of error
            $_errorDetail = '';  //detailed error description

    private $_responseCode = 200; //response code

    private $_data = []; //data returned

    /**
     * Return error to user
     *
     * @param string $title title of error
     * @param string $detail detailed description of error
     * @param string $responseCode response code (default is 500, generic error)
     */
    public function error(string $title, string $detail, int $responseCode = 500) : void {
        $this->_error = true; //set error to true
        $this->_errorTitle = $title; //set error title
        $this->_errorDetail = $detail; //set error details
        $this->setResponseCode($responseCode); //set response code

        $this->print(); //print json response (and stop script)
    }

    /**
     * Set the response code
     *
     * @param int $code
     */
    public function setResponseCode(int $code) : void {
        $this->_responseCode = $code; //set code
    }

    /**
     * Set data in json response (this mostly will be objects)
     *
     * @param $data object|array in object or array form
     */
    public function setData($data) : void {
        $this->_data = $data;
    }

    /**
     * Print the json response to the screen and stop further execution.
     */
    public function print() : void {
        //create json object
        $json = json_encode([
            'error' => [
                'error'     => $this->_error,
                'title'     => $this->_errorTitle,
                'detail'    => $this->_errorDetail
            ],
            'data' => $this->_data
        ], JSON_FORCE_OBJECT);

        //set header and response code
        header('Content-Type: application/json');
        http_response_code($this->_responseCode);

        echo $json; //print generated json to screen

        exit(); //stop further script execution
    }
}