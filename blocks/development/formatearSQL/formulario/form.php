<?php
/**
 * IMPORTANTE: Este formulario está utilizando jquery. Por tanto en el archivo ready.php se delaran algunas funciones js
 * que lo complementan. La variable $_REQUEST['tiempo'] se declara en dicho archivo ya que es el primero que se procesa. 
 */
$_REQUEST ['tiempo'] = time ();
// Rescatar los datos de este bloque
$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
/**
 * Atributos que deben ser aplicados a todos los controles de este formulario.
 * Se utiliza un arreglo
 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
 *
 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
 * $atributos= array_merge($atributos,$atributosGlobales);
 */
$atributosGlobales ['campoSeguro'] = 'true';

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

// ---------------- SECCION: Controles del Formulario -----------------------------------------------

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
$esteCampo = 'campoCadena';
$atributos ['id'] = $esteCampo;
$atributos ['nombre'] = $esteCampo;
$atributos ['estilo'] = 'jqueryui';
$atributos ['columnas'] = 100;
$atributos ['filas'] = 10;
$atributos ['tabIndex'] = $tab;
$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
$atributos ['deshabilitado'] = false;
$tab ++;

// Aplica atributos globales al control
$atributos = array_merge ( $atributos, $atributosGlobales );
echo $this->miFormulario->campoTextArea ( $atributos );
// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------

// ------------------Division para los botones-------------------------
$atributos ["id"] = "botones";
$atributos ["estilo"] = "marcoBotones";
echo $this->miFormulario->division ( "inicio", $atributos );

// -----------------CONTROL: Botón ----------------------------------------------------------------
$esteCampo = 'botonCodificar';
$atributos ["id"] = $esteCampo;
$atributos ["tabIndex"] = $tab;
$atributos ["tipo"] = 'boton';
// submit: no se coloca si se desea un tipo button genérico
$atributos ['submit'] = true;
$atributos ["estiloMarco"] = '';
$atributos ["estiloBoton"] = 'jqueryui';
// verificar: true para verificar el formulario antes de pasarlo al servidor.
$atributos ["verificar"] = '';
$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
$tab ++;

// Aplica atributos globales al control
$atributos = array_merge ( $atributos, $atributosGlobales );
echo $this->miFormulario->campoBoton ( $atributos );
// -----------------FIN CONTROL: Botón -----------------------------------------------------------

// -----------------CONTROL: Botón ----------------------------------------------------------------
$esteCampo = 'botonDecodificar';
$atributos ["id"] = $esteCampo;
$atributos ["tabIndex"] = $tab;
$atributos ["tipo"] = 'boton';
// submit: no se coloca si se desea un tipo button genérico
$atributos ['submit'] = true;
$atributos ["estiloMarco"] = '';
$atributos ["estiloBoton"] = 'jqueryui';
// verificar: true para verificar el formulario antes de pasarlo al servidor.
$atributos ["verificar"] = '';
$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
$tab ++;

// Aplica atributos globales al control
$atributos = array_merge ( $atributos, $atributosGlobales );
echo $this->miFormulario->campoBoton ( $atributos );
// -----------------FIN CONTROL: Botón -----------------------------------------------------------

// ------------------Fin Division para los botones-------------------------
echo $this->miFormulario->division ( "fin" );

// ------------------- SECCION: Paso de variables ------------------------------------------------

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
//$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
$valorCodificado .= "&bloque=" . $esteBloque ["id_bloque"];
$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
/**
 * SARA permite que los nombres de los campos sean dinámicos.
 * Para ello utiliza la hora en que es creado el formulario para
 * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
 * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
 * (b) asociando el tiempo en que se está creando el formulario
 */
$valorCodificado .= "&tiempo=" . $_REQUEST ['tiempo'];
/**
 * Se agrega el 'campoSeguro' para que la variable $_REQUEST llegue decodificada a la función codificador.php
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

// ----------------FIN SECCION: Paso de variables -------------------------------------------------

// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------

//---------------- INICIO SECCION: Funcion Formatear / Desformatear -------------------------------

if(isset($_REQUEST['campoCadena'])){
	//Debido al escape de cadena que hace SARA
	$_REQUEST['campoCadena'] = str_replace("\_","_",$_REQUEST['campoCadena']);
	echo "<br><div style='padding:20px; 
						  border-style: dashed;
    					  border-width: 1px;'>";

	if ( $_REQUEST ['botonCodificar'] =='true') {


		$cadena= nl2br($_REQUEST['campoCadena']);

		$linea=explode('<br />',$cadena);

		foreach ($linea as $key => $value) {
			echo '$cadenaSql';
			if($key!=0)
			{echo '.';}
			echo '=" '.$value.'";<br>';
		}


	}else{

		$cadena= nl2br($_REQUEST['campoCadena']);

		$linea=explode('<br />',$cadena);

		foreach ($linea as $key => $value) {
			$caracteres = array('$cadenaSql.="','$cadenaSql="','$cadenaSql .= "','$cadenaSql = "', '$cadenaSql .= ');
			$value = str_replace($caracteres,'',$value);
			$value = substr($value, 0, -2);
			echo $value.'<br>';
		}

	}
	echo "</div>";
}

//---------------- FINAL SECCION: Funcion Formatear / Desformatear -------------------------------

// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
// Se debe declarar el mismo atributo de marco con que se inició el formulario.
$atributos ['marco'] = true;
$atributos ['tipoEtiqueta'] = 'fin';
echo $this->miFormulario->formulario ( $atributos );

?>

