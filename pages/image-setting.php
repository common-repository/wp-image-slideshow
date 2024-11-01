<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <div class="form-wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br>
    </div>
    <h2><?php _e('Wp image slideshow', 'wp-image-slideshow'); ?></h2>
	<h3><?php _e('Widget Setting', 'wp-image-slideshow'); ?></h3>
    <?php
	$wpis_title = get_option('wpis_title');
	$wpis_width = get_option('wpis_width');
	$wpis_height = get_option('wpis_height');
	$wpis_pause = get_option('wpis_pause');
	$wpis_random = get_option('wpis_random');
	$wpis_type = get_option('wpis_type');
	
	if (isset($_POST['wpis_submit'])) 
	{
		//	Just security thingy that wordpress offers us
		check_admin_referer('wpis_form_setting');
			
		$wpis_title 	= stripslashes(sanitize_text_field($_POST['wpis_title']));
		$wpis_width 	= intval($_POST['wpis_width']);
		$wpis_height 	= intval($_POST['wpis_height']);
		$wpis_pause 	= intval($_POST['wpis_pause']);
		$wpis_random 	= stripslashes(sanitize_text_field($_POST['wpis_random']));
		$wpis_type 		= stripslashes(sanitize_text_field($_POST['wpis_type']));
		
		if(!is_numeric($wpis_width) || $wpis_width == 0) { $wpis_width = 250; }
		if(!is_numeric($wpis_height) || $wpis_height == 0) { $wpis_height = 170; }
		if(!is_numeric($wpis_pause) || $wpis_pause == 0) { $wpis_pause = 3000; }

		update_option('wpis_title', $wpis_title );
		update_option('wpis_width', $wpis_width );
		update_option('wpis_height', $wpis_height );
		update_option('wpis_pause', $wpis_pause );
		update_option('wpis_random', $wpis_random );
		update_option('wpis_type', $wpis_type );
		
		?>
		<div class="updated fade">
			<p><strong><?php _e('Details successfully updated', 'wp-image-slideshow'); ?></strong></p>
		</div>
		<?php
	}
	?>
    <form name="wpis_form" method="post" action="">
      <label for="tag-title"><?php _e('Enter widget title', 'wp-image-slideshow'); ?></label>
      <input name="wpis_title" id="wpis_title" type="text" value="<?php echo $wpis_title; ?>" maxlength="200" size="80" />
      <p><?php _e('Enter Widget title, Only for widget.', 'wp-image-slideshow'); ?></p>
      <label for="tag-width"><?php _e('Enter width', 'wp-image-slideshow'); ?></label>
      <input name="wpis_width" id="wpis_width" type="text" value="<?php echo $wpis_width; ?>" maxlength="4" />
      <p><?php _e('Enter widget width, only number.', 'wp-image-slideshow'); ?></p>
      <label for="tag-height"><?php _e('Enter height', 'wp-image-slideshow'); ?></label>
      <input name="wpis_height" id="wpis_height" type="text" value="<?php echo $wpis_height; ?>" maxlength="4" />
      <p><?php _e('Enter widget height, only number.', 'wp-image-slideshow'); ?></p>
      <label for="tag-title"><?php _e('Enter pause duration', 'wp-image-slideshow'); ?></label>
      <input name="wpis_pause" id="wpis_pause" type="text" value="<?php echo $wpis_pause; ?>" maxlength="4" />
      <p><?php _e('Only Number / Pause between content change (millisec).', 'wp-image-slideshow'); ?></p>
      <label for="tag-height"><?php _e('Random display option', 'wp-image-slideshow'); ?></label>
      <input name="wpis_random" id="wpis_random" type="text" value="<?php echo $wpis_random; ?>" maxlength="4" />
      <p><?php _e('Enter YES or NO', 'wp-image-slideshow'); ?></p>
      <label for="tag-height"><?php _e('Enter gallery type (This option available in add image form)', 'wp-image-slideshow'); ?></label>
      <input name="wpis_type" id="wpis_type" type="text" value="<?php echo $wpis_type; ?>" maxlength="50" />
      <p><?php _e('This field is to group the images. Enter your group name to fetch the images for widget.', 'wp-image-slideshow'); ?></p>
      <input name="wpis_submit" id="wpis_submit" class="button-primary" value="Submit" type="submit" />
	  <input name="publish" lang="publish" class="button-primary" onclick="wpis_redirect()" value="<?php _e('Cancel', 'wp-image-slideshow'); ?>" type="button" />
        <input name="Help" lang="publish" class="button-primary" onclick="wpis_help()" value="<?php _e('Help', 'wp-image-slideshow'); ?>" type="button" />
	  <?php wp_nonce_field('wpis_form_setting'); ?>
    </form>
  </div>
</div>
