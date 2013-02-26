<?php
/*
Plugin Name: PG CTA Plugin
Plugin URI: http://patrickg.net
Description: Display Call To Action on all Posts and Pages
Author: Patrick Godbey
Version: 1
Author URI: http://patrickg.net
*/


	$pg_cta_version = "1";
	$table_name = $wpdb->prefix . "pg_cta_";

function pg_cta_install() {
	global $wpdb;
	global $pg_cta_version;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
 
	add_option("pg_cta_version", $pg_cta_version);
	add_option("pg_cta_defaultmessage", NULL);
	add_option("pg_cta_structure", NULL);
	add_option('pg_cta_boxurl', NULL);
	add_option('pg_cta_theme', NULL);
}

register_activation_hook(__FILE__,'pg_cta_install');

add_action('admin_menu', 'pg_cta_menu');

function pg_cta_menu() {
	add_options_page('SK CTA', 'SK CTA', 'manage_options', 'pg_cta_options', 'pg_cta_options');
}

function pg_cta_get_theme_list($override, $postid){
	$dir = ABSPATH . '/wp-content/plugins/sk-call-to-action/themes/';
	$dh  = opendir($dir);
	while (false !== ($filename = readdir($dh))) {
		$files[] = $filename;
	}		
	foreach ($files as $filename) {
		if($filename !== ".." || $filename !== "."){
			$ext = substr(strrchr($filename,'.'),1);
			if($ext == "css"){	
				$filename = basename($filename, '.css');
				$theme = get_option('pg_cta_theme');
				unset($selected);
				if($theme == $filename){ $selected = 'selected="selected"'; }
				if($override){
					unset($selected);
					$values = get_post_custom( $postid );
					$override_theme = isset( $values['pg_cta_override_theme'] ) ? esc_attr( $values['pg_cta_override_theme'][0] ) : '';
					if($override_theme == $filename){ $selected = 'selected="selected"'; }
				}
				$themelist .= "<option value=".$filename." ".$selected.">".$filename."</option>";
			}
		}
	}
	if($themelist){
		return $themelist;
	}else{
		return "<option>no themes</option>";
	}
}

function pg_cta_get_theme(){
	$theme = get_option('pg_cta_theme');
	if($theme){
		return stripslashes( $theme );
	}else{
		return FALSE;
	}
}

function pg_cta_update_theme($theme){
	$updatetheme = update_option('pg_cta_theme', $theme);
	if($updatetheme){
		return $updatetheme;
	}else{
		return FALSE;
	}
}

function pg_cta_get_boxurl(){
	$value = get_option('pg_cta_boxurl');
	if($value){
		return stripslashes( $value );
	}else{
		return FALSE;
	}
}

function pg_cta_update_boxurl($value){
	$value = update_option('pg_cta_boxurl', $value);
	if($valuee){
		return stripslashes( $value );
	}else{
		return FALSE;
	}
}

function pg_cta_get_defaultmessage($where){
	$defaultmessage = get_option('pg_cta_defaultmessage_'.$where);
	if($defaultmessage){
		return stripslashes( $defaultmessage );
	}else{
		return FALSE;
	}
}
function pg_cta_update_defaultmessage($where, $defaultmessage){

	$defaultmessage = update_option('pg_cta_defaultmessage_'.$where, $defaultmessage);
	if($updatedefaultmessage){
		return $updatedefaultmessage;
	}else{
		return FALSE;
	}
}

function pg_cta_get_imgurl(){
	$value = get_option('pg_cta_imgurl');
	if($value){
		return stripslashes( $value );
	}else{
		return FALSE;
	}
}

function pg_cta_update_imgurl($value){
	$value = update_option('pg_cta_imgurl', $value);
	if($valuee){
		return stripslashes( $value );
	}else{
		return FALSE;
	}
}


