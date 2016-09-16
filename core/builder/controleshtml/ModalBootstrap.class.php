<?php
require_once "core/builder/HtmlBase.class.php";

class ModalBootstrap extends HtmlBase {

    public function modal($atributos = "") {

        $this->setAtributos($atributos);

        $this->cadenaHTML = "";

        if ($this->atributos['tipoEtiqueta'] == self::INICIO) {

            $this->cadenaHTML = '<div ';

            if (isset($atributos["id"])) {
                $this->cadenaHTML .= "id='" . $atributos["id"] . "' ";
            }

            $this->cadenaHTML .= $this->atributoClassModal();

            $this->cadenaHTML .= '>';

            $this->cadenaHTML .= $this->contenidoModal();

        } else {
            $this->cadenaHTML .= $this->finModal();
        }

        return $this->cadenaHTML;

    }

    private function atributoClassModal() {

        $cadena = self::HTMLCLASS . "'";

        // --------------Atributo class --------------------------------

        $this->atributos[self::ESTILO] = 'modal fade';

        $cadena .= $this->atributos[self::ESTILO];

        $cadena .= "' ";

        $cadena .= "role='dialog' ";

        return $cadena;

        // ----------- Fin del atributo class ----------------------------
    }

    private function contenidoModal() {

        if (isset($this->atributos['estiloLinea']) && $this->atributos['estiloLinea'] != '') {
            switch ($this->atributos['estiloLinea']) {
                case 'success':
                    $estilo = 'color: #3c763d;background-color: #dff0d8;border-color: #d6e9c6;';
                    break;
                case 'error':
                    $estilo = 'color: #a94442;background-color: #f2dede;border-color: #ebccd1;';
                    break;
                case 'information':
                    $estilo = 'color: #31708f;background-color: #d9edf7;border-color: #bce8f1;';
                    break;
                case 'warning':
                    $estilo = 'color: #8a6d3b;background-color: #fcf8e3;border-color: #faebcc;';
                    break;
            }
        } else {
            $estilo = '';
        }

        $cadena = '<div class="modal-dialog">';
        $cadena .= '<div class="modal-content">';
        $cadena .= '<div class="modal-header" ';
        $cadena .= 'style="' . $estilo . '"';
        $cadena .= ' >';
        $cadena .= '<button type="button" class="close" data-dismiss="modal">';
        $cadena .= '&times;';
        $cadena .= '</button>';
        $cadena .= '<h4 class="modal-title">';
        $cadena .= '<b>' . $this->atributos['titulo'] . '</b>';
        $cadena .= '</h4>';
        $cadena .= '</div>';
        $cadena .= '<div class="modal-body">';
        $cadena .= '<form role="form" id="form' . $this->atributos['id'] . 'Bootstrap">';

        return $cadena;

    }

    private function finModal() {

        $cadena = '</form>';
        $cadena .= ' </div>';
        $cadena .= '</div>';
        $cadena .= ' </div>';
        $cadena .= ' </div>';

        return $cadena;

    }

}