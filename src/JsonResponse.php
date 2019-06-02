<?php
namespace Orbis;

/**
 * Creates json response
 *
 * Class JsonResponse
 * @package Orbis
 */
class JsonResponse
{
    /*
     * Storing error stuff
     */
    private static  $_error = false,     //true if error occurred
                    $_errorTitle = '',   //title of error
                    $_errorDetail = '';  //detailed error description

    private static  $_responseCode = 200; //response code

    private static  $_data = []; //data returned

    /**
     * Return error to user
     *
     * @param string $title title of error
     * @param string $detail detailed description of error
     * @param string $responseCode response code (default is 500, generic error)
     */
    public static function error(string $title = 'Something went wrong', string $detail = 'Unknown error occurred.', int $responseCode = 500) : void {
        self::$_error = true; //set error to true
        self::$_errorTitle = $title; //set error title
        self::$_errorDetail = $detail; //set error details
        self::setResponseCode($responseCode); //set response code

        self::print(); //print json response (and stop script)
    }

    /**
     * Set the response code
     *
     * @param int $code
     */
    public static function setResponseCode(int $code) : void {
        self::$_responseCode = $code; //set code
    }

    /**
     * Set data in json response (this mostly will be objects)
     *
     * @param $data object|array in object or array form
     */
    public static function setData($data) : void {
        self::$_data = $data;
    }

    /**
     * Print the json response to the screen and stop further execution.
     */
    public static function print() : void {
        //create json object
        $json = json_encode([
            'error' => [
                'error'     => self::$_error,
                'title'     => self::$_errorTitle,
                'detail'    => self::$_errorDetail
            ],
            'data' => self::$_data
        ], JSON_FORCE_OBJECT);

        //set header and response code
        header('Content-Type: application/json');
        http_response_code(self::$_responseCode);

        echo $json; //print generated json to screen

        exit(); //stop further script execution
    }
}