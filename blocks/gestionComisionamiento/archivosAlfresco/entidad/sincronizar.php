<?php

namespace gestionComisionamiento\archivosAlfresco\funcion;

include_once ('RestClient.class.php');

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
}
class sincronizar {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;

	}
	public function iniciarSincronizacion($beneficiario) {
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$resultado = 0;
		
		// Carpetas
		$alfresco = $this->alfresco ( $beneficiario, $archivo);
		$resultado = $alfresco ['estado'];
		
		if ($resultado > 0) {
			$mensajes = $beneficiario . ': ' . $alfresco ['mensaje'] ;
		} else {
			$mensajes = 0;
		}
		
		return $mensajes;
	}
	// La siguiente funcionalidad es para crear carpetas de Comisionamiento
	public function alfresco($beneficiario) {
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoDirectorio', '' );
		$directorio = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoUser', $beneficiario );
		$variable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'alfrescoLog', $beneficiario );
		$datosConexion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$url = "http://" . $datosConexion [0] ['host'] . "/alfresco/service/api/site/folder/" . $variable [0] ['site'] . "/documentLibrary/" . $directorio [0] [0] . "/" . $variable [0] ['padre'] . "/" . $variable [0] ['hijo']; // pendiente la pagina para modificar parametro
		
		$carchivo = json_encode ( array (
				'filedata' => $archivo,
				'siteid' => $variable [0] ['site'],
				'containerid' => 'documentLibrary',
				'uploaddirectory' => "/". $directorio[0][0] . "/" . $variable [0] ['padre'] . "/" . $variable[0]['hijo'],
				'contenttype' => 'cm:content',
		) );
		
		$result = RestClient::post ( $url, $archivo, $datosConexion [0] ['usuario'], $datosConexion [0] ['password'] );
		
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
		
		return $estado;
	}
	
	// La siguiente función ayuda a crear el Cliente para el caso de uso Asignación de Equipos a Beneficiarios en ERPNext
	public function crearCliente($url) {
		$variable = array (
				'estado' => 1,
				'mensaje' => "Error creando Cliente en ERPNext" 
		);
		
		$operar = file_get_contents ( $url );
		$validacion = strpos ( $operar, 'modified_by' );
		
		if (is_numeric ( $validacion )) {
			$variable = array (
					'estado' => 0,
					'mensaje' => "Cliente Creado con Éxito" 
			);
		}
		
		return $variable;
	}
	
	// La siguiente función ayuda a crear la Solicitud de Material de acuerdo a la lista de Materiales programada
	public function crearMaterial($url) {
		$variable = array (
				'estado' => 1,
				'mensaje' => "Error creando Solicitud de Material en ERPNext" 
		);
		$operar = file_get_contents ( $url );
		$validacion = strpos ( $operar, 'MREQ' );
		if (is_numeric ( $validacion )) {
			$variable = array (
					'estado' => 0,
					'mensaje' => "Solicitud de Material creada con ID. " . str_replace ( '"', '', $operar ) 
			);
		}
		
		return $variable;
	}
	public function recuperarOrden($url) {
		$variable = 0;
		$operar = file_get_contents ( $url );
		
		if (strlen ( $operar ) > 0) {
			$variable = json_decode ( $operar, true );
		}
		
		return $variable;
	}
	public function kit($url) {
		$variable = 0;
		$operar = file_get_contents ( $url );
		$validacion = strlen ( $operar );
		
		if ($validacion != 0) {
			$variable = json_decode ( $operar, true );
		}
		
		return $variable;
	}
	public function datosmaterial($orden, $items, $base) {
		$parametros = 0;
		
		foreach ( $items as $key => $values ) {
			$items [$key] = array_merge ( $values, array (
					'warehouse' => $orden [0] ['proyecto'] . " - CPNDC" 
			) );
		}
		
		$parametros = array (
				"title" => "Kit Comisionamiento " . $base,
				"material_request_type" => "Material Transfer",
				"id_orden_trabajo" => $orden [0] ['orden_trabajo'],
				"descripcion_orden" => 'Hogar Comisionamiento',
				"centro_costos" => $orden [0] ['proyecto'] . " - CPNDC",
				"status" => "Draft",
				"project" => $orden [0] ['proyecto'],
				"docstatus" => 0,
				"transaction_date" => date ( 'Y-m-d' ),
				"description" => "Solicitud de Kit de Comisionamiento por Agendamiento de Beneficiario",
				"items" => $items 
		);
		
		return $parametros;
	}
	
	// Aquí creamos las URL de Acceso a la información
	public function crearUrlMaterial($base = '') {
		if ($base != 0) {
			// URL base
			$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
			$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
			$url .= "/index.php?";
			// Variables
			$variable = "pagina=openKyosApi";
			$variable .= "&procesarAjax=true";
			$variable .= "&action=index.php";
			$variable .= "&bloqueNombre=" . "llamarApi";
			$variable .= "&bloqueGrupo=" . "";
			$variable .= "&tiempo=" . $_REQUEST ['tiempo'];
			$variable .= "&metodo=crearSolicitud";
			$variable .= "&variables=" . json_encode ( $base );
			// Codificar las variables
			$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $enlace );
			// URL definitiva
			$material = $url . $cadena;
		} else {
			$base = 0;
		}
		return $material;
	}
	public function kitURL($parametros = '') {
		$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
		$url .= "/index.php?";
		// Variables
		$variable = "pagina=openKyosApi";
		$variable .= "&procesarAjax=true";
		$variable .= "&action=index.php";
		$variable .= "&bloqueNombre=" . "llamarApi";
		$variable .= "&bloqueGrupo=" . "";
		$variable .= "&tiempo=" . $_REQUEST ['tiempo'];
		$variable .= "&metodo=consultarKit";
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $enlace );
		// URL definitiva
		$kit = $url . $cadena;
		
		return $kit;
	}
	public function recuperarOrdenURL($parametros = '') {
		
		// URL base
		$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
		$url .= "/index.php?";
		// Variables
		$variable = "pagina=openKyosApi";
		$variable .= "&procesarAjax=true";
		$variable .= "&action=index.php";
		$variable .= "&bloqueNombre=" . "llamarApi";
		$variable .= "&bloqueGrupo=" . "";
		$variable .= "&tiempo=" . $_REQUEST ['tiempo'];
		$variable .= "&metodo=consultarOrdenTrabajo";
		$variable .= "&variables=" . $parametros;
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $enlace );
		// URL definitiva
		$orden = $url . $cadena;
		
		return $orden;
	}
	public function crearUrlCliente($parametros = '') {
		$base = array (
				"customer_name" => $parametros,
				"customer_type" => "Individual",
				"customer_group" => "Individual",
				"territory" => "Colombia" 
		);
		
		// URL base
		$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
		$url .= "/index.php?";
		// Variables
		$variable = "pagina=openKyosApi";
		$variable .= "&procesarAjax=true";
		$variable .= "&action=index.php";
		$variable .= "&bloqueNombre=" . "llamarApi";
		$variable .= "&bloqueGrupo=" . "";
		$variable .= "&tiempo=" . $_REQUEST ['tiempo'];
		$variable .= "&metodo=crearCliente";
		$variable .= "&variables=" . json_encode ( $base );
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $enlace );
		// URL definitiva
		$material = $url . $cadena;
		
		return $material;
	}
	function resetForm() {
		foreach ( $_REQUEST as $clave => $valor ) {
			
			if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
				unset ( $_REQUEST [$clave] );
			}
		}
	}
}

?>