function pg_cta_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if(isset($_POST['toptext'])){
			$defaultmessage_response = pg_cta_update_defaultmessage( 'top', $_POST['toptext'] );
	}
	if(isset($_POST['bottomtext'])){
			$defaultmessage_response = pg_cta_update_defaultmessage( 'bottom', $_POST['bottomtext'] );
	}
	if(isset($_POST['skcta-theme'])){
			$defaultmessage_response = pg_cta_update_theme( $_POST['skcta-theme'] );
	}
	if(isset($_POST['skcta_boxurl'])){
		$skcta_boxurl = pg_cta_update_boxurl( $_POST['skcta_boxurl'] );	
	}
	if(isset($_POST['skcta_imgurl'])){
		$skcta_boxurl = pg_cta_update_imgurl( $_POST['skcta_imgurl'] );	
	}

	echo '<div class="wrap">';
	echo '<h2><div id="icon-options-general" class="icon32"></div> SpottedKoi CTA Plugin Settings</h2>';
	echo '<form method="POST" action="'; echo $_SERVER['REQUEST_URI']; echo '">';
	echo wp_nonce_field( 'pg_cta_settings_nonce', 'pg_cta_settings_nonce' );
	echo '<ul>';
	echo '<h3>CTA Box URL</h3>';
	echo '<li><input type="text" name="skcta_boxurl" size="65" value="';
	echo pg_cta_get_boxurl();
	echo '"></li> ';
	echo '<li><h3>CTA Theme</h3>
			create/modify theme files in <code>wp-content/plugins/sk-call-to-action/themes/</code> directory</li>
			<li><select name="skcta-theme">';
	echo pg_cta_get_theme_list();
	echo '</select></li>';
	echo '<a href="http://spottedkoi.com/wp-admin/plugin-editor.php?file=sk-call-to-action%2Fthemes%2F'.pg_cta_get_theme().'.css&plugin=sk-call-to-action%2Fsk-ctaplugin.php" target="_blank">edit selected theme</a>';
	echo '  <li><h3>Default Message</h3></li>
			<li><input type="text" id="toptext" name="toptext" size="65" value="'.pg_cta_get_defaultmessage('top').'"></li>
			<li><input type="text" id="bottomtext" name="bottomtext" size="65" value="'.pg_cta_get_defaultmessage('bottom').'"></li>';
	echo '<h3>Image Upload</h3>';
	
	?>
	
		<script language="JavaScript">
			jQuery(document).ready(function() {
			jQuery('#upload_image_button').click(function() {
			formfield = jQuery('#upload_image').attr('name');
			tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			return false;
			});

			window.send_to_editor = function(html) {
			imgurl = jQuery('img',html).attr('src');
			jQuery('#upload_image').val(imgurl);
			tb_remove();
			}

			});
		</script>

		<tr valign="top">
			<td>Upload Image</td>
			<td><label for="upload_image">
				<input id="upload_image" type="text" size="36" name="skcta_imgurl" value="<?php echo pg_cta_get_imgurl(); ?>" />
				<input id="upload_image_button" type="button" value="Upload Image" />
				<br />Enter an URL or upload an image for the banner.
				</label>
			</td>
		</tr>
	
	<?php
		echo '		<li><input class="button-primary" type="submit" name="Save" value="Submit" id="submitbutton" /></li>
			    </ul>
			</form>';
	echo '<h3>Preview</h3>';
	echo pg_cta_getcta();
	echo '</div>';
}

function pg_cta_getcta($postid = NULL){

		$theme = pg_cta_get_theme();
		$getstructure = wp_remote_get(site_url('/').'/wp-content/plugins/sk-call-to-action/themes/'.$theme.'.php');
		$structure = $getstructure['body'];
		$getcss = wp_remote_get(site_url('/').'/wp-content/plugins/sk-call-to-action/themes/'.$theme.'.css');
		$css = $getcss['body'];
		
		
		global $post;
		$postid = $post->ID;
		$overridetop = get_post_meta($postid, 'pg_cta_override_top', TRUE);
		$overridebottom = get_post_meta($postid, 'pg_cta_override_bottom', TRUE);
		$overridetheme = get_post_meta($postid, 'pg_cta_override_theme', TRUE);
		$overrideurl = get_post_meta($postid, 'pg_cta_override_url', TRUE);
		if( strlen($overridetop) > 1){
			$toptext = $overridetop;
		}else{
			$toptext = pg_cta_get_defaultmessage('top');
		}
		
		if( strlen($overridebottom) > 1 ){
			$bottomtext = $overridebottom;
		}else{
			$bottomtext = pg_cta_get_defaultmessage('bottom');
		}
		
		if( strlen($overrideurl) > 1 ){
			$boxurl = $overrideurl;
		}else{
			$boxurl = pg_cta_get_boxurl();
		}
		
		if( strlen($overridetheme) > 1 ){
			$theme = $overridetheme;
			$getstructure = wp_remote_get(site_url('/').'/wp-content/plugins/sk-call-to-action/themes/'.$theme.'.php');
			$structure = $getstructure['body'];
			$getcss = wp_remote_get(site_url('/').'/wp-content/plugins/sk-call-to-action/themes/'.$theme.'.css');
			$css = $getcss['body'];
		}
		
		$output = str_replace("%SKCTATOP%", $toptext, $structure);
		$output = str_replace("%SKCTABOTTOM%", $bottomtext, $output);
		$output = str_replace('%SKCTAURL%', $boxurl, $output);
		$imgurl = pg_cta_get_imgurl();
		$output = str_replace('%SKCTAIMG%', $imgurl, $output);
		return $css.$output;

}

