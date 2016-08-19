//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------

function enableCaptcha(siteurl,module, option, onAction)
{
        var selected = true;
	document.getElementById('loading-image').style.display = "block";
	if(jQuery('#isWidget').prop('checked'))
        {
                var checked = true;
                selected = false;
        }
        else
        {
                var checked = false;
        }
	var shortcode = '';
	if(onAction == 'onEditShortCode')
	{
		shortcode = jQuery('#shortcode').val();
	}
	var  data_array = {
	    'action'	    : 'adminAllActions',
	    'doaction'	    : 'SwitchWidget',
	    'adminaction'   : 'isWidget',
	    'module'	    : module,
	    'option'	    : option,
	    'onAction'	    : onAction,
	    'shortcode'	    : shortcode,
	    'checked'	    : checked, 
	    'selected'	    : selected,
	};

        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data_array,
                success:function(data) {
                        if(data.indexOf("true") != -1)
                        {
                                jQuery("#isWidget_status").html('Saved');
                                jQuery("#isWidget_status").css('display','inline').fadeOut(3000);
                        }
                        else
                        {
                                jQuery("#isWidget_status").html('Not Saved');
                                jQuery('#isWidget').attr("checked", selected);
                                jQuery("#isWidget_status").css('display','inline').fadeOut(3000);
                        }
			document.getElementById('loading-image').style.display = "none";
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
        });
}

function save_thirdparty_option(thirdparty)
{
	jQuery.ajax({
		type:'post',
		url: ajaxurl,
		data:{
		action:'save_thirdparty_free',
		thirdparty_option: thirdparty
		},
	success:function( data )
	{
	},
	error:function(errorThrown)
	{
		console.log(errorThrown);
	}

});

}

function selectedPlug( thiselement )
{
var pluginselect = document.getElementById('pluginselect');
var select = pluginselect.options[pluginselect.selectedIndex].value;
var old_crm = jQuery( "#revert_old_crm" ).val();
var get_config = jQuery( "#get_config_free" ).val();

if( get_config == 'no' )
        {        
	document.getElementById('loading-image').style.display = "block";
        var pluginselect_value;
        for(var i = 0; i < pluginselect.length; i++){
            if(pluginselect[i].selected == true){
                pluginselect_value = pluginselect[i].value;
            }
        }
        var redirectURL=document.getElementById('plug_URL').value;
        var postdata = pluginselect_value;
        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'   : 'selectplug',
                    'postdata' : postdata,
                },
                success:function(data) {
                        location.href=redirectURL+'&__module=Settings&__action=view';   //      Redirect to Plugin Settings page
                },
                error: function(errorThrown){
                        console.log(errorThrown);
                }
        });

        }
	else
	{	
jQuery.confirm({
	title:'',
	content:'If You change the CRM, the existing form integration with your CRM will not work..',
	confirmButtonClass: 'btn-info',
	cancelButtonClass: 'btn-danger',
	confirmButton: 'Confirm',
	cancelButton:'Cancel',
//	closeIcon: true,
	animation: 'opacity',
//    	closeAnimation: 'scale',
	keyboardEnabled: true,

	confirm: function(){
	document.getElementById('loading-image').style.display = "block";
        var pluginselect_value;
        for(var i = 0; i < pluginselect.length; i++){
            if(pluginselect[i].selected == true){
                pluginselect_value = pluginselect[i].value;
            }
        }
        var redirectURL=document.getElementById('plug_URL').value;
        var postdata = pluginselect_value;     
        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'   : 'selectplug',
                    'postdata' : postdata,
                },
                success:function(data) { 
                        location.href=redirectURL+'&__module=Settings&__action=view';   //      Redirect to Plugin Settings page
                },
                error: function(errorThrown){
                        console.log(errorThrown);
                }
        });

    	},	
	
	cancel: function(){
	jQuery("#pluginselect").val( old_crm );
    	}
})
}
}


//Two Factor Authentication

function TFA_Authkey_Save_free( auth_val)
{
        var TFA_authtoken = auth_val;
        jQuery.ajax({
                type: 'POST',
                url : ajaxurl,
                data : {
                        action : 'TFA_auth_save_free',
                        authtoken : TFA_authtoken
                } ,
                success: function(data){
                },
                error: function(errorThrown){
                        console.log( data );
                }

        });
}

function enablesmackTFA(id) {
        if(document.getElementById("TFA_check").checked == true) {
                document.getElementById("TFA_authkey").disabled = false;
        } else {
                document.getElementById("TFA_authkey").disabled = true;
        }
}


function goToTop()
{
	jQuery(window).scrollTop(0);
}

function selectAll(formid,module)
{
var i;
var data="";
var form =document.getElementById(formid);
var chkall = form.elements['selectall'];
var chkBx_count = form.elements['no_of_rows'].value;
	if(chkall.checked == true){
		for (i=0;i<chkBx_count;i++){
			if(document.getElementById('select'+i).disabled == false)
				document.getElementById('select'+i).checked = true;
		}
	}else{
		for (i=0;i<chkBx_count;i++){
			if(document.getElementById('select'+i).disabled == false)
				document.getElementById('select'+i).checked = false;
		}
	}
}

function syncCrmFields(siteurl, module, option, onAction)
{
	document.getElementById('loading-image').style.display = "block";
	var shortcode = '';
	if(onAction == 'onEditShortCode')
	{
		shortcode = jQuery('#shortcode').val();
	}

        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'	 : 'adminAllActions',
		    'doaction' 	 : 'FetchCrmFields',
                    'siteurl'	 : siteurl,
		    'module'	 : module,
		    'option'	 : option,
		    'onAction'	 : onAction,
		    'shortcode'	 : shortcode,
                },
                success:function(data) {
			jQuery("#fieldtable").html(data);
			document.getElementById('loading-image').style.display = "none";
			document.getElementById('crmfield').style.display = 'block';
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
        });
}
function debugmod(id) {
        if(document.getElementById("debug_mode").checked == true) {
                jQuery( "#debug_mode" ).val('on');
        } else {
                jQuery( "#debug_mode" ).val('off');
        }
}

function smack_email_check(id) { 
  if(document.getElementById("smack_email").selected == false) {
                document.getElementById("email").disabled = false;
		jQuery("#smack_email" ).val('on');
        } else {
                document.getElementById("email").disabled = true;
		jQuery("#smack_email" ).val('off');
        }
}


/*validate Redirect*/

function getvalidate(formid)
{
	var redirecturl = document.fieldform.redirecturl.value;
	var form = document.getElementById(formid);
	var enable = form.elements['enableurlredirection'];
	var tomatch = /(http(s)?:|http:|ftp:\\)?([\w-]+\.)+[\w-]+[.com|.in|.org]+(\[\?%&=]*)?/
	if (enable.checked == true)
	{
		if(tomatch.test(redirecturl))
		{
			return true;
		}
		else
		{
			window.alert("Invalid URL Tryagain");
			return false;
		}
	}
}

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
