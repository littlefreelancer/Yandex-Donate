<?php
/*
Plugin Name: Яндекс Донат
Description: Простое добавление на сайт кнопки Yandex пожертвований.
Author: Konstantin Teplouxov
Version: 0.1.2
Author URI: https://github.com/littlefreelancer
License: GPLv2 or later
*/
$true_page = 'yandex-donate.php';
 
add_action('admin_menu', 'true_options');
add_action( 'admin_init', 'true_option_settings' );
add_shortcode( 'yd-button-donate', 'yd_buttom_shortcode' );
add_action( 'admin_print_footer_scripts', 'appthemes_add_quicktags' );

function true_options() {
	global $true_page;
	add_menu_page( 'Я.Донат', 'Я.Донат', 'manage_options', $true_page, 'yd_option_page');  
}
function yd_option_page(){
	global $true_page;
	?><div class="wrap">
		<h2>Настройки плагина 'Я.Донат'</h2>
		<form method="post" enctype="multipart/form-data" action="options.php">
			<?php 
			settings_fields('true_options');
			do_settings_sections($true_page);
			?>
			<p class="submit">  
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
			</p>
		</form>
	</div><?php
}
function true_option_settings() {
	global $true_page;
	register_setting( 'true_options', 'true_options'); 
	add_settings_section( 'global_section', '', '', $true_page );
	$true_field_params = array(
		'type'      => 'text',
		'id'        => 'yd_account',
		'desc'      => 'Ваш кошелёк формата 410010000000000',
		'label_for' => 'yd_account'
	);
	add_settings_field( 'my_text_field_acc', 'Номер кошелька', 'true_option_display_settings', $true_page, 'global_section', $true_field_params );
 
 	$true_field_params = array(
		'type'      => 'text',
		'id'        => 'yd_summa',
		'desc'      => 'Сумма доната',
		'label_for' => 'yd_summa'
	);
	add_settings_field( 'my_text_field_summ', 'Сумма доната', 'true_option_display_settings', $true_page, 'global_section', $true_field_params );
}
function true_option_display_settings($args) {
	extract( $args );
 
	$option_name = 'true_options';
 
	$o = get_option( $option_name );
 
	switch ( $type ) {  
		case 'text':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<input class='regular-text' type='text' pattern='^[ 0-9]+$' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
		break;
	}
}
function yd_buttom_shortcode()
{
	$all_options = get_option('true_options');
	$yandex_buttom_src = '<iframe src="https://money.yandex.ru/quickpay/button-widget?targets=%D0%9F%D0%BE%D0%B4%D0%B4%D0%B5%D0%B6%D0%BA%D0%B0%20%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B0&default-sum=' . $all_options['yd_summa'] . '&button-text=13&yamoney-payment-type=on&button-size=m&button-color=orange&successURL=&quickpay=small&account=' . $all_options['yd_account'] . '" width="184" height="36" frameborder="0" allowtransparency="true" scrolling="no"></iframe>';
	return $yandex_buttom_src;
}
function appthemes_add_quicktags() {
	if ( ! wp_script_is('quicktags') )
		return;
	?>
	<script type="text/javascript">
		QTags.addButton( 'yd_button', 'Я.Донат', '[yd-button-donate]' );
	</script>
	<?php
}
