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
    public $accion;
    public $arreglo_registro;
    public $tema_campos;

    public function configurar($datos) {
        $this->_api_url_v2 = $datos['host'] . $datos['api_url_v2'];
        $this->_api_url_v3 = $datos['host'] . $datos['api_url_v3'];
        $this->type = $datos['type'];
        $this->token = $datos['token'];
    }

    public function search($rb, $conditions = '', $fields = '', $version_api = '', $accion = '', $campos_registrar = '') {

        {
            // Variables dado el tipo de Acción

            $this->accion = (($accion != '') ? $accion : 'GET');

            $this->arreglo_registro = ((is_array($campos_registrar)) ? $campos_registrar : NULL);

            $this->tema_campos = $fields;

        }

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
                    $url = $this->_api_url_v3 . $rb . "/" . $conditions . "/" . $fields;
                }

                /**
                 * Procesar Petición de acuerdo a la Acción
                 **/

                $this->procesarPeticionApi($url);

                return $this;

            } catch (Exception $e) {

                trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                    E_USER_ERROR);

            }
        }
    }

    public function procesarPeticionApi($url = '') {

        $ch = curl_init($url);

        switch ($this->accion) {

            case 'POST':
                $variables = $this->generarEstructuraRegistro();

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $variables);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($variables))
                );

                break;

            case 'GET':

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

                break;
        }

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

    }

    public function generarEstructuraRegistro() {

        switch ($this->tema_campos) {

            case 'work_packages':

                $arreglo = array(
                    'subject' => $this->arreglo_registro['nombre'],
                    'percentageDone' => $this->arreglo_registro['porcentaje_avance'],
                    'description' => array('format' => 'textile', 'raw' => $this->arreglo_registro['descripcion']),
                    '_links' => array('type' => array('href' => '/api/v3/types/' . $this->arreglo_registro['tipo']),
                        'status' => array('href' => '/api/v3/statuses/' . $this->arreglo_registro['estado']),
                        'priority' => array('href' => '/api/v3/priorities/' . $this->arreglo_registro['prioridad']),
                        'parent' => array('href' => '/api/v3/work_packages/' . $this->arreglo_registro['paquete_trabajo_padre']),

                    ),

                );

            /**
             * Estrutura Campos Personalizados
             *$campos_personalizados = array(
             *"customField14" => array(
             *'value' => '',
             *'tipo' => 'string_objects',
             * ),
             *"customField15" => array(
             *'value' => '',
             *'tipo' => 'string_objects',
             *),
             *);
             **/
                foreach ($this->arreglo_registro['camposPersonalizados'] as $key => $value) {

                    $value['value'] = str_replace(" ", "%20", $value['value']);
                    $campos[$key] = array('href' => '/api/v3/' . $value['tipo'] . "?value=" . $value['value']);
                }

                $arreglo['_links'] = array_merge($arreglo['_links'], $campos);

                return json_encode($arreglo);

                break;

            default:
                # code...
                break;
        }

    }

}

?>