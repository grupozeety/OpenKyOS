<?php

/**
 * Genera cuadros de selección (<select>)
 * 
 * Listado de atributos que se requieren para definir el control:
 * 
 * $atributos['nombre']:        Nombre del control. Corresponde al atributo name en html.
 * $atributos['id']:            id del control. Equivale al atributo id de html.
 * $atributos['etiqueta']:      Etiqueta que acompaña al control.
 * $atributos['tab']:           orden de tabulador dentro del formulario. (Requerido).   
 * $atributos['seleccion']:     -1 muestra una linea vacia al inicio de la lista, <-1 entonces se seleccciona 
 *                              el primer registro devuelto en la busqueda. >0 muestra el registro correspondiente en la lista.
 * $atributos['evento']:        Evento (javascript) asociado cuando se cambia el elemento seleccionado.
 * $atributos['deshabilitado']: true el control está deshabilitado. (Opcional)
 * $atributos['columnas']:      Define el tamaño relativo del marco que engloba el control. (Opcional) 
 * $atributos['tamanno']:       Define cuantas líneas se muestran. (Opcional) 
 * $atributos['estilo']:        Estilo a aplicar en el control. (Opcional)
 * $atributos['validar']:       cadena de validación conforme al formato jquery-validation-engine (Opcional)
 * $atributos['limitar']:       Define la cantidad de caracteres que se muestra en cada línea de la lista.
 * $Atributos['matrizItems']:   Matriz de datos para llenar la lista del control. Si no existe entonces se busca en 
 *                              la base de datos. Matriz de arreglos del tipo $matrizItems[valor][etiqueta_a_mostrar]
 * $Atributos['miniRegistro']:  Define si se muestra un listado inicial de opciones. Tal como una lista de los más importantes.                             
 * $atributos['baseDatos']:     Nombre de la conexión a ser utilizada para buscar los registros que llenan la lista.
 * $atributos['cadena_sql']:    Cadena Sql que se utiliza para consultar a la base de datos.
 * 
 * Ejemplo de como definir la matriz de items:
 * $matrizItems=array(
 *       array(1,'Registrar Página'),
 *       array(2,'Registrar Bloque'),
 *       array(3,'Diseñar Página')
 *   );
 * 
 *       
 *                            
 */
require_once ("core/builder/HtmlBase.class.php");

class Select extends HtmlBase {

    /**
     * Punto de entrada para generar controles tipo <select>
     *
     * @param array $atributos        	
     * @return string
     */
    function campoCuadroLista($atributos) {
    	/*
    	 * Estas 2 líneas hacen el paso de atributos de los componentes que heredan de la clase HtmlBase
    	 * en este caso de pasan del componente a la instancia de FormularioHtml
    	 */
    	$campoValidar = (isset($atributos ["validar"]))?$atributos ["validar"]:'';
    	$this->instanciaFormulario->validadorCampos[$atributos ["id"]] = $campoValidar;
    	
        if (isset($atributos [self::COLUMNAS]) && $atributos [self::COLUMNAS] != '' && is_numeric($atributos [self::COLUMNAS])) {

            $this->cadenaHTML = "<div class='campoCuadroLista anchoColumna" . $atributos [self::COLUMNAS] . "'>\n";
        } else {
            $this->cadenaHTML = "<div class='campoCuadroLista'>\n";
        }

        $this->cadenaHTML .= $this->etiqueta($atributos);
        $this->cadenaHTML .= $this->cuadro_lista($atributos);
        $this->cadenaHTML .= "</div>\n";

        return $this->cadenaHTML;
    }

    /**
     *
     * @name cuadro_lista
     * @param string $cuadroSql
     *        	Clausula SQL desde donde se extraen los valores de la lista
     * @param string $nombre
     *        	Nombre del control que se va a crear
     * @param string $configuracion        	
     * @param int $seleccion
     *        	id (o nombre??) del elemento seleccionado en la lista
     * @param int $evento
     *        	Evento javascript que desencadena el control
     * @return void
     * @access public
     */
    function cuadro_lista($atributos) {
        $this->setAtributos($atributos);
        $this->campoSeguro();

        // Invocar la funcion que rescata el registro de los valores que se mostraran en la lista

        if (isset($this->atributos ['matrizItems'])) {
            $this->cuadro_registro = $this->atributos ['matrizItems'];
            $this->cuadroCampos = count($this->atributos ['matrizItems']);
            $resultado = true;
        } else {
            $resultado = $this->rescatarRegistroCuadroLista();
        }

        $this->cadena_html = '';

        if ($resultado) {

            if (!isset($this->atributos [self::NOMBRE])) {
                $this->atributos [self::NOMBRE] = $this->atributos ["id"];
            }

            if (!isset($this->atributos [self::SELECCION])) {
                $this->atributos [self::SELECCION] = - 1;
            }

            $this->atributos [self::EVENTO] = $this->definirEvento();

            $this->armarSelect();
        } else {
            $this->cadena_html .= "No Data";
        }

        return $this->cadena_html;
    }

