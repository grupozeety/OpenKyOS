		/**
		 * Código JavaScript del Bloque
		 */

		    $('#<?php echo $this->campoSeguro("fecha_expedicion");?>').datetimepicker({
        	    format: 'yyyy-mm-dd',
                language: "es",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            });


              $('#<?php echo $this->campoSeguro("fecha_inicio_vigencia_servicio");?>').datetimepicker({
               format: 'yyyy-mm-dd',
               language: "es",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            }).on('changeDate', function(ev){
                 $('#<?php echo $this->campoSeguro("fecha_fin_vigencia_servicio");?>').val('');
               $('#<?php echo $this->campoSeguro("fecha_fin_vigencia_servicio");?>').datetimepicker('setStartDate', $('#<?php echo $this->campoSeguro("fecha_inicio");?>').val());
               $('#<?php echo $this->campoSeguro("fecha_fin_vigencia_servicio");?>')[0].focus();

                });

            $('#<?php echo $this->campoSeguro("fecha_fin_vigencia_servicio");?>').datetimepicker({
                format: 'yyyy-mm-dd',
                language: "es",
                weekStart: 1,
                todayBtn:  0,
                autoclose: 1,
                todayHighlight: 0,
                startView: 2,
                minView: 2,
                forceParse: 0
            }).on('changeDate', function(ev){

                });

            

      $("#mensaje").modal("show");