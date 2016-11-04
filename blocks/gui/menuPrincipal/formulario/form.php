<?php

namespace gui\menuPrincipal\formulario;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/auth/SesionSso.class.php";

class FormularioMenu {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $atributosMenu;
    public $miSesionSso;
    public $_rutaBloque;

    public function __construct($lenguaje, $formulario, $sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;

        $this->miSesionSso = \SesionSso::singleton();
    }
    public function logout() {

        foreach ($_REQUEST as $clave => $valor) {
            unset($_REQUEST[$clave]);
        }

        $variable = "pagina=index&&event=logout";
        $url = $this->miConfigurador->configuracion["host"] . $this->miConfigurador->configuracion["site"] . "/index.php?";
        $enlace = $this->miConfigurador->configuracion['enlace'];
        $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar($variable);
        $_REQUEST[$enlace] = $enlace . '=' . $variable;
        $redireccion = $url . $_REQUEST[$enlace];
        echo "<script>location.replace('" . $redireccion . "')</script>";
        exit();
    }

    public function formulario() {

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
        $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
        $rutaBloque .= $esteBloque['grupo'] . '/' . $esteBloque['nombre'];

        $this->_rutaBloque = $rutaBloque;
        /**
         * IMPORTANTE: Este formulario está utilizando jquery.
         * Por tanto en el archivo ready.php se delaran algunas funciones js
         * que lo complementan.
         */

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
         * Atributos que deben ser aplicados a todos los controles de este formulario.
         * Se utiliza un arreglo
         * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
         *
         * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
         * $atributos= array_merge($atributos,$atributosGlobales);
         */
        $atributosGlobales['campoSeguro'] = 'true';
        $_REQUEST['tiempo'] = time();

        // -------------------------------------------------------------------------------------------------

        // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque['nombre'];
        $atributos['id'] = $esteCampo;
        $atributos['nombre'] = $esteCampo;
        /**
         * Nuevo a partir de la versión 1.0.0.2, se utiliza para crear de manera rápida el js asociado a
         * validationEngine.
         */
        // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
        $atributos['tipoFormulario'] = 'multipart/form-data';

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

        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos['tipoEtiqueta'] = 'inicio';
        // $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->formulario($atributos);
        unset($atributos);
        // ---------------- SECCION: Controles del Formulario -----------------------------------------------

        $respuesta = $this->miSesionSso->getParametrosSesionAbierta();

        $salida = (isset($respuesta['description']) == false) ? $this->logout() : "";

        foreach ($respuesta['description'] as $key => $rol) {

            $respuesta['rol'][] = $rol;

        }

        $cadenaSql = $this->miSql->getCadenaSql("consultarDatosMenu", $respuesta['rol']);

        $this->atributosMenu = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $this->ConstruirMenu($rutaBloque);

        // En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:

        // Paso 1: crear el listado de variables

        $valorCodificado = "actionBloque=" . $esteBloque["nombre"];
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
        $valorCodificado .= "&opcion=registrarBloque";
        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo.
         */
        $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
        $valorCodificado .= "&tiempo=" . time();
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

        // ----------------FIN SECCION: Paso de variables -------------------------------------------------

        // ---------------- FIN SECCION: Controles del Formulario -------------------------------------------

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function ConstruirMenu() {

        $menu = '';

        $menuGeneral = array();

        foreach ($this->atributosMenu as $valor) {

            $menuGeneral[] = $valor['nombre_menu'];
        }
        $menuGeneral = array_unique($menuGeneral);

        foreach ($menuGeneral as $valor) {

            foreach ($this->atributosMenu as $valorMenu) {

                if ($valor == $valorMenu['nombre_menu']) {

                    $arreglo[$valor][] = $valorMenu;
                }
            }
        }
        $i = 0;
        foreach ($arreglo as $valor => $key) {

            if (isset($key[0]['clase_enlace']) && $key[0]['clase_enlace'] == 'menu') {

                $menu .= ($i == 0) ? '<li><a data-toggle="dropdown" href="' . $this->CrearUrl($key[0]) . '">' . $valor . '</a></li>' : '<li><a href="' . $this->CrearUrl($key[0]) . '" data-toggle="dropdown">' . $valor . '</a><li>';
            } else {

                $menu .= $this->ConstruirGrupoGeneralMenu($key, $valor);
            }
            $i++;
        }

        $cadenaHTML = '<div class="navbar navbar-default navbar-fixed-top" role="navigation">
                        <div class="container">
                          <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse"
                            data-target=".navbar-ex1-collapse">
                                <span class="sr-only">Desplegar navegación</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                          </div>

                          <div class="collapse navbar-collapse navbar-ex1-collapse">
                            <ul class="nav navbar-nav">
                                <li><a href="/OpenKyOS/index.php">Inicio</a></li>

                ';
        $cadenaHTML .= $menu;

        $cadenaHTML .= '    </ul>
                          </div>
                         </div>
                        </div>';

        echo $cadenaHTML;
    }
    public function ConstruirGrupoGeneralMenu($ArrayAtributos, $nombre) {
        $submenu = '';
        $i = 0;
        foreach ($ArrayAtributos as $valor) {

            if (isset($valor['clase_enlace']) && $valor['clase_enlace'] == "normal") {

                $enlace = $valor['id_enlace'];

                $submenu .= ' <li class="dropdown dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $valor['titulo_enlace'] . '</a>
                                <ul class="dropdown-menu">
                                ';

                foreach ($ArrayAtributos as $valor) {

                    if ($valor['submenu'] == $enlace) {

                        $image = "";

                        if ($valor['icon'] != "") {
                            $image = '<img src="' . $valor['icon'] . '">  ';
                        }

                        $submenu .= '<li><a href="' . $this->CrearUrl($valor) . '">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&#9672 ' . $image . $valor['titulo_enlace'] . '</a></li>';
                    }

                }

                $submenu .= '
                            </ul>
                        </li>';

            } else if ($valor['submenu'] == null) {

                $image = "";

                if ($valor['icon'] != "") {
                    $image = '<img src="' . $this->_rutaBloque . "/imagenes/" . $valor['icon'] . '">  ';
                }

                $submenu .= '<li><a href="' . $this->CrearUrl($valor) . '">' . $image . $valor['titulo_enlace'] . '</a></li>';
            }

        }

        $cadena = '';

        $cadena .= '<li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $nombre . '<b class="caret"></b></a>
                    <ul class="dropdown-menu multi-level">';
        $cadena .= $submenu;

        $cadena .= '  </ul>

                    </li>';

        return $cadena;
    }

    public function ConstruirSubGrupoGeneralMenu($ArrayAtributos, $nombre) {
        $submenu = '';
        $i = 0;
        foreach ($ArrayAtributos as $valor) {

            $submenu .= '<li><a href="' . $this->CrearUrl($valor) . '">' . $valor['titulo_enlace'] . '</a></li>';
        }

        $cadena = '';

        $cadena .= '<li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $nombre . '<b class="caret"></b></a>
                    <ul class="dropdown-menu multi-level">';
        $cadena .= $submenu;

        $cadena .= '</li>';

        return $cadena;
    }

    public function CrearUrl($atributos) {
        if ($atributos['tipo_enlace'] == 'interno' && !is_null($atributos['enlace'])) {

            $url = $this->miConfigurador->configuracion['host'] . $this->miConfigurador->configuracion['site'] . '/index.php?';

            $enlace = $this->miConfigurador->configuracion['enlace'] . "=";

            $variable = "pagina=" . $atributos['enlace'];
            $variable .= "&" . $atributos['parametros'];

            $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar($variable);

            $direccion = $url . $enlace . $variable;
        } elseif ($atributos['tipo_enlace'] == 'externo' && !is_null($atributos['enlace'])) {

            $direccion = $atributos['enlace'];
        } else {

            $direccion = '#';
        }

        return $direccion;
    }
}

$miFormulario = new FormularioMenu($this->lenguaje, $this->miFormulario, $this->sql);

$miFormulario->formulario();

?>