<?php

namespace facturacion\masivoCalFactura\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once 'Redireccionador.php';
include_once 'RestClient.class.php';
class sincronizarErp {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $esteRecursoDB;
	public function __construct($lenguaje, $sql) {
		date_default_timezone_set ( 'America/Bogota' );

		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;

		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );

		$this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );

		if (! isset ( $_REQUEST ["bloqueGrupo"] ) || $_REQUEST ["bloqueGrupo"] == "") {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloque"] . "/";
		} else {
			$this->rutaURL .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
			$this->rutaAbsoluta .= "/blocks/" . $_REQUEST ["bloqueGrupo"] . "/" . $_REQUEST ["bloque"] . "/";
		}
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

		$_REQUEST ['tiempo'] = time ();
	}

	/**
	 * Crear Cliente en ERPNext
	 */
	public function crearUrlCliente($parametros = '') {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiario', $_REQUEST ['id_beneficiario'] );
		$ben = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		$base = array (
				"customer_name" => $ben [0] [0],
				"customer_type" => "Individual",
				"customer_group" => $ben [0] [2],
				"territory" => "Colombia",
				"customer_details" => $ben [0] [2]
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

	/**
	 * Crear Factura en ERPNext
	 */
	public function crearUrlFactura($parametros = '') {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiario', $_REQUEST ['id_beneficiario'] );
		$ben = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		$cadenaSql = $this->miSql->getCadenaSql ( 'parametrosGlobales', $_REQUEST ['id_beneficiario'] );
		$resultado = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		foreach ( $resultado as $key => $values ) {
			$valores [$values [0]] = $values [1];
		}

		$fechaOportuna = date ( 'Y-m-d', strtotime ( $parametros ['fecha'] . '+ ' . $valores ['diasPago'] . ' day' ) );

		if(strtotime ( $fechaOportuna)<strtotime ( date('Y-m-d'))){
			$fechaOportuna=date('Y-m-d');
		}
		
		$parametros['fechaOportuna']=$fechaOportuna;
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFecha', 	$parametros );
		$fecha = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "registro" );
		
		$items [0] = array (
				"qty" => 1,
				"item_name" => $valores ['itemName_erp'],
				"item_code" => $valores ['itemCode_erp'],
				"stock_uom" => $valores ['stockUOM_erp'],
				"doctype" => "Sales Invoice Item",
				"description" => $parametros ['id_ciclo'],
				"rate" => $parametros ['total_factura'],
				"debit_to" => $valores ['cuentaDebito_erp'],
				"parenttype" => "Sales Invoice",
				"parentfield" => "items"
		);
		$base = array (
				"customer" => $ben [0] [0],
				"customer_name" => $ben [0] [0],
				"due_date" => 	$fechaOportuna ,
				"customer_group" => $ben [0] [2],
				"territory" => "Colombia",
				"customer_details" => $ben [0] [2],
				"title" => $parametros ['id_factura']. " - " . $ben [0] [0],
				"income_account" => $valores ['cuentaCredito_erp'],
				"items" => $items,
				"docstatus" => 1
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
		$variable .= "&metodo=crearFactura";
		$variable .= "&variables=" . json_encode ( $base );
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $enlace );
		// URL definitiva
		$material = $url . $cadena;

		return $material;
	}
	public function crearFactura($url) {
		$variable = array (
				'estado' => 1,
				'mensaje' => "Error creando Factura en ERPNext"
		);

		$operar = file_get_contents ( $url );
		$validacion = strpos ( $operar, 'modified_by' );

		$res = ( array ) json_decode ( $operar );
		$res2 = ( array ) ($res ['items'] [0]);

		if (is_numeric ( $validacion )) {
			$variable = array (
					'estado' => 0,
					'mensaje' => "Factura Creada con Éxito",
					'recibo' => $res2 ['parent']
			);
		}

		return $variable;
	}
	public function actualizarFactura($invoice) {
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFactura', $invoice );
		$invoice = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	}
}

$miProcesador = new sincronizarErp ( $this->lenguaje, $this->sql );
?>

