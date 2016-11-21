<?php
namespace reportes\parametrizacionInforme\frontera;
/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Parametrizador {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public function __construct($lenguaje, $formulario, $sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }
    public function parametrizacionForm() {

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultarCampos', 'core');
        $camposCore = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultarCampos', 'cabecera');
        $camposCabecera = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultarCampos', 'hfc');
        $camposHfc = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        //var_dump($camposHfc);
        $cadenaSql = $this->miSql->getCadenaSql('consultarCampos', 'wman');
        $camposWman = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultarTema', 'core');
        $temaCore = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultarTema', 'cabecera');
        $temaCabecera = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultarTema', 'hfc');

        $temaHfc = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consultarTema', 'wman');
        $temaWman = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------

        $atributosGlobales['campoSeguro'] = 'true';

        $_REQUEST['tiempo'] = time();
        // -------------------------------------------------------------------------------------------------

        // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque['nombre'];
        $atributos['id'] = $esteCampo;
        $atributos['nombre'] = $esteCampo;
        // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
        $atributos['tipoFormulario'] = '';
        // Si no se coloca, entonces toma el valor predeterminado 'POST'
        $atributos['metodo'] = 'POST';
        // Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
        $atributos['action'] = 'index.php';
        $atributos['titulo'] = $this->lenguaje->getCadena($esteCampo);
        // Si no se coloca, entonces toma el valor predeterminado.
        $atributos['estilo'] = '';
        $atributos['marco'] = true;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------

        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);
        {

            $esteCampo = 'AgrupacionParametrizacion';
            $atributos['id'] = $esteCampo;
            $atributos['leyenda'] = "<b>Parametrización Informe</b>";
            echo $this->miFormulario->agrupacion('inicio', $atributos);
            unset($atributos);

            {

                {

                    // -----------------CONTROL: Botón ----------------------------------------------------------------
                    $esteCampo = 'botonConsulta';
                    $atributos["id"] = $esteCampo;
                    $atributos["tabIndex"] = $tab;
                    $atributos["tipo"] = 'boton';
                    // submit: no se coloca si se desea un tipo button genérico
                    $atributos['submit'] = false;
                    $atributos["simple"] = false;
                    $atributos["estiloMarco"] = '';
                    $atributos["estiloBoton"] = 'default';
                    $atributos["block"] = false;
                    // verificar: true para verificar el formulario antes de pasarlo al servidor.
                    $atributos["verificar"] = false;
                    $atributos["tipoSubmit"] = ' '; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                    $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos['nombreFormulario'] = " ";
                    $tab++;

                    // Aplica atributos globales al control
                    //$atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoBotonBootstrapHtml($atributos);
                    unset($atributos);

                    // -----------------FIN CONTROL: Botón -----------------------------------------------------------

                }
                echo "<br><br>";
                $esteCampo = 'tipo_proyecto';
                $atributos['nombre'] = $esteCampo;
                $atributos['id'] = $esteCampo;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['evento'] = '';
                $atributos['seleccion'] = -1;
                $atributos['deshabilitado'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['estilo'] = "bootstrap";
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                $atributos['cadena_sql'] = 'required';
                //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                $matrizItems = array(
                    array(
                        'core',
                        'CORE',
                    ),
                    array(
                        'cabecera',
                        'CABECERA',
                    ),

                    array(
                        'hfc',
                        'HFC',
                    ),

                    array(
                        'wman',
                        'WMAN',
                    ),
                );
                $atributos['matrizItems'] = $matrizItems;
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                unset($atributos);

                // ----------------INICIO CONTROL: Lista Proyectos---------------------------

                $esteCampo = 'proyecto';
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "text";
                $atributos['id'] = $esteCampo;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
                $atributos['valor'] = "";
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'id_proyecto';
                $atributos["id"] = $esteCampo; // No cambiar este nombre
                $atributos["tipo"] = "hidden";
                $atributos['estilo'] = '';
                $atributos["obligatorio"] = false;
                $atributos['marco'] = true;
                $atributos["etiqueta"] = "";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = '';
                }
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTexto($atributos);
                unset($atributos);

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "division_hogar_nodo";
                $atributos["estilo"] = " ";
                $atributos["estiloEnLinea"] = "display:none;";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {

                    // ----------------INICIO CONTROL: Lista Proyectos---------------------------

                    $esteCampo = 'act_nodo';
                    $atributos['nombre'] = $esteCampo;
                    $atributos['tipo'] = "text";
                    $atributos['id'] = $esteCampo;
                    $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["etiquetaObligatorio"] = true;
                    $atributos['tab'] = $tab++;
                    $atributos['anchoEtiqueta'] = 2;
                    $atributos['estilo'] = "bootstrap";
                    $atributos['evento'] = '';
                    $atributos['deshabilitado'] = false;
                    $atributos['readonly'] = false;
                    $atributos['columnas'] = 1;
                    $atributos['tamanno'] = 1;
                    $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
                    $atributos['valor'] = "";
                    $atributos['ajax_function'] = "";
                    $atributos['ajax_control'] = $esteCampo;
                    $atributos['limitar'] = false;
                    $atributos['anchoCaja'] = 10;
                    $atributos['miEvento'] = '';
                    $atributos['validar'] = 'required';
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = 'id_act_nodo';
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["tipo"] = "hidden";
                    $atributos['estilo'] = '';
                    $atributos["obligatorio"] = false;
                    $atributos['marco'] = true;
                    $atributos["etiqueta"] = "";
                    if (isset($_REQUEST[$esteCampo])) {
                        $atributos['valor'] = $_REQUEST[$esteCampo];
                    } else {
                        $atributos['valor'] = '';
                    }
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ----------------INICIO CONTROL: Lista Proyectos---------------------------

                    $esteCampo = 'hogar';
                    $atributos['nombre'] = $esteCampo;
                    $atributos['tipo'] = "text";
                    $atributos['id'] = $esteCampo;
                    $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["etiquetaObligatorio"] = true;
                    $atributos['tab'] = $tab++;
                    $atributos['anchoEtiqueta'] = 2;
                    $atributos['estilo'] = "bootstrap";
                    $atributos['evento'] = '';
                    $atributos['deshabilitado'] = false;
                    $atributos['readonly'] = false;
                    $atributos['columnas'] = 1;
                    $atributos['tamanno'] = 1;
                    $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
                    $atributos['valor'] = "";
                    $atributos['ajax_function'] = "";
                    $atributos['ajax_control'] = $esteCampo;
                    $atributos['limitar'] = false;
                    $atributos['anchoCaja'] = 10;
                    $atributos['miEvento'] = '';
                    $atributos['validar'] = 'required';
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = 'id_hogar';
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["tipo"] = "hidden";
                    $atributos['estilo'] = '';
                    $atributos["obligatorio"] = false;
                    $atributos['marco'] = true;
                    $atributos["etiqueta"] = "";
                    if (isset($_REQUEST[$esteCampo])) {
                        $atributos['valor'] = $_REQUEST[$esteCampo];
                    } else {
                        $atributos['valor'] = '';
                    }
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);
                }
                // ------------------Fin Division para los botones-------------------------
                echo $this->miFormulario->division("fin");
                unset($atributos);

                {

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "division_core";
                    $atributos["estilo"] = " ";
                    $atributos["estiloEnLinea"] = "display:none;height:550px;overflow-y:auto;overflow-x:hidden;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        foreach ($temaCore as $valor) {

                            $esteCampo = 'AgrupacionParametrizacion';
                            $atributos['id'] = $esteCampo;
                            $atributos['leyenda'] = $valor['tipo'] . ": " . $valor['sub_tipo'];
                            echo $this->miFormulario->agrupacion('inicio', $atributos);
                            unset($atributos);
                            {
                                $tabla = "<table id='contenido' class='table  table-hover'>";
                                foreach ($camposCore as $key => $value) {

                                    if ($valor['sub_tipo'] == $value['sub_tipo'] && $valor['tipo'] == $value['tipo']) {

                                        $esteCampo = $value['campo'];
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['tipo'] = "text";
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = $value['nombre_formulario'];
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['evento'] = '';
                                        $atributos['deshabilitado'] = false;
                                        $atributos['readonly'] = false;
                                        $atributos['columnas'] = 1;
                                        $atributos['tamanno'] = 1;
                                        $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
                                        $atributos['valor'] = "";
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var1 = $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['campo'] . "-informacion";
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = "Información Actividades Hijas";
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['evento'] = '';
                                        $atributos['seleccion'] = 0;
                                        $atributos['deshabilitado'] = false;
                                        $atributos['columnas'] = 1;
                                        $atributos['tamanno'] = 1;
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        $atributos['cadena_sql'] = 'required';
                                        //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                                        //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                                        $matrizItems = array(
                                            array(
                                                '0',
                                                'NO',
                                            ),
                                            array(
                                                '1',
                                                'SI',
                                            ),
                                        );
                                        $atributos['matrizItems'] = $matrizItems;
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var2 = $this->miFormulario->campoCuadroListaBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['identificador_campo'];
                                        $atributos["id"] = $esteCampo; // No cambiar este nombre
                                        $atributos["tipo"] = "hidden";
                                        $atributos['estilo'] = '';
                                        $atributos["obligatorio"] = false;
                                        $atributos['marco'] = true;
                                        $atributos["etiqueta"] = "";
                                        if (isset($_REQUEST[$esteCampo])) {
                                            $atributos['valor'] = $_REQUEST[$esteCampo];
                                        } else {
                                            $atributos['valor'] = '';
                                        }
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        echo $this->miFormulario->campoCuadroTexto($atributos);
                                        unset($atributos);

                                        $tabla .= "<tr>
                                                        <td>" . $var1 . "</td>
                                                        <td>" . $var2 . "</td>
                                                   </tr>";

                                    }

                                }
                                $tabla .= "</table>";
                                echo $tabla;

                            }

                            echo $this->miFormulario->agrupacion('fin');
                            unset($atributos);

                        }

                    }
                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "division_cabecera";
                    $atributos["estilo"] = " ";
                    $atributos["estiloEnLinea"] = "display:none;height:550px;overflow-y:auto;overflow-x:hidden;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        foreach ($temaCabecera as $valor) {

                            $esteCampo = 'AgrupacionParametrizacion';
                            $atributos['id'] = $esteCampo;
                            $atributos['leyenda'] = $valor['tipo'] . ": " . $valor['sub_tipo'];
                            echo $this->miFormulario->agrupacion('inicio', $atributos);
                            unset($atributos);
                            {
                                $tabla = "<table id='contenido' class='table  table-hover'>";
                                foreach ($camposCabecera as $key => $value) {

                                    if ($valor['sub_tipo'] == $value['sub_tipo'] && $valor['tipo'] == $value['tipo']) {

                                        $esteCampo = $value['campo'];
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['tipo'] = "text";
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = $value['nombre_formulario'];
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['evento'] = '';
                                        $atributos['deshabilitado'] = false;
                                        $atributos['readonly'] = false;
                                        $atributos['columnas'] = 2;
                                        $atributos['tamanno'] = 1;
                                        $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
                                        $atributos['valor'] = "";
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var1 = $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['campo'] . "-informacion";
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = "Información Actividades Hijas";
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['evento'] = '';
                                        $atributos['seleccion'] = 0;
                                        $atributos['deshabilitado'] = false;
                                        $atributos['columnas'] = 1;
                                        $atributos['tamanno'] = 1;
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        $atributos['cadena_sql'] = 'required';
                                        //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                                        //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                                        $matrizItems = array(
                                            array(
                                                '0',
                                                'NO',
                                            ),
                                            array(
                                                '1',
                                                'SI',
                                            ),
                                        );
                                        $atributos['matrizItems'] = $matrizItems;
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var2 = $this->miFormulario->campoCuadroListaBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['identificador_campo'];
                                        $atributos["id"] = $esteCampo; // No cambiar este nombre
                                        $atributos["tipo"] = "hidden";
                                        $atributos['estilo'] = '';
                                        $atributos["obligatorio"] = false;
                                        $atributos['marco'] = true;
                                        $atributos["etiqueta"] = "";
                                        if (isset($_REQUEST[$esteCampo])) {
                                            $atributos['valor'] = $_REQUEST[$esteCampo];
                                        } else {
                                            $atributos['valor'] = '';
                                        }
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        echo $this->miFormulario->campoCuadroTexto($atributos);
                                        unset($atributos);

                                        $tabla .= "<tr>
                                                        <td>" . $var1 . "</td>
                                                        <td>" . $var2 . "</td>
                                                   </tr>";

                                    }

                                }

                                $tabla .= "</table>";
                                echo $tabla;

                            }

                            echo $this->miFormulario->agrupacion('fin');
                            unset($atributos);

                        }

                    }
                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "division_wman";
                    $atributos["estilo"] = " ";
                    $atributos["estiloEnLinea"] = "display:none;height:550px;overflow-y:auto;overflow-x:hidden;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        foreach ($temaWman as $valor) {

                            $esteCampo = 'AgrupacionParametrizacion';
                            $atributos['id'] = $esteCampo;
                            $atributos['leyenda'] = $valor['tipo'] . ": " . $valor['sub_tipo'];
                            echo $this->miFormulario->agrupacion('inicio', $atributos);
                            unset($atributos);
                            {

                                $tabla = "<table id='contenido' class='table  table-hover'>";

                                foreach ($camposWman as $key => $value) {

                                    if ($valor['sub_tipo'] == $value['sub_tipo'] && $valor['tipo'] == $value['tipo']) {

                                        $esteCampo = $value['campo'];
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['tipo'] = "text";
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = $value['nombre_formulario'];
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['evento'] = '';
                                        $atributos['deshabilitado'] = false;
                                        $atributos['readonly'] = false;
                                        $atributos['columnas'] = 2;
                                        $atributos['tamanno'] = 1;
                                        $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
                                        $atributos['valor'] = "";
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var1 = $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['campo'] . "-informacion";
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = "Información Actividades Hijas";
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['evento'] = '';
                                        $atributos['seleccion'] = 0;
                                        $atributos['deshabilitado'] = false;
                                        $atributos['columnas'] = 2;
                                        $atributos['tamanno'] = 1;
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        $atributos['cadena_sql'] = 'required';
                                        //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                                        //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                                        $matrizItems = array(
                                            array(
                                                '0',
                                                'NO',
                                            ),
                                            array(
                                                '1',
                                                'SI',
                                            ),
                                        );
                                        $atributos['matrizItems'] = $matrizItems;
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var2 = $this->miFormulario->campoCuadroListaBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['identificador_campo'];
                                        $atributos["id"] = $esteCampo; // No cambiar este nombre
                                        $atributos["tipo"] = "hidden";
                                        $atributos['estilo'] = '';
                                        $atributos["obligatorio"] = false;
                                        $atributos['marco'] = true;
                                        $atributos["etiqueta"] = "";
                                        if (isset($_REQUEST[$esteCampo])) {
                                            $atributos['valor'] = $_REQUEST[$esteCampo];
                                        } else {
                                            $atributos['valor'] = '';
                                        }
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        echo $this->miFormulario->campoCuadroTexto($atributos);
                                        unset($atributos);

                                        $tabla .= "<tr>
                                                        <td>" . $var1 . "</td>
                                                        <td>" . $var2 . "</td>
                                                   </tr>";
                                    }

                                }

                                $tabla .= "</table>";
                                echo $tabla;

                            }

                            echo $this->miFormulario->agrupacion('fin');
                            unset($atributos);

                        }

                    }
                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "division_hfc";
                    $atributos["estilo"] = " ";
                    $atributos["estiloEnLinea"] = "display:none;height:550px;overflow-y:auto;overflow-x:hidden;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        foreach ($temaHfc as $valor) {

                            $esteCampo = 'AgrupacionParametrizacion';
                            $atributos['id'] = $esteCampo;
                            $atributos['leyenda'] = $valor['tipo'] . ": " . $valor['sub_tipo'];
                            echo $this->miFormulario->agrupacion('inicio', $atributos);
                            unset($atributos);
                            {

                                $tabla = "<table id='contenido' class='table  table-hover'>";
                                foreach ($camposHfc as $key => $value) {

                                    if ($valor['sub_tipo'] == $value['sub_tipo'] && $valor['tipo'] == $value['tipo']) {

                                        $esteCampo = $value['campo'];
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['tipo'] = "text";
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = $value['nombre_formulario'];
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['evento'] = '';
                                        $atributos['deshabilitado'] = false;
                                        $atributos['readonly'] = false;
                                        $atributos['columnas'] = 2;
                                        $atributos['tamanno'] = 1;
                                        $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
                                        $atributos['valor'] = "";
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var1 = $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['campo'] . "-informacion";
                                        $atributos['nombre'] = $esteCampo;
                                        $atributos['id'] = $esteCampo;
                                        $atributos['etiqueta'] = "Información Actividades Hijas";
                                        $atributos["etiquetaObligatorio"] = true;
                                        $atributos['tab'] = $tab++;
                                        $atributos['anchoEtiqueta'] = 2;
                                        $atributos['evento'] = '';
                                        $atributos['seleccion'] = 0;
                                        $atributos['deshabilitado'] = false;
                                        $atributos['columnas'] = 1;
                                        $atributos['tamanno'] = 1;
                                        $atributos['ajax_function'] = "";
                                        $atributos['ajax_control'] = $esteCampo;
                                        $atributos['estilo'] = "bootstrap";
                                        $atributos['limitar'] = false;
                                        $atributos['anchoCaja'] = 10;
                                        $atributos['miEvento'] = '';
                                        $atributos['validar'] = 'required';
                                        $atributos['cadena_sql'] = 'required';
                                        //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                                        //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                                        $matrizItems = array(
                                            array(
                                                '0',
                                                'NO',
                                            ),
                                            array(
                                                '1',
                                                'SI',
                                            ),
                                        );
                                        $atributos['matrizItems'] = $matrizItems;
                                        // Aplica atributos globales al control
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        $var2 = $this->miFormulario->campoCuadroListaBootstrap($atributos);
                                        unset($atributos);

                                        $esteCampo = $value['identificador_campo'];
                                        $atributos["id"] = $esteCampo; // No cambiar este nombre
                                        $atributos["tipo"] = "hidden";
                                        $atributos['estilo'] = '';
                                        $atributos["obligatorio"] = false;
                                        $atributos['marco'] = true;
                                        $atributos["etiqueta"] = "";
                                        if (isset($_REQUEST[$esteCampo])) {
                                            $atributos['valor'] = $_REQUEST[$esteCampo];
                                        } else {
                                            $atributos['valor'] = '';
                                        }
                                        $atributos = array_merge($atributos, $atributosGlobales);
                                        echo $this->miFormulario->campoCuadroTexto($atributos);
                                        unset($atributos);

                                        $tabla .= "<tr>
                                                        <td>" . $var1 . "</td>
                                                        <td>" . $var2 . "</td>
                                                   </tr>";

                                    }

                                }

                                $tabla .= "</table>";
                                echo $tabla;

                            }

                            echo $this->miFormulario->agrupacion('fin');
                            unset($atributos);

                        }

                    }
                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);
                }
                {
                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "division_bt";
                    $atributos["estilo"] = "marcoBotones";
                    $atributos["estiloEnLinea"] = "display:block;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        // -----------------CONTROL: Botón ----------------------------------------------------------------
                        $esteCampo = 'botonGuardar';
                        $atributos["id"] = $esteCampo;
                        $atributos["tabIndex"] = $tab;
                        $atributos["tipo"] = 'boton';
                        // submit: no se coloca si se desea un tipo button genérico
                        $atributos['submit'] = true;
                        $atributos["simple"] = true;
                        $atributos["estiloMarco"] = '';
                        $atributos["estiloBoton"] = 'default';
                        $atributos["block"] = false;
                        // verificar: true para verificar el formulario antes de pasarlo al servidor.
                        $atributos["verificar"] = '';
                        $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                        $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
                        $atributos['nombreFormulario'] = $esteBloque['nombre'];
                        $tab++;

                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoBotonBootstrapHtml($atributos);
                        unset($atributos);
                        // -----------------FIN CONTROL: Botón -----------------------------------------------------------

                    }
                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);

                }
            }
            echo $this->miFormulario->agrupacion('fin');
            unset($atributos);

        }

        {
            /**
             * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
             * SARA permite realizar esto a través de tres
             * mecanismos:
             * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
             * la base de datos.
             * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
             * formsara, cuyo valor será una cadena codificada que contiene las variables.
             * (c) a través de campos ocultos en los formularios. (deprecated)
             */

            // En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:

            // Paso 1: crear el listado de variables

            $valorCodificado = "action=" . $esteBloque["nombre"];
            $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
            $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
            $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
            $valorCodificado .= "&opcion=procesarParametrizacion";

            /**
             * SARA permite que los nombres de los campos sean dinámicos.
             * Para ello utiliza la hora en que es creado el formulario para
             * codificar el nombre de cada campo.
             */
            $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
            // Paso 2: codificar la cadena resultante
            $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);

            $atributos["id"] = "formSaraData"; // No cambiar este nombre
            $atributos["tipo"] = "hidden";
            $atributos['estilo'] = '';
            $atributos["obligatorio"] = false;
            $atributos['marco'] = true;
            $atributos["etiqueta"] = "";
            $atributos["valor"] = $valorCodificado;
            echo $this->miFormulario->campoCuadroTexto($atributos);
            unset($atributos);
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);

        // ----------------INICIO CONTROL: Ventana Modal Beneficiario Eliminado---------------------------------
        {
            // Consulta de Parametrización
            $atributos['id'] = 'consulta';
            $atributos['estiloLinea'] = 'information';
            $atributos['tipoEtiqueta'] = 'inicio';
            $atributos['titulo'] = 'Consulta Parametrización';
            echo $this->miFormulario->modal($atributos);
            unset($atributos);

            $tabla = "<table id='contenido_consulta'class='table  table-hover' cellspacing='0' width='100%'>
                        <thead>
                        <tr>
                        <th><center>Tipo de Proyecto</center></th>
                        <th><center>Identificador Proyecto</center></th>
                        <th><center>Nombre Proyectos</center></th>
                        </tr>
                        </thead>
                        </table>";

            echo $tabla;

            $atributos['tipoEtiqueta'] = 'fin';
            echo $this->miFormulario->modal($atributos);
            unset($atributos);
        }

        if (isset($_REQUEST['mensaje'])) {
            $this->mensaje($tab, $esteBloque['nombre']);
        }

    }

    public function mensaje($tab = '', $nombreBloque = '') {

        switch ($_REQUEST['mensaje']) {
            case 'inserto':
                $mensaje = "Se ha registrado con Exito la parametrización de Proyecto";
                $atributos['estiloLinea'] = 'success';     //success,error,information,warning
                break;

            case 'noInserto':
                $mensaje = "Error en el registro de la Parametrización del Proyecto";
                $atributos['estiloLinea'] = 'error';     //success,error,information,warning
                break;

        }

        // ----------------INICIO CONTROL: Ventana Modal Beneficiario Eliminado---------------------------------

        $atributos['tipoEtiqueta'] = 'inicio';
        $atributos['titulo'] = 'Mensaje';
        $atributos['id'] = 'mensaje';
        echo $this->miFormulario->modal($atributos);
        unset($atributos);

        // ----------------INICIO CONTROL: Mapa--------------------------------------------------------
        echo '<div style="text-align:center;">';

        echo '<p><h5>' . $mensaje . '</h5></p>';

        echo '</div>';

        // ----------------FIN CONTROL: Mapa--------------------------------------------------------

        echo '<div style="text-align:center;">';

        echo '</div>';

        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->modal($atributos);
        unset($atributos);

    }

}

$miSeleccionador = new Parametrizador($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->parametrizacionForm();

?>
