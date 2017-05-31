<?php
require_once "core/builder/HtmlBase.class.php";
/**
 *
 * @author paulo
 *
 * $atributos['estilo']
 * $atributos['filas']
 * $atributos['columnas']
 *
 */
class TextAreaBootstrap extends HtmlBase {

    public function campoTextAreaBootstrap($atributos) {

        $this->setAtributos($atributos);

        $this->cadenaHTML = '<div class="form-group">';

        $this->campoSeguro();

        $this->cadenaHTML .= ' <label for="' . $atributos['id'] . '">' . $atributos['etiqueta'] . '</label>';

        $this->cadenaHTML .= ' <textarea class="form-control" rows="' . $atributos['filas'] . '" id="' . $this->atributos['id'] . '"  name="' . $this->atributos['id'] . '"  value="' . $atributos['valor'] . '" ';

        if (isset($atributos['validar']) && $atributos['validar'] = 'required') {
            $this->cadenaHTML .= 'required="true"  ';
        }

        $this->cadenaHTML .= ' >';

        if (isset($atributos['valor'])) {
            $this->cadenaHTML .= $atributos['valor'];
        }
        $this->cadenaHTML .= '</textarea>';

        $this->cadenaHTML .= '</div>';

        return $this->cadenaHTML;

    }

}