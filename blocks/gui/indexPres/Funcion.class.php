<?php

if (!isset($GLOBALS["autorizado"])) {
    include("../index.php");
    exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("core/builder/Mensaje.class.php");
include_once("core/crypto/Encriptador.class.php");

//Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
//metodos mas utilizados en la aplicacion
//Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
//en camel case precedido por la palabra Funcion

class FuncionindexPres {

    var $sql;
    var $funcion;
    var $lenguaje;
    var $ruta;
    var $miConfigurador;
    var $miInspectorHTML;
    var $error;
    var $miRecursoDB;
    var $crypto;

    function verificarCampos() {
        include_once($this->ruta . "/funcion/verificarCampos.php");
        if ($this->error == true) {
            return false;
        } else {
            return true;
        }
    }

    function verificarRegistro() {
        include_once($this->ruta . "/funcion/verificarRegistro.php");
    }

    function nuevo() {
        include_once($this->ruta . "/funcion/procesarNuevo.php");
    }

    function confirmar() {
        include_once($this->ruta . "/funcion/procesarConfirmar.php");
    }

    function confirmarEditar() {
        include_once($this->ruta . "/funcion/procesarConfirmarEditar.php");
    }

    function editar() {
        include_once($this->ruta . "/funcion/procesarEditar.php");
    }

    function redireccionar($opcion, $valor = "") {
        include_once($this->ruta . "/funcion/redireccionar.php");
    }

    function rescatarUsuario($usuario) {
        include_once($this->ruta . "/funcion/rescatarUsuario.php");
        
        return $datosUsuario;
    }

    function action() {

        //Evitar que se ingrese codigo HTML y PHP en los campos de texto
        //Campos que se quieren excluir de la limpieza de código. Formato: nombreCampo1|nombreCampo2|nombreCampo3
        $excluir = "";
        $_REQUEST = $this->miInspectorHTML->limpiarPHPHTML($_REQUEST);

        if (!isset($_REQUEST["opcion"]) || (isset($_REQUEST["opcion"]) && ($_REQUEST["opcion"] == "nuevo" || $_REQUEST["opcion"] == "editar"))) {

            //Realizar una validación específica para los campos de este formulario:
            $validacion = $this->verificarCampos();

            if ($validacion == false) {
                //Instanciar a la clase pagina con mensaje de correcion de datos
                echo "Datos Incorrectos";
            } else {
                //Validar las variables para evitar un tipo  insercion de SQL
                $_REQUEST = $this->miInspectorHTML->limpiarSQL($_REQUEST);

                if (!isset($_REQUEST['opcion']) || $_REQUEST["opcion"] == "nuevo") {


                    $resultado = $this->verificarRegistro();

                    if ($resultado != "error") {

                        if ($resultado == true) {

                            $identificador = time();
                            if (isset($_REQUEST["opcion"]) && $_REQUEST["opcion"] == "antiguo") {
                                $this->recursoPersonalAntiguo();
                            } else {
                                //echo "El nombre de usuario ya est&aacute; registrado.";exit;
                                $this->redireccionar("personalExiste");
                            }
                        } else {
                            $this->nuevo();
                        }
                    } else {
                        /**
                         * Esto se considera un error de ámbito de sistema y por tanto debe manejarse con un objeto de la
                         * clase Mensaje. El texto debe colocarse en la carpeta LC_MESSAGES correspondiente dentro de
                         * core->locale
                         */
                        $this->miMensaje->mostrarMensaje("errorAcceso");
                    }
                } else {

                    if ($_REQUEST["opcion"] == "editar") {
                        $this->editar();
                    }
                }
            }
        } else {

            if ($_REQUEST["opcion"] == "confirmar") {
                $this->confirmar();
            } else {

                if ($_REQUEST["opcion"] == "confirmarEditar") {
                    $this->confirmarEditar();
                }
            }
        }
    }

    function __construct() {

        $this->miConfigurador = Configurador::singleton();
        $this->miInspectorHTML = InspectorHTML::singleton();
        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");
        $this->miMensaje = Mensaje::singleton();
        $this->sql = new SqlindexPres();

        $conexion = "aplicativo";
        $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        if (!$this->miRecursoDB) {

            $this->miConfigurador->fabricaConexiones->setRecursoDB($conexion, "tabla");
            $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        }
    }

    public function setRuta($unaRuta) {
        $this->ruta = $unaRuta;
        //Incluir las funciones
    }

    function setSql($a) {
        $this->sql = $a;
    }

    function setFuncion($funcion) {
        $this->funcion = $funcion;
    }

    public function setLenguaje($lenguaje) {
        $this->lenguaje = $lenguaje;
    }

    public function setFormulario($formulario) {
        $this->formulario = $formulario;
    }

}

?>
