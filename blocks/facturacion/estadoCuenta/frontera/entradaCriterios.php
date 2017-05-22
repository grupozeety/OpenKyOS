<?php

namespace facturacion\estadoCuenta\frontera;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Consultar {
	public $miConfigurador;
	public $lenguaje;
	public $miSql;
	public $miFormulario;
	public function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
	}
	public function seleccionarForm() {
		
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();

		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'contratoBeneficiario', $_REQUEST ['id_beneficiario'] );
		$beneficiario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'totalPagado', $_REQUEST ['id_beneficiario'] );
		$pagado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$imagen=      '';
		$cadenaSql = $this->miSql->getCadenaSql ( 'imagenServicio', $_REQUEST ['id_beneficiario'] );
		$imagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if($imagen==FALSE){
			$imagen[0][0]='Activo.png';
		}
		$conexion2 = "otun";
		$this->esteRecursoDBOtun = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion2 );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'estadoServicio', $_REQUEST ['id_beneficiario'] );
		$estadoServicio = $this->esteRecursoDBOtun->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// -------------------------------------------------------------------------------------------------
		
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque ['nombre'];
		$atributos ['id'] = $esteCampo;
		$atributos ['nombre'] = $esteCampo;
		// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		$atributos ['tipoFormulario'] = '';
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
				$atributos ['leyenda'] = "Estado de Cuenta " . $beneficiario[0]['nombre'];
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
			}
			{
				$url = $this->miConfigurador->configuracion ['host'] . $this->miConfigurador->configuracion ['site'];
				$atributos ['imagen'] =  $url.'/blocks/facturacion/'.$esteBloque ['nombre'].'/frontera/css/imagen/'.$imagen[0][0];
				$atributos ['estilo'] = '';
				$atributos ['etiqueta'] = '';
				$atributos ['borde'] = '';
				$atributos ['ancho'] = '';
				$atributos ['alto'] = '';
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoImagen ( $atributos );
				unset ( $atributos );
				
				echo "<br><br><br><br>";
				
				$esteCampo = 'usuario';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['anchoEtiqueta'] = 2;
				$atributos ['estilo'] = "bootstrap";
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['readonly'] = true;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['texto'] = $beneficiario [0] ['nombre'];
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 5;
				$atributos ['miEvento'] = '';
				$atributos ['validar'] = '';
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				$esteCampo = 'numero_contrato';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['anchoEtiqueta'] = 2;
				$atributos ['estilo'] = "bootstrap";
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['readonly'] = true;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['texto'] = $beneficiario [0] ['numero_contrato'];
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 2;
				$atributos ['miEvento'] = '';
				$atributos ['validar'] = '';
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				$esteCampo = 'fecha_contrato';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['anchoEtiqueta'] = 2;
				$atributos ['estilo'] = "bootstrap";
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['readonly'] = true;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['texto'] = $beneficiario [0] ['fecha_contrato'];
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 5;
				$atributos ['miEvento'] = '';
				$atributos ['validar'] = '';
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				$esteCampo = 'estado_contrato';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['texto'] = $beneficiario [0] ['descr_contrato'];
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['anchoEtiqueta'] = 2;
				$atributos ['estilo'] = "bootstrap";
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['readonly'] = true;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 2;
				$atributos ['miEvento'] = '';
				$atributos ['validar'] = '';
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				$esteCampo = 'estado_servicio';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['texto'] = $estadoServicio [0] [1];
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['anchoEtiqueta'] = 2;
				$atributos ['estilo'] = "bootstrap";
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['readonly'] = true;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 2;
				$atributos ['miEvento'] = '';
				$atributos ['validar'] = '';
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				$esteCampo = 'total_contrato';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['texto'] = "$ " . number_format ( $beneficiario [0] ['valor_total'], 2, '.', ',' );
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['anchoEtiqueta'] = 2;
				$atributos ['estilo'] = "bootstrap";
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['readonly'] = true;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 2;
				$atributos ['miEvento'] = '';
				$atributos ['validar'] = '';
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				$esteCampo = 'deuda_contrato';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['texto'] = "$ " . number_format ( $beneficiario [0] ['valor_total'] - $pagado [0] [1], 2, '.', ',' );
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['anchoEtiqueta'] = 2;
				$atributos ['estilo'] = "bootstrap";
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['readonly'] = true;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 2;
				$atributos ['miEvento'] = '';
				$atributos ['validar'] = '';
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				echo $this->miFormulario->agrupacion ( 'fin' );
				unset ( $atributos );
			}
			
			{
				$esteCampo = 'Agrupacion';
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Facturas registradas";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
				echo '
        		<table id="example" class="display" cellspacing="0" width="100%">
			        <thead>
			            <tr>
							<th>Número Factura</th>
				     		<th>Ciclo Facturación</th>
			                <th>Valor Total</th>
							<th>Estado Factura</th>
						    <th>Ver Factura</th>
			            </tr>
			        </thead>
			    </table>
        	';
				
				echo $this->miFormulario->agrupacion ( 'fin' );
				unset ( $atributos );
			}
			
			{
				$esteCampo = 'Agrupacion';
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Pagos registrados";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
				echo '
        		<table id="example2" class="display" cellspacing="0" width="100%">
			        <thead>
			            <tr>
							<th>Número Pago</th>
						    <th>Factura</th>
				     		<th>Fecha de Pago</th>
			                <th>Cajero</th>
						 	<th>Valor Factura</th>
						    <th>Valor Abono</th>
							<th>Valor Total</th>
			            </tr>
			        </thead>
			    </table>
        	';
				
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
				
				$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
				$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
				$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
				$valorCodificado .= "&opcion=verificarUsuario";
				
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
		
		// ----------------INICIO CONTROL: Ventana Modal Confirmación Eliminar Cabecera---------------------------------
		
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ['titulo'] = 'Confirmar Eliminación';
		$atributos ['id'] = 'myModal';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// ----------------INICIO CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		echo '<p><h5>' . $this->lenguaje->getCadena ( "eliminarCabecera" ) . '</h5></p>';
		
		echo '</div>';
		
		// ----------------FIN CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		// -----------------CONTROL: Botón ----------------------------------------------------------------
		$esteCampo = 'botonCancelarElim';
		$atributos ["id"] = $esteCampo;
		$atributos ["tabIndex"] = $tab;
		$atributos ["tipo"] = 'boton';
		$atributos ["basico"] = false;
		$atributos ["columnas"] = 2;
		// submit: no se coloca si se desea un tipo button genérico
		$atributos ['submit'] = true;
		$atributos ["estiloMarco"] = 'text-center';
		$atributos ["sinDivision"] = true;
		$atributos ["estiloBoton"] = 'default';
		$atributos ["block"] = false;
		$atributos ['deshabilitado'] = true;
		
		// verificar: true para verificar el formulario antes de pasarlo al servidor.
		$atributos ["verificar"] = '';
		$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
		$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		$tab ++;
		
		// Aplica atributos globales al control
		// $atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
		unset ( $atributos );
		// -----------------FIN CONTROL: Botón -----------------------------------------------------------
		
		// -----------------CONTROL: Botón ----------------------------------------------------------------
		$esteCampo = 'botonAceptarElim';
		$atributos ["id"] = $esteCampo;
		$atributos ["tabIndex"] = $tab;
		$atributos ["tipo"] = 'boton';
		$atributos ["basico"] = false;
		$atributos ["columnas"] = 2;
		// submit: no se coloca si se desea un tipo button genérico
		$atributos ['submit'] = true;
		$atributos ["estiloMarco"] = 'text-center';
		$atributos ["sinDivision"] = true;
		$atributos ["estiloBoton"] = 'danger';
		$atributos ["block"] = false;
		$atributos ['deshabilitado'] = true;
		
		// verificar: true para verificar el formulario antes de pasarlo al servidor.
		$atributos ["verificar"] = '';
		$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
		$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		$tab ++;
		
		// Aplica atributos globales al control
		// $atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
		unset ( $atributos );
		// -----------------FIN CONTROL: Botón -----------------------------------------------------------
		
		echo '</div>';
		
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// -----------------FIN CONTROL: Ventana Modal Confirmación Eliminar Cabecera -----------------------------------------------------------
		
		// ----------------INICIO CONTROL: Ventana Modal Cabecera Eliminado---------------------------------
		
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ['titulo'] = 'Mensaje';
		$atributos ['id'] = 'confirmacionElim';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// ----------------INICIO CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		echo '<p><h5>' . $this->lenguaje->getCadena ( "cabeceraEliminado" ) . '</h5></p>';
		
		echo '</div>';
		
		// ----------------FIN CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		// -----------------CONTROL: Botón ----------------------------------------------------------------
		$esteCampo = 'botonCerrar';
		$atributos ["id"] = $esteCampo;
		$atributos ["tabIndex"] = $tab;
		$atributos ["tipo"] = 'boton';
		$atributos ["basico"] = false;
		$atributos ["columnas"] = 2;
		// submit: no se coloca si se desea un tipo button genérico
		$atributos ['submit'] = true;
		$atributos ["estiloMarco"] = 'text-center';
		$atributos ["sinDivision"] = true;
		$atributos ["estiloBoton"] = 'default';
		$atributos ["block"] = false;
		$atributos ['deshabilitado'] = true;
		
		// verificar: true para verificar el formulario antes de pasarlo al servidor.
		$atributos ["verificar"] = '';
		$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
		$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		$tab ++;
		
		// Aplica atributos globales al control
		// $atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
		unset ( $atributos );
		// -----------------FIN CONTROL: Botón -----------------------------------------------------------
		
		echo '</div>';
		
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// -----------------FIN CONTROL: Ventana Modal Cabecera Eliminado -----------------------------------------------------------
		
		// ----------------INICIO CONTROL: Ventana Modal Cabecera Eliminado---------------------------------
		
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ['titulo'] = 'Mensaje';
		$atributos ['id'] = 'confirmacionNoElim';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// ----------------INICIO CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		echo '<p><h5>' . $this->lenguaje->getCadena ( "cabeceraNoEliminado" ) . '</h5></p>';
		
		echo '</div>';
		
		// ----------------FIN CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		// -----------------CONTROL: Botón ----------------------------------------------------------------
		$esteCampo = 'botonCerrar2';
		$atributos ["id"] = $esteCampo;
		$atributos ["tabIndex"] = $tab;
		$atributos ["tipo"] = 'boton';
		$atributos ["basico"] = false;
		$atributos ["columnas"] = 2;
		// submit: no se coloca si se desea un tipo button genérico
		$atributos ['submit'] = true;
		$atributos ["estiloMarco"] = 'text-center';
		$atributos ["sinDivision"] = true;
		$atributos ["estiloBoton"] = 'default';
		$atributos ["block"] = false;
		$atributos ['deshabilitado'] = true;
		
		// verificar: true para verificar el formulario antes de pasarlo al servidor.
		$atributos ["verificar"] = '';
		$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
		$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		$tab ++;
		
		// Aplica atributos globales al control
		// $atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
		unset ( $atributos );
		// -----------------FIN CONTROL: Botón -----------------------------------------------------------
		
		echo '</div>';
		
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// -----------------FIN CONTROL: Ventana Modal Cabecera Eliminado -----------------------------------------------------------
		
		// -----------------INICIO CONTROL: Ventana Modal Mensaje -----------------------------------------------------------
		
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ['titulo'] = 'Mensaje';
		$atributos ['id'] = 'myModalMensaje';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		echo "<h5><p>" . $this->lenguaje->getCadena ( $_REQUEST ['mensaje'] ) . "</p></h5>";
		
		// -----------------CONTROL: Botón ----------------------------------------------------------------
		$esteCampo = 'regresarConsultar';
		$atributos ["id"] = $esteCampo;
		$atributos ["tabIndex"] = $tab;
		$atributos ["tipo"] = 'boton';
		$atributos ["basico"] = false;
		// submit: no se coloca si se desea un tipo button genérico
		$atributos ['submit'] = true;
		$atributos ["estiloMarco"] = 'text-center';
		$atributos ["estiloBoton"] = 'default';
		$atributos ["block"] = false;
		$atributos ['deshabilitado'] = true;
		
		// verificar: true para verificar el formulario antes de pasarlo al servidor.
		$atributos ["verificar"] = '';
		$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
		$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		$tab ++;
		
		// Aplica atributos globales al control
		// $atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
		unset ( $atributos );
		// -----------------FIN CONTROL: Botón -----------------------------------------------------------
		
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// -----------------FIN CONTROL: Ventana Modal Mensaje -----------------------------------------------------------
		
		if (isset ( $_REQUEST ['mensajeModal'] )) {
			
			$this->mensajeModal ();
		}
	}
	public function mensajeModal() {
		switch ($_REQUEST ['mensajeModal']) {
			
			case 'errorConsulta' :
				$mensaje = "Advertencia<br>El Método ya está registrado en el Sistema.";
				$atributos ['estiloLinea'] = 'warning'; // success,error,information,warning
				break;
			
			case 'exitoInformacion' :
				$mensaje = "Exito<br>Método Registrado.";
				$atributos ['estiloLinea'] = 'success'; // success,error,information,warning
				break;
			
			case 'errorCreacion' :
				$mensaje = "Error<br>Método no Registrado.";
				$atributos ['estiloLinea'] = 'error'; // success,error,information,warning
				break;
			
			case 'errorActualizacion' :
				$mensaje = "Error durante la actualización de registros, informe al Administrador del sistema.";
				$atributos ['estiloLinea'] = 'error'; // success,error,information,warning
				break;
			
			case 'exitoActualizacion' :
				$mensaje = "Exito<br>Método Actualizado.";
				$atributos ['estiloLinea'] = 'success'; // success,error,information,warning
				break;
		}
		
		// ----------------INICIO CONTROL: Ventana Modal Beneficiario Eliminado---------------------------------
		
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ['titulo'] = 'Mensaje';
		$atributos ['id'] = 'mensajeModal';
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

$miSeleccionador = new Consultar ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->seleccionarForm ();

?>