    function armarSelect() {
        $this->cadena_html = "<select ";

        if (isset($this->atributos [self::DESHABILITADO]) && $this->atributos [self::DESHABILITADO]) {
            $this->cadena_html .= "disabled ";
        }

        if ($this->atributos [self::ID] != '') {
            $this->cadena_html .= "id='" . $this->atributos [self::ID] . "' ";
        }

        $this->atributoClassSelect();

        if (isset($this->atributos [self::ANCHOETIQUETA])) {
            $this->cadena_html .= " style='width:" . $this->atributos [self::ANCHOETIQUETA] . "' ";
        }

        $this->cadena_html .= "name='" . $this->atributos [self::NOMBRE] . "' size='" . $this->atributos [self::TAMANNO] . "' " . $this->atributos [self::EVENTO] . " " . self::HTMLTABINDEX . "'" . $this->atributos ['tab'] . "'>\n";

        // Si no se especifica una seleccion se agrega un espacio en blanco
        if ($this->atributos [self::SELECCION] == - 1) {
            $this->cadena_html .= "<option value=''>Seleccione .....</option>\n";
        }

        // Si el control esta asociado a otro control que aparece si no hay un valor en la lista
        if (isset($this->atributos ["otraOpcion"])) {
            if ($this->atributos [self::SELECCION] == "sara") {
                $this->cadena_html .= "<option value='sara' selected='true'>" . $this->atributos [self::OTRAOPCIONETIQUETA] . "</option>\n";
            } else {
                $this->cadena_html .= "<option value='sara'>" . $this->atributos [self::OTRAOPCIONETIQUETA] . "</option>\n";
            }
        }

        $this->listadoInicialCuadroLista();
        $this->ArmarListado();
        $this->cadena_html .= "</select>\n";
    }

    private function ArmarListado() {

        if (isset($this->atributos [self::SELECCION])) {
            $seleccion = $this->atributos [self::SELECCION];
        } else {
            $seleccion = 0;
        }

        if ($this->atributos [self::COLUMNAS] == 2) {

            $ancho = 26;
        } else if (isset($this->atributos [self::ANCHOCAJA])) {

            $ancho = $this->atributos [self::ANCHOCAJA];
        } else {
            $ancho = 20;
        }




        $limitar = $this->atributos ["limitar"];

        // Calcular el número de columnas
        $this->keys = array_keys($this->cuadro_registro [0]);

        $this->columnas = 0;
        foreach ($this->keys as $clave => $valor) {
            if (is_string($valor)) {
                $this->columnas ++;
            }
        }

        for ($j = 0; $j < $this->cuadroCampos; $j ++) {

            $this->cuadro_contenido = '';

            if ($limitar == 1) {
                $cadenaTexto = substr($this->cuadro_registro [$j] [1], 0, $ancho);
            } else {
                $cadenaTexto = htmlentities($this->cuadro_registro [$j] [1]);
            }
            if ($this->cuadro_registro [$j] [0] == $seleccion) {
                $this->cadena_html .= "<option value='" . $this->cuadro_registro [$j] [0] . "' selected='true'>" . $cadenaTexto . "</option>\n";
            } else {
                $this->cadena_html .= "<option value='" . $this->cuadro_registro [$j] [0] . "'>" . $cadenaTexto . "</option>\n";
            }
        }
    }

