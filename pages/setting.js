function wpis_submit()
{
	if(document.wpis_form.wpis_path.value=="")
	{
		alert(wpis_adminscripts.wpis_path)
		document.wpis_form.wpis_path.focus();
		return false;
	}
	else if(document.wpis_form.wpis_link.value=="")
	{
		alert(wpis_adminscripts.wpis_link)
		document.wpis_form.wpis_link.focus();
		return false;
	}
	else if(document.wpis_form.wpis_target.value=="")
	{
		alert(wpis_adminscripts.wpis_target)
		document.wpis_form.wpis_target.focus();
		return false;
	}
	else if(document.wpis_form.wpis_order.value=="")
	{
		alert(wpis_adminscripts.wpis_order)
		document.wpis_form.wpis_order.focus();
		return false;
	}
	else if(isNaN(document.wpis_form.wpis_order.value))
	{
		alert(wpis_adminscripts.wpis_order)
		document.wpis_form.wpis_order.focus();
		return false;
	}
	else if(document.wpis_form.wpis_status.value=="")
	{
		alert(wpis_adminscripts.wpis_status)
		document.wpis_form.wpis_status.focus();
		return false;
	}
	else if(document.wpis_form.wpis_type.value=="")
	{
		alert(wpis_adminscripts.wpis_type)
		document.wpis_form.wpis_type.focus();
		return false;
	}
}

function wpis_delete(id)
{
	if(confirm(wpis_adminscripts.wpis_delete))
	{
		document.frm_wpis_display.action="options-general.php?page=wp-image-slideshow&ac=del&did="+id;
		document.frm_wpis_display.submit();
	}
}	

function wpis_redirect()
{
	window.location = "options-general.php?page=wp-image-slideshow";
}

function wpis_help()
{
	window.open("http://www.gopiplus.com/work/2011/05/06/wordpress-plugin-wp-image-slideshow/");
}
