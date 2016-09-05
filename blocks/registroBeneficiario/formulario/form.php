<?php


if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
/**
 * Este script está incluido en el método html de la clase Frontera.class.php.
 *
 * La ruta absoluta del bloque está definida en $this->ruta
 */

$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

$nombreFormulario = $esteBloque ["nombre"];

include_once ("core/crypto/Encriptador.class.php");
$cripto = Encriptador::singleton ();
$valorCodificado = "action=" . $esteBloque ["nombre"];
$valorCodificado .= "&bloque=" . $esteBloque ["id_bloque"];
$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];

$valorCodificado = $cripto->codificar ( $valorCodificado );
$directorio = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" ) . "/imagen/";

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

// ---------------- SECCION: Controles del Formulario -----------------------------------------------

// ------------------Division para las pestañas-------------------------
$atributos ["id"] = "tabs";
$atributos ["estilo"] = "";

echo $this->miFormulario->division ( "inicio", $atributos );
unset ( $atributos );
{

	echo '
			<div class="row">
                <div class="wizard">
                    <div class="wizard-inner">
						<div class="connecting-line"></div>
							<ul class="nav nav-tabs" role="tablist">
                    			<li role="presentation" class="active">
                     			<a href="#datosBasicos" data-toggle="tab" title="Datos Básicos de Beneficiario">
                      				<span class="round-tabs one">
                              			<i class="glyphicon glyphicon-user"></i>
                      				</span>
                  				</a></li>
							 	<li role="presentation" class="disabled">
                  				<a href="#conformacionHogar" data-toggle="tab" title="Conformación Hogar Beneficiario">
                     			<span class="round-tabs two">
                         			<i class="glyphicon glyphicon-home"></i>
                     			</span>
           						</a></li>
								<li role="presentation" class="disabled">
                 				<a href="#otrasSecciones" data-toggle="tab" title="Otras Secciones">
                     				<span class="round-tabs three">
                          				<i class="glyphicon glyphicon-ok"></i>
                     				</span>
								</a></li>
                     		</ul>
						</div>
	
                    	<div class="tab-content">';
	
						echo '<div class="tab-pane fade in active" id="datosBasicos">';
				
							include ($this->ruta . "formulario/tabs/datosBasicos.php");
				
						echo '</div>';
						
			            echo '<div class="tab-pane fade" id="conformacionHogar">';
			            
			            	include ($this->ruta . "formulario/tabs/conformacionHogar.php");
			             
			            echo '</div>';
				
			            echo '<div class="tab-pane fade" id="otrasSecciones">';
			            	
			            	include ($this->ruta . "formulario/tabs/finalizacionRegistro.php");
			            
			            echo '</div>';
			            
            
		            echo '
					<div class="clearfix"></div>
					</div>
					</div>
					</div>
		            </div>
		            ';
}
echo $this->miFormulario->division ( "fin" );

// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
// Se debe declarar el mismo atributo de marco con que se inició el formulario.

$atributos ['marco'] = true;
$atributos ['tipoEtiqueta'] = 'fin';
echo $this->miFormulario->formulario ( $atributos );


?>