add_shortcode('skcta', 'pg_cta_getcta');


///// Meta fields to override default message on a per post/page basis /////

add_action( 'add_meta_boxes', 'pg_cta_meta_box_add' );
function pg_cta_meta_box_add(){

	add_meta_box( 'pg_cta_meta_box', 'SK Call to Action Message', 'pg_cta_meta_box', 'post', 'normal', 'high' );
	add_meta_box( 'pg_cta_meta_box', 'SK Call to Action Message', 'pg_cta_meta_box', 'page', 'normal', 'high' );
}

function pg_cta_meta_box( $post ){

	$values = get_post_custom( $post->ID );
	$toptext = isset( $values['pg_cta_override_top'] ) ? esc_attr( $values['pg_cta_override_top'][0] ) : '';
	$bottomtext = isset( $values['pg_cta_override_bottom'] ) ? esc_attr( $values['pg_cta_override_bottom'][0] ) : '';
	$boxurl = isset( $values['pg_cta_override_url'] ) ? esc_attr( $values['pg_cta_override_url'][0] ) : '';
	wp_nonce_field( 'pg_cta_meta_box_nonce', 'meta_box_nonce' );
	?>
	<table>
		<tr>
			<td>
				<label for="pg_cta_meta_box_text">CTA Message (html allowed)</label>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Select Theme</strong>
			</td>
			<td>
				<select name="pg_cta_override_theme" id="pg_cta_override_theme">
				<option value="">Default Theme</option>
				<option value="">----------</option>
				<?php echo pg_cta_get_theme_list(TRUE, $post->ID); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<strong>CTA Box URL</strong>
			</td>
			<td>
				<input type="text" name="pg_cta_override_url" id="pg_cta_override" size="50" value="<?php echo $boxurl; ?>">
			</td>
		</tr>
		<tr>
			<td>
				<strong>Top Text</strong>
			</td>
			<td>
				<input type="text" name="pg_cta_override_top" id="pg_cta_override" size="50" value="<?php echo $toptext; ?>">
			</td>
		</tr>
		<tr>
			<td>
				<strong>Bottom Text</strong>
			</td>
			<td>
				<input type="text" name="pg_cta_override_bottom" id="pg_cta_override" size="50" value="<?php echo $bottomtext; ?>">
			</td>
		</tr>
	</table>
	<?php	
}


add_action( 'save_post', 'pg_cta_meta_box_save' );
function pg_cta_meta_box_save( $post_id )
{
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pg_cta_meta_box_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// Probably a good idea to make sure your data is set
	if( isset( $_POST['pg_cta_override_top'] ) )
		update_post_meta( $post_id, 'pg_cta_override_top', wp_kses( $_POST['pg_cta_override_top'], $allowed ) );
	if( isset( $_POST['pg_cta_override_bottom'] ) )
		update_post_meta( $post_id, 'pg_cta_override_bottom', wp_kses( $_POST['pg_cta_override_bottom'], $allowed ) );
	if( isset( $_POST['pg_cta_override_theme'] ) )
		update_post_meta( $post_id, 'pg_cta_override_theme', wp_kses( $_POST['pg_cta_override_theme'], $allowed ) );
	if( isset( $_POST['pg_cta_override_url'] ) )
		update_post_meta( $post_id, 'pg_cta_override_url', wp_kses( $_POST['pg_cta_override_url'], $allowed ) );
}

function skcta_options_enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('skcta-upload');

}
add_action('admin_enqueue_scripts', 'skcta_options_enqueue_scripts');

?>