    function listadoInicialCuadroLista() {
        if (isset($this->atributos ['miniRegistro'])) {
            if (isset($this->atributos [self::SELECCION])) {
                $seleccion = $this->atributos [self::SELECCION];
            } else {
                $seleccion = 0;
            }

            if (isset($miniRegistro)) {

                $keys = array_keys($miniRegistro [0]);

                $columnas = 0;
                foreach ($keys as $clave => $valor) {
                    if (is_string($valor)) {
                        $columnas ++;
                    }
                }

                for ($i = 0; $i < $totalMiniRegistro; $i ++) {
                    $this->cuadro_contenido = "";

                    if ($limitar == 1) {
                        $cadenaTexto = substr($miniRegistro [$i] [1], 0, 20);
                    } else {
                        $cadenaTexto = htmlentities($miniRegistro [$i] [0]);
                    }

                    if ($miniRegistro [$i] [0] == $seleccion) {
                        $this->cadena_html .= "<option class='texto_negrita' value='" . $miniRegistro [$i] [0] . "' selected='true'>" . $cadenaTexto . "</option>\n";
                        $seleccion = time();
                    } else {
                        $this->cadena_html .= "<option class='texto_negrita' value='" . $miniRegistro [$i] [0] . "'>" . $cadenaTexto . "</option>\n";
                    }
                }
            }
            $this->cadena_html .= "<option value='-1'></option>\n";
            $this->cadena_html .= "<option value='-1'>--------------</option>\n";
            $this->cadena_html .= "<option value='-1'></option>\n";
        }
    }

    private function definirEvento() {
        switch ($this->atributos [self::EVENTO]) {
            case 1 :
            case 'submit' :
                $miEvento = 'onchange="this.form.submit()"';
                break;

            case 2 :
                $miEvento = $this->armarEvento();
                break;

            case 3 :
                $miEvento = $this->armarEvento2();
                break;

            default :
                $miEvento = '';
        }

        return $miEvento;
    }

    /**
     *
     * @return string
     */
    private function armarEvento() {
        $this->control = explode("|", $this->atributos ["ajax_control"]);
        $miEvento = "onchange=\"" . $this->atributos ["ajax_function"];
        $miEvento .= "(";
        foreach ($this->control as $miControl) {
            $miEvento .= "document.getElementById('" . $miControl . "').value,";
        }
        $miEvento = substr($miEvento, 0, (strlen($miEvento) - 1));
        return $miEvento . ")\"";
    }

    private function armarEvento2() {
        $this->control = explode("|", $this->atributos ["ajax_control"]);
        $miEvento = "onchange=\"" . $this->atributos ["ajax_function"];

        return $miEvento . "\"";
    }

    /**
     *
     * Define el registro de datos a partir del cual se construira el cuadro de lista
     *
     * @name rescatarRegistroCuadroLista
     * @param
     *        	none
     * @access private
     * @return none
     */
    function rescatarRegistroCuadroLista() {
        // Si no se ha pasado una tabla de valores, entonces debe realizarse una busqueda con la opcion determinada
        // Si se ha pasado una tabla de valores, entonces se utiliza esa tabla y no se hacen consultas
        $cuadroSql = $this->atributos ["cadena_sql"];

        if (!is_array($cuadroSql)) {

            // Si no se ha definido de donde tomar los datos se utiliza la base de datos definida en config.inc

            if (!isset($this->atributos [self::BASEDATOS]) || (isset($this->atributos [self::BASEDATOS]) && $this->atributos [self::BASEDATOS] == "")) {
                $this->atributos [self::BASEDATOS] = "configuracion";
            }

            $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($this->atributos [self::BASEDATOS]);
            if (!$esteRecursoDB) {
                // Esto se considera un error fatal
                exit();
            }

            $this->cuadro_registro = $esteRecursoDB->ejecutarAcceso($cuadroSql, "busqueda");

            if ($this->cuadro_registro) {

                $this->cuadroCampos = $esteRecursoDB->getConteo();

                // En el caso que se requiera una minilista de opciones al principio
                if (isset($this->atributos ["subcadena_sql"])) {
                    $this->cuadro_miniRegistro = $esteRecursoDB->ejecutarAcceso($this->atributos ["subcadena_sql"], "busqueda");
                }
                $respuesta = true;
            } else {

                $respuesta = false;
            }
        } else {
            $this->cuadro_registro = $cuadroSql;
            $this->cuadroCampos = count($cuadroSql);
            $respuesta = true;
        }

        return $respuesta;
    }

