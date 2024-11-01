<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? $_GET['did'] : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }

// First check if ID exist with requested ID
$sSql = $wpdb->prepare(
	"SELECT COUNT(*) AS `count` FROM ".WP_wpis_TABLE."
	WHERE `wpis_id` = %d",
	array($did)
);
$result = '0';
$result = $wpdb->get_var($sSql);

if ($result != '1')
{
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'wp-image-slideshow'); ?></strong></p></div><?php
}
else
{
	$wpis_errors = array();
	$wpis_success = '';
	$wpis_error_found = FALSE;
	
	$sSql = $wpdb->prepare("
		SELECT *
		FROM `".WP_wpis_TABLE."`
		WHERE `wpis_id` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'wpis_path' => $data['wpis_path'],
		'wpis_link' => $data['wpis_link'],
		'wpis_target' => $data['wpis_target'],
		'wpis_title' => $data['wpis_title'],
		'wpis_order' => $data['wpis_order'],
		'wpis_status' => $data['wpis_status'],
		'wpis_type' => $data['wpis_type']
	);
}
// Form submitted, check the data
if (isset($_POST['wpis_form_submit']) && $_POST['wpis_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('wpis_form_edit');
	
	$form['wpis_path'] = isset($_POST['wpis_path']) ? esc_url_raw($_POST['wpis_path']) : '';
	if ($form['wpis_path'] == '')
	{
		$wpis_errors[] = __('Please enter the image path.', 'wp-image-slideshow');
		$wpis_error_found = TRUE;
	}

	$form['wpis_link'] = isset($_POST['wpis_link']) ? esc_url_raw($_POST['wpis_link']) : '';
	if ($form['wpis_link'] == '')
	{
		$wpis_errors[] = __('Please enter the target link.', 'wp-image-slideshow');
		$wpis_error_found = TRUE;
	}
	
	$form['wpis_target'] = isset($_POST['wpis_target']) ? $_POST['wpis_target'] : '';
	if($form['wpis_target'] != "_blank" && $form['wpis_target'] != "_parent" && $form['wpis_target'] != "_self" && $form['wpis_target'] != "_new")
	{
		$form['wpis_target'] = "_blank";
	}
	
	$form['wpis_title'] = isset($_POST['wpis_title']) ? sanitize_text_field($_POST['wpis_title']) : '';
	
	$form['wpis_order'] = isset($_POST['wpis_order']) ? intval($_POST['wpis_order']) : '';
	
	$form['wpis_status'] = isset($_POST['wpis_status']) ? sanitize_text_field($_POST['wpis_status']) : '';
	if($form['wpis_status'] != "YES" && $form['wpis_status'] != "NO")
	{
		$form['wpis_status'] = "YES";
	}
	
	$form['wpis_type'] = isset($_POST['wpis_type']) ? sanitize_text_field($_POST['wpis_type']) : '';

	//	No errors found, we can add this Group to the table
	if ($wpis_error_found == FALSE)
	{	
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_wpis_TABLE."`
				SET `wpis_path` = %s,
				`wpis_link` = %s,
				`wpis_target` = %s,
				`wpis_title` = %s,
				`wpis_order` = %d,
				`wpis_status` = %s,
				`wpis_type` = %s
				WHERE wpis_id = %d
				LIMIT 1",
				array($form['wpis_path'], $form['wpis_link'], $form['wpis_target'], $form['wpis_title'], $form['wpis_order'], $form['wpis_status'], $form['wpis_type'], $did)
			);
		$wpdb->query($sSql);
		
		$wpis_success = __('Image details was successfully updated.', 'wp-image-slideshow');
	}
}

