<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
if (isset($_POST['frm_wpis_display']) && $_POST['frm_wpis_display'] == 'yes')
{
	$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
	if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
	
	$wpis_success = '';
	$wpis_success_msg = FALSE;
	
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
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('wpis_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_wpis_TABLE."`
					WHERE `wpis_id` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$wpis_success_msg = TRUE;
			$wpis_success = __('Selected record was successfully deleted.', 'wp-image-slideshow');
		}
	}
	
	if ($wpis_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $wpis_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2><?php _e('Wp image slideshow', 'wp-image-slideshow'); ?>
	<a class="add-new-h2" href="<?php echo WP_wpis_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'wp-image-slideshow'); ?></a></h2>
    <div class="tool-box">
	<?php
		$sSql = "SELECT * FROM `".WP_wpis_TABLE."` order by wpis_type, wpis_order";
		$myData = array();
		$myData = $wpdb->get_results($sSql, ARRAY_A);
		?>
		<form name="frm_wpis_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
		  	<th scope="col"><?php _e('Title', 'wp-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Type', 'wp-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Image', 'wp-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Link', 'wp-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Target', 'wp-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Order', 'wp-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Display', 'wp-image-slideshow'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th scope="col"><?php _e('Title', 'wp-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Type', 'wp-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Image', 'wp-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Link', 'wp-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Target', 'wp-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Order', 'wp-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Display', 'wp-image-slideshow'); ?></th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			if(count($myData) > 0 )
			{
				foreach ($myData as $data)
				{
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
						<td>
						<strong><?php echo esc_html(stripslashes($data['wpis_title'])); ?></strong>
						<div class="row-actions">
						<span class="edit"><a title="Edit" href="<?php echo WP_wpis_ADMIN_URL; ?>&amp;ac=edit&amp;did=<?php echo $data['wpis_id']; ?>">
						<?php _e('Edit', 'wp-image-slideshow'); ?></a> | </span>
						<span class="trash"><a onClick="javascript:wpis_delete('<?php echo $data['wpis_id']; ?>')" href="javascript:void(0);">
						<?php _e('Delete', 'wp-image-slideshow'); ?></a></span> 
						</div>
						</td>
						<td><?php echo esc_html(stripslashes($data['wpis_type'])); ?></td>
						<td><a href="<?php echo esc_html($data['wpis_path']); ?>" target="_blank"><img src="<?php echo WP_wpis_PLUGIN_URL; ?>/inc/image-icon.png"  /></a></td>
						<td><a href="<?php echo esc_html($data['wpis_link']); ?>" target="_blank"><img src="<?php echo WP_wpis_PLUGIN_URL; ?>/inc/link-icon.gif"  /></a></td>
						<td><?php echo esc_html(stripslashes($data['wpis_target'])); ?></td>
						<td><?php echo esc_html(stripslashes($data['wpis_order'])); ?></td>
						<td><?php echo esc_html(stripslashes($data['wpis_status'])); ?></td>
					</tr>
					<?php 
					$i = $i+1; 
				}
			}
			else
			{
				?><tr><td colspan="7" align="center"><?php _e('No records available', 'wp-image-slideshow'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('wpis_form_show'); ?>
		<input type="hidden" name="frm_wpis_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo WP_wpis_ADMIN_URL; ?>&amp;ac=add"><input class="button action" type="button" value="<?php _e('Add New', 'wp-image-slideshow'); ?>" /></a>
	  <a href="<?php echo WP_wpis_ADMIN_URL; ?>&amp;ac=set"><input class="button action" type="button" value="<?php _e('Widget Setting', 'wp-image-slideshow'); ?>" /></a>
	  <a target="_blank" href="<?php echo WP_wpis_FAV; ?>"><input class="button action" type="button" value="<?php _e('Help', 'wp-image-slideshow'); ?>" /></a>
	  <a target="_blank" href="<?php echo WP_wpis_FAV; ?>"><input class="button button-primary" type="button" value="<?php _e('Short Code', 'wp-image-slideshow'); ?>" /></a>
	  </div>
	</div>
</div>