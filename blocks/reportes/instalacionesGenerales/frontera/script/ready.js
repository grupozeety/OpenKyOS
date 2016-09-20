		/**
		 * Código JavaScript del Bloque



	 $('#<?php echo $this->campoSeguro('fecha_inicio')?>').datepicker();

	 $('#<?php echo $this->campoSeguro('fecha_final')?>').datepicker();


	 		 */

$('.input-daterange input').each(function() {
    $(this).datepicker("clearDates");
});

var nowTemp = new Date();
var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

var checkin = $('#<?php echo $this->campoSeguro('fecha_inicio')?>').datepicker().on('changeDate', function(ev) {
  if (ev.date.valueOf() > checkout.date.valueOf()) {
    var newDate = new Date(ev.date)
    newDate.setDate(newDate.getDate() + 1);
    checkout.setValue(newDate);
  }
  checkin.hide();
  $('#<?php echo $this->campoSeguro('fecha_final')?>')[0].focus();
}).data('datepicker');






var checkout = $('#<?php echo $this->campoSeguro('fecha_final')?>').datepicker({
  onRender: function(date) {

    return date.valueOf() >= checkin.date.valueOf() ? '' : 'disabled';
  }
}).on('changeDate', function(ev) {

  checkout.hide();
}).data('datepicker');