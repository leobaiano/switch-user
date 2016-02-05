<?php
/*
Plugin Name: Switch User
Description: Switch to another user account quickly. Do not activate in production!
Version: 1.0.1
Author: Mário Valney
Author URI: http://www.mariovalney.com
Text Domain: switch-user
*/

if (!defined( 'ABSPATH' ))
	exit; // You shall not pass

define('SU_TEXTDOMAIN', 'switch-user');

/**
 * Load the plugin text domain for translation.
 *
 * @return void
 */
function su_load_plugin_textdomain() {
	load_plugin_textdomain( SU_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'su_load_plugin_textdomain' );

function su_frontend() {
    if (is_user_logged_in()) :
        $users = get_users(array('order_by' => 'login'));

        if (!empty($users)) : ?>
	        <div class="su-wrapper">
	            <span class="su-wrapper-toggle"></span>
	            <h1><?php _e('Switch User:', SU_TEXTDOMAIN) ?></h1>
	            <hr>
	            <ul>
	                <?php
	                    foreach ($users as $user) {
	                        if ($user->ID == get_current_user_id()) {
	                            echo '<li class="current-user" data-user-id="' . $user->ID . '">' . $user->user_login . '</li>';
	                        } else {
	                            echo '<li class="js-su-user" data-user-id="' . $user->ID . '">' . $user->user_login . '</li>';
	                        }
	                    }
	                ?>
	            </ul>
	            <?php wp_nonce_field('su-change-user-nonce', 'su-change-user-security'); ?>
	        </div>
    <?php endif; endif;
}
add_action('wp_footer', 'su_frontend');


/**
 * Load scripts js and styles css
 */
function su_enqueue_scripts() {
	$msgs = array(
				'change_success'	=> __( 'Current user successfully changed', SU_TEXTDOMAIN ),
				'change_error'		=> __( 'Oops... error: please try again.', SU_TEXTDOMAIN ),
				'connection_error'	=> __( '', SU_TEXTDOMAIN )
			);
	wp_enqueue_style(SU_TEXTDOMAIN . '_css_main', plugins_url( 'assets/css/main.css', __FILE__ ), array(), null, 'all');
	wp_enqueue_script(SU_TEXTDOMAIN . '_js_main', plugins_url( 'assets/js/main.js', __FILE__ ), array( 'jquery' ), null, true);
	wp_localize_script(SU_TEXTDOMAIN . '_js_main', 'SU', array('ajaxurl' => admin_url('admin-ajax.php'), 'msgs' => $msgs));
}
add_action('wp_enqueue_scripts', 'su_enqueue_scripts');

/**
 * Ajax: altera o usuário
 */
add_action('wp_ajax_su_change_user', 'su_change_user');
function su_change_user(){
    // Checa a referência (wp_nonce)
    check_ajax_referer('su-change-user-nonce', 'su_nonce');

    $return = array("status" => "error");

    if (isset($_POST['user_id']) && $_POST['user_id'] != '') {

        wp_set_auth_cookie($_POST['user_id']);
        $return = array("status" => "ok");

    } else {
        $return = array("status" => "error", "msg" => __("Invalid user ID.", SU_TEXTDOMAIN));
    }

    wp_send_json($return);
}
