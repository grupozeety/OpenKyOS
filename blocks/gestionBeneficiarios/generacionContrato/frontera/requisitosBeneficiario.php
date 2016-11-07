<?php

namespace gestionBeneficiarios\generacionContrato\frontera;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}
/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Registrador {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $ruta;
	public $rutaURL;
	public function __construct($lenguaje, $formulario, $sql) {
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
	public function seleccionarForm() {
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( "pagina" );
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// Consulta información
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionBeneficiario' );
		$infoBeneficiario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$infoBeneficiario = $infoBeneficiario [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionAprobacion' );
		$estadoAprobacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionAprobacionContrato' );
		$estadoAprobacionContrato = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if ($estadoAprobacion != false) {
			
			if ($estadoAprobacionContrato != false) {
				$estadoAprobacion = array_merge ( $estadoAprobacion, $estadoAprobacionContrato );
			}
			
			foreach ( $estadoAprobacion as $key => $values ) {
				if ($estadoAprobacion [$key] ['ruta_relativa'] != NULL) {
					
					$variable = "pagina=" . $miPaginaActual;
					$variable .= "&opcion=verArchivo";
					$variable .= "&mensaje=confirma";
					$variable .= "&id_beneficiario=" . $_REQUEST ['id_beneficiario'];
					$variable .= "&tipo_beneficiario=" . $infoBeneficiario ['tipo_beneficiario'];
					$variable .= "&ruta=" . $estadoAprobacion [$key] ['ruta_relativa'];
					$variable .= "&archivo=" . $estadoAprobacion [$key] ['id'];
					$variable .= "&tipologia=" . $estadoAprobacion [$key] ['tipologia_documento'];
					$url = $this->miConfigurador->configuracion ["host"] . $this->miConfigurador->configuracion ["site"] . "/index.php?";
					$enlace = $this->miConfigurador->configuracion ['enlace'];
					$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
					$_REQUEST [$enlace] = $enlace . '=' . $variable;
					$redireccion [$estadoAprobacion [$key] ['codigo_requisito']] = $url . $_REQUEST [$enlace];
				}
			}
		}
		
		// Para revisar los requisitos según el perfil
		$a = 0;
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaRequisitos', $infoBeneficiario ['tipo_beneficiario'] );
		$requisitos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaRequisitosContrato' );
		$requisitosContrato = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
			// Cuando Existe Registrado un borrador del contrato
		if (is_null ( $infoBeneficiario ['id_contrato'] ) != true) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultaRequisitosVerificados' );
			$infoArchivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarContratoExistente' );
			$infoArchivoContrato = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if ($infoArchivoContrato != FALSE) {
				$infoArchivo = array_merge ( $infoArchivo, $infoArchivoContrato );
			}
			
			if ($requisitosContrato != FALSE) {
				$requisitos = array_merge ( $requisitos, $requisitosContrato );
			}
		}


		
	
		// Rescatar los datos de este bloque
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();
		// -------------------------------------------------------------------------------------------------
		
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque ['nombre'];
		$atributos ['id'] = $esteCampo;
		$atributos ['nombre'] = $esteCampo;
		// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		$atributos ['tipoFormulario'] = 'multipart/form-data';
		// Si no se coloca, entonces toma el valor predeterminado 'POST'
		$atributos ['metodo'] = 'POST';
		// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
		$atributos ['action'] = 'index.php';
		$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
		// Si no se coloca, entonces toma el valor predeterminado.
		$atributos ['estilo'] = '';
		$atributos ['marco'] = true;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{
			{
				$esteCampo = 'Agrupacion';
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Cargue Requisitos Tipo de Beneficiario: " . $infoBeneficiario ['descripcion_tipo'];
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
				{
					if (is_null ( $infoBeneficiario ['id_contrato'] ) != true && ! isset ( $_REQUEST ['mensaje'] )) {
						$_REQUEST ['mensaje'] = 'inserto';
						$this->mensaje ();
						unset ( $atributos );
					} elseif (isset ( $_REQUEST ['mensaje'] )) {
						
						$this->mensaje ();
						unset ( $atributos );
					}
					
					$esteCampo = 'codigo_beneficiario'; // Nombre Beneficiario
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'textoElegante';
					$atributos ['columnas'] = 1;
					$atributos ['dobleLinea'] = false;
					$atributos ['tabIndex'] = $tab;
					$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . $infoBeneficiario ['id_beneficiario'];
					$tab ++;
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoTexto ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'nombre_beneficiario'; // Nombre Beneficiario
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'textoElegante';
					$atributos ['columnas'] = 1;
					$atributos ['dobleLinea'] = false;
					$atributos ['tabIndex'] = $tab;
					$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . $infoBeneficiario ['nombre'] . " " . $infoBeneficiario ['primer_apellido'] . " " . $infoBeneficiario ['segundo_apellido'];
					$tab ++;
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoTexto ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'identificacion_beneficiario'; // Identificacion Beneficiario
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'textoElegante';
					$atributos ['columnas'] = 1;
					$atributos ['dobleLinea'] = false;
					$atributos ['tabIndex'] = $tab;
					$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . $infoBeneficiario ['identificacion'];
					$tab ++;
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoTexto ( $atributos );
					unset ( $atributos );
					
					$filasTabla = array ();
					foreach ( $requisitos as $key => $values ) {
						$esteCampo = $requisitos [$key] ['codigo']; // Código documento
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = false;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 6;
						$atributos ["tamanno"] = 500000;
						$atributos ["etiqueta"] = "<b>" . $requisitos [$key] ['codigo'] . "</b> " . $requisitos [$key] ['descripcion'];
						if ($requisitos [$key] ['obligatoriedad'] == 1) {
							$atributos ["etiqueta"] = "<b>" . $requisitos [$key] ['codigo'] . "</b> " . $requisitos [$key] ['descripcion'] . "<b> (*)</b>";
						}
						$atributos ["estilo"] = "file";
						$atributos ["anchoCaja"] = 1;
						
						$atributos ["bootstrap"] = true;
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos );
						
						if (isset ( $infoArchivo )) {
							
							$indice = array_search ( $requisitos [$key] ['codigo'], array_column ( $infoArchivo, 'codigo_requisito' ), true );
							
							if (! is_null ( $indice ) && isset ( $redireccion [$requisitos [$key] ['codigo']] )) {
								$cadena = "<center><a href='" . $redireccion [$requisitos [$key] ['codigo']] . "' >" . "<b>" . $requisitos [$key] ['codigo'] . "</b> " . $requisitos [$key] ['descripcion'] . "</a></center>";
							} else {
								$a ++;
								$cadena = "<center>" . $this->miFormulario->campoCuadroTexto ( $atributos ) . "</center>";
							}
						} else {
							$a ++;
							$cadena = "<center>" . $this->miFormulario->campoCuadroTexto ( $atributos ) . "</center>";
						}
						$filasTabla [$key] = $cadena;
						unset ( $atributos );
					}
					
					$tabla = "<table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                             <th><center>Documento<center></th>
                                        </tr>
                                        </thead>";
					foreach ( $filasTabla as $key => $values ) {
						$tabla .= " <tr>
                                             <td >" . $filasTabla [$key] . "</td>
                                        </tr>";
					}
					$tabla .= "</table>";
					
					echo $tabla;
				}
				{
					// ------------------Division para los botones-------------------------
					$atributos ["id"] = "botones";
					$atributos ["estilo"] = "marcoBotones";
					$atributos ["estiloEnLinea"] = "display:block;";
					echo $this->miFormulario->division ( "inicio", $atributos );
					unset ( $atributos );
					if ($a > 0) {
						// -----------------CONTROL: Botón ----------------------------------------------------------------
						$esteCampo = 'botonCargarRequisitos';
						$atributos ["id"] = $esteCampo;
						$atributos ["tabIndex"] = $tab;
						$atributos ["tipo"] = 'boton';
						// submit: no se coloca si se desea un tipo button genérico
						$atributos ['submit'] = true;
						$atributos ["simple"] = true;
						$atributos ["estiloMarco"] = '';
						$atributos ["estiloBoton"] = 'default';
						$atributos ["block"] = false;
						// verificar: true para verificar el formulario antes de pasarlo al servidor.
						$atributos ["verificar"] = '';
						$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
						$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
						unset ( $atributos );
					} else {
						$esteCampo = 'botonVerificar';
						$atributos ["id"] = $esteCampo;
						$atributos ["tabIndex"] = $tab;
						$atributos ["tipo"] = 'boton';
						// submit: no se coloca si se desea un tipo button genérico
						$atributos ['submit'] = true;
						$atributos ["simple"] = true;
						$atributos ["estiloMarco"] = '';
						$atributos ["estiloBoton"] = 'default';
						$atributos ["block"] = false;
						// verificar: true para verificar el formulario antes de pasarlo al servidor.
						$atributos ["verificar"] = '';
						$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
						$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
						unset ( $atributos );
					}
					// ------------------Fin Division para los botones-------------------------
					echo $this->miFormulario->division ( "fin" );
					unset ( $atributos );
				}
				echo $this->miFormulario->agrupacion ( 'fin' );
				unset ( $atributos );
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
				
				// $valorCodificado = "action=" . $esteBloque["nombre"];
				
				if ($a > 0) {
					$valorCodificado = "action=" . $esteBloque ["nombre"];
				} else {
					$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
				}
				
				$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
				$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
				
				if ($a > 0) {
					$valorCodificado .= "&opcion=cargarRequisitos";
					$valorCodificado .= "&proceso=cargueRequisitos";
				} else {
					$valorCodificado .= "&opcion=verificarRequisitos";
					$valorCodificado .= "&proceso=verificarRequisitos";
				}
				
				$valorCodificado .= "&tipo=" . $infoBeneficiario ['tipo_beneficiario'];
				$valorCodificado .= "&id_beneficiario=" . $_REQUEST ['id_beneficiario'];
				
				if (is_null ( $infoBeneficiario ['id_contrato'] ) != true) {
					$valorCodificado .= "&numero_contrato=" . $infoBeneficiario ['numero_contrato'];
				}
				
				/**
				 * SARA permite que los nombres de los campos sean dinámicos.
				 * Para ello utiliza la hora en que es creado el formulario para
				 * codificar el nombre de cada campo.
				 */
				$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
				// Paso 2: codificar la cadena resultante
				$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
				
				$atributos ["id"] = "formSaraData"; // No cambiar este nombre
				$atributos ["tipo"] = "hidden";
				$atributos ['estilo'] = '';
				$atributos ["obligatorio"] = false;
				$atributos ['marco'] = true;
				$atributos ["etiqueta"] = "";
				$atributos ["valor"] = $valorCodificado;
				echo $this->miFormulario->campoCuadroTexto ( $atributos );
				unset ( $atributos );
			}
		}
		
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
	public function mensaje() {
		// var_dump($_REQUEST);
		$atributos ["mensaje"] = "";
		switch ($_REQUEST ['mensaje']) {
			case 'inserto' :
				
				if (isset ( $_REQUEST ['alfresco'] ) && $_REQUEST ['alfresco'] > 0) {
					$estilo_mensaje = 'warning';
					$atributos ["mensaje"] = '<br>Errores de Gestor Documental:' . $_REQUEST ['alfresco'];
				} else {
					$estilo_mensaje = 'success';
					$atributos ["mensaje"] = 'Requisitos Correctamente Subidos.';
				}
				break;
			
			case 'noinserto' :
				$estilo_mensaje = 'error'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Error al validar los Requisitos.<br>Verifique los Documentos.';
				break;
			
			case 'insertoInformacionContrato' :
				$estilo_mensaje = 'success'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Se ha registrado la información de contrato con exito.<br>Habilitado la Opcion de Descargar Contrato';
				break;
			
			case 'noInsertoInformacionContrato' :
				$estilo_mensaje = 'error'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Error al registrar información del contrato';
				break;
			
			case 'verifico' :
				$estilo_mensaje = 'success'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Documento Verificado';
				break;
			
			case 'noverifico' :
				$estilo_mensaje = 'warning'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Atención, fallo en actualización.';
				break;
			
			case 'novalido' :
				$estilo_mensaje = 'error'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Tipo de Archivo no Válido.';
				break;
			
			default :
				// code...
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
}


$miSeleccionador = new Registrador ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->seleccionarForm ();

?>

