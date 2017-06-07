<?php

namespace facturacion\masivoCalFactura\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
include_once 'RestClient.class.php';
include_once 'sincronizarErp.php';
class Calcular
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miFuncion;
    public $miSql;
    public $conexion;
    public $sincronizar;
    public $estadoFactura;
    public $numeracionFactura = null;
    public $indiceFacturacion = null;
    public function __construct($lenguaje, $sql)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->sincronizar = new sincronizarErp($lenguaje, $sql);

        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

    }
    public function calcularFactura($beneficiario, $roles, $estadoFactura)
    {

        /**
         * Definir variables Gloables*
         */
        $_REQUEST['id_beneficiario'] = $beneficiario;
        $this->estadoFactura = $estadoFactura;

        /**
         * 1.
         * Organizar Información por Roles
         */

        $this->rolesPeriodo = $roles;

        /**
         * 2.
         * Recuperar Reglas por Rol
         */

        $this->reglasRol();
        $this->consultarUsuarioRol();

        // /**
        // * 3.
        // * Obtener Datos del Contrato
        // */

        // $this->datosContrato ();

        /**
         * 3.
         * Verificar que no exista una factura para el rol para el periodo
         */

        $resultado = $this->verificarFactura();

        if ($resultado > 0) {

            /**
             * 6.
             * Revisar Resultado Proceso
             */

            return $this->registroConceptos;
        } else {

            $this->datosContrato();
            $this->estadoServicio();

            /**
             * 4.
             * Calcular Valores
             */
            $this->reducirFormula();

            $this->calculoPeriodo();
            $this->registrarPeriodo();
            $this->revisarMora();
            $this->calculoMora();
            $this->calculoFactura();

            /**
             * 5.
             * Parametrización de Numeración Facturación
             */

            if ($this->estadoFactura == 'Mora') {

                $this->parametrizacionNumeracionFacturacion();
            }

            /**
             * 6.
             * Guardar Conceptos de Facturación
             */

            $this->guardarFactura();

            $this->guardarConceptos();

            /**
             * Crear Cliente
             */
            $this->consultarCliente();

            $facturaCrear['estado'] = 1;

            $this->registroConceptos['resultado'];

            if ($this->registroConceptos['resultado'] == 0 && $this->clienteEstado == 'f') {
                // // Crear el cliente
                $clienteURL = $this->sincronizar->crearUrlCliente($_REQUEST['id_beneficiario']);
                $clienteCrear = $this->sincronizar->crearCliente($clienteURL);
                if ($clienteCrear['estado'] == 1) {
                    $this->registroConceptos['cliente'][0] = 'Cliente no creado correctamente. Crearlo en ERPNext';
                } elseif ($clienteCrear['estado'] == 0) {

                    $cadenaSql = $this->miSql->getCadenaSql('updateestadoCliente', $_REQUEST['id_beneficiario']);
                    $updatecliente = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                    $this->registroConceptos['cliente'][0] = 'Cliente creado correctamente.';
                    // $facturaCrearURL = $this->sincronizar->crearUrlFactura ( $this->informacion_factura );
                    // $facturaCrear = $this->sincronizar->crearFactura ( $facturaCrearURL );
                }
            }

            // Se elimina la creación de las facturas en los conceptos y se pasa a impresión de factura

            /*
             * else {
             * $facturaCrearURL = $this->sincronizar->crearUrlFactura ( $this->informacion_factura );
             * $facturaCrear = $this->sincronizar->crearFactura ( $facturaCrearURL );
             * }
             *
             * if ($facturaCrear ['estado'] == 0) {
             * $invoice = array (
             * 'invoice' => $facturaCrear ['recibo'],
             * 'id_factura' => $this->informacion_factura ['id_factura']
             * );
             *
             * $this->registroConceptos ['cliente'] [1] = 'Factura creada en ERPNext.';
             * $this->sincronizar->actualizarFactura ( $invoice );
             * } else {
             * $this->registroConceptos ['cliente'] [1] = 'Error en la creacion de Factura en ERPNext.';
             * }
             */

            /**
             * 6.
             * Revisar Resultado Proceso
             */

            return json_encode($this->registroConceptos);
        }
    }
    public function reglasRol()
    {
        foreach ($this->rolesPeriodo as $key => $vales) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarReglas', $key);
            $reglas = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            foreach ($reglas as $a => $b) {
                $this->rolesPeriodo[$key]['reglas'][$reglas[$a]['identificador']] = $reglas[$a]['formula'];
                $this->rolesPeriodo[$key]['mora'] = 0;
                $this->rolesPeriodo[$key]['totalMora'] = 0;
            }
        }
    }
    public function consultarUsuarioRol()
    {
        $cadenaSql = $this->miSql->getCadenaSql('consultarUsuarioRol', $_REQUEST['id_beneficiario']);
        $this->idUsuarioRol = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($this->idUsuarioRol as $key => $values) {
            foreach ($this->rolesPeriodo as $llave => $valores) {
                if ($this->idUsuarioRol[$key]['id_rol'] == $llave) {
                    $this->rolesPeriodo[$llave]['id_usuario_rol'] = $this->idUsuarioRol[$key]['id_usuario_rol'];
                }
            }
        }
    }
    public function verificarFactura()
    {
        $res = 0;
        foreach ($this->rolesPeriodo as $key => $vales) {
            foreach ($this->rolesPeriodo as $llave => $valores) {
                // Revisar si el ciclo anterior al facturar está en condiciones para ser recalculado
                $ciclo = date("Y", strtotime($this->rolesPeriodo[$key]['fecha'])) . '-' . date("m", strtotime($this->rolesPeriodo[$key]['fecha']));

                $datos = array(
                    'id_usuario_rol' => $this->rolesPeriodo[$llave]['id_usuario_rol'],
                    'id_ciclo' => $ciclo,
                    'id_beneficiario' => $_REQUEST['id_beneficiario'],
                    'id_rol' => $key,
                );

                $cadenaSql = $this->miSql->getCadenaSql('consultarFacturaB', $datos);
                $resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($resultado != false) {
                    $cadenaSql = $this->miSql->getCadenaSql('inhabilitarFactura', $resultado[0]['id_factura']);
                    $inhabilitar = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "actualizar");

                    $cadenaSql = $this->miSql->getCadenaSql('consultarUsuarioRolPeriodo', $datos);
                    $this->rolesPeriodo[$key]['fecha'] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0][0];
                }

                $fechaCiclo = date('Y/m/d H:i:s', strtotime($this->rolesPeriodo[$key]['fecha'] . '+ 1 day'));

                $ciclo = date("Y", strtotime($fechaCiclo)) . '-' . date("m", strtotime($fechaCiclo));
                $datos = array(
                    'id_usuario_rol' => $this->rolesPeriodo[$llave]['id_usuario_rol'],
                    'id_ciclo' => $ciclo,
                    'id_beneficiario' => $_REQUEST['id_beneficiario'],
                );

                $cadenaSql = $this->miSql->getCadenaSql('consultarFacturaActiva', $datos);
                $resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($resultado != false) {
                    $this->registroConceptos['observaciones'] = 'Ya existe una factura para el ciclo ' . $ciclo;
                    $res++;
                } else {
                    $res = 0;
                }
            }
        }

        return $res;
    }
    public function datosContrato()
    {
        $cadenaSql = $this->miSql->getCadenaSql('consultarContrato', $_REQUEST['id_beneficiario']);
        $this->datosContrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
    }
    public function reducirFormula()
    {
        $contar = 0;
        $formula_base = 0;

        do {

            foreach ($this->rolesPeriodo as $key => $values) {
                foreach ($values['reglas'] as $variable => $c) {
                    foreach ($values['reglas'] as $incognita => $d) {
                        $incognita = preg_replace("/\b" . $incognita . "\b/", $d, $c, -1, $contar);
                        if ($contar == 1) {
                            $this->rolesPeriodo[$key]['reglas'][$variable] = $incognita;
                            $termina = false;
                        }
                    }
                    $formula_base = $formula_base . "+" . $this->rolesPeriodo[$key]['reglas'][$variable];
                }
                $formulaRol[$key] = $formula_base;
                $formula_base = 0;
            }

            $termina = true;
        } while ($termina == false);

        $this->formularRolGlobal = $formulaRol;
    }
    public function calculoPeriodo()
    {
        foreach ($this->rolesPeriodo as $key => $values) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarPeriodo', $values['periodo']);
            $periodoUnidad = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['valor'];

            $this->rolesPeriodo[$key]['periodoValor'] = (double) ($periodoUnidad);
        }
    }
    public function calculoMora()
    {
        $cadenaSql = $this->miSql->getCadenaSql('consultarMoras', $_REQUEST['id_beneficiario']);
        $facturasVencidas = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $dm = 0;

        if ($facturasVencidas != false) {

            foreach ($facturasVencidas as $llave => $valor) {
                foreach ($this->rolesPeriodo as $key => $values) {

                    if ($values['id_usuario_rol'] == $facturasVencidas[$llave]['id_usuario_rol']) {
                        $fin = new \DateTime($facturasVencidas[$llave]['fin_periodo']);
                        $inicio = new \DateTime($facturasVencidas[$llave]['inicio_periodo']);
                        $dm_calculo = $fin->diff($inicio);
                        $dias = $dm_calculo->d;

                        $this->rolesPeriodo[$key]['mora'] = $this->rolesPeriodo[$key]['mora'] + $dias;
                        // $this->rolesPeriodo [$key] ['facturasMora'] [$facturasVencidas [$llave] ['id_factura'] . "(" . $facturasVencidas [$llave] ['id_ciclo'] . ")"] = $facturasVencidas [$llave] ['total_factura'];
                        $this->rolesPeriodo[$key]['facturasMora'][$facturasVencidas[$llave]['id_factura']] = $facturasVencidas[$llave]['total_factura'];
                        $this->rolesPeriodo[$key]['totalMora'] = $this->rolesPeriodo[$key]['totalMora'] + $facturasVencidas[$llave]['total_factura'];
                    }
                }
            }
        } else {
            foreach ($this->rolesPeriodo as $key => $values) {
                $this->rolesPeriodo[$key]['mora'] = 0;
            }
        }
    }

    // Registrar el ciclo de facturación de acuerdo al periodo seleccionado
    public function registrarPeriodo()
    {
        foreach ($this->rolesPeriodo as $key => $values) {

            // Acá se debe controlar el ciclo de facturación
            $this->rolesPeriodo[$key]['fecha'];
            $dia = date('d', strtotime($this->rolesPeriodo[$key]['fecha'] . '+ 1 day'));

            $inicio = date('Y/m/d H:i:s', strtotime($this->rolesPeriodo[$key]['fecha'] . '+ 1 day'));
            $fecha_fin_mes = date("Y-m-t", strtotime($this->rolesPeriodo[$key]['fecha']));

            if ($dia != 1) {
                if ($this->rolesPeriodo[$key]['periodoValor'] == 1) {
                    $fin = date('Y/m/t H:i:s', strtotime($fecha_fin_mes));
                } elseif ($this->rolesPeriodo[$key]['periodoValor'] == 720) {
                    $fin = date('Y/m/d H:i:s', strtotime($this->rolesPeriodo[$key]['fecha'] . '+' . $values['cantidad'] . ' hours'));
                } elseif ($this->rolesPeriodo[$key]['periodoValor'] == 30) {
                    $fin = date('Y/m/d H:i:s', strtotime($this->rolesPeriodo[$key]['fecha'] . '+' . $values['cantidad'] . ' days'));
                } else {
                    $fin = date('Y/m/t H:i:s', strtotime($this->rolesPeriodo[$key]['fecha']));
                }
                $diferencia = 1 + (strtotime($fin) - strtotime($this->rolesPeriodo[$key]['fecha'])) / (60 * 60 * 24);
                $this->rolesPeriodo[$key]['cantidad'] = $diferencia;
                $this->rolesPeriodo[$key]['periodoValor'] = (int) date('t', mktime(0, 0, 0, date("m", strtotime($this->rolesPeriodo[$key]['fecha'])), 1, date("Y", strtotime($this->rolesPeriodo[$key]['fecha']))));
            } else {
                // Aquí se aumentan los periodos de facturacion

                if ($this->rolesPeriodo[$key]['periodoValor'] == 1) {
                    $fin = date("Y-m-t H:i:s", strtotime($this->rolesPeriodo[$key]['fecha'] . '+ 1 day'));
                } elseif ($this->rolesPeriodo[$key]['periodoValor'] == 720) {
                    $fin = date('Y/m/d H:i:s', strtotime($this->rolesPeriodo[$key]['fecha'] . '+' . $values['cantidad'] . ' hours'));
                } elseif ($this->rolesPeriodo[$key]['periodoValor'] == 30) {
                    $fin = date('Y/m/d H:i:s', strtotime($this->rolesPeriodo[$key]['fecha'] . '+' . $values['cantidad'] . ' days'));
                } else {
                    $fin = date('Y/m/t H:i:s', strtotime($this->rolesPeriodo[$key]['fecha'] . '+ 1 day'));
                }
            }
            // En un mundo ideal un float alcanzaría para dates basados en meses ((1 / $this->rolesPeriodo [$key] ['periodoValor']) * $values ['cantidad']);

            $usuariorolperiodo = array(
                'id_usuario_rol' => $this->rolesPeriodo[$key]['id_usuario_rol'],
                'id_periodo' => $this->rolesPeriodo[$key]['periodo'],
                'inicio_periodo' => $inicio,
                'fin_periodo' => $fin,
                'id_ciclo' => date("Y", strtotime($inicio)) . '-' . date("m", strtotime($inicio)),
            );

            $cadenaSql = $this->miSql->getCadenaSql('registrarPeriodoRolUsuario', $usuariorolperiodo);
            $periodoRolUsuario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_usuario_rol_periodo'];
            $this->rolesPeriodo[$key]['id_usuario_rol_periodo'] = $periodoRolUsuario;
            $this->rolesPeriodo[$key]['finPeriodo'] = $fin;
            $this->rolesPeriodo[$key]['ciclo'] = date("Y", strtotime($inicio)) . '-' . date("m", strtotime($inicio));
        }
    }
    public function calculoFactura()
    {
        $total = 0;
        $vm = $this->datosContrato['vm'];
        $factura = 0;

        if ($this->estadoServicio != 'ET0') {
            $vm = 0;
        }

        foreach ($this->rolesPeriodo as $key => $values) {
            $total = 0;
            foreach ($values['reglas'] as $variable => $c) {
                $a = preg_replace("/\bvm\b/", ($vm / $values['periodoValor']) * $values['cantidad'], $c, -1, $contar);
                $b = preg_replace("/\bdm\b/", $values['mora'], $a, -1, $contar);
                $valor = round(eval('return (' . $b . ');'));
                $this->rolesPeriodo[$key]['valor'][$variable] = $valor;
                $total = $total + $this->rolesPeriodo[$key]['valor'][$variable];
            }

            $factura = $factura + $total;
            $this->rolesPeriodo[$key]['valor']['vm'] = round(($vm / $values['periodoValor']) * $values['cantidad']);
            $this->rolesPeriodo[$key]['valor']['total'] = $total;
        }
    }

    public function parametrizacionNumeracionFacturacion()
    {

        $cadenaSql = $this->miSql->getCadenaSql('departamentoBeneficiario', $_REQUEST['id_beneficiario']);
        $departamento = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['departamento'];

        switch ($departamento) {
            case '23':

                $cadenaSql = $this->miSql->getCadenaSql('consultarNumeracionFactura', 'FVM');
                $numeracion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                $limite = 130316;
                $this->indiceFacturacion = 'FVM';
                break;

            case '70':

                $cadenaSql = $this->miSql->getCadenaSql('consultarNumeracionFactura', 'FVS');
                $numeracion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                $limite = 1445;
                $this->indiceFacturacion = 'FVS';
                break;

        }

        if (is_null($numeracion[0]['numeracion'])) {

            $numero_factura = 1;

        } else {

            $numero_factura = $numeracion[0]['numeracion'] + 1;
        }

        $this->numeracionFactura = $numero_factura;

    }
    public function guardarFactura()
    {
        $total = 0;

        foreach ($this->rolesPeriodo as $key => $values) {
            $mora = $this->rolesPeriodo[$key]['totalMora'];
            break;
        }

        foreach ($this->rolesPeriodo as $key => $values) {
            $total = $this->rolesPeriodo[$key]['valor']['total'] + $total + $mora;
        }

        $this->informacion_factura = array(
            'total_factura' => $total,
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'id_ciclo' => $this->rolesPeriodo[$key]['ciclo'],
            'fecha' => $this->rolesPeriodo[$key]['finPeriodo'],
            'estado_factura' => $this->estadoFactura,
            'indice_facturacion' => $this->indiceFacturacion,
            'numero_factura' => $this->numeracionFactura,
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarFactura', $this->informacion_factura);
        $this->registroFactura['factura'] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_factura'];

        $this->informacion_factura['id_factura'] = $this->registroFactura['factura'];
    }
    public function guardarConceptos()
    {
        $this->registroConceptos['observaciones'] = 'Sin observaciones';
        $a = 0;

        foreach ($this->rolesPeriodo as $key => $values) {
            foreach ($values['reglas'] as $llave => $valores) {
                $cadenaSql = $this->miSql->getCadenaSql('consultarReglaID', $llave);
                $reglaid = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_regla'];

                $registroConceptos = array(
                    'id_factura' => $this->registroFactura['factura'],
                    'id_regla' => $reglaid,
                    'valor_calculado' => $values['valor'][$llave],
                    'id_usuario_rol_periodo' => $this->rolesPeriodo[$key]['id_usuario_rol_periodo'],
                    'observacion' => '',
                );

                $cadenaSql = $this->miSql->getCadenaSql('registrarConceptos', $registroConceptos);
                $this->registroConceptos[$key] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "registro");

                if ($this->registroConceptos[$key] == false) {
                    $a++;
                }
            }
        }

        if (isset($values['facturasMora'])) {
            foreach ($this->rolesPeriodo as $key => $values) {
                foreach ($values['facturasMora'] as $llave => $valores) {
                    $cadenaSql = $this->miSql->getCadenaSql('consultarReglaID', 'facturasMora');
                    $reglaid = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['id_regla'];

                    $registroConceptos = array(
                        'id_factura' => $this->registroFactura['factura'],
                        'id_regla' => $reglaid,
                        'valor_calculado' => $valores,
                        'id_usuario_rol_periodo' => $this->rolesPeriodo[$key]['id_usuario_rol_periodo'],
                        'observacion' => $llave,
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('registrarConceptos', $registroConceptos);
                    $this->registroConceptos[$key] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "registro");

                    if ($this->registroConceptos[$key] == false) {
                        $a++;
                    }
                }
                break;
            }
        }

        $this->registroConceptos['resultado'] = $a;

        if ($a == 0) {
            $this->registroConceptos['observaciones'] = 'Factura Generada Exitosamente';
        } else {
            $this->registroConceptos['observaciones'] = 'Error en el registro de conceptos de factura.';
        }
    }
    public function consultarCliente()
    {
        $this->registroConceptos['cliente'][0] = '';

        $cadenaSql = $this->miSql->getCadenaSql('estadoCliente', $_REQUEST['id_beneficiario']);
        $this->clienteEstado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0][0];
    }
    public function revisarMora()
    {
        $cadenaSql = $this->miSql->getCadenaSql('revisarMora', $_REQUEST['id_beneficiario']);
        $moras = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
    }
    public function estadoServicio()
    {
        $conexion2 = "otun";
        $this->esteRecursoDBOtun = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion2);

        $cadenaSql = $this->miSql->getCadenaSql('estadoServicio', $_REQUEST['id_beneficiario']);
        $this->estadoServicio = $this->esteRecursoDBOtun->ejecutarAcceso($cadenaSql, "busqueda")[0][0];
    }
}
