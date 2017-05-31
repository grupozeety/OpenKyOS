<?php
namespace gestionBeneficiarios\generacionContrato\frontera;
if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Contrato {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $ruta;
    public $rutaURL;
    public function __construct($lenguaje, $formulario, $sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;

        $esteBloque = $this->miConfigurador->configuracion['esteBloque'];

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        if (!isset($esteBloque["grupo"]) || $esteBloque["grupo"] == "") {
            $ruta .= "/blocks/" . $esteBloque["nombre"] . "/";
            $this->rutaURL .= "/blocks/" . $esteBloque["nombre"] . "/";
        } else {
            $this->ruta .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
            $this->rutaURL .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
        }
    }
    public function mostrarContrato() {

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "openproject";
        $esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //Consulta información

        if (isset($_REQUEST['opcion']) && $_REQUEST['opcion'] == 'editarContrato') {

            $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionContratoParticular');
            $contratoInfo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            //exit;

            $arreglo = array(
                'nombres' => $contratoInfo['nombres'],
                'primer_apellido' => $contratoInfo['primer_apellido'],
                'segundo_apellido' => $contratoInfo['segundo_apellido'],
                'tipo_documento' => $contratoInfo['tipo_documento'],
                'numero_identificacion' => $contratoInfo['numero_identificacion'],
                'direccion_domicilio' => $contratoInfo['direccion_domicilio'],
                'departamento' => $contratoInfo['departamento'],
                'municipio' => $contratoInfo['municipio'],
                'urbanizacion' => $contratoInfo['urbanizacion'],
                'barrio' => $contratoInfo['barrio'],
                'estrato' => $contratoInfo['estrato'],
                'telefono' => $contratoInfo['telefono'],
                'celular' => $contratoInfo['celular'],
                'correo' => $contratoInfo['correo'],
                'velocidad_internet' => $contratoInfo['velocidad_internet'],
                'num_manzana' => $contratoInfo['manzana'],
                'num_bloque' => $contratoInfo['bloque'],
                'num_torre' => $contratoInfo['torre'],
                'num_apto_casa' => $contratoInfo['casa_apartamento'],
                'tipo_tecnologia' => $contratoInfo['tipo_tecnologia'],
                'medio_pago' => $contratoInfo['medio_pago'],
                'tipo_pago' => $contratoInfo['tipo_pago'],
                'estrato_economico' => $contratoInfo['estrato_socioeconomico'],
                'nombre_comisionador' => $contratoInfo['nombre_comisionador'],
                'lote' => $contratoInfo['lote'],
                'interior' => $contratoInfo['interior'],
                'piso' => $contratoInfo['piso'],
                'fecha_contrato' => $contratoInfo['fecha_contrato'],
                // 'clausulas' => '',

            );

            $_REQUEST = array_merge($_REQUEST, $arreglo);

        } else {

            $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');

            $infoBeneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            $arreglo = array(
                'nombres' => $infoBeneficiario['nombre'],
                'primer_apellido' => $infoBeneficiario['primer_apellido'],
                'segundo_apellido' => $infoBeneficiario['segundo_apellido'],
                'tipo_documento' => $infoBeneficiario['tipo_documento'],
                'numero_identificacion' => $infoBeneficiario['identificacion'],
                'direccion_domicilio' => $infoBeneficiario['direccion'],
                'departamento' => $infoBeneficiario['nombre_departamento'],
                'municipio' => $infoBeneficiario['nombre_municipio'],
                'urbanizacion' => $infoBeneficiario['proyecto'],
                'estrato' => $infoBeneficiario['tipo_beneficiario'],
                'telefono' => $infoBeneficiario['telefono'],
                'celular' => $infoBeneficiario['celular'],
                'correo' => $infoBeneficiario['correo'],
                'num_manzana' => $infoBeneficiario['manzana'],
                'num_bloque' => $infoBeneficiario['bloque'],
                'num_torre' => $infoBeneficiario['torre'],
                'num_apto_casa' => $infoBeneficiario['apartamento'],
                'lote' => $infoBeneficiario['lote'],
                'interior' => $infoBeneficiario['interior'],
                // 'clausulas' => '',

            );

            $_REQUEST = array_merge($_REQUEST, $arreglo);

            if (is_null($infoBeneficiario['id_contrato']) != true) {

                $cadenaSql = $this->miSql->getCadenaSql('consultaRequisitosVerificados');
                $infoArchivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            }

        }

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------

        $atributosGlobales['campoSeguro'] = 'true';

        $_REQUEST['tiempo'] = time();
        // -------------------------------------------------------------------------------------------------

        // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque['nombre'];
        $atributos['id'] = $esteCampo;
        $atributos['nombre'] = $esteCampo;
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

        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);
        {

            {

                $esteCampo = 'Agrupacion';
                $atributos['id'] = $esteCampo;
                $atributos['leyenda'] = "<b>Número de Contrato : " . $_REQUEST['numero_contrato'] . "</b>";
                echo $this->miFormulario->agrupacion('inicio', $atributos);
                unset($atributos);

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "espacio_trabajo";
                $atributos["estilo"] = " ";
                $atributos["estiloEnLinea"] = "display:block;height:700px;overflow-y:auto;overflow-x:hidden;";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {

                    $esteCampo = 'Agrupacion';
                    $atributos['id'] = $esteCampo;
                    $atributos['leyenda'] = "Fotografías Contrato";
                    echo $this->miFormulario->agrupacion('inicio', $atributos);
                    unset($atributos);

                    $esteCampo = "foto_pag1";
                    $atributos["id"] = $esteCampo;
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["anchoCaja"] = "12";
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = " ";
                    $atributos["estilo"] = "file";
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["bootstrap"] = true;
                    $tab++;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "foto_pag2";
                    $atributos["id"] = $esteCampo;
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["anchoCaja"] = "12";
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = " ";
                    $atributos["estilo"] = "file";
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["bootstrap"] = true;
                    $tab++;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "botones";
                    $atributos["estilo"] = "marcoBotones";
                    $atributos["estiloEnLinea"] = "";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        // -----------------CONTROL: Botón ----------------------------------------------------------------
                        $esteCampo = 'botonGuardarCargar';
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
                $valorCodificado .= "&opcion=guardarSoporteContrato";
                $valorCodificado .= "&tipo_beneficiario=" . $_REQUEST['tipo_beneficiario'];
                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                $valorCodificado .= "&numero_contrato=" . $_REQUEST['numero_contrato'];

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
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }

}

$miSeleccionador = new Contrato($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->mostrarContrato();

?>
