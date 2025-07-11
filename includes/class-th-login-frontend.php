<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles all frontend-related functionalities for TH Login.
 */
class TH_Login_Frontend {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_modal_wrapper'));
        add_action('template_redirect', array($this, 'handle_wp_login_redirect'));
        add_action('init', array($this, 'handle_email_verification'));
        add_action('wp_logout', array($this, 'handle_logout_redirect'));
    }

    public function enqueue_scripts() {
        $asset_file = TH_LOGIN_PATH . 'app/build/public.asset.php';

        if (file_exists($asset_file)) {
            $asset_config = require_once $asset_file;
            if (!is_array($asset_config)) {
                $asset_config = array(
                    'dependencies' => array(),
                    'version' => TH_LOGIN_VERSION,
                );
            }
        } else {
            $asset_config = array(
                'dependencies' => array(),
                'version' => TH_LOGIN_VERSION,
            );
        }

        // Enqueue script with defer attribute
        wp_enqueue_script(
            'th-login-frontend-script',
            TH_LOGIN_URL . 'app/build/public.js',
            $asset_config['dependencies'],
            $asset_config['version'],
            array(
                'in_footer' => true,
                'strategy' => 'defer' // Using defer for better loading performance
            )
        );

        wp_enqueue_style(
            'th-login-frontend-style',
            TH_LOGIN_URL . 'app/build/public.css',
            array(),
            $asset_config['version']
        );

        wp_enqueue_style('dashicons');

        $general_settings = json_decode(get_option('th_login_general_settings', '{}'), true);
        $design_settings = json_decode(get_option('th_login_design_settings', '{}'), true);
        $form_fields_settings = json_decode(get_option('th_login_form_fields_settings', '{}'), true);
        $display_triggers_settings = json_decode(get_option('th_login_display_triggers_settings', '{}'), true);
        $security_settings = json_decode(get_option('th_login_security_settings', '{}'), true);

        wp_localize_script(
            'th-login-frontend-script',
            'thLoginFrontendData',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_rest'),
                'siteUrl' => get_site_url(),
                'currentUrl' => home_url(add_query_arg(null, null)),
                'settings' => array(
                    'general' => $general_settings,
                    'design' => $design_settings,
                    'form_fields' => $form_fields_settings,
                    'display_triggers' => $display_triggers_settings,
                    'security' => $security_settings,
                ),
                'isUserLoggedIn' => is_user_logged_in(),
                'currentUserRoles' => is_user_logged_in() ? wp_get_current_user()->roles : array(),
            )
        );
    }

    public function render_modal_wrapper() {
        $general_settings = json_decode(get_option('th_login_general_settings', '{}'), true);
        if (($general_settings['plugin_status'] ?? 'enabled') === 'disabled') {
            return;
        }
        require_once TH_LOGIN_PATH . 'templates/modal-wrapper.php';
    }

    public function handle_wp_login_redirect() {
        $general_settings = json_decode(get_option('th_login_general_settings', '{}'), true);
        $disable_wp_login = $general_settings['disable_wp_login_page'] ?? false;

        if ($disable_wp_login && (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false || strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false)) {
            $allowed_actions = array('logout', 'rp', 'resetpass');
            if (isset($_GET['action']) && in_array($_GET['action'], $allowed_actions, true)) {
                return;
            }
            if (is_user_logged_in()) {
                wp_redirect(admin_url());
                exit;
            }
            wp_redirect(add_query_arg('th_login_action', 'login', home_url()));
            exit;
        }
    }

    public function handle_logout_redirect() {
        $general_settings = json_decode(get_option('th_login_general_settings', '{}'), true);
        $redirect_settings = $general_settings['redirects']['after_logout'] ?? array('type' => 'home_page');
        $redirect_url = home_url();

        if ('custom_url' === $redirect_settings['type'] && !empty($redirect_settings['url'])) {
            $redirect_url = esc_url_raw($redirect_settings['url']);
        }

        wp_safe_redirect($redirect_url);
        exit;
    }

    public function handle_email_verification() {
        if (isset($_GET['th_login_verify_email']) && isset($_GET['user_id'])) {
            $verification_key = sanitize_text_field(wp_unslash($_GET['th_login_verify_email']));
            $user_id = absint($_GET['user_id']);

            $stored_key = get_user_meta($user_id, 'th_login_email_verification_key', true);

            if ($stored_key === $verification_key) {
                update_user_meta($user_id, 'th_login_email_verified', true);
                delete_user_meta($user_id, 'th_login_email_verification_key');

                $general_settings = json_decode(get_option('th_login_general_settings', '{}'), true);
                $redirect_after_verification = $general_settings['email_verification']['redirect_after_verification'] ?? 'login_form';
                $custom_redirect_url = $general_settings['email_verification']['custom_redirect_url'] ?? '';

                $redirect_url = home_url();

                if ('dashboard' === $redirect_after_verification) {
                    $redirect_url = admin_url();
                } elseif ('home_page' === $redirect_after_verification) {
                    $redirect_url = home_url();
                } elseif ('custom_url' === $redirect_after_verification && !empty($custom_redirect_url)) {
                    $redirect_url = esc_url_raw($custom_redirect_url);
                } elseif ('login_form' === $redirect_after_verification) {
                    $redirect_url = add_query_arg('th_login_action', 'login', home_url());
                }

                wp_safe_redirect($redirect_url);
                exit;
            } else {
                wp_die(esc_html__('Invalid verification link.', 'th-login'));
            }
        }
    }
}