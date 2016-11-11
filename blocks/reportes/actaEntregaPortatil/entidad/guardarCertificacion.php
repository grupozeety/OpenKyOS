<?php
namespace reportes\actaEntregaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

use reportes\actaEntregaPortatil\entidad\Redireccionador;

include_once 'Redireccionador.php';
class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;
    public $esteRecursoDB;
    public $datos_contrato;
    public $rutaURL;
    public $rutaAbsoluta;
    public $clausulas;
    public $registro_info_contrato;
    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }
        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         *  1. CargarArchivos en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Procesar Informacion Contrato
         **/

        $this->procesarInformacion();
        
        if ($this->archivos_datos != '') {

            include_once "guardarDocumentoCertificacion.php";

        }
        
        if ($this->registroActa) {
            Redireccionador::redireccionar("InsertoInformacionActa");
        } else {
            Redireccionador::redireccionar("NoInsertoInformacionActa");
        }

    }

    public function procesarInformacion() {

        if ($this->archivos_datos === '') {
            $url_firma_beneficiario = '';
            //$url_firma_contratista = '';

        } else {

            $url_firma_beneficiario = $this->archivos_datos[0]['ruta_archivo'];

            //$url_firma_contratista = $this->archivos_datos[0]['ruta_archivo'];

        }

        
        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'nombres' => $_REQUEST['nombres'],
            'primer_apellido' => $_REQUEST['primer_apellido'],
            'segundo_apellido' => $_REQUEST['segundo_apellido'],
            'identificacion' => $_REQUEST['numero_identificacion'],
        	'tipo_documento' => $_REQUEST['tipo_documento'],
        	'fecha_instalacion' => $_REQUEST['fecha_instalacion'],
        	'fecha_instalacion' => $_REQUEST['fecha_instalacion'],
        	'tipo_beneficiario' => $_REQUEST['tipo_beneficiario'],
        	'estrato' => $_REQUEST['estrato'],
        	'direccion' => $_REQUEST['direccion'],
        	'urbanizacion' => $_REQUEST['urbanizacion'],
        	'id_urbanizacion' => $_REQUEST['id_urbanizacion'],
        	'departamento' => $_REQUEST['departamento'],
        	'municipio' => $_REQUEST['municipio'],
        	'codigo_dane' => $_REQUEST['codigo_dane'],
        	'contacto' => $_REQUEST['contacto'],
        	'telefono' => $_REQUEST['telefono'],
        	'tipo_tecnologia' => $_REQUEST['tipo_tecnologia'],
            'ciudad_expedicion_identificacion' => $_REQUEST['ciudad'],
            'ciudad_firma' => $_REQUEST['ciudad_firma'],
            'ruta_firma' => $url_firma_beneficiario,
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarActaEntrega', $arreglo);
        $cadenaSql = str_replace("''", 'null', $cadenaSql);
        $this->registroActa = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
        
    }

    public function cargarArchivos() {

        $archivo_datos = '';
        foreach ($_FILES as $key => $archivo) {

            if ($archivo['error'] == 0) {

                $this->prefijo = substr(md5(uniqid(time())), 0, 6);
                /*
                 * obtenemos los datos del Fichero
                 */
                $tamano = $archivo['size'];
                $tipo = $archivo['type'];
                $nombre_archivo = str_replace(" ", "", $archivo['name']);
                /*
                 * guardamos el fichero en el Directorio
                 */
                $ruta_absoluta = $this->rutaAbsoluta . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $ruta_relativa = $this->rutaURL . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $archivo['rutaDirectorio'] = $ruta_absoluta;

                if (!copy($archivo['tmp_name'], $ruta_absoluta)) {
                    Redireccionador::redireccionar("ErrorCargarFicheroDirectorio");
                }

                $archivo_datos[] = array(
                    'ruta_archivo' => $ruta_relativa,
                    'nombre_archivo' => $archivo['name'],
                    'campo' => $key,
                );
                
            }

        }

        $this->archivos_datos = $archivo_datos;

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

