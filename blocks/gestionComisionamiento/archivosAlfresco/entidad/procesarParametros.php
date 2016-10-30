<?php
namespace gestionComisionamiento\archivosAlfresco\entidad;
include_once('RestClient.class.php');

class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;


    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
     
    }
    
    public function procesarFormulario(){
        $_REQUEST['tiempo'] = time();
              foreach ($_FILES as $key=>$values){
        	$archivo=$_FILES[$key];
        }
              
        $args = new \CURLFile('/tmp/texto.txt',$archivo['type'],'archivo.txt');
        //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
       // $fp=fopen($archivo,'r');
        var_dump($args);
    
        $beneficiario='4444';
        $archivo['name']='pruebea';
    	$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoDirectorio', '' );
		$directorio = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoUser', $beneficiario );
		$variable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoLog', $beneficiario );
		$datosConexion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
		$url = "http://" . $datosConexion [0] ['host'] . "/alfresco/service/api/site/folder/" . $variable [0] ['site'] . "/documentLibrary/" . $directorio [0] [0] . "/" . $variable [0] ['padre'] . "/" . $variable [0] ['hijo']; // pendiente la pagina para modificar parametro
		
		$archivo =  array (
				'filedata' =>'@' . $args,
				'siteid' => $variable [0] ['site'],
				'containerid' => 'documentLibrary',
				'uploaddirectory' => "/". $directorio[0][0] . "/" . $variable [0] ['padre'] . "/" . $variable[0]['hijo'],
				'contenttype' => 'cm:content',
		 );
		
		$result = RestClient::post($url, $archivo, $datosConexion [0] ['usuario'], $datosConexion [0] ['password'] );
		var_dump($result);
		$json_decode = json_decode ( json_encode ( $result->getResponse () ), true );
		
		var_dump($json_decode);
		exit;
		$validacion = strpos ( $json_decode, 'error' );
		if (! is_numeric ( $validacion )) {
		
			$estado = array (
					'estado' => 0,
					'mensaje' => "Documento subido exitosamente en el Gestor de Documentos" 
			);
		} else {
			$estado = array (
					'estado' => 1,
					'mensaje' => "Error en la subida de documento." 
			);
		}

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);

$resultado = $miProcesador->procesarFormulario ();

?>

