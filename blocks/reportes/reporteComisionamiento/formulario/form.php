<?php 
namespace reporteAgendamientos\formulario\formRegistro;

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


class Formulario {

	
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;

    function __construct($lenguaje, $formulario, $sql) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
        
        $this->miSql = $sql;

    }

    function formulario() {

        /**
         * IMPORTANTE: Este formulario está utilizando jquery.
         * Por tanto en el archivo script/ready.php y script/ready.js se declaran 
         * algunas funciones js que lo complementan.
         */

    	$conexion = "estructura";
    	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
    	
    	$conexion = "openproject";
    	$esteRecursoDBOP = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
    	
        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
        * Atributos que deben ser aplicados a todos los controles de este formulario.
        * Se utiliza un arreglo independiente debido a que los atributos individuales se reinician cada vez que se 
        * declara un campo.
        *
        * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
        * $atributos= array_merge($atributos,$atributosGlobales);
        */
        
        $atributosGlobales ['campoSeguro'] = 'true';
                
        if(!isset($_REQUEST['tiempo'])){
        	$_REQUEST['tiempo']=time();
        }
        
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
        echo $this->miFormulario->formularioBootstrap ( $atributos );
        unset($atributos);
        
        $deshabilitado = false;
        
        if(isset($_REQUEST['id']) && $_REQUEST['id'] != ''){
        	$cadena_sql = $this->miSql->getCadenaSql ( "cargarCabecera", $_REQUEST['id'] );
        	$cargueDatos = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" )[0];
        	$cargueDatos['select_urbanizacion'] = $cargueDatos['urbanizacion'];
        	$deshabilitado = true;
        }
        // ---------------- SECCION: Controles del Formulario -----------------------------------------------

        if(isset($_REQUEST['mensaje'])){
        	$esteCampo = 'mensajemodal';
        	$atributos ["id"] = $esteCampo; // No cambiar este nombre
        	$atributos ["tipo"] = "hidden";
        	$atributos ['estilo'] = '';
        	$atributos ["obligatorio"] = false;
        	$atributos ['marco'] = true;
        	$atributos ["etiqueta"] = "";
        	$atributos ['valor'] = $_REQUEST['mensaje'];
        	$atributos = array_merge ( $atributos, $atributosGlobales );
        	echo $this->miFormulario->campoCuadroTexto ( $atributos );
        	unset ( $atributos );
        }
        
        echo "<div class='modalLoad'></div>";
        
	        $esteCampo = 'ficheros';
	        $atributos ['id'] = $esteCampo;
	        $atributos ['leyenda'] = "Generar Reporte de Visitas";
	        echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
	        unset ( $atributos );
	        	
	        $esteCampo = 'Agrupacionfiltros';
	        $atributos ['id'] = $esteCampo;
	        $atributos ['leyenda'] = "Filtrar información";
	        echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
	        unset ( $atributos );
	        
	        {
	        	// ----------------INICIO CONTROL: Lista Proyectos---------------------------
	        
	        	$esteCampo = 'urbanizacion';
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
	        	$atributos ['readonly'] = false;
	        	$atributos ['columnas'] = 1;
	        	$atributos ['tamanno'] = 1;
	        	$atributos ['placeholder'] = "Ingrese los criterios de busqueda";
	        	$atributos ['valor'] = "";
	        	$atributos ['ajax_function'] = "";
	        	$atributos ['ajax_control'] = $esteCampo;
	        	$atributos ['limitar'] = false;
	        	$atributos ['anchoCaja'] = 10;
	        	$atributos ['miEvento'] = '';
	        	// 			$atributos ['validar'] = 'required';
	        	// Aplica atributos globales al control
	        	$atributos = array_merge ( $atributos, $atributosGlobales );
	        	echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
	        	unset ( $atributos );
	        
	        	$esteCampo = 'id_urbanizacion';
	        	$atributos ["id"] = $esteCampo; // No cambiar este nombre
	        	$atributos ["tipo"] = "hidden";
	        	$atributos ['estilo'] = '';
	        	$atributos ["obligatorio"] = false;
	        	$atributos ['marco'] = true;
	        	$atributos ["etiqueta"] = "";
	        	if (isset ( $_REQUEST [$esteCampo] )) {
	        		$atributos ['valor'] = $_REQUEST [$esteCampo];
	        	} else {
	        		$atributos ['valor'] = '';
	        	}
	        	$atributos = array_merge ( $atributos, $atributosGlobales );
	        	echo $this->miFormulario->campoCuadroTexto ( $atributos );
	        	unset ( $atributos );
	        		
	        }
	        
	        // ----------------INICIO CONTROL: Lista Tipo de Vivienda--------------------------------------------------------
	        	
	        $esteCampo = 'tipo_agendamiento';
	        $atributos['nombre'] = $esteCampo;
	        $atributos['id'] = $esteCampo;
	        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
	        $atributos["etiquetaObligatorio"] = true;
	        $atributos['tab'] = $tab++;
	        $atributos['anchoEtiqueta'] = 2;
	        $atributos['evento'] = '';
	        $atributos['seleccion'] = -1;
	        $atributos['deshabilitado'] = false;
	        $atributos['columnas'] = 1;
	        $atributos['tamanno'] = 1;
	        $atributos['ajax_function'] = "";
	        $atributos['ajax_control'] = $esteCampo;
	        $atributos['estilo'] = "bootstrap";
	        $atributos['limitar'] = false;
	        $atributos['anchoCaja'] = 10;
	        $atributos['miEvento'] = '';
	        //         $atributos ['validar'] = '';
	        $atributos['cadena_sql'] = $this->miSql->getCadenaSql("parametroTipoAgendamiento");
	        $matrizItems = array(
	        		array(
	        				0,
	        				' ',
	        		),
	        );
	        $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos['cadena_sql'], "busqueda");
	        $atributos['matrizItems'] = $matrizItems;
	        // Aplica atributos globales al control
	        	
	        if (isset($cargueDatos[$esteCampo])) {
	        	$atributos['seleccion'] = $cargueDatos[$esteCampo];
	        } else {
	        	$atributos['seleccion'] = -1;
	        }
	        	
	        $atributos = array_merge($atributos, $atributosGlobales);
	        echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
	        unset($atributos);
	        	
	        // ----------------FIN CONTROL: Lista Tipo de Vivienda--------------------------------------------------------
	        
	        {
	        	// ----------------INICIO CONTROL: Lista Proyectos---------------------------
	        		
	        	$esteCampo = 'manzana';
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
	        	$atributos ['readonly'] = false;
	        	$atributos ['columnas'] = 1;
	        	$atributos ['tamanno'] = 1;
	        	$atributos ['placeholder'] = "Ingrese los criterios de busqueda";
	        	$atributos ['valor'] = "";
	        	$atributos ['ajax_function'] = "";
	        	$atributos ['ajax_control'] = $esteCampo;
	        	$atributos ['limitar'] = false;
	        	$atributos ['anchoCaja'] = 10;
	        	$atributos ['miEvento'] = '';
	        	// 				$atributos ['validar'] = 'required';
	        	// Aplica atributos globales al control
	        	$atributos = array_merge ( $atributos, $atributosGlobales );
	        	echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
	        	unset ( $atributos );
	        		
	        	$esteCampo = 'id_manzana';
	        	$atributos ["id"] = $esteCampo; // No cambiar este nombre
	        	$atributos ["tipo"] = "hidden";
	        	$atributos ['estilo'] = '';
	        	$atributos ["obligatorio"] = false;
	        	$atributos ['marco'] = true;
	        	$atributos ["etiqueta"] = "";
	        	if (isset ( $_REQUEST [$esteCampo] )) {
	        		$atributos ['valor'] = $_REQUEST [$esteCampo];
	        	} else {
	        		$atributos ['valor'] = '';
	        	}
	        	$atributos = array_merge ( $atributos, $atributosGlobales );
	        	echo $this->miFormulario->campoCuadroTexto ( $atributos );
	        	unset ( $atributos );
	        		
	        }
	        
	        {
	        	// ----------------INICIO CONTROL: Lista Proyectos---------------------------
	        	 
	        	$esteCampo = 'comisionador';
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
	        	$atributos ['readonly'] = false;
	        	$atributos ['columnas'] = 1;
	        	$atributos ['tamanno'] = 1;
	        	$atributos ['placeholder'] = "Ingrese los criterios de busqueda";
	        	$atributos ['valor'] = "";
	        	$atributos ['ajax_function'] = "";
	        	$atributos ['ajax_control'] = $esteCampo;
	        	$atributos ['limitar'] = false;
	        	$atributos ['anchoCaja'] = 10;
	        	$atributos ['miEvento'] = '';
	        	// 				$atributos ['validar'] = 'required';
	        	// Aplica atributos globales al control
	        	$atributos = array_merge ( $atributos, $atributosGlobales );
	        	echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
	        	unset ( $atributos );
	        	 
	        	$esteCampo = 'id_comisionador';
	        	$atributos ["id"] = $esteCampo; // No cambiar este nombre
	        	$atributos ["tipo"] = "hidden";
	        	$atributos ['estilo'] = '';
	        	$atributos ["obligatorio"] = false;
	        	$atributos ['marco'] = true;
	        	$atributos ["etiqueta"] = "";
	        	if (isset ( $_REQUEST [$esteCampo] )) {
	        		$atributos ['valor'] = $_REQUEST [$esteCampo];
	        	} else {
	        		$atributos ['valor'] = '';
	        	}
	        	$atributos = array_merge ( $atributos, $atributosGlobales );
	        	echo $this->miFormulario->campoCuadroTexto ( $atributos );
	        	unset ( $atributos );
	        	 
	        }
	        echo $this->miFormulario->agrupacion ( 'fin' );
	        unset ( $atributos );
        
	        
 			echo '
                		<table id="example" class="display table-container" cellspacing="0" width="100%">
        			        <thead>
        			        	<tr id="filterrow">
        			                <th>Agendamiento</th>
 									<th>Identificación</th>
                					<th>Todo<input type="checkbox" id="seleccionar_todo"/></th>
        			            </tr>
        			        </thead>
                	</table>
                	';
        
        // ------------------Division para los botones-------------------------
        $atributos ["id"] = "botones";
        $atributos ["estilo"] = "marcoBotones";
        echo $this->miFormulario->division ( "inicio", $atributos );
        unset($atributos);

        // -----------------CONTROL: Botón ----------------------------------------------------------------
                    $esteCampo = 'botonAceptar';
                    $atributos["id"] = $esteCampo;
                    $atributos["tabIndex"] = $tab;
                    $atributos["tipo"] = 'boton';
                    // submit: no se coloca si se desea un tipo button genérico
                    $atributos['submit'] = false;
                    $atributos["basic"] = true;
                    $atributos["estiloMarco"] = '';
                    $atributos["estiloBoton"] = 'default';
                    $atributos["block"] = false;
                    // verificar: true para verificar el formulario antes de pasarlo al servidor.
                    $atributos["verificar"] = '';
                    $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                    $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos['nombreFormulario'] = $esteBloque['nombre'];
                    $tab++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoBotonBootstrapHtml($atributos);
                    unset($atributos);
                    // -----------------FIN CONTROL: Botón ---
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------

        // ------------------Fin Division para los botones-------------------------
        echo $this->miFormulario->division ( "fin" );

        // ------------------- SECCION: Paso de variables ------------------------------------------------

        
        echo $this->miFormulario->agrupacion ( 'fin' );
        unset ( $atributos );
        
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

        $valorCodificado = "action=" . $esteBloque ["nombre"];
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
        $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
        $valorCodificado .= "&opcion=generarReporte";
        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo. 
         */
        $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
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

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        
        $atributos ['marco'] = true;
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario ( $atributos );

        // -----------------INICIO CONTROL: Ventana Modal Mensaje -----------------------------------------------------------
        
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ['titulo'] = 'Mensaje';
        $atributos ['id'] = 'myModalMensaje';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
        echo '<div style="text-align:center;">';
        
        echo '<p><h5>' . $this->lenguaje->getCadena ( $_REQUEST['mensaje'] ) . '</h5></p>';
        
        echo '</div>';
         
        // ----------------FIN CONTROL: Mapa--------------------------------------------------------
        
        echo '<div style="text-align:center;">';
        
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
        //         $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
        unset($atributos);
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
        
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
        // -----------------FIN CONTROL: Ventana Modal Mensaje -----------------------------------------------------------
        
        }
        
        
        function mensaje() {
        
        	// Si existe algun tipo de error en el login aparece el siguiente mensaje
        	$mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        	$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
        
        	if ($mensaje) {
        
        		$tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
        
        		if ($tipoMensaje == 'json') {
        
        			$atributos ['mensaje'] = $mensaje;
        			$atributos ['json'] = true;
        		} else {
        			$atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
        		}
        		// -------------Control texto-----------------------
        		$esteCampo = 'divMensaje';
        		$atributos ['id'] = $esteCampo;
        		$atributos ["tamanno"] = '';
        		$atributos ["estilo"] = 'information';
        		$atributos ["etiqueta"] = '';
        		$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
        		echo $this->miFormulario->campoMensaje ( $atributos );
        		unset ( $atributos );
        
        return true;

    }
    
    
    function elementosAdicionales(){
    	
    	

             
        }

        return true;

    }

}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario, $this->sql  );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>
<?php 
namespace cabecera\formulario\form;

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


