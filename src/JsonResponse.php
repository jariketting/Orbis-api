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
     * Set error
     *
     * @param string $title
     * @param string $detail
     */
    public function error(string $title, string $detail, int $responseCode = 500) : void {
        $this->_error = true;
        $this->_errorTitle = $title;
        $this->_errorDetail = $detail;
        $this->setResponseCode($responseCode);

        $this->print();
    }

    public function setResponseCode(int $code) : void {
        $this->_responseCode = $code;
    }

    public function setData($data) : void {
        $this->_data = $data;
    }

    public function print() : void {
        $json = json_encode([
            'error' => [
                'error'     => $this->_error,
                'title'     => $this->_errorTitle,
                'detail'    => $this->_errorDetail
            ],
            'data' => $this->_data
        ], JSON_FORCE_OBJECT);

        header('Content-Type: application/json');
        http_response_code($this->_responseCode);

        echo $json;

        exit();
    }
}