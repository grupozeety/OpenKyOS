<?php
/**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

if (!isset($esteBloque["grupo"]) || $esteBloque["grupo"] == "") {
    $ruta .= "/blocks/" . $esteBloque["nombre"] . "/";
    $rutaURL .= "/blocks/" . $esteBloque["nombre"] . "/";
} else {
    $ruta .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
    $rutaURL .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
}
use reportes\parametrizacionInforme\Sql;

include_once $ruta . "control/Sql.class.php";

$sql = new Sql();

$conexion = "estructura";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarProyectos";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarProyectos = $url . $cadena;

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarActividades";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarActividades = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarParametrizacion";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarParametrizacion = $url . $cadena;

$cadenaSql = $sql->getCadenaSql('consultarCamposGeneral');
$campos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

$script = "<script type='text/javascript'>

			function camposActividadesProyectos( ) {";

foreach ($campos as $key => $value) {

    $script .= '
    							$("#' . $this->campoSeguro($value['campo']) . '").autocomplete({
								   	minChars: 1,
								   	serviceUrl:\'' . $urlConsultarActividades . '&proyecto=\'+$("#' . $this->campoSeguro("id_proyecto") . '").val(),
								   	onSelect: function (suggestion) {

									     	$("#' . $this->campoSeguro($value['identificador_campo']) . '").val(suggestion.data);
								   	    }
								   });

								   ';

}

$script .= "}
</script>";

echo $script;

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */



			function DivisionesTipos() {

				if($("#<?php echo $this->campoSeguro('id_proyecto');?>").val()!=''){
							switch ($("#<?php echo $this->campoSeguro('tipo_proyecto');?>").val()) {
							case 'core':

							$("#division_cabecera").find("input").attr("required","true");
							$("#division_core").css("display","block");


							$("#division_cabecera").find("input").removeAttr("required");
							$("#division_cabecera").css("display","none");

							$("#division_wman").find("input").removeAttr("required");
							$("#division_wman").css("display","none");

							$("#division_hfc").find("input").removeAttr("required");
							$("#division_hfc").css("display","none");

							$("#<?php echo $this->campoSeguro('act_nodo');?>").removeAttr("required");
							$("#<?php echo $this->campoSeguro('hogar');?>").removeAttr("required");

							break;

							case 'cabecera':

							$("#division_core").find("input").removeAttr("required");
							$("#division_core").css("display","none");

							$("#division_cabecera").find("input").attr("required","true");
							$("#division_cabecera").css("display","block");

							$("#division_wman").find("input").removeAttr("required");
							$("#division_wman").css("display","none");

							$("#division_hfc").find("input").removeAttr("required");
							$("#division_hfc").css("display","none");

							$("#<?php echo $this->campoSeguro('act_nodo');?>").removeAttr("required");
							$("#<?php echo $this->campoSeguro('hogar');?>").removeAttr("required");


							break;

							case 'hfc':

							$("#division_core").find("input").removeAttr("required");
							$("#division_core").css("display","none");


							$("#division_cabecera").find("input").removeAttr("required");
							$("#division_cabecera").css("display","none");

							$("#division_wman").find("input").removeAttr("required");
							$("#division_wman").css("display","none");

							$("#division_hfc").find("input").attr("required","true");
							$("#division_hfc").css("display","block");

							$("#<?php echo $this->campoSeguro('act_nodo');?>").attr("required","true");
							$("#<?php echo $this->campoSeguro('hogar');?>").attr("required","true");

							break;

							case 'wman':

							$("#division_core").find("input").removeAttr("required");
							$("#division_core").css("display","none");

							$("#division_cabecera").find("input").removeAttr("required");
							$("#division_cabecera").css("display","none")


							$("#division_wman").find("input").attr("required","true");
							$("#division_wman").css("display","block");

							$("#division_hfc").find("input").removeAttr("required");
							$("#division_hfc").css("display","none");

							$("#<?php echo $this->campoSeguro('act_nodo');?>").attr("required","true");
							$("#<?php echo $this->campoSeguro('hogar');?>").attr("required","true");


							break;

							default:

							$("#division_core").find("input").attr("required","true");
							$("#division_core").css("display","none");


							$("#division_cabecera").find("input").attr("required","true");
							$("#division_cabecera").css("display","none");

							$("#division_wman").find("input").attr("required","true");
							$("#division_wman").css("display","none");

							$("#division_hfc").find("input").attr("required","true");
							$("#division_hfc").css("display","none");



							$("#<?php echo $this->campoSeguro('act_nodo');?>").attr("required","true");
							$("#<?php echo $this->campoSeguro('hogar');?>").attr("required","true");
							break;
						}






				}else{
							$("#division_core").css("display","none");
							$("#division_cabecera").css("display","none");
							$("#division_wman").css("display","none");
							$("#division_hfc").css("display","none");

				}


			}


			function camposActividades( ) {
							$("#<?php echo $this->campoSeguro('act_nodo');?>").autocomplete({
								   	minChars: 1,
								   	serviceUrl: '<?php echo $urlConsultarActividades;?>&proyecto='+$("#<?php echo $this->campoSeguro('id_proyecto');?>").val(),
								   	onSelect: function (suggestion) {

									     	$("#<?php echo $this->campoSeguro('id_act_nodo');?>").val(suggestion.data);
								   	    }
								   });


								$("#<?php echo $this->campoSeguro('hogar');?>").autocomplete({
								   	minChars: 1,
								   	serviceUrl: '<?php echo $urlConsultarActividades;?>&proyecto='+$("#<?php echo $this->campoSeguro('id_proyecto');?>").val(),
								   	onSelect: function (suggestion) {

									     	$("#<?php echo $this->campoSeguro('id_hogar');?>").val(suggestion.data);
								   	    }
								   });


			}






			$("#<?php echo $this->campoSeguro('proyecto');?>").attr("disabled", "disabled");
			$("#<?php echo $this->campoSeguro('act_nodo');?>").attr("disabled", "disabled");
			$("#<?php echo $this->campoSeguro('hogar');?>").attr("disabled", "disabled");








		    $("#<?php echo $this->campoSeguro('tipo_proyecto');?>").change(function() {







		    $("#<?php echo $this->campoSeguro('proyecto');?>").val("");
			$("#<?php echo $this->campoSeguro('id_proyecto');?>").val("");


			$("#<?php echo $this->campoSeguro('act_nodo');?>").val("");
			$("#<?php echo $this->campoSeguro('id_act_nodo');?>").val("");

			$("#<?php echo $this->campoSeguro('hogar');?>").val("");
			$("#<?php echo $this->campoSeguro('id_hogar');?>").val("");

			$("#<?php echo $this->campoSeguro('act_nodo');?>").attr("disabled", "disabled");
			$("#<?php echo $this->campoSeguro('hogar');?>").attr("disabled", "disabled");

			if($("#<?php echo $this->campoSeguro('tipo_proyecto');?>").val() !=''){


						$("#<?php echo $this->campoSeguro('proyecto');?>").removeAttr("disabled");

						$("#<?php echo $this->campoSeguro('proyecto');?>").autocomplete({
						   	minChars: 3,
						   	serviceUrl: '<?php echo $urlConsultarProyectos;?>&tipo_proyecto='+$("#<?php echo $this->campoSeguro('tipo_proyecto');?>").val(),
						   	onSelect: function (suggestion) {



							     	$("#<?php echo $this->campoSeguro('id_proyecto');?>").val(suggestion.data);

							     	$("#<?php echo $this->campoSeguro('act_nodo');?>").removeAttr("disabled");
									$("#<?php echo $this->campoSeguro('hogar');?>").removeAttr("disabled");
									$("#division_bt").css("display","block");
							     	DivisionesTipos();
									camposActividades();
									camposActividadesProyectos();

						   	    }
						   });



				if($("#<?php echo $this->campoSeguro('tipo_proyecto');?>").val() =='wman' || $("#<?php echo $this->campoSeguro('tipo_proyecto');?>").val() =='hfc' ){
					$("#division_hogar_nodo").css("display","block");
				}else{
					$("#division_hogar_nodo").css("display","none");
				}


			}else{

				$("#division_hogar_nodo").css("display","none");

				$("#<?php echo $this->campoSeguro('proyecto');?>").val("");

				$("#<?php echo $this->campoSeguro('id_proyecto');?>").val("");

				$("#<?php echo $this->campoSeguro('proyecto');?>").attr("disabled", "disabled");

			}

							$("#division_core").css("display","none");
							$("#division_cabecera").css("display","none");
							$("#division_wman").css("display","none");
							$("#division_hfc").css("display","none");




		   });




      $("#mensaje").modal("show");



		$("#botonConsultaA").click(function() {
			$("#consulta").modal("show");
		});



       $('#contenido_consulta').DataTable( {
             processing: true,
                 searching: true,
                 info:true,
                 paging: true,
                "scrollY":"200px",
              	"scrollCollapse": true,
              	responsive: true,
                  ajax:{
                      url:"<?php echo $urlConsultarParametrizacion;?>",
                      dataSrc:"data"
                  },
                  columns: [
                  { data :"tipo_proyecto" },
                  { data :"id_proyecto" },
                  { data :"nombre_proyecto" },

                            ]
    } );



</script>

