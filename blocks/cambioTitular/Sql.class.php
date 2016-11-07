<?php

namespace cambioTitular;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";
// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	public $miConfigurador;
	public $miSesionSso;
	public function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		
		// $this->miSesionSso = \SesionSso::singleton();
	}
	public function getCadenaSql($tipo, $variable = "") {
		
		// $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();
		
		// foreach ($info_usuario['description'] as $key => $rol) {
		
		// $info_usuario['rol'][] = $rol;
		
		// }
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas genéricas.
			 * se espera que estén en todos los formularios
			 * que utilicen esta plantilla
			 */
			
			case 'consultarBeneficiariosPotenciales' :
				$cadenaSql = " SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, id_beneficiario  AS data  ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  cast(identificacion  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR nombre ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR primer_apellido ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR segundo_apellido ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case "iniciarTransaccion" :
				$cadenaSql = "START TRANSACTION";
				break;
			
			case "finalizarTransaccion" :
				$cadenaSql = "COMMIT";
				break;
			
			case "cancelarTransaccion" :
				$cadenaSql = "ROLLBACK";
				break;
			
			case "eliminarTemp" :
				
				$cadenaSql = "DELETE ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion = '" . $variable . "' ";
				break;
			
			case "insertarTemp" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "( ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				
				foreach ( $_REQUEST as $clave => $valor ) {
					$cadenaSql .= "( ";
					$cadenaSql .= "'" . $idSesion . "', ";
					$cadenaSql .= "'" . $variable ['formulario'] . "', ";
					$cadenaSql .= "'" . $clave . "', ";
					$cadenaSql .= "'" . $valor . "', ";
					$cadenaSql .= "'" . $variable ['fecha'] . "' ";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				break;
			
			case "rescatarTemp" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion='" . $idSesion . "'";
				break;
			
			/* Consultas del desarrollo */
			
			case "consultarBeneficiario" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "proyecto  AS urbanizacion,";
				$cadenaSql .= "(nombre ||' '|| primer_apellido ||' '|| segundo_apellido) as nombre,";
				$cadenaSql .= "identificacion,";
				$cadenaSql .= "tipoben.descripcion as tipo_beneficiario, ";
				$cadenaSql .= "id_beneficiario ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "interoperacion.beneficiario_potencial, ";
				$cadenaSql .= "(SELECT        ";
				$cadenaSql .= "codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Beneficario o Cliente') AS tipoben ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "tipo_beneficiario=cast ( tipoben.codigo as int8) ";
				$cadenaSql .= "AND ";
				$cadenaSql .= "estado_registro=true ";
				
				break;
			
			case "informacionBeneficiarioPotencial" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_beneficiario AS id_beneficiario,";
				$cadenaSql .= "tipo_beneficiario AS tipo_beneficiario,";
				$cadenaSql .= "tipo_documento AS tipo_documento,";
				$cadenaSql .= "identificacion AS identificacion,";
				$cadenaSql .= "nombre AS nombre,";
				$cadenaSql .= "primer_apellido AS primer_apellido,";
				$cadenaSql .= "segundo_apellido AS segundo_apellido,";
				$cadenaSql .= "genero AS genero,";
				$cadenaSql .= "edad AS edad,";
				$cadenaSql .= "nivel_estudio AS nivel_estudio,";
				$cadenaSql .= "correo AS correo,";
				$cadenaSql .= "foto AS foto,";
				$cadenaSql .= 'ruta_foto AS "ruta_foto",';
				$cadenaSql .= 'url_foto AS "url_foto",';
				$cadenaSql .= "direccion AS direccion,";
				$cadenaSql .= "tipo_vivienda AS tipo_vivienda,";
				$cadenaSql .= "manzana AS manzana,";
				$cadenaSql .= "bloque AS bloque,";
				$cadenaSql .= "torre AS torre,";
				$cadenaSql .= "apartamento AS apartamento,";
				$cadenaSql .= "telefono AS telefono,";
				$cadenaSql .= "celular AS celular,";
				$cadenaSql .= "whatsapp AS whatsapp,";
				$cadenaSql .= "facebook AS facebook,";
				$cadenaSql .= "departamento AS departamento,";
				$cadenaSql .= "municipio AS municipio,";
				$cadenaSql .= "id_proyecto AS id_proyecto,";
				$cadenaSql .= "proyecto AS proyecto,";
				$cadenaSql .= "territorio AS territorio,";
				$cadenaSql .= "estrato AS estrato,";
				$cadenaSql .= "geolocalizacion AS geolocalizacion,";
				$cadenaSql .= "jefe_hogar AS jefe_hogar,";
				$cadenaSql .= "pertenencia_etnica AS pertenencia_etnica,";
				$cadenaSql .= "ocupacion AS ocupacion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "estado_registro=true ";
				$cadenaSql .= "AND ";
				$cadenaSql .= "id_beneficiario=" . "'" . $variable ['id'] . "'";
				break;
			
			case "registrarTitular" :
				
				$cadenaSql = "UPDATE interoperacion.beneficiario_potencial SET ";
				$cadenaSql .= "tipo_beneficiario=" . "'" . $variable ['tipo_beneficiario'] . "',";
				$cadenaSql .= "tipo_documento=" . "'" . $variable ['tipo_documento'] . "',";
				$cadenaSql .= "identificacion=" . "'" . $variable ['identificacion'] . "',";
				$cadenaSql .= "nombre=" . "'" . $variable ['nombre'] . "',";
				$cadenaSql .= "primer_apellido=" . "'" . $variable ['primer_apellido'] . "',";
				$cadenaSql .= "segundo_apellido=" . "'" . $variable ['segundo_apellido'] . "',";
				$cadenaSql .= "genero=" . "'" . $variable ['genero'] . "',";
				$cadenaSql .= "edad=" . "'" . $variable ['edad'] . "',";
				$cadenaSql .= "nivel_estudio=" . "'" . $variable ['nivel_estudio'] . "',";
				$cadenaSql .= "correo=" . "'" . $variable ['correo'] . "',";
				$cadenaSql .= "foto=" . "'" . $variable ['foto'] . "',";
				$cadenaSql .= "ruta_foto=" . "'" . $variable ['ruta_foto'] . "',";
				$cadenaSql .= "url_foto=" . "'" . $variable ['url_foto'] . "',";
				$cadenaSql .= "direccion=" . "'" . $variable ['direccion'] . "',";
				$cadenaSql .= "tipo_vivienda=" . "'" . $variable ['tipo_vivienda'] . "',";
				$cadenaSql .= "manzana=" . "'" . $variable ['manzana'] . "',";
				$cadenaSql .= "bloque=" . "'" . $variable ['bloque'] . "',";
				$cadenaSql .= "torre=" . "'" . $variable ['torre'] . "',";
				$cadenaSql .= "apartamento=" . "'" . $variable ['apartamento'] . "',";
				$cadenaSql .= "telefono=" . "'" . $variable ['telefono'] . "',";
				$cadenaSql .= "celular=" . "'" . $variable ['celular'] . "',";
				$cadenaSql .= "whatsapp=" . "'" . $variable ['whatsapp'] . "',";
				$cadenaSql .= "facebook=" . "'" . $variable ['facebook'] . "',";
				$cadenaSql .= "departamento=" . "'" . $variable ['departamento'] . "',";
				$cadenaSql .= "municipio=" . "'" . $variable ['municipio'] . "',";
				$cadenaSql .= "proyecto=" . "'" . $variable ['proyecto'] . "',";
				$cadenaSql .= "id_proyecto=" . "'" . $variable ['id_proyecto'] . "',";
				$cadenaSql .= "territorio=" . "'" . $variable ['territorio'] . "',";
				$cadenaSql .= "estrato=" . "'" . $variable ['estrato'] . "',";
				$cadenaSql .= "geolocalizacion=" . "'" . $variable ['geolocalizacion'] . "',";
				$cadenaSql .= "jefe_hogar=" . "'" . $variable ['jefe_hogar'] . "',";
				$cadenaSql .= "pertenencia_etnica=" . "'" . $variable ['pertenencia_etnica'] . "',";
				$cadenaSql .= "ocupacion=" . "'" . $variable ['ocupacion'] . "' ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_beneficiario=" . "'" . $variable ['id_beneficiario'] . "';";
				break;
			
			case "registrarFamiliar" :
				
				$cadenaSql = "INSERT INTO interoperacion.familiar_beneficiario_potencial(";
				$cadenaSql .= "id_beneficiario,";
				$cadenaSql .= "tipo_documento,";
				$cadenaSql .= "identificacion_familiar,";
				$cadenaSql .= "nombre_familiar,";
				$cadenaSql .= "primer_apellido_familiar,";
				$cadenaSql .= "segundo_apellido_familiar,";
				$cadenaSql .= "parentesco,";
				$cadenaSql .= "genero_familiar,";
				$cadenaSql .= "edad_familiar,";
				$cadenaSql .= "celular_familiar,";
				$cadenaSql .= "nivel_estudio_familiar,";
				$cadenaSql .= "correo_familiar,";
				$cadenaSql .= "grado_estudio_familiar,";
				$cadenaSql .= "institucion_educativa_familiar,";
				$cadenaSql .= "pertenencia_etnica_familiar,";
				$cadenaSql .= "ocupacion_familiar";
				$cadenaSql .= ") VALUES ";
				
					$cadenaSql .= "(";
					$cadenaSql .= "'" . $variable ['id_beneficiario'] . "',";
					$cadenaSql .= "'" . $variable ['tipo_documento'] . "',";
					$cadenaSql .= "'" . $variable ['identificacion_familiar'] . "',";
					$cadenaSql .= "'" . $variable ['nombre_familiar'] . "',";
					$cadenaSql .= "'" . $variable ['primer_apellido'] . "',";
					$cadenaSql .= "'" . $variable ['segundo_apellido'] . "',";
					$cadenaSql .= "'" . $variable ['parentesco'] . "',";
					$cadenaSql .= "'" . $variable ['genero_familiar'] . "',";
					$cadenaSql .= "'" . $variable ['edad_familiar'] . "',";
					$cadenaSql .= "'" . $variable ['celular'] . "',";
					$cadenaSql .= "'" . $variable ['nivel_estudio_familiar'] . "',";
					$cadenaSql .= "'" . $variable ['correo_familiar'] . "',";
					$cadenaSql .= "'" . $variable ['grado'] . "',";
					$cadenaSql .= "'" . $variable ['institucion_educativa'] . "',";
					$cadenaSql .= "'" . $variable ['pertenencia_etnica_familiar'] . "',";
					$cadenaSql .= "'" . $variable ['ocupacion_familiar'] . "'";
					$cadenaSql .= "),";
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				$cadenaSql .= ";";
				break;
			
			case "actualizarTitular" :
				
				$cadenaSql = "UPDATE interoperacion.beneficiario_potencial ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "estado_registro=FALSE ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_beneficiario=";
				$cadenaSql .= "'" . $variable . "';";
				break;
			
			case "actualizarFamiliar" :
				
				$cadenaSql = "UPDATE interoperacion.familiar_beneficiario_potencial ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "estado_registro=FALSE ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "identificacion_familiar=";
				$cadenaSql .= "'" . $variable . "';";
				break;
				
			case "informacionFamiliar" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_beneficiario,";
				$cadenaSql .= "tipo_documento AS tipo_documento_familiar,";
				$cadenaSql .= "identificacion_familiar,";
				$cadenaSql .= "nombre_familiar,";
				$cadenaSql .= "primer_apellido_familiar,";
				$cadenaSql .= "segundo_apellido_familiar,";
				$cadenaSql .= "parentesco,";
				$cadenaSql .= "genero_familiar,";
				$cadenaSql .= "edad_familiar,";
				$cadenaSql .= "celular_familiar,";
				$cadenaSql .= "nivel_estudio_familiar,";
				$cadenaSql .= "correo_familiar,";
				$cadenaSql .= "grado_estudio_familiar,";
				$cadenaSql .= "pertenencia_etnica_familiar,";
				$cadenaSql .= "institucion_educativa_familiar,";
				$cadenaSql .= "ocupacion_familiar ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "interoperacion.familiar_beneficiario_potencial ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "estado_registro=true ";
				$cadenaSql .= "AND ";
				$cadenaSql .= "id_beneficiario=" . "'" . $variable ['id'] . "' ";
				$cadenaSql .= "AND ";
				$cadenaSql .= "identificacion_familiar=" . "'" . $variable ['titular'] . "'";
				break;
			
			case "cargarFamiliares" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_beneficiario,";
				$cadenaSql .= "identificacion_familiar,";
				$cadenaSql .= "(nombre_familiar ||' '|| primer_apellido_familiar ||' '|| segundo_apellido_familiar) as nombre_familiar ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "interoperacion.familiar_beneficiario_potencial ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "estado_registro=true ";
				$cadenaSql .= "AND ";
				$cadenaSql .= "id_beneficiario=" . "'" . $variable . "'";
				break;
			
			case "parametroTipoBeneficiario" :
				
				break;
			
			case "inhabilitarBeneficiario" :
				
				$cadenaSql = "UPDATE interoperacion.beneficiario_potencial ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "estado_registro=FALSE ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_beneficiario=" . "'" . $variable . "'";
				break;
		}
		
		return $cadenaSql;
	}
}

?>
