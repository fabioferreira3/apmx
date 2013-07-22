function prepareIE(height, overflow) {
  bod = document.getElementsByTagName('body')[0];
  bod.style.height = height;
  bod.style.overflow = overflow;

  htm = document.getElementsByTagName('html')[0];
  htm.style.height = height;
  htm.style.overflow = overflow;
}

function initMsg() {
  bod = document.getElementsByTagName('body')[0];
  overlay = document.createElement('div');
  overlay.id = 'overlay';
  bod.appendChild(overlay);
  $('overlay').style.display = 'block';
  $('lightbox1').style.display = 'block';
  prepareIE("auto", "auto");
}
function cancelMsg() {
  bod = document.getElementsByTagName('body')[0];
  olddiv = document.getElementById('overlay');
  if(overlay){
	bod.removeChild(olddiv);
  }
}

function addQuote(url) {
  frmAdd2Cart = $('product_addtocart_form');
  if(frmAdd2Cart) {
	var validator = new Validation(frmAdd2Cart);
	if(validator)
	  if (validator.validate()){ 
		frmAdd2Cart.writeAttribute('action', url);
		frmAdd2Cart.submit();
	  }
  }
}

function isExistUserEmail(event, url, errorMsg){ 
  elmEmail		  = Event.element(event);
  elmEmailMsg	  = $('email_message');
  loaderEmailDiv  = $("please-wait");
  btnSubm		  = $('submitOrder')
  
  if(btnSubm) {btnSubm.disabled=false; }
  
  val = $F(elmEmail);  //$F('customer:email'); 
  var pars = 'email=' +  val;
  
  //loader
  loaderEmailDiv.show();
  
  new Ajax.Request( url, {
	method: 'post',
	parameters: pars,
	//onCreate: function() {  },
	onSuccess: function( transport ){	 
	  var responseStr = transport.responseText;
	  if(responseStr=='exists'){
		elmEmailMsg.show();
		elmEmailMsg.innerHTML=errorMsg;
		elmEmailMsg.addClassName("validation-advice");

		if($('advice-required-entry-customer:email')) $('advice-required-entry-customer:email').hide();
		if($('advice-validate-email-customer:email')) $('advice-validate-email-customer:email').hide();

		elmEmail.addClassName('validation-failed');
        if(btnSubm){ 
			btnSubm.setStyle({background: '#dddddd'});
			btnSubm.disabled=true;
		}

	  }else{
		elmEmailMsg.hide();
		elmEmailMsg.removeClassName("validation-advice");
	  }  
	  loaderEmailDiv.hide();
	},
	onFailure: function() {  
	  loaderEmailDiv.hide(); 
	  alert('Connection Error. Try again later.');
	}	     	
  });	
  
  return(false);
}