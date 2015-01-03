function ref_account_valid(campo)
{
	return /^[A-Za-zñÑ0-9\-\_]{1,7}$/.test(campo.val());
}

function name_valid(campo)
{
	return /^[A-Za-zñÑ\s]{1,200}$/.test( campo.val() );
}

function email_valid(campo)
{
	return  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test( campo.val() );
}

function email_conf_valido(campo)
{
	return campo.val() == $('#email').val();
}

function telephone_valid(campo)
{
	return  /^(?:\d|\+){1}[0-9]{1,19}$/.test( campo.val() );
}

function comcar_valido(campo)
{
	return  /^[A-Za-z]{0,150}$/.test( campo.val() );
}

function comments_valido(campo)
{
	return  /^[A-Za-z0-9_.,\s]{5,2000}$/.test( campo.val() );
}

function address_valid(campo)
{
	return /^[A-Za-z0-9ñÑ\_\-\.\,\#\s]{5,200}$/.test( campo.val() );
}

function cp_valido(campo)
{
	return /^[0-9]{4,5}$/.test( campo.val() );
}

function limit_credit_valid(campo)
{
	return /^[0-9]{1,10}$/.test( campo.val() );
}

function blink(ele) {
    blink1(ele);
}
function blink1(ele) {
    ele.removeClass();
    ele.addClass("bordetitilante");
    setTimeout(function () { blink2(ele); }, 150);
}

function blink2(ele) {
    ele.removeClass();
    ele.addClass("bordenormal");
	setTimeout(function () { blink3(ele); }, 150);
}

function blink3(ele) {
    ele.removeClass();
    ele.addClass("bordetitilante");
	setTimeout(function () { blink4(ele); }, 150);
}

function blink4(ele) {
    ele.removeClass();
    ele.addClass("bordenormal");
	setTimeout(function () { blink5(ele); }, 150);
}

function blink5(ele) {
    ele.removeClass();
    ele.addClass("bordetitilante");
	setTimeout(function () { blink6(ele); }, 150);
}

function blink6(ele) {
    ele.removeClass();
    ele.addClass("bordenormal");
}

$( '#type' ).change(function() {
	if( $(this).val() == "1" ) //credit
	{
		$( '#account_reference' ).prop( 'disabled', false );
		$( '#credit_limit' ).prop( 'disabled', false );
	}else {
		if( $(this).val() == "0" ) //cash
		{
			$( '#account_reference' ).prop( 'disabled', true );
			$( '#credit_limit' ).prop( 'disabled', true );
		}
	}
});

$( '#nombre' ).focusout(function() {
	if( !nomape_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

$( '#apellido' ).focusout(function() {
	if( !nomape_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

$( '#email' ).focusout(function() {
	if( !email_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

$( '#email_conf' ).focusout(function() {
	if( !email_conf_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

$( '#direccion' ).focusout(function() {
	if( !direccion_valida( $(this) ) )
	{
		blink( $(this) );
	}

});

$( '#cp' ).focusout(function() {
	if( !cp_valido( $(this) ) )
	{
		blink( $(this) );
	}

});

$( '#telefono' ).focusout(function() {
	if( !telefono_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

$( '#empresa' ).focusout(function() {
	if( !comcar_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

$( '#cargo' ).focusout(function() {
	if( !comcar_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

$( '#comentarios' ).focusout(function() {
	if( !comments_valido( $(this) ) )
	{
		blink( $(this) );
	}
});

/* informacion/contactanos */
$( '#form_contacto' ).submit( function( event ) {
	event.preventDefault();
	
	errores = '<ul>';
	
	if( !nomape_valido( $('#nombre') ) )
	{
		errores = errores + '<li>El nombre es obligatorio y debe tener solo letras</li>';
	}
	
	if( !nomape_valido( $('#apellido') ) )
	{
		errores = errores + '<li>El apellido es obligatorio y debe tener solo letras</li>';
	}
	
	if( !email_valido( $('#email') ) )
	{
		errores = errores + '<li>El email es obligatorio y debe tener el formato <i>usuario@dominio.com</i></li>';
	}
	
	if( !telefono_valido( $('#telefono') ) )
	{
		errores = errores + '<li>El número de teléfono es obligatorio, debe tener solo números y puede contener el signo + adelante</li>';
	}
	
	if( !comcar_valido( $('#empresa') ) )
	{
		errores = errores + '<li>El nombre de su empresa solo puede contener letras y números</li>';
	}
	
	if( !comcar_valido( $('#cargo') ) )
	{
		errores = errores + '<li>El nombre de su cargo solo puede contener letras y números</li>';
	}
	
	if( !comments_valido( $('#comentarios') ) )
	{
		errores = errores + '<li>Debe introducir algun comentario y este, solo puede contener letras números y los signos _ , y . </li>';
	}
	
	errores = errores + '</ul>';
	
	if( errores != '<ul></ul>')
	{
		$('#respuesta_form_contacto').html(errores);
		$('#respuesta_form_contacto').css("display", "inline-block");
	}else {
		var form = $(this);
        var str = form.serialize();
		$.ajax({
			type: "POST",
			url: "enviar_coments",
			data: str
		})
		.done(function( msg ) {
			$('#respuesta_form_contacto').html( msg );
			$('#respuesta_form_contacto').css("display", "inline-block");
		})
		.fail(function( msg ) {
			$('#respuesta_form_contacto').html( "Hubo un error enviando el formulario. Por favor, vuelva a intentarlo." );
			$('#respuesta_form_contacto').css("display", "inline-block");
		});
	
	}
});

/* New customer form */
$( '#new_customer_form' ).submit( function( event ) {
	
	err = '';
	
	if( $( '#type' ).val() == 1 && !ref_account_valid( $( '#account_reference' ) ) )
	{		
		err = err + 'If the type of the account is credit, you must enter an account reference.\n';
	}
	
	if( !name_valid( $('#name') ) )
	{
		err = err + 'The name field is empty or contains invalid characters.\n';
	}
	
	if( $( '#address' ).val().length > 0 && !address_valid( $('#address') ) )
	{
		err = err + 'The address contains invalid characters.\n';
	}
	
	if( $( '#telephone' ).val().length > 0 && !telephone_valid( $('#telephone') ) )
	{
		err = err + 'The phone field contains invalid characters.\n';
	}
	
	if( $( '#fax' ).val().length > 0 && !telephone_valid( $('#fax') ) )
	{
		err = err + 'The fax field contains invalid characters.\n';
	}
	
	if( !email_valid( $('#email') ) )
	{
		err = err + 'The email field is empty or contains invalid characters.\n';
	}	
	
	if( $( '#contact_name' ).val().length > 0 && !name_valid( $('#contact_name') ) )
	{
		err = err + 'The contact name field contains invalid characters.\n';
	}
	
	if( $( '#representative' ).val().length > 0 && !name_valid( $(representative) ) )
	{
		err = err + 'The representative field contains invalid characters.\n';
	}
	
	if( $( '#type' ).val() == 1 && !limit_credit_valid( $(credit_limit) ) )
	{
		err = err + 'The credit limit field contains invalid characters.\n';
	}

	if( $( '#statement_address' ).val().length > 0 && !address_valid( $('#statement_address') ) )
	{
		err = err + 'The statement address contains invalid characters.\n';
	}
	
	if( err != '')
	{
		alert( err );
		return false;
	}else
	{		
		return true;
	}
	
});