if ($wpis_error_found == TRUE && isset($wpis_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $wpis_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($wpis_error_found == FALSE && strlen($wpis_success) > 0)
{
	?>
	<div class="updated fade">
		<p><strong><?php echo $wpis_success; ?> <a href="<?php echo WP_wpis_ADMIN_URL; ?>"><?php _e('Click here', 'wp-image-slideshow'); ?></a> 
		<?php _e('view the details', 'wp-image-slideshow'); ?></strong></p>
	</div>
	<?php
}
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#wpis_path').val(img_imageurl);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery'); // jQuery
wp_enqueue_media(); // This will enqueue the Media Uploader script
?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('Wp image slideshow', 'wp-image-slideshow'); ?></h2>
	<form name="wpis_form" method="post" action="#" onsubmit="return wpis_submit()"  >
      <h3><?php _e('Update image details', 'wp-image-slideshow'); ?></h3>
      <label for="tag-image"><?php _e('Enter image path', 'wp-image-slideshow'); ?></label>
      <input name="wpis_path" type="text" id="wpis_path" value="<?php echo $form['wpis_path']; ?>" size="90" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the picture located on the internet', 'wp-image-slideshow'); ?></p>
      <label for="tag-link"><?php _e('Enter target link', 'wp-image-slideshow'); ?></label>
      <input name="wpis_link" type="text" id="wpis_link" value="<?php echo $form['wpis_link']; ?>" size="90" />
      <p><?php _e('When someone clicks on the picture, where do you want to send them', 'wp-image-slideshow'); ?></p>
      <label for="tag-target"><?php _e('Enter target option', 'wp-image-slideshow'); ?></label>
      <select name="wpis_target" id="wpis_target">
        <option value='_blank' <?php if($form['wpis_target']=='_blank') { echo 'selected' ; } ?>>_blank</option>
        <option value='_parent' <?php if($form['wpis_target']=='_parent') { echo 'selected' ; } ?>>_parent</option>
        <option value='_self' <?php if($form['wpis_target']=='_self') { echo 'selected' ; } ?>>_self</option>
        <option value='_new' <?php if($form['wpis_target']=='_new') { echo 'selected' ; } ?>>_new</option>
      </select>
      <p><?php _e('Do you want to open link in new window?', 'wp-image-slideshow'); ?></p>
      <label for="tag-title"><?php _e('Enter image reference', 'wp-image-slideshow'); ?></label>
      <input name="wpis_title" type="text" id="wpis_title" value="<?php echo $form['wpis_title']; ?>" size="90" />
      <p><?php _e('Enter image reference. This is only for reference.', 'wp-image-slideshow'); ?></p>
      <label for="tag-select-gallery-group"><?php _e('Select gallery type', 'wp-image-slideshow'); ?></label>
      <select name="wpis_type" id="wpis_type">
        <option value='GROUP1' <?php if($form['wpis_type']=='GROUP1') { echo 'selected' ; } ?>>Group1</option>
        <option value='GROUP2' <?php if($form['wpis_type']=='GROUP2') { echo 'selected' ; } ?>>Group2</option>
        <option value='GROUP3' <?php if($form['wpis_type']=='GROUP3') { echo 'selected' ; } ?>>Group3</option>
        <option value='GROUP4' <?php if($form['wpis_type']=='GROUP4') { echo 'selected' ; } ?>>Group4</option>
        <option value='GROUP5' <?php if($form['wpis_type']=='GROUP5') { echo 'selected' ; } ?>>Group5</option>
        <option value='GROUP6' <?php if($form['wpis_type']=='GROUP6') { echo 'selected' ; } ?>>Group6</option>
        <option value='GROUP7' <?php if($form['wpis_type']=='GROUP7') { echo 'selected' ; } ?>>Group7</option>
        <option value='GROUP8' <?php if($form['wpis_type']=='GROUP8') { echo 'selected' ; } ?>>Group8</option>
        <option value='GROUP9' <?php if($form['wpis_type']=='GROUP9') { echo 'selected' ; } ?>>Group9</option>
        <option value='GROUP0' <?php if($form['wpis_type']=='GROUP0') { echo 'selected' ; } ?>>Group0</option>
		<option value='widget' <?php if($form['wpis_type']=='Widget') { echo 'selected' ; } ?>>Widget</option>
		<option value='sample' <?php if($form['wpis_type']=='Sample') { echo 'selected' ; } ?>>Sample</option>
      </select>
      <p><?php _e('This is to group the images. Select your slideshow group.', 'wp-image-slideshow'); ?></p>
      <label for="tag-display-status"><?php _e('Display status', 'wp-image-slideshow'); ?></label>
      <select name="wpis_status" id="wpis_status">
        <option value='YES' <?php if($form['wpis_status']=='YES') { echo 'selected' ; } ?>>Yes</option>
        <option value='NO' <?php if($form['wpis_status']=='NO') { echo 'selected' ; } ?>>No</option>
      </select>
      <p><?php _e('Do you want the picture to show in your galler?', 'wp-image-slideshow'); ?></p>
      <label for="tag-display-order"><?php _e('Display order', 'wp-image-slideshow'); ?></label>
      <input name="wpis_order" type="text" id="wpis_order" size="10" value="<?php echo $form['wpis_order']; ?>" maxlength="3" />
      <p><?php _e('What order should the picture be played in. should it come 1st, 2nd, 3rd, etc.', 'wp-image-slideshow'); ?></p>
      <input name="wpis_id" id="wpis_id" type="hidden" value="">
      <input type="hidden" name="wpis_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button-primary" value="<?php _e('Update Details', 'wp-image-slideshow'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button-primary" onclick="wpis_redirect()" value="<?php _e('Cancel', 'wp-image-slideshow'); ?>" type="button" />
        <input name="Help" lang="publish" class="button-primary" onclick="wpis_help()" value="<?php _e('Help', 'wp-image-slideshow'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('wpis_form_edit'); ?>
    </form>
</div>
</div>
