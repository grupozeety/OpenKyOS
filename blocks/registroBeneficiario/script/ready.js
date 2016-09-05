//Deshabilitar el comportamiento predeterminado de los botones 
$(function() {
	$("button").button().click(function(event) {
		event.preventDefault();
	});
});

$("#<?php echo $this->campoSeguro("geolocalizacion")?>").keydown(function(e){
    e.preventDefault();
});

$(function() {
	$("#<?php echo $this->campoSeguro("geolocalizacion")?>").focus(function() {
        $("#myModal").modal("show");
    });
});	

$("#botonAgregarLocalizacion").click(function( e ) {
    $("#<?php echo $this->campoSeguro("geolocalizacion")?>").val( $("#geomodal").val());
	$("#<?php echo $this->campoSeguro("geolocalizacion")?>").change();
	$('#myModal').modal('hide');
  });


$(function(){
	$('a[title]').tooltip();
});


$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });
   
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}


//according menu

$(document).ready(function()
{
    //Add Inactive Class To All Accordion Headers
    $('.accordion-header').toggleClass('inactive-header');
	
	//Set The Accordion Content Width
	var contentwidth = $('.accordion-header').width();
	$('.accordion-content').css({});
	
	//Open The First Accordion Section When Page Loads
	$('.accordion-header').first().toggleClass('active-header').toggleClass('inactive-header');
	$('.accordion-content').first().slideDown().toggleClass('open-content');
	
	// The Accordion Effect
	$('.accordion-header').click(function () {
		if($(this).is('.inactive-header')) {
			$('.active-header').toggleClass('active-header').toggleClass('inactive-header').next().slideToggle().toggleClass('open-content');
			$(this).toggleClass('active-header').toggleClass('inactive-header');
			$(this).next().slideToggle().toggleClass('open-content');
		}
		
		else {
			$(this).toggleClass('active-header').toggleClass('inactive-header');
			$(this).next().slideToggle().toggleClass('open-content');
		}
	});
	
	return false;
});

$(document).ready(function () {
	  var navListItems = $('div.wizard div a'),
			  allWells = $('.tab-pane'),
			  allNextBtn = $('.next-step');

	

	  allNextBtn.click(function(){
		  var curStep = $(this).closest(".tab-pane"),
			  curStepBtn = curStep.attr("id"),
			  nextStepWizard = $('div.wizard div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
			  curInputs = curStep.find("input"),
			  curSelect = curStep.find("select"),
			  isValid = true;

		  $(".form-group").removeClass("has-error");
		  
		  for(var i=0; i<curInputs.length; i++){
			  if (!curInputs[i].validity.valid){
				  isValid = false;
				  $(curInputs[i]).closest(".form-group").addClass("has-error");
			  }
		  }
		  
		  for(var i=0; i<curSelect.length; i++){
			  if (!curSelect[i].validity.valid){
				  isValid = false;
				  $(curSelect[i]).closest(".form-group").addClass("has-error");
			  }
		  }
		  
		  if (isValid){
			  var $active = $('.wizard .nav-tabs li.active');
	      	  $active.next().removeClass('disabled');
	          nextTab($active);
	  	  }else{
	  		alert("Por favor verifique la información ingresada en los campos marcados en rojo");
	  	  }
	          
	  });

	  $('div.wizard div a.btn-primary').trigger('click');
	});

function generar(longitud)
{
  var caracteres = "abcdefghijkmnpqrtuvwxyzABCDEFGHIJKLMNPQRTUVWXYZ2346789";
  var contraseña = "";
  for (i=0; i<longitud; i++) contraseña += caracteres.charAt(Math.floor(Math.random()*caracteres.length));
  return contraseña;
}

$("#<?php echo $this->campoSeguro("id_beneficiario")?>").val(generar(5));