class Formulario {

	
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;

    function __construct($lenguaje, $formulario, $sql) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
        
        $this->miSql = $sql;

    }

    function formulario() {

        /**
         * IMPORTANTE: Este formulario está utilizando jquery.
         * Por tanto en el archivo script/ready.php y script/ready.js se declaran 
         * algunas funciones js que lo complementan.
         */

    	$conexion = "estructura";
    	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
    	
        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
        * Atributos que deben ser aplicados a todos los controles de este formulario.
        * Se utiliza un arreglo independiente debido a que los atributos individuales se reinician cada vez que se 
        * declara un campo.
        *
        * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
        * $atributos= array_merge($atributos,$atributosGlobales);
        */
        
        $atributosGlobales ['campoSeguro'] = 'true';
                
        if(!isset($_REQUEST['tiempo'])){
        	$_REQUEST['tiempo']=time();
        }
        
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
//         $atributos ['tipoEtiqueta'] = 'inicio';
//         echo $this->miFormulario->formularioBootstrap ( $atributos );
//         unset($atributos);

        // ---------------- SECCION: Controles del Formulario -----------------------------------------------

        if(isset($_REQUEST['mensaje'])){
        	$esteCampo = 'mensajemodal';
        	$atributos ["id"] = $esteCampo; // No cambiar este nombre
        	$atributos ["tipo"] = "hidden";
        	$atributos ['estilo'] = '';
        	$atributos ["obligatorio"] = false;
        	$atributos ['marco'] = true;
        	$atributos ["etiqueta"] = "";
        	$atributos ['valor'] = $_REQUEST['mensaje'];
        	$atributos = array_merge ( $atributos, $atributosGlobales );
        	echo $this->miFormulario->campoCuadroTexto ( $atributos );
        	unset ( $atributos );
        }
        
        $esteCampo = 'ficheros';
        $atributos ['id'] = $esteCampo;
        $atributos ['leyenda'] = "Registro de Cabecera";
        echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
        unset ( $atributos );
        
               
        
        
        // ------------------Division para los botones-------------------------
        $atributos ["id"] = "botones";
        $atributos ["estilo"] = "marcoBotones";
        echo $this->miFormulario->division ( "inicio", $atributos );
        unset($atributos);

		// -----------------CONTROL: Botón ----------------------------------------------------------------
        $esteCampo = 'botonAceptar';
        $atributos ["id"] = $esteCampo;
        $atributos ["tabIndex"] = $tab;
        $atributos ["tipo"] = 'boton';
        // submit: no se coloca si se desea un tipo button genérico
        $atributos ['submit'] = true;
        $atributos ["simple"] = false;
        $atributos ["estiloMarco"] = '';
        $atributos ["estiloBoton"] = 'primary';
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
        unset($atributos);
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------

        // ------------------Fin Division para los botones-------------------------
        echo $this->miFormulario->division ( "fin" );

        // ------------------- SECCION: Paso de variables ------------------------------------------------

        
        echo $this->miFormulario->agrupacion ( 'fin' );
        unset ( $atributos );
        
        
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

        $valorCodificado = "action=" . $esteBloque ["nombre"];
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
        $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
        $valorCodificado .= "&opcion=registrarConsumo";
        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo. 
         */
        $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
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

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        
//         $atributos ['marco'] = true;
//         $atributos ['tipoEtiqueta'] = 'fin';
//         echo $this->miFormulario->formulario ( $atributos );

        
        // ----------------INICIO CONTROL: Ventana Modal Confirmación Eliminar Cabecera---------------------------------
        
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ['titulo'] = 'Confirmar Eliminación';
        $atributos ['id'] = 'myModal';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
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
        //         $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
        unset($atributos);
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
        //         $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
        unset($atributos);
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
        
         echo '</div>'; 
         
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
        // -----------------FIN CONTROL: Ventana Modal Confirmación Eliminar Cabecera -----------------------------------------------------------
        
        // ----------------INICIO CONTROL: Ventana Modal Cabecera Eliminado---------------------------------
        
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ['titulo'] = 'Mensaje';
        $atributos ['id'] = 'confirmacionElim';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
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
        //         $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
        unset($atributos);
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
        
        echo '</div>';
         
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
        // -----------------FIN CONTROL: Ventana Modal Cabecera Eliminado -----------------------------------------------------------
        
        // ----------------INICIO CONTROL: Ventana Modal Cabecera Eliminado---------------------------------
        
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ['titulo'] = 'Mensaje';
        $atributos ['id'] = 'confirmacionNoElim';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
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
        //         $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
        unset($atributos);
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
        
        echo '</div>';
         
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
        // -----------------FIN CONTROL: Ventana Modal Cabecera Eliminado -----------------------------------------------------------
        
         // -----------------INICIO CONTROL: Ventana Modal Mensaje -----------------------------------------------------------
        
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ['titulo'] = 'Mensaje';
        $atributos ['id'] = 'myModalMensaje';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
        echo "<h5><p>". $this->lenguaje->getCadena ( $_REQUEST['mensaje'] ) . "</p></h5>";
        
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
        //         $atributos = array_merge ( $atributos, $atributosGlobales );
        echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
        unset($atributos);
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
        
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->modal ( $atributos );
        unset($atributos);
        
        // -----------------FIN CONTROL: Ventana Modal Mensaje -----------------------------------------------------------
        
        
        }
        
        
        function mensaje() {
        
        	// Si existe algun tipo de error en el login aparece el siguiente mensaje
        	$mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        	$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
        
        	if ($mensaje) {
        
        		$tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
        
        		if ($tipoMensaje == 'json') {
        
        			$atributos ['mensaje'] = $mensaje;
        			$atributos ['json'] = true;
        		} else {
        			$atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
        		}
        		// -------------Control texto-----------------------
        		$esteCampo = 'divMensaje';
        		$atributos ['id'] = $esteCampo;
        		$atributos ["tamanno"] = '';
        		$atributos ["estilo"] = 'information';
        		$atributos ["etiqueta"] = '';
        		$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
        		echo $this->miFormulario->campoMensaje ( $atributos );
        		unset ( $atributos );
        
        return true;

    }
    
    
    function elementosAdicionales(){
    	
    	

             
        }

        return true;

    }

}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario, $this->sql  );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>
