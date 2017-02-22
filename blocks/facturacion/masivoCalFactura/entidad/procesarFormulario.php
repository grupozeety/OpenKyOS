<?php

namespace facturacion\calculoFactura\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once 'Redireccionador.php';
class FormProcessor {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $esteRecursoDB;
	public function __construct($lenguaje, $sql) {
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
		
		if ($_REQUEST ['urbanizacion'] != '') {
			$filtro = array (
					'urbanizacion' => $_REQUEST ['urbanizacion'] 
			);
		} elseif ($_REQUEST ['municipio'] != '') {
			$filtro = array (
					'municipio' => $_REQUEST ['municipio'] 
			);
		} elseif ($_REQUEST ['departamento'] != '') {
			$filtro = array (
					'departamento' => $_REQUEST ['departamento'] 
			);
		} else {
			Redireccionador::redireccionar ( "ErrorInformacion", '' );
		}
		
		/**Determinar Beneficiarios**/
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarBeneficiarios', $filtro );
		$this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$_REQUEST ['tiempo'] = time ();

		/**Determinar Roles**/
		/** Aquí es dónde se especifica qué periodo aplica para cada rol**/
		
		foreach ($this->beneficiarios as $key=>$values){
			//Saber qué roles tiene asociados
			
			var_dump($values['id_beneficiario']);
			exit;
			//Saber qué periodo aplica cada rol
		}
		
		
		if ($this->registroConceptos ['resultado'] == 0) {
			Redireccionador::redireccionar ( "ExitoInformacion" );
		} else {
			Redireccionador::redireccionar ( "ErrorInformacion", $this->registroConceptos ['resultado'] );
		}
	}
	
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );
?>

