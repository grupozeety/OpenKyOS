<?php

class OpenProject {

    private $_curl_timeout = 30;
    private $_limit_page_length = 50;
    private $type = 'json';
    private $token = "";
    private $_api_url_v2 = "";
    private $_api_url_v3 = "";
    public $errorno = 0;
    public $error;
    public $body;
    public $header;

    public function configurar($datos) {
        $this->_api_url_v2 = $datos['host'] . $datos['api_url_v2'];
        $this->_api_url_v3 = $datos['host'] . $datos['api_url_v3'];
        $this->type = $datos['type'];
        $this->token = $datos['token'];
    }

    public function search($rb, $conditions = '', $fields = '', $version_api = '') {

        if ($version_api != 'v3') {
            try {

                if ($conditions == '') {
                    $url = $this->_api_url_v2 . "/" . $rb . "/" . $fields . "." . $this->type . "?key=" . $this->token;
                } else {
                    $url = $this->_api_url_v2 . "/" . $rb . "/" . $conditions . "/" . $fields . "." . $this->type . "?key=" . $this->token;
                }

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_timeout);
                $response = curl_exec($ch);

                $this->header = curl_getinfo($ch);
                $error_no = curl_errno($ch);
                $error = curl_error($ch);
                curl_close($ch);
                if ($error_no) {
                    $this->error_no = $error_no;
                }
                if ($error) {
                    $this->error = $error;
                }
                $this->body = @json_decode($response, true);

                if (JSON_ERROR_NONE != json_last_error()) {
                    $this->body = $response;
                }

                return $this;

            } catch (Exception $e) {

                trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                    E_USER_ERROR);

            }
        } elseif ($version_api == 'v3') {

            try {

                if ($conditions == '') {
                    $url = $this->_api_url_v3 . "/" . $rb . "/" . $fields;
                } else {
                    $url = $this->_api_url_v3 . "/" . $rb . "/" . $conditions . "/" . $fields;
                }

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_USERPWD, 'apikey:' . $this->token); //Authenticate
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_timeout);
                $response = curl_exec($ch);

                $this->header = curl_getinfo($ch);
                $error_no = curl_errno($ch);
                $error = curl_error($ch);
                curl_close($ch);
                if ($error_no) {
                    $this->error_no = $error_no;
                }
                if ($error) {
                    $this->error = $error;
                }
                $this->body = @json_decode($response, true);

                if (JSON_ERROR_NONE != json_last_error()) {
                    $this->body = $response;
                }
                return $this;

            } catch (Exception $e) {

                trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                    E_USER_ERROR);

            }
        }
    }
}

?>