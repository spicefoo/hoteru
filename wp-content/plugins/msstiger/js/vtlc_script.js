function goToTop() {
    //main-page 
    jQuery(window).scrollTop(0);
    /*
        jQuery("a[href='#main-page']").click(function() {
          $("html, body").animate({ scrollTop: 0 }, "slow");
          return false;
        });
    */
}

function enableTestDatabaseCredentials() {
    var str = document.getElementById("dbpass").value;
    if (str != '') {
        document.getElementById("Test-Database-Credentials").disabled = false;
    } else {
        document.getElementById("Test-Database-Credentials").disabled = true;
    }
}

function enableTestVtigerCredentials() {
    var str = document.getElementById("VTS_host_access_key").value;
    if (str != '') {
        document.getElementById("Test-Vtiger-Credentials").disabled = false;
    } else {
        document.getElementById("Test-Vtiger-Credentials").disabled = true;
    }
}


function testVtigerCredentials(siteurl) {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'VtstestVtigerAccess',
            'url': jQuery("#url").val(),
            'Vts_host_username': jQuery("#Vts_host_username").val(),
            'VTS_host_access_key': jQuery("#VTS_host_access_key").val(),
            'check': "checkVtigerWebservice",
        },
        success: function(data) {
            if (data.indexOf("success") != -1) {
                document.getElementById('vtst-vtiger-test-results').style.fontWeight = "bold";
                document.getElementById('vtst-vtiger-test-results').style.color = "green";
                document.getElementById('vtst-vtiger-test-results').innerHTML = "Vtiger connected successfully";
            } else {
                document.getElementById('vtst-vtiger-test-results').style.fontWeight = "bold";
                document.getElementById('vtst-vtiger-test-results').style.color = "red";
                document.getElementById('vtst-vtiger-test-results').innerHTML = "Vtiger Credentials are wrong";
            }
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function testDatabaseCredentials(siteurl) {
    //document.getElementById("database_process").style.display=" ";
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'VtsTigertestAccess',
            'hostname': jQuery("#hostname").val(),
            'dbuser': jQuery("#dbuser").val(),
            'dbpass': jQuery("#dbpass").val(),
            'dbname': jQuery("#dbname").val(),
            'check': "checkdatabase",
        },
        success: function(data) {
            if (data.indexOf("Success") != -1) {
                document.getElementById('vtst-database-test-results').style.fontWeight = "bold";
                //       document.getElementById('database_process').style.display = "none";
                document.getElementById('vtst-vtiger-test-results').style.color = "green";
                document.getElementById('vtst-database-test-results').style.color = "green";
                document.getElementById('vtst-database-test-results').innerHTML = "Database connected successfully";
            } else if (data == "1") {
                document.getElementById('vtst-database-test-results').style.fontWeight = "bold";
                //     document.getElementById('database_process').style.display = "none";
                document.getElementById('vtst-database-test-results').style.color = "red";
                document.getElementById('vtst-database-test-results').innerHTML = "Not a VTiger database";
            } else {
                document.getElementById('vtst-database-test-results').style.fontWeight = "bold";
                //   document.getElementById('database_process').style.display = "none";
                document.getElementById('vtst-database-test-results').style.color = "red";
                document.getElementById('vtst-database-test-results').innerHTML = "Database Credentials are wrong";
            }
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function select_allfields(formid, module) {
    var i;
    var data = "";
    var form = document.getElementById(formid);
    var chkall = form.elements['selectall'];
    var chkBx_count = form.elements['no_of_vt_fields'].value;
    if (chkall.checked == true) {
        for (i = 0; i < chkBx_count; i++) {
            if (document.getElementById('smack_vtlc_field' + i).disabled == false)
                document.getElementById('smack_vtlc_field' + i).checked = true;
        }
    } else {
        for (i = 0; i < chkBx_count; i++) {
            if (document.getElementById('smack_vtlc_field' + i).disabled == false)
                document.getElementById('smack_vtlc_field' + i).checked = false;
        }
    }
}

function duplicateUpdateRecords(tickid, removetickid) {
    if (jQuery("#" + tickid).attr('checked') == 'checked') {
        jQuery("#" + tickid).attr("checked", true);
        jQuery("#" + removetickid).attr("checked", false);
    }
}


function updateRecord(siteurl, module, option, onAction) {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'adminActions',
            'checked': 'true',
            'adminaction': "checkupdaterecord",
            'module': module,
            'option': option,
            'onAction': onAction,
        },
        success: function(data) {
            if (data.indexOf("true") != -1) {
                jQuery("#update_record_status").html('Saved');
                jQuery("#check_duplicate").attr("checked", false);
            } else {
                jQuery("#update_record_status").html('Not Saved');
                jQuery("#update_record").attr("checked", selected);
            }
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function checkDuplicate(siteurl, module, option, onAction, value) {
    var shortcode = '';
    if (onAction == 'onEditShortCode') {
        var shortcode = jQuery('#shortcode').val();
    }
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'adminActions',
            'adminaction': "checkduplicate",
            'module': module,
            'option': option,
            'onAction': onAction,
            'value': value,
            'shortcode': shortcode,
        },
        success: function(data) {
            if (data.indexOf("true") != -1) {
                jQuery("#check_duplicate_status").html('Saved');
                jQuery("#update_record").attr("checked", false);
            } else {
                jQuery("#check_duplicate_status").html('Not Saved');
                jQuery("#check_duplicate_none").attr("checked", true);
            }
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function captureAlreadyRegisteredUsers(siteurl) {

    document.getElementById('loading-image').style.display = "block";
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'captureWpUsers',
            'siteurl': siteurl,
        },
        success: function(data) {
            document.getElementById('capuserid').style.fontWeight = "bold";
            document.getElementById('capuserid').style.color = "green";
            jQuery('#capuserid').html("Successfully add User");
            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function testAccessKey(siteurl) {
    var hostaddress = jQuery("#smack_host_address").val();
    var username = jQuery("#smack_host_username").val();
    var accesskey = jQuery("#smack_host_access_key").val();
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'instantTestAccess',
            'siteurl': siteurl,
            'hostaddress': hostaddress,
            'username': username,
            'accesskey': accesskey,
        },
        success: function(data) {
            if (data.indexOf("Login Success") != -1) {
                document.getElementById('smack-access-key-test-results').style.fontWeight = "bold";
                document.getElementById('smack-access-key-test-results').style.color = "green";
                document.getElementById('smack-access-key-test-results').innerHTML = "Connected Successfully";
            } else {
                document.getElementById('smack-access-key-test-results').style.fontWeight = "bold";
                document.getElementById('smack-access-key-test-results').style.color = "red";
                document.getElementById('smack-access-key-test-results').innerHTML = "Credentials are incorrect";
            }
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function showOrHideRecaptcha(option) {
    if (option == "no") {
        jQuery("#recaptcha_public_key").css("display", 'none');
        jQuery("#recaptcha_private_key").css("display", 'none');
    } else {
        jQuery("#recaptcha_public_key").css("display", 'block');
        jQuery("#recaptcha_private_key").css("display", 'block');
    }
}

function syncCrmFields(siteurl, module, option, onAction) {
    document.getElementById('loading-image').style.display = "block";

    var shortcode = '';
    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'getCrmFields',
            'siteurl': siteurl,
            'adminaction': 'get_fields_from_database',
            'module': module,
            'option': option,
            'onAction': onAction,
            'shortcode': shortcode,
        },
        success: function(data) {
            jQuery("#fieldtable").html(data);
            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}


function move(position, direction, siteurl, module, option, onAction) {

    if (direction == 'down') {
        var new_position = position + 1;
    } else {
        var new_position = position - 1;
    }


    document.getElementById('loading-image').style.display = "block";

    var shortcode = jQuery('#vts_label' + new_position).val();
    if (onAction == 'onEditShortCode') {
        // shortcode = jQuery('#vts_label'+position+1).val();
    }
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'adminActions',
            'position': position,
            'direction': direction,
            'module': module,
            'option': option,
            'onAction': onAction,
            'shortcode1': shortcode,
        },
        success: function(data) {
            jQuery("#fieldtable").html(data);
            jQuery("#saved_msg").html('Field Moved');
            jQuery("#saved_msg").css('display', 'inline').fadeOut(3000);
            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function published(position, status, siteurl, module, option, onAction) {
    document.getElementById('loading-image').style.display = "block";

    var shortcode = jQuery('#shortcode').val();
    if (onAction == 'onEditShortCode') {
        shortcode = '';
    }

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'adminActions',
            'position': position,
            'publish': status,
            'module': module,
            'option': option,
            'onAction': onAction,
            'shortcode': shortcode,
        },
        success: function(data) {
            jQuery("#fieldtable").html(data);
            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function makeMandatory(siteurl, formid, makemandatory, module, option, onAction) {
    var shortcode = "";
    document.getElementById('loading-image').style.display = "block";

    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }

    var i;
    var form = document.getElementById(formid);
    var chkBx_count = form.elements['no_of_fields'].value;

    var data_array = {
        'action': 'adminActions',
        'makemandatory': makemandatory,
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
        'countchecked': chkBx_count,
    };

    for (i = 0; i < chkBx_count; i++) {
        if (document.getElementById('check' + i).checked == true) {
            data_array['makemandatory' + i] = 1;
        } else {
            data_array['makemandatory' + i] = 0;
        }
    }

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,

        success: function(data) {
            jQuery("#fieldtable").html(data);
            jQuery("#saved_msg").html('Saved Mandatory Fields');
            jQuery("#saved_msg").css('display', 'inline').fadeOut(3000);
            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function publishSelected(siteurl, formid, publish, module, option, onAction) {
    var shortcode = "";
    document.getElementById('loading-image').style.display = "block";

    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }

    var i;
    var form = document.getElementById(formid);
    var chkall = form.elements['selectall'];
    var chkBx_count = form.elements['no_of_fields'].value;

    var data_array = {
        'action': 'adminActions',
        'publishSelected': 'publishSelected',
        'publishChoice': publish,
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
        'countchecked': chkBx_count,
    };

    for (i = 0; i < chkBx_count; i++) {
        if (document.getElementById('select' + i).checked == true) {
            data_array['published' + i] = 1;
        } else {
            data_array['published' + i] = 0;
        }
    }

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,

        success: function(data) {
            jQuery("#fieldtable").html(data);
            //publish_selected    unpublish_selected
            if (publish == "publish_selected") {
                jQuery("#saved_msg").html('Selected Fields Published');
                //jQuery("#saved_msg").css('color','green');
            } else {
                jQuery("#saved_msg").html('Selected Fields Unpublished');
            }

            jQuery("#saved_msg").css('display', 'block').fadeOut(3000);

            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function selectAll(formid, module) {
    var i;
    var data = "";
    var form = document.getElementById(formid);
    var chkall = form.elements['selectall'];
    var chkBx_count = form.elements['no_of_fields'].value;
    if (chkall.checked == true) {
        for (i = 0; i < chkBx_count; i++) {
            if (document.getElementById('select' + i).disabled == false)
                document.getElementById('select' + i).checked = true;
        }
    } else {
        for (i = 0; i < chkBx_count; i++) {
            if (document.getElementById('select' + i).disabled == false)
                document.getElementById('select' + i).checked = false;
        }
    }
}

function saveDisplayName(siteurl, formid, savedisplayname, module, option, onAction) {

    var i;

    // var form =document.getElementById(formid);

    var text_value;

    var no_of_fields = jQuery('#no_of_vt_fields').val();
    document.getElementById('loading-image').style.display = "block";

    var shortcode = '';

    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }

    var data_array = {
        'action': 'adminActions',
        'savedisplayname': savedisplayname,
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
        'no_of_fields': no_of_fields,
    };

    for (i = 0; i < no_of_fields; i++) {
        text_value = document.getElementById('field_label_display_textbox' + i).value;
        data_array['display_label' + i] = text_value;
    }

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,

        success: function(data) {
            jQuery("#fieldtable").html(data);

            jQuery("#saved_msg").html('Saved Display Name Of Fields');
            jQuery("#saved_msg").css('display', 'inline').fadeOut(3000);

            document.getElementById('loading-image').style.display = "none";

            window.location.href = window.location.href;
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });

}

function saveDisplayNameLeadsShortCode(siteurl, formid, savedisplayname, module, option, onAction, shortCode) {

    var i;

    // var form =document.getElementById(formid);

    var text_value;

    var no_of_fields = jQuery('#no_of_vt_fields').val();
    document.getElementById('loading-image').style.display = "block";

    var shortcode = '';

    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }

    var data_array = {
        'action': 'adminActions',
        'savedisplayname': savedisplayname,
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
        'no_of_fields': no_of_fields,
    };

    for (i = 0; i < no_of_fields; i++) {
        text_value = document.getElementById('field_label_display_textbox' + i).value;
        data_array['display_label' + i] = text_value;
    }

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,

        success: function(data) {
            jQuery("#fieldtable").html(data);

            jQuery("#saved_msg").html('Saved Display Name Of Fields');
            jQuery("#saved_msg").css('display', 'inline').fadeOut(3000);
           
            document.getElementById('loading-image').style.display = "none";
            window.location.href = siteurl + "admin.php?page=vtlc&action=vtiger_db_fields&EditShortCode=" + shortCode;
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });

}

function saveDisplayNameLeads(siteurl, formid, savedisplayname, module, option, onAction) {

    var i;

    // var form =document.getElementById(formid);

    var text_value;

    var no_of_fields = jQuery('#no_of_vt_fields').val();
    document.getElementById('loading-image').style.display = "block";

    var shortcode = '';

    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }

    var data_array = {
        'action': 'adminActions',
        'savedisplayname': savedisplayname,
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
        'no_of_fields': no_of_fields,
    };

    for (i = 0; i < no_of_fields; i++) {
        text_value = document.getElementById('field_label_display_textbox' + i).value;
        data_array['display_label' + i] = text_value;
    }

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,

        success: function(data) {
            jQuery("#fieldtable").html(data);

            jQuery("#saved_msg").html('Saved Display Name Of Fields');
            jQuery("#saved_msg").css('display', 'inline').fadeOut(3000);

            document.getElementById('loading-image').style.display = "none";
            window.location.href = siteurl + "admin.php?page=vtlc&action=vtiger_db_fields";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });

}

function createNewShortcode(siteurl, formid, module, option, onAction) {
    var shortcode = '';
    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }
    var action;
    if (module == 'Leads')
        action = 'vtigerLeadFields';
    else if (module == 'Contacts')
        action = 'vtigerContactFields';

    var data_array = {
        'action': 'adminActions',
        'adminaction': 'createNewShortcode',
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,
        success: function(data) {
            window.location.href = siteurl + '/wp-admin/admin.php?page=wp-tiger-pro&action=' + action + '&EditShortCode=' + data;
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function makeWidget(siteurl, module, option, onAction) {
    var selected = true;
    document.getElementById('loading-image').style.display = "block";

    if (jQuery('#isWidget').attr('checked') == 'checked') {
        var checked = true;
        selected = false;
    } else {
        var checked = false;
    }
    var shortcode = '';
    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }
    var data_array = {
        'action': 'adminActions',
        'adminaction': 'isWidget',
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
        'checked': checked,
        'selected': selected,
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,
        success: function(data) {
            if (data.indexOf("true") != -1) {
                jQuery("#isWidget_status").html('Saved');
                jQuery("#isWidget_status").css('display', 'inline').fadeOut(3000);
            } else {
                jQuery("#isWidget_status").html('Not Saved');
                jQuery('#isWidget').attr("checked", selected);
                jQuery("#isWidget_status").css('display', 'inline').fadeOut(3000);
            }
            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function saveAssignedTo(siteurl, module, option, onAction) {
    var selected = true;
    document.getElementById('loading-image').style.display = "block";

    selected_value = jQuery('#assignedto :selected').val();
    var shortcode = '';
    if (onAction == 'onEditShortCode') {
        shortcode = jQuery('#shortcode').val();
    }

    var data_array = {
        'action': 'adminActions',
        'adminaction': 'saveassignedto',
        'module': module,
        'option': option,
        'onAction': onAction,
        'shortcode': shortcode,
        'selected_value': selected_value,
        'selected': selected,
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data_array,
        success: function(data) {
            if (data.indexOf("true") != -1) {
                jQuery("#assignedto_status").html('Saved');
                jQuery("#assignedto_status").css('display', 'inline').fadeOut(3000);
            } else {
                jQuery("#assignedto_status").html('Not Saved');
                jQuery('#assignedto').attr("checked", selected);
                jQuery("#assignedto_status").css('display', 'inline').fadeOut(3000);
            }
            document.getElementById('loading-image').style.display = "none";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
}

function recature() {
    var captueval = jQuery('#wp_tiger_vtst_user_capture').attr("checked");
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'recatuerget',
            'captueval': captueval,
        },
        success: function(data) {
            jQuery("#capture").html(data);
            document.getElementById('capture').style.fontWeight = "bold";
            document.getElementById('capture').style.color = "green";
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });

}

function confirmDelete(shortcode, module, adminurl) {
    //Do you surely want to trash all records ?
    selected_action = jQuery('#' + shortcode + ' :selected').val();

    if (selected_action == 'delete') {
        var checkConfirmation = confirm("Are you sure you want to delete?");
        if (checkConfirmation)
            doAction(shortcode, module, adminurl);
        else
            jQuery('#' + shortcode).val('Select Action');
    } else
        doAction(shortcode, module, adminurl);
}

function doAction(shortcode, module, adminurl) {

    var selected_value;
    var action;
    var link_url;
    selected_action = jQuery('#' + shortcode + ' :selected').val();
    if (selected_action == 'edit') {
        if (module == "lead") {
            action = "vtiger_db_fields";
        } else if (module == "widget") {
            action = "widget_fields";
        }

        link_url = adminurl + "admin.php?page=vtlc&action=" + action;
        link_url += "&EditShortCode=" + shortcode;
    } else if (selected_action == 'delete') {
        action = "deleteShortCodes";
        link_url = adminurl + "admin.php?page=vtlc&action=" + action;
        link_url += "&deleteShortCode=" + shortcode;
    }
    jQuery('#' + shortcode).val('Select Action');
    window.location.href = link_url;
}

function saveFormFields(data_array,link_url) {


    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'update_from_fields',
            'fields': JSON.parse(data_array),
        },
        success: function(data) {
            console.log('suc ', data);
           // window.location.href = link_url;

        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
    return false;
}




var setGlobalId = 0;
var gloabalCounter = 0;
function checkBoxSelect(event) {
    if(event.checked) { 
        var key = event.className;
        var key = event.className;
        if(gloabalCounter == 0) {
           setGlobalId = jQuery('.checkBoxSelect_'+key).val(); 
            
        }
        gloabalCounter++;
        jQuery('.checkBoxSelect_'+key).val(parseInt(setGlobalId));

    }else { 
        var key = event.className;
        var hiddenVal = jQuery('.checkBoxSelect_'+key).val();
        if(gloabalCounter == 0) {
           setGlobalId = hiddenVal; 
            
        }
        jQuery('.checkBoxSelect_'+key).val('0');
        gloabalCounter++;

    }
}