    // Funcion que genera listas desplegables con grupos de opciones
    // matrizItems es un vector, donde la posicion cero y las posiciones pares corresponden a los labels de los grupos de opciones y las posiciones impares corresponden a las opciones por cada grupo.
    // Las posiciones impar contienen un vector con las opciones correspondientes al grupo de opciones
    function cuadro_listaGrupos($arregloAtributos, $datosConfiguracion) {

        /**
         * Los atributos que acepta este widget son:
         * matrizItems
         * nombre
         * seleccion
         * evento
         * limitar
         * tab
         * id
         */
        include_once ($datosConfiguracion ["raiz_documento"] . $datosConfiguracion ["clases"] . "/cadenas.class.php");
        $this->formato = new cadenas ();
        $this->cuadro_registro = $arregloAtributos ['matrizItems'];
        $this->cuadroCampos = count($arregloAtributos ['matrizItems']);

        $this->mi_cuadro = '';

        if ($this->cuadroCampos <= 0) {
            erro_log('Imposible rescatar los datos.');
            return false;
        }

        $this->mi_cuadro .= $this->procesarAtributosCuadroLista($arregloAtributos, $datosConfiguracion);

        for ($this->grupos_contador = 0; $this->grupos_contador < $this->cuadroCampos - 1; $this->grupos_contador ++) {
            $this->valor = $this->cuadro_registro [$this->grupos_contador];
            $this->mi_cuadro .= "<optgroup ";
            $this->mi_cuadro .= "label='" . $this->valor . "'>";

            // Almacena en otra variable el vector que viene en $this->cuadro_registro[$this->grupos_contador+1] para poderlo manipular
            $this->opciones = $this->cuadro_registro [$this->grupos_contador + 1];

            // scribe las opciones del select
            $this->opciones_num_campos = count($this->opciones);
            $this->opciones_contador_valor = 0;
            $this->opciones_contador_texto = 1;

            while ($this->opciones_contador_texto < $this->opciones_num_campos) {

                $this->mi_cuadro .= "<option ";
                $this->mi_cuadro .= "value=" . $this->opciones [$this->opciones_contador_valor];

                // i debe seleccionar un registro especifico
                if ($seleccion == $this->opciones [$this->opciones_contador_valor]) {
                    $this->mi_cuadro .= " selected='true'";
                }
                $this->mi_cuadro .= ">";
                $this->texto = $this->opciones [$this->opciones_contador_texto];

                // i debe limitar el texto en la visualizacion
                if ($limitar == 1) {
                    $this->texto = $this->formato->unhtmlentities(substr($this->texto, 0, 20));
                } else {
                    $this->texto = $this->formato->formatohtml($this->texto);
                }
                $this->mi_cuadro .= $this->texto;
                $this->mi_cuadro .= "</option>";

                $this->opciones_contador_valor = $this->opciones_contador_valor + 2;
                $this->opciones_contador_texto = $this->opciones_contador_texto + 2;
            }

            $this->mi_cuadro .= "</optgroup>";
            $this->grupos_contador + 1;
        }
        $this->mi_cuadro .= "</select>\n";

        return $this->mi_cuadro;
    }

    private function procesarAtributosCuadroLista($arregloAtributos, $datosConfiguracion) {
        switch ($arregloAtributos [self::EVENTO]) {
            case 1 :
                $miEvento = 'onchange="this.form.submit()"';
                break;

            case 2 :
                $miEvento = "onchange=\"" . $datosConfiguracion ["ajax_function"] . "(document.getElementById('" . $datosConfiguracion ["ajax_control"] . "').value)\"";
                break;
            case 3 :
                $miEvento = 'disabled="yes"';
                break;
            default :
                $miEvento = "";
        }

        if ($arregloAtributos ['id'] != "") {
            $id = "id='" . $arregloAtributos ['id'] . "'";
        }

        $cadena = "<select name='" . $arregloAtributos [self::NOMBRE] . "' size='1' " . $miEvento . " " . self::HTMLTABINDEX . "'" . $arregloAtributos ['tab'] . "' " . $id . ">\n";

        if ($arregloAtributos ['seleccion'] < 0) {
            $cadena .= "<option value=''>Seleccione </option>\n";
        }

        return $cadena;
    }

    private function atributoClassSelect() {
        $this->cadena_html .= " class='";

        if (isset($this->atributos [self::ESTILO]) && $this->atributos [self::ESTILO] == self::JQUERYUI) {

            $this->cadena_html .= "selectboxdiv ";
        }

        // Si se especifica que puede ser multiple
        if (isset($this->atributos [self::MULTIPLE]) && $this->atributos [self::MULTIPLE]) {
            $this->cadena_html .= " multiple \n";
        }

        // Si se utiliza jQuery-Validation-Engine
        if (isset($this->atributos [self::VALIDAR])) {
            $this->cadena_html .= " validate[" . $this->atributos [self::VALIDAR] . "] ";
        }

        $this->cadena_html .= "'";
    }

}
