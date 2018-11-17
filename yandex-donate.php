<?php
/**
* Plugin Name: Яндекс.Донат
* Plugin URI:  https://github.com/littlefreelancer/Yandex-Donate
* Description: Позволяет собирать пожертвования на ваш Яндекс кошелек.
* Author: Konstantin Teplouxov
* Author URI:  https://vk.com/little.freelancer
* Version: 0.1.4
* License: GPLv2
*/
$yd_url_setting = 'yandex-donate.php';

add_action( 'admin_menu' , 'yd_admin_menu' );
add_action( 'admin_init', 'yd_option_settings' );
add_shortcode( 'yd-button-donate', 'yd_button_shortcode' );
add_action( 'admin_print_footer_scripts', 'yd_appthemes_add_quicktags' );
add_action( 'widgets_init', 'yd_register_widgets' );

function yd_admin_menu()
{
	global $yd_url_setting;
	add_menu_page('Яндекс.Донат', 'Яндекс.Донат', 'manage_options', $yd_url_setting, 'yd_option_page');
}
function yd_option_page()
{
	global $yd_url_setting;
	?><div class="wrap">
		<h2>Настройки плагина 'Яндекс.Донат'</h2>
		<form method="post" enctype="multipart/form-data" action="options.php">
			<?php 
			settings_fields('yd_donate_group');
			do_settings_sections($yd_url_setting);
			?>
			<p class="submit">  
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div><?php
}
function yd_option_settings()
{
	global $yd_url_setting;
	register_setting( 'yd_donate_group', 'yd_donate_db' );
	add_settings_section( 'global_section', 'Общие настройки', '', $yd_url_setting );

	$yd_field_params = array(
		'type'      => 'text',
		'desc'      => '',
		'id'        => 'yd_account',
		'placeholder' => '410015713140253',
		'label_for' => 'yd_account'
	);
	add_settings_field( 'yd_field_account', 'Номер вашего кошелька', 'yd_setting_callback_function', $yd_url_setting, 'global_section', $yd_field_params );

	$yd_field_params = array(
		'type'      => 'text',
		'desc'      => 'Максимум — 15000',
		'id'        => 'yd_summa',
		'placeholder' => '100',
		'label_for' => 'yd_summa'
	);
	add_settings_field( 'yd_field_summa', 'Сумма доната', 'yd_setting_callback_function', $yd_url_setting, 'global_section', $yd_field_params );

	$yd_field_params = array(
		'type'      => 'text',
		'desc'      => 'Например, на поддержку проекта',
		'id'        => 'yd_targets',
		'placeholder' => '',
		'label_for' => 'yd_targets'
	);
	add_settings_field( 'yd_field_targets', 'Назначение перевода', 'yd_setting_callback_function', $yd_url_setting, 'global_section', $yd_field_params );
}
function yd_setting_callback_function($args)
{
	$yd = get_option( 'yd_donate_db' );
	$id = $args['id'];
	$type = $args['type'];
	$desc = $args['desc'];
	$placeholder = $args['placeholder'];
	if ($type == 'text') 
	{
		echo "<input type='text'placeholder='$placeholder' required class='regular-text' id='$id' name='yd_donate_db[$id]' value='$yd[$id]'/>";
		if ($desc != "")
			echo "<br /><span class='description'>$desc</span>";
	}
}
function yd_button_shortcode()
{
	$all_options = get_option('yd_donate_db');

	$yandex_buttom_src = '<iframe src="https://money.yandex.ru/quickpay/button-widget?targets=' . $all_options['yd_targets'] . '&default-sum=' . $all_options['yd_summa'] . '&button-text=13&yamoney-payment-type=on&button-size=m&button-color=orange&successURL=&quickpay=small&account=' . $all_options['yd_account'] . '" width="184" height="36" frameborder="0" allowtransparency="true" scrolling="no"></iframe>';

	return $yandex_buttom_src;
}
function yd_appthemes_add_quicktags() {
	if ( ! wp_script_is('quicktags') )
		return;
	?>
	<script type="text/javascript">
		QTags.addButton( 'yd_button', 'Я.Донат', '[yd-button-donate]' );
	</script>
	<?php
}
function yd_register_widgets() {
	register_widget( 'YDonate_Widget' );
}
class YDonate_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'',
			'Яндекс.Донат',
			array('description' => 'Кнопка Я.Донат')
		);
	}
	function widget( $args, $instance ){
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		echo do_shortcode('[yd-button-donate]');

		echo $args['after_widget'];
	}
}
