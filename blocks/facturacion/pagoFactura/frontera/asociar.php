<?php

namespace facturacion\pagoFactura\frontera;

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
	public $miFormulario;
	public function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->sql = $sql;
	}
	public function seleccionarForm() {
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->sql->getCadenaSql ( 'consultarFactura_especifico', $_REQUEST ['id'] );
		$factura = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->sql->getCadenaSql ( 'consultarConceptos_especifico', $_REQUEST ['id'] );
		$conceptos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

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
				$atributos ['leyenda'] = "Consultar Detalle Factura";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
				
				{
					
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
					$atributos ['texto'] = $factura [0] ['nombres'];
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
					
					$esteCampo = 'ciclo';
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
					$atributos ['texto'] = $factura [0] ['id_ciclo'];
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
					
					$esteCampo = 'estado';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['texto'] = $factura [0] ['estado_factura'];
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
					
					$esteCampo = 'total';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['texto'] = "$ " . number_format ( $factura [0] ['total_factura'], 2, '.', ',' );
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
					
					// ------------------Division para los botones-------------------------
					$atributos ["id"] = "botones";
					$atributos ["estilo"] = "marcoBotones";
					$atributos ["estiloEnLinea"] = "display:block;";
					echo $this->miFormulario->division ( "inicio", $atributos );
					unset ( $atributos );
					{
						// -----------------CONTROL: Botón ----------------------------------------------------------------
						$esteCampo = 'botonPagar1';
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
						if ($factura [0] ['estado_factura'] == 'Aprobado' || $factura [0] ['estado_factura'] == 'Mora') {
							echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
						}
						unset ( $atributos );
						// -----------------FIN CONTROL: Botón -----------------------------------------------------------
					}
					// ------------------Fin Division para los botones-------------------------
					echo $this->miFormulario->division ( "fin" );
					unset ( $atributos );
				}
				
				echo $this->miFormulario->agrupacion ( 'fin' );
				unset ( $atributos );
				
				$esteCampo = 'Agrupacion';
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Roles Asociados y Conceptos de Facturación";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
				
				{
					$tabla = "<table id='example2' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                             <th><center>Rol<center></th>
							                 <th><center>Periodo Facturación<center></th>
							 				 <th><center>Concepto<center></th>
											 <th><center>Valor Concepto<center></th>
							                 </tr>
                                        </thead>";
					
					foreach ( $conceptos as $key => $values ) {
						$tabla .= " <tr>";
						$tabla .= " <td>" . $conceptos [$key] ['descripcion'] . "</td>";
						$tabla .= " <td align='center'>" . $conceptos [$key] ['inicio_periodo'] . " - " . $conceptos [$key] ['fin_periodo'] . "</td>";
						$tabla .= " <td>" . $conceptos [$key] ['regla'] . "</td>";
						$tabla .= " <td align='right'>$ " . number_format ( $conceptos [$key] ['valor_calculado'], 2, '.', ',' ) . "</td>";
						$tabla .= " </tr>";
					}
					
					$tabla .= " <tr>
                                            		<th colspan=3><center>Total Factura<center></th>					
				<th align='right'><center>$ " . number_format ( $factura [0] ['total_factura'], 2, '.', ',' ) . "<center></th>			
                                        </tr>";
					$tabla .= "</table>";
					
					// echo $tabla;
					
					echo "<br>";
					
					echo $tabla;
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
				
				$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
				$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
				$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
				$valorCodificado .= "&opcion=pagar";
				$valorCodificado .= "&id=" . $_REQUEST ['id'];
				
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
}

$miSeleccionador = new Consultar ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->seleccionarForm ();

?>
