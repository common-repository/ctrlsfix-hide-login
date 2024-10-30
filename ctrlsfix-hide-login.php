<?php
/* Plugin Name:       Ctrlsfix Hide Login
 * Plugin URI:        https://ctrlsfix.com/
 * Description:       Hide Login.
 * Version:           1.1
 * Author:            Praveen Kumar Joshi || Deepak Bansal
 * Author URI:        https://ctrlsfix.business.site/
 */
/* --Start-- Create Constant */
!defined('CTRLSFIX_HIDE_LOGIN_PLUGIN_VERSION') && define('CTRLSFIX_HIDE_LOGIN_PLUGIN_VERSION', '1.1');
!defined('CTRLSFIX_HIDE_LOGIN_PLUGIN_PLUGIN_URL') && define('CTRLSFIX_HIDE_LOGIN_PLUGIN_PLUGIN_URL', plugin_dir_url(__FILE__));
/* --End-- Create Constant */
function ctrlsfix__hideLoginActivate(){
    add_option('wpseh_l01gnhdlwp', 'root', '', 'yes');
}
register_activation_hook(__FILE__, 'ctrlsfix__hideLoginActivate');
function ctrlsfix__hideLoginHead(){
    $__ctrlsfix__slug = get_option('wpseh_l01gnhdlwp');
    if(isset($_GET['action']) && isset($_GET['key']))
        return;
    if(isset($_GET['action']) && $_GET['action'] == 'resetpass')
        return;
    if(isset($_GET['action']) && $_GET['action'] == 'rp')
        return;
    if(isset($_POST['redirect_slug']) && $_POST['redirect_slug'] == $__ctrlsfix__slug)
        return false;
    if(strpos($_SERVER['REQUEST_URI'], 'action=logout') !== false):
        check_admin_referer('log-out');
        wp_get_current_user();
        wp_logout();
        wp_safe_redirect(home_url(), 302);
        die;
    endif;
    if((strpos($_SERVER['REQUEST_URI'], $__ctrlsfix__slug) === false ) && ( strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false )):
        wp_safe_redirect(home_url('404'), 302);
        exit();
    endif;
}
add_action('login_init', 'ctrlsfix__hideLoginHead', 1);
function ctrlsfix__hideLoginHiddenField(){
    $__ctrlsfix__slug = get_option('wpseh_l01gnhdlwp', '');
    $__ctrlsfix__redirect_slug_input = '<input type="hidden" name="redirect_slug" value="' . sanitize_text_field(rawurldecode($__ctrlsfix__slug)) . '" />';
    echo html_entity_decode(esc_html($__ctrlsfix__redirect_slug_input));
}
add_action('login_form', 'ctrlsfix__hideLoginHiddenField');
function ctrlsfix__hideLoginInit(){
    $__ctrlsfix__slug = get_option('wpseh_l01gnhdlwp');
    if(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) == $__ctrlsfix__slug):
        wp_safe_redirect(home_url("wp-login.php?$__ctrlsfix__slug&redirect=false"));
        exit();
    endif;
}
add_action('init', 'ctrlsfix__hideLoginInit');
add_filter('lostpassword_url', 'ctrlsfix__hideLoginLostPassword', 10, 0);
function ctrlsfix__hideLoginLostPassword(){
    $__ctrlsfix__slug = get_option('wpseh_l01gnhdlwp');
    return site_url("wp-login.php?action=lostpassword&$__ctrlsfix__slug&redirect=false");
}
/* add_filter( 'logout_url', 'ctrlsfix___hideLogout', 10, 2 ); */
function ctrlsfix___hideLogout($logoutUrl){
    return home_url();
}
/* This adds the "redirect_slug" field to the password reset form and re-enables the email to be sent */
add_action('lostpassword_form', 'ctrlsfix__hideLoginHiddenField');
/* This sends the user back to the login page after the password reset email has been sent. This is the same behaviour as vanilla WordPress */
function ctrlsfix__hideLoginLostPasswordRedirect($lostpasswordRedirect){
    $__ctrlsfix__slug = get_option('wpseh_l01gnhdlwp');
    return 'wp-login.php?checkemail=confirm&redirect=false&' . $__ctrlsfix__slug;
}
add_filter('lostpassword_redirect', 'ctrlsfix__hideLoginLostPasswordRedirect', 100, 1);
add_action('admin_menu', 'ctrlsfix__loginPluginMenu');
function ctrlsfix__loginPluginMenu(){
    wp_enqueue_style('"ctrlsfix__hide_login_plugin_style', CTRLSFIX_HIDE_LOGIN_PLUGIN_PLUGIN_URL . 'style.css', '', CTRLSFIX_HIDE_LOGIN_PLUGIN_VERSION);
    add_options_page('Hide Login Options', 'Hide Login', 'manage_options', '__ctrlsfix__hideLogin', 'ctrlsfix__hideLoginPluginOptions');
}
function ctrlsfix__hideLoginPluginOptions(){
    if(!current_user_can('manage_options'))
        wp_die(__('You do not have permissions!'));
    $__ctrlsfix__hide_login_plugin_input_error_message = false;
    $__ctrlsfix__hide_login_plugin_input_sucess_message = false;
    if($_SERVER['REQUEST_METHOD'] === 'POST'):
        $__ctrlsfix__slug = sanitize_text_field(rawurlencode($_POST['__ctrlsfix__slug']));
        if(!empty($__ctrlsfix__slug)):
            update_option('wpseh_l01gnhdlwp', $__ctrlsfix__slug);
            $__ctrlsfix__hide_login_plugin_input_sucess_message = true;
        else:
            $__ctrlsfix__hide_login_plugin_input_error_message = true;
        endif;
    endif;
    $__ctrlsfix__htmlForm = '';
    $__ctrlsfix__htmlForm .= '<div class="__ctrlsfix__hide_login_plugin_admin_menu">';
    $__ctrlsfix__htmlForm .= '<h2>Hide Login :</h2>';
    $__ctrlsfix__htmlForm .= '<form action="options-general.php?page=__ctrlsfix__hideLogin&_wpnonce=' . sanitize_text_field(wp_create_nonce('__ctrlsfix__hideLogin')) . '" method="POST">';
    $__ctrlsfix__htmlForm .= '<div class="__ctrlsfix__hide_login_plugin_row_1"><label class="__ctrlsfix__hide_login_plugin_lable">Slug</label>';
    $__ctrlsfix__htmlForm .= '<input type="text" value="' . sanitize_text_field(rawurldecode(get_option('wpseh_l01gnhdlwp', ''))) . '" name="__ctrlsfix__slug" class="__ctrlsfix__hide_login_plugin_input">';
    $__ctrlsfix__htmlForm .= '</div>';
    if($__ctrlsfix__hide_login_plugin_input_sucess_message)
        $__ctrlsfix__htmlForm .= '<div class="__ctrlsfix__hide_login_plugin_row_2"><b style="color:green;">SuccessfullY Update!</b></div>';
    if($__ctrlsfix__hide_login_plugin_input_error_message)
        $__ctrlsfix__htmlForm .= '<div class="__ctrlsfix__hide_login_plugin_row_2"><b style="color:red;">This Field Is Required!</b></div>';
    $__ctrlsfix__htmlForm .= '<br><div class="__ctrlsfix__hide_login_plugin_row_2" style="color:#2E68C5;margin-bottom: 10px;">Your New Login URL : </div>';
    $__ctrlsfix__htmlForm .= '<div class="__ctrlsfix__hide_login_plugin_row_2"> <strong>' . get_site_url() . '?' . rawurldecode(get_option('wpseh_l01gnhdlwp', '')) . ' </strong> </div>';
    $__ctrlsfix__htmlForm .= '<div class="__ctrlsfix__hide_login_plugin_row_3"><input type="submit" class="__ctrlsfix__hide_login_plugin_submit" value="Update"></div>';
    $__ctrlsfix__htmlForm .= '</form>';
    $__ctrlsfix__htmlForm .= '</div>';
    echo html_entity_decode(esc_html($__ctrlsfix__htmlForm));
}
/* --Start-- Add Setting Link On Plugin Page */
function ctrlsfix__hideLoginSettingsLink($__ctrlsfix__link){
    $__agembed__settingsLink = '<a href="options-general.php?page=__ctrlsfix__hideLogin">Settings</a>';
    array_unshift($__ctrlsfix__link, $__agembed__settingsLink);
    return $__ctrlsfix__link;
}
$__ctrlsfix__pluginActionLink = plugin_basename(__FILE__);
add_filter("plugin_action_links_$__ctrlsfix__pluginActionLink", 'ctrlsfix__hideLoginSettingsLink');
/* --End-- Add Setting Link On Plugin Page */

