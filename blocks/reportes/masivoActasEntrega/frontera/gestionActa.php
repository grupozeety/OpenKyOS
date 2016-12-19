<?php

namespace reportes\masivoActas\frontera;

use reportes\masivoActas\entidad\GenerarDocumento;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}
class GestionarContrato {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $ruta;
	public $rutaURL;
	public function __construct($lenguaje, $formulario, $sql) {
		
		ini_set('memory_limit', '650M');
		ini_set('max_execution_time', 100000);
		
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
		
		$esteBloque = $this->miConfigurador->configuracion ['esteBloque'];
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		if (! isset ( $esteBloque ["grupo"] ) || $esteBloque ["grupo"] == "") {
			$ruta .= "/blocks/" . $esteBloque ["nombre"] . "/";
			$this->rutaURL .= "/blocks/" . $esteBloque ["nombre"] . "/";
		} else {
			$this->ruta .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
			$this->rutaURL .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
		}
	}
	public function formulario() {
		include_once $this->ruta . "entidad/guardarDocumentoCertificacion.php";
		
		$beneficiarios = explode ( ", ", $_REQUEST ['beneficiario'] );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( "pagina" );
		
		$conexion = "produccion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$_REQUEST['beneficiario'] ='92028474, 64871425, 64867738, 92029212, 9308275, 92034015, 1100542788, 64871339, 951379, 18895063, 64871391, 33083215, 3988641, 64871926, 3845528, 1100395177, 23161881, 64868910, 23160152, 64865278, 23161841, 64866514, 64697130, 22242286, 64866786, 92027060, 92032770, 22868353, 64870791, 64868702, 92031563, 92385057, 64565939, 64871676, 64741793, 1100395423, 64868440, 92034047, 92030959, 92097748, 64870737, 32940249, 33083496, 64871125, 64865789, 1100393969, 23160377, 64868854, 66742534, 3989174, 64868883, 64870584, 92033726, 1100394489, 3989103, 23159328, 92026291, 92033017, 64871618, 64926372, 15675155, 64867266, 92025853, 64870171, 64871240, 64871632, 64870167, 33238632, 1100396650, 64479051, 92032292, 1100394641, 64866582, 1100396114, 92096645, 22889560, 1100394124, 33082504, 64871809, 9314139, 92028425, 8860976, 92031679, 92189520, 92033554, 64868887, 23161411, 1100393968, 92032422, 64868729, 64867143, 64868156, 92033114, 64871155, 92031266, 64869150, 92031437, 64871307, 33180060, 92026639, 64870425, 92033005, 3996318, 1005441741, 64871801, 3989480, 64868699, 18855159, 1100394221, 3988540, 92033037, 92031708, 92033680, 3944327, 64866079, 23160562, 22866084, 64869753, 64872013, 64930883, 64871411, 64869996, 1100392915, 23068567, 64868534, 64748874, 23068864, 92032749, 3988825, 64869662, 64871149, 92033358, 64872061, 64868410, 1100395834, 64869921, 1100395400, 64867779, 64868258, 92033703, 23068233, 92028208, 92026029, 3989457, 22938119, 92030374, 92028268, 92097074, 92029762, 64749281, 3944094, 92032653, 950799, 92032479, 92029005, 42345009, 92032237, 73238247, 92027121, 92031023, 1100397630, 64749011, 64866790, 92005025, 32876833, 64749500, 64870805, 92034065, 92028900, 92600964, 1007649607, 92029993, 64871622, 1100392776, 64866563, 92031174, 64868706, 23160909, 92027804, 1100392061, 92028615, 92026914, 64867368, 7450743, 42203291, 92033546, 92698932, 92030615, 92028859, 64869991, 64868679, 64868495, 23159475, 64866982, 3853674, 92033837, 92029797, 64868908, 1100392559, 92028133, 8860953, 22246937, 64868315, 92029729, 12593037, 92032034, 43693181, 1100395830, 60368768, 92029096, 92026881, 18895037, 64742100, 64868215, 92030555, 73551217, 23066465, 92030608, 92030606, 64559296, 1100394665, 92027982, 92028119, 64865071, 92000009, 92027594, 3944086, 92029585, 3835634, 3944110, 1102575850, 64872087, 1100393725, 92033157, 64868106, 92028608, 92027337, 64871040, 64867313, 1100393723, 33084421, 92032909, 64872006, 92034063, 1100395873, 23161885, 1100395403, 43972595, 92031483, 64866365, 32930655, 64871437, 92028515, 73087328, 92032683';
		
		$contratos = explode(", ",$_REQUEST['beneficiario']);

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
		
		foreach ( $contratos as $generarActa ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarInformacionActa', $generarActa);
			$infoCertificado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" )[0];

			$_REQUEST = $infoCertificado;
			
			$_REQUEST['fecha_instalacion'] = date("d") . "-" . date("m") . "-" . date("Y");
			$miDocumento = new GenerarDocumento ();
			$miDocumento->crearActa ( $this->miSql, $this->rutaURL, $generarActa, $this->lenguaje);
			
			unset ( $miDocumento );
			$miDocumento = NULL;
			
			unset ( $_REQUEST );
			$_REQUEST = NULL;
			
			$cadenaSql = NULL;
			
			unset($infoCertificado);
			$infoCertificado = NULL;
			
			unset($beneficiarios);
			$beneficiarios = NULL;
			
			echo $generarActa . "<br>";
		}
		
		// $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
		// $miPaginaActual = $this->miConfigurador->getVariableConfiguracion("pagina");
		
		// $conexion = "interoperacion";
		// $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// $_REQUEST ['id_beneficiario'] = $_REQUEST ['id'];
		// $_REQUEST['mensaje'] = "insertoInformacionCertificado";
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionCertificado' );
		// $infoCertificado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
		// if($infoCertificado){
		
		// $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
		// $infoBeneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
		
		// $_REQUEST = array_merge($_REQUEST, $infoBeneficiario);
		
		// $_REQUEST = array_merge($_REQUEST, $infoCertificado);
		
		// $_REQUEST['nombres'] = $_REQUEST['nombre'];
		// $_REQUEST['numero_identificacion'] = $_REQUEST['identificacion'];
		
		// $_REQUEST['mensaje'] = "insertoInformacionCertificado";
		
		// if($infoCertificado['firmabeneficiario'] != "" && $infoCertificado['ruta_documento_ps'] == ""){
		// $_REQUEST['firmabeneficiario'] = $infoCertificado['firmabeneficiario'];
		// include_once $this->ruta . "entidad/guardarDocumentoCertificacion.php";
		// }else if($infoCertificado['firmabeneficiario_aes'] != "" && $infoCertificado['ruta_documento_ps'] == ""){
		// $_REQUEST['firmabeneficiario'] = $infoCertificado['firmabeneficiario_aes'];
		// include_once $this->ruta . "entidad/guardarDocumentoCertificacion.php";
		// }
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionCertificado' );
		// $infoCertificado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
		// $_REQUEST = array_merge($_REQUEST, $infoCertificado);
		
		// {
		
		// $anexo_dir = '';
		
		// if ($infoBeneficiario['manzana_contrato'] != 0) {
		// $anexo_dir .= " Manzana #" . $infoBeneficiario['manzana_contrato'] . " - ";
		// }
		
		// if ($infoBeneficiario['bloque_contrato'] != 0) {
		// $anexo_dir .= " Bloque #" . $infoBeneficiario['bloque_contrato'] . " - ";
		// }
		
		// if ($infoBeneficiario['torre_contrato'] != 0) {
		// $anexo_dir .= " Torre #" . $infoBeneficiario['torre_contrato'] . " - ";
		// }
		
		// if ($infoBeneficiario['casa_apto_contrato'] != 0) {
		// $anexo_dir .= " Casa/Apartamento #" . $infoBeneficiario['casa_apto_contrato'];
		// }
		
		// if ($infoBeneficiario['interior_contrato'] != 0) {
		// $anexo_dir .= " Interior #" . $infoBeneficiario['interior_contrato'];
		// }
		
		// if ($infoBeneficiario['lote_contrato'] != 0) {
		// $anexo_dir .= " Lote #" . $infoBeneficiario['lote_contrato'];
		// }
		
		// }
		// }else{
		// $_REQUEST['mensaje'] = "certificadoNoDisponible";
		// }
		
		// // Rescatar los datos de este bloque
		
		// // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		
		// {
		// $atributosGlobales['campoSeguro'] = 'true';
		// }
		
		// $_REQUEST['tiempo'] = time();
		// // -------------------------------------------------------------------------------------------------
		
		// // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		// $esteCampo = $esteBloque['nombre'];
		// $atributos['id'] = $esteCampo;
		// $atributos['nombre'] = $esteCampo;
		// // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		// $atributos['tipoFormulario'] = 'multipart/form-data';
		// // Si no se coloca, entonces toma el valor predeterminado 'POST'
		// $atributos['metodo'] = 'POST';
		// // Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
		// $atributos['action'] = 'index.php';
		// $atributos['titulo'] = $this->lenguaje->getCadena($esteCampo);
		// // Si no se coloca, entonces toma el valor predeterminado.
		// $atributos['estilo'] = '';
		// $atributos['marco'] = true;
		// $tab = 1;
		// // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		// $atributos['tipoEtiqueta'] = 'inicio';
		// echo $this->miFormulario->formulario($atributos);
		
		// {
		// {
		// $esteCampo = 'Agrupacion';
		// $atributos['id'] = $esteCampo;
		// $atributos['leyenda'] = "ACTA DE ENTREGA DE PORTATIL Y SERVICIOS";
		// echo $this->miFormulario->agrupacion('inicio', $atributos);
		// unset($atributos);
		
		// {
		
		// $this->mensaje();
		
		// if($infoCertificado){
		// // ------------------Division para los botones-------------------------
		// $atributos["id"] = "botones";
		// $atributos["estilo"] = "marcoBotones";
		// $atributos["estiloEnLinea"] = "display:block;";
		// echo $this->miFormulario->division("inicio", $atributos);
		// unset($atributos);
		
		// // Acordar Roles
		
		// {
		
		// $url = $this->miConfigurador->getVariableConfiguracion("host");
		// $url .= $this->miConfigurador->getVariableConfiguracion("site");
		// $url .= "/index.php?";
		
		// // ------------------Division para los botones-------------------------
		// $atributos["id"] = "botones_sin";
		// $atributos["estilo"] = "marcoBotones";
		// $atributos["estiloEnLinea"] = "display:block;";
		// echo $this->miFormulario->division("inicio", $atributos);
		// unset($atributos);
		
		// {
		
		// $valorCodificado = "action=" . $esteBloque["nombre"];
		// $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
		// $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
		// $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
		// $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
		// $valorCodificado .= "&opcion=generarCertificacion";
		// $valorCodificado .= "&tipo_beneficiario=" . $infoBeneficiario['tipo_beneficiario'];
		// $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
		// $valorCodificado .= "&estrato_socioeconomico=" . $infoBeneficiario['estrato_socioeconomico'];
		
		// $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
		// $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);
		
		// $urlpdfNoFirmas = $url . $cadena;
		
		// echo "<b><a id='link_b' href='" . $urlpdfNoFirmas . "'>Acta Entrega de Servicios Instalados <br> Sin Firma</a></b>";
		
		// }
		
		// // ------------------Fin Division para los botones-------------------------
		// echo $this->miFormulario->division("fin");
		// unset($atributos);
		
		// // ------------------Division para los botones-------------------------
		// $atributos["id"] = "botones_pdf";
		// $atributos["estilo"] = "marcoBotones";
		// $atributos["estiloEnLinea"] = "display:block;";
		// echo $this->miFormulario->division("inicio", $atributos);
		// unset($atributos);
		
		// {
		// echo "<b><a id='link_a' target='_blank' href='" . $infoCertificado['ruta_documento_ps'] . "'>Acta Entrega de Servicios Instalados <br> Con Firma</a></b>";
		// }
		
		// // ------------------Fin Division para los botones-------------------------
		// echo $this->miFormulario->division("fin");
		// unset($atributos);
		
		// }
		
		// // ------------------Fin Division para los botones-------------------------
		// echo $this->miFormulario->division("fin");
		// unset($atributos);
		// }
		
		// }
		// echo $this->miFormulario->agrupacion('fin');
		// unset($atributos);
		// }
		
		// }
		
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
	public function mensaje() {
		switch ($_REQUEST ['mensaje']) {
			
			case 'insertoInformacionCertificado' :
				$estilo_mensaje = 'success'; // information,warning,error,validation
				$atributos ["mensaje"] = '<b>Acta de Entrega Disponible</b>';
				break;
			
			case 'certificadoNoDisponible' :
				$estilo_mensaje = 'error'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Al parecer no se ha generado el Acta de Entrega de Portatil o el Acta de Entrega de Servicios<b>';
				break;
		}
		// ------------------Division para los botones-------------------------
		$atributos ['id'] = 'divMensaje';
		$atributos ['estilo'] = 'marcoBotones';
		echo $this->miFormulario->division ( "inicio", $atributos );
		
		// -------------Control texto-----------------------
		$esteCampo = 'mostrarMensaje';
		$atributos ["tamanno"] = '';
		$atributos ["etiqueta"] = '';
		$atributos ["estilo"] = $estilo_mensaje;
		$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
		echo $this->miFormulario->campoMensaje ( $atributos );
		unset ( $atributos );
		
		// ------------------Fin Division para los botones-------------------------
		echo $this->miFormulario->division ( "fin" );
		unset ( $atributos );
	}
	public function mensajeModal() {
		switch ($_REQUEST ['mensaje']) {
			
			case 'insertoInformacionContrato' :
				$mensaje = "Exito en el registro información del Acta de Entrega";
				$atributos ['estiloLinea'] = 'success'; // success,error,information,warning
				break;
			case 'errorGenerarArchivo' :
				$mensaje = "Error en el registro de información del Acta de Entrega";
				$atributos ['estiloLinea'] = 'error'; // success,error,information,warning
				
				break;
		}
		
		// ----------------INICIO CONTROL: Ventana Modal Beneficiario Eliminado---------------------------------
		
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ['titulo'] = 'Mensaje';
		$atributos ['id'] = 'mensaje';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// ----------------INICIO CONTROL: Mapa--------------------------------------------------------
		echo '<div style="text-align:center;">';
		
		echo '<p><h5>' . $mensaje . '</h5></p>';
		
		echo '</div>';
		
		// ----------------FIN CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		echo '</div>';
		
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
	}
}

$miSeleccionador = new GestionarContrato ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->formulario ();

?>
