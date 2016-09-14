<?php
/**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */

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
$cadenaACodificar .= "&funcion=consultarContratos";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarContratos = $url . $cadena;

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax(Aprobación Contrato).
 */

 $(document).ready(function() {


      $("#mensaje").modal("show");



      $('#example').DataTable( {
             processing: true,
                 searching: true,
                 info:true,
                 paging: true,
                  ajax:{
                      url:"<?php echo $urlConsultarContratos;?>",
                      dataSrc:"data"
                  },
                  columns: [
                  { data :"numeroContrato" },
                  { data :"urbanizacion" },
                  { data :"identificacionBeneficiario" },
                  { data :"nombreBeneficiario" },
                  { data :"opcion" }
                            ]
    } );
} );

$("#<?php echo $this->campoSeguro('medio_pago');?>").select2();
$("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").select2();
</script>

