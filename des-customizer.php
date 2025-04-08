<?php
/*
Plugin Name: DES Customizer
Description: Customize WordPress login page with URL, colors, backgrounds and more.
Author URI: https://digitalenterstudio.com
Version: 1.4.1
Author: David Gomez
Requires PHP: 7.4
Tested up to: 6.4.2
Update URI: https://digitalenterstudio.com/custom-login
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    die( '-1' );
}

// Prevent update notifications
add_filter('site_transient_update_plugins', function($value) {
    if (isset($value->response[plugin_basename(__FILE__)])) {
        unset($value->response[plugin_basename(__FILE__)]);
    }
    return $value;
});

// Check PHP version with improved messaging
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        $message = sprintf(
            __('Des Customizer requires PHP 7.4 or higher. Your current PHP version is %s. Please upgrade your PHP version or contact your hosting provider.', 'wordpress'),
            PHP_VERSION
        );
        echo '<div class="notice notice-error is-dismissible"><p>' . $message . '</p></div>';
    });
    return;
}

// Function to create a settings page
function cl_add_admin_menu() {
    add_options_page('DES Customizer Settings', 'DES Customizer', 'manage_options', 'des_customizer', 'cl_options_page');
}
add_action('admin_menu', 'cl_add_admin_menu');

// Add settings link to plugins page (appears before Deactivate)
function cl_add_action_links($links) {
    $settings_link = array(
        '<a href="' . admin_url('options-general.php?page=des_customizer') . '">' . __('Settings') . '</a>'
    );
    return array_merge($settings_link, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cl_add_action_links');

// Function to register settings
function cl_settings_init() {
    register_setting('pluginPage', 'cl_settings');

    add_settings_section(
        'cl_pluginPage_section',
        __('General Settings', 'wordpress'),
        null,
        'pluginPage'
    );

    add_settings_field(
        'cl_enable_custom_login',
        __('Enable DES Customizer', 'wordpress'),
        'cl_enable_custom_login_render',
        'pluginPage',
        'cl_pluginPage_section'
    );

    add_settings_field(
        'cl_custom_login_url',
        __('Custom Login URL', 'wordpress'),
        'cl_custom_login_url_render',
        'pluginPage',
        'cl_pluginPage_section'
    );

    add_settings_field(
        'cl_enable_custom_logo',
        __('Enable Custom Logo', 'wordpress'),
        'cl_enable_custom_logo_render',
        'pluginPage',
        'cl_pluginPage_section'
    );

    add_settings_field(
        'cl_custom_logo_url',
        __('Custom Logo URL', 'wordpress'),
        'cl_custom_logo_url_render',
        'pluginPage',
        'cl_pluginPage_section'
    );

    // New section for login styles
    add_settings_section(
        'cl_login_styles_section',
        __('Login Styles', 'wordpress'),
        null,
        'pluginPage'
    );

    add_settings_field(
        'cl_background_type',
        __('Background Type', 'wordpress'),
        'cl_background_type_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_background_color',
        __('Background Color', 'wordpress'),
        'cl_background_color_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_background_image',
        __('Background Image URL', 'wordpress'),
        'cl_background_image_render',
        'pluginPage',
        'cl_login_styles_section'
    );


    add_settings_field(
        'cl_enable_overlay',
        __('Enable Background Overlay', 'wordpress'),
        'cl_enable_overlay_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_overlay_color',
        __('Overlay Color', 'wordpress'),
        'cl_overlay_color_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_overlay_opacity',
        __('Overlay Opacity', 'wordpress'),
        'cl_overlay_opacity_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_login_position',
        __('Login Form Position', 'wordpress'),
        'cl_login_position_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_form_background_color',
        __('Form Background Color', 'wordpress'),
        'cl_form_background_color_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_form_background_opacity',
        __('Form Background Opacity', 'wordpress'),
        'cl_form_background_opacity_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_button_color',
        __('Login Button Color', 'wordpress'),
        'cl_button_color_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_button_hover_color',
        __('Login Button Hover Color', 'wordpress'),
        'cl_button_hover_color_render',
        'pluginPage',
        'cl_login_styles_section'
    );

    add_settings_field(
        'cl_custom_login_css',
        __('Additional CSS', 'wordpress'),
        'cl_custom_login_css_render',
        'pluginPage',
        'cl_login_styles_section'
    );
}
add_action('admin_init', 'cl_settings_init');

// Render the enable custom login checkbox
function cl_enable_custom_login_render() {
    $options = get_option('cl_settings');
    ?>
    <input type='checkbox' name='cl_settings[cl_enable_custom_login]' <?php checked($options['cl_enable_custom_login'], 1); ?> value='1'>
    <?php
}

// Render the custom login URL field
function cl_custom_login_url_render() {
    $options = get_option('cl_settings');
    ?>
    <input type='text' name='cl_settings[cl_custom_login_url]' value='<?php echo esc_attr($options['cl_custom_login_url']); ?>'>
    <p class="description">Enter a custom login URL (e.g., my-login).</p>
    <?php
}

// Render the custom logo URL field
function cl_custom_logo_url_render() {
    $options = get_option('cl_settings');
    $logo_url = isset($options['cl_custom_logo_url']) ? esc_url($options['cl_custom_logo_url']) : '';
    ?>
    <input type='text' name='cl_settings[cl_custom_logo_url]' value='<?php echo esc_attr($options['cl_custom_logo_url']); ?>'>
    <p class="description">Enter the URL of your custom logo image.</p>
    <?php if (!empty($logo_url)): ?>
        <div style="margin-top: 10px;">
            <p><strong>Logo Preview:</strong></p>
            <div style="width: 100%; height: 100px; background-size: contain; background-repeat: no-repeat; background-position: center; background-image: url('<?php echo $logo_url; ?>'); border: 1px solid #ddd;"></div>
            <p class="description">This shows how your logo will appear on the login page (100px height).</p>
        </div>
    <?php endif; ?>
    <?php
}

// Render form background color picker
function cl_form_background_color_render() {
    $options = get_option('cl_settings');
    $color = isset($options['cl_form_background_color']) ? $options['cl_form_background_color'] : '#ffffff';
    ?>
    <input type='color' name='cl_settings[cl_form_background_color]' value='<?php echo esc_attr($color); ?>'>
    <p class="description">Select background color for login form</p>
    <?php
}

// Render login button color picker
function cl_button_color_render() {
    $options = get_option('cl_settings');
    $color = isset($options['cl_button_color']) ? $options['cl_button_color'] : '#2271b1';
    ?>
    <input type='color' name='cl_settings[cl_button_color]' value='<?php echo esc_attr($color); ?>'>
    <p class="description">Select color for login button</p>
    <?php
}

// Render login button hover color picker
function cl_button_hover_color_render() {
    $options = get_option('cl_settings');
    $color = isset($options['cl_button_hover_color']) ? $options['cl_button_hover_color'] : '#135e96';
    ?>
    <input type='color' name='cl_settings[cl_button_hover_color]' value='<?php echo esc_attr($color); ?>'>
    <p class="description">Select hover color for login button</p>
    <?php
}

// Render form background opacity slider
function cl_form_background_opacity_render() {
    $options = get_option('cl_settings');
    $opacity = isset($options['cl_form_background_opacity']) ? $options['cl_form_background_opacity'] : 100;
    ?>
    <input type='range' name='cl_settings[cl_form_background_opacity]' min='0' max='100' value='<?php echo esc_attr($opacity); ?>' oninput="this.nextElementSibling.textContent=this.value+'%'">
    <span><?php echo esc_attr($opacity); ?>%</span>
    <p class="description">Adjust form background opacity (0-100%)</p>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.querySelector('input[name="cl_settings[cl_form_background_opacity]"]');
        if (slider) {
            slider.addEventListener('input', function() {
                this.nextElementSibling.textContent = this.value + '%';
            });
        }
    });
    </script>
    <?php
}

// Render the login position selector
function cl_login_position_render() {
    $options = get_option('cl_settings');
    $position = isset($options['cl_login_position']) ? $options['cl_login_position'] : 'center';
    ?>
    <select name='cl_settings[cl_login_position]'>
        <option value='left' <?php selected($position, 'left'); ?>><?php _e('Left', 'wordpress'); ?></option>
        <option value='center' <?php selected($position, 'center'); ?>><?php _e('Center', 'wordpress'); ?></option>
        <option value='right' <?php selected($position, 'right'); ?>><?php _e('Right', 'wordpress'); ?></option>
    </select>
    <p class="description">Select the position of the login form on the page</p>
    <?php
}

// Render the custom login CSS field
function cl_custom_login_css_render() {
    $options = get_option('cl_settings');
    ?>
    <textarea name='cl_settings[cl_custom_login_css]' rows='10' cols='50'><?php echo esc_textarea($options['cl_custom_login_css']); ?></textarea>
    <p class="description">Enter your custom CSS for the login page.</p>
    <?php
}

// Function to display the settings page
function cl_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>DES Customizer</h2>
        <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>
    </form>
    <?php
}

// Handle login redirection
function cl_handle_login_redirect() {
    $options = get_option('cl_settings');
    $enable_custom_login = isset($options['cl_enable_custom_login']) ? $options['cl_enable_custom_login'] : 0;
    $custom_login_url = isset($options['cl_custom_login_url']) ? sanitize_title($options['cl_custom_login_url']) : '';
    
    if ($enable_custom_login && !empty($custom_login_url)) {
        $current_url = home_url($_SERVER['REQUEST_URI']);
        $login_page_url = home_url($custom_login_url);
        
        // Block direct access to wp-login.php - redirect to home
        if (strpos($current_url, 'wp-login.php') !== false && !is_admin()) {
            wp_redirect(home_url());
            exit;
        }
        
        // Block access to wp-admin when not logged in - redirect to home
        if (strpos($current_url, 'wp-admin') !== false && !is_user_logged_in()) {
            wp_redirect(home_url());
            exit;
        }
        
        // Handle custom login page access
        if (untrailingslashit($current_url) === untrailingslashit($login_page_url)) {
            if (!is_user_logged_in()) {
                // Load the login form on custom URL
                require_once(ABSPATH . 'wp-login.php');
                exit;
            } else {
                wp_redirect(admin_url());
                exit;
            }
        }
        
        // Redirect to custom login URL when authentication fails
        add_action('wp_login_failed', function() use ($login_page_url) {
            wp_redirect(add_query_arg('login', 'failed', $login_page_url));
            exit;
        });
        
        // Redirect to custom login URL when logging out
        add_action('wp_logout', function() use ($login_page_url) {
            wp_redirect(add_query_arg('loggedout', 'true', $login_page_url));
            exit;
        });
    }
}
add_action('init', 'cl_handle_login_redirect');

// Handle custom login page authentication
function cl_authenticate_user($user, $username, $password) {
    // Only validate if this is a login attempt (POST request)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $options = get_option('cl_settings');
        $enable_custom_login = isset($options['cl_enable_custom_login']) ? $options['cl_enable_custom_login'] : 0;
        $custom_login_url = isset($options['cl_custom_login_url']) ? $options['cl_custom_login_url'] : '';
        
        if ($enable_custom_login && !empty($custom_login_url) && !is_admin()) {
            if (empty($username) || empty($password)) {
                return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Please enter both username and password.'));
            }
        }
    }
    return $user;
}
add_filter('authenticate', 'cl_authenticate_user', 30, 3);

// Render enable custom logo checkbox
function cl_enable_custom_logo_render() {
    $options = get_option('cl_settings');
    ?>
    <input type='checkbox' name='cl_settings[cl_enable_custom_logo]' <?php checked(isset($options['cl_enable_custom_logo']) ? $options['cl_enable_custom_logo'] : 0, 1); ?> value='1'>
    <?php
}

// Render background type radio buttons
function cl_background_type_render() {
    $options = get_option('cl_settings');
    $bg_type = isset($options['cl_background_type']) ? $options['cl_background_type'] : 'color';
    ?>
    <label>
        <input type='radio' name='cl_settings[cl_background_type]' value='color' <?php checked($bg_type, 'color'); ?>>
        <?php _e('Color', 'wordpress'); ?>
    </label><br>
    <label>
        <input type='radio' name='cl_settings[cl_background_type]' value='image' <?php checked($bg_type, 'image'); ?>>
        <?php _e('Image', 'wordpress'); ?>
    </label>
    <?php
}

// Render background color field
function cl_background_color_render() {
    $options = get_option('cl_settings');
    $bg_color = isset($options['cl_background_color']) ? $options['cl_background_color'] : '#f1f1f1';
    ?>
    <input type='color' name='cl_settings[cl_background_color]' value='<?php echo esc_attr($bg_color); ?>'>
    <p class="description">Select background color for login page</p>
    <?php
}

// Render enable overlay checkbox
function cl_enable_overlay_render() {
    $options = get_option('cl_settings');
    ?>
    <input type='checkbox' name='cl_settings[cl_enable_overlay]' <?php checked(isset($options['cl_enable_overlay']) ? $options['cl_enable_overlay'] : 0, 1); ?> value='1'>
    <p class="description">Enable color overlay on background image</p>
    <?php
}

// Render overlay color field
function cl_overlay_color_render() {
    $options = get_option('cl_settings');
    $color = isset($options['cl_overlay_color']) ? $options['cl_overlay_color'] : '#000000';
    ?>
    <input type='color' name='cl_settings[cl_overlay_color]' value='<?php echo esc_attr($color); ?>'>
    <p class="description">Select overlay color</p>
    <?php
}

// Render overlay opacity field
function cl_overlay_opacity_render() {
    $options = get_option('cl_settings');
    $opacity = isset($options['cl_overlay_opacity']) ? $options['cl_overlay_opacity'] : 50;
    ?>
    <input type='range' name='cl_settings[cl_overlay_opacity]' min='0' max='100' value='<?php echo esc_attr($opacity); ?>' oninput="this.nextElementSibling.textContent=this.value+'%'">
    <span><?php echo esc_attr($opacity); ?>%</span>
    <p class="description">Adjust overlay opacity (0-100%)</p>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.querySelector('input[name="cl_settings[cl_overlay_opacity]"]');
        if (slider) {
            slider.addEventListener('input', function() {
                this.nextElementSibling.textContent = this.value + '%';
            });
        }
    });
    </script>
    <?php
}

// Render background image field
function cl_background_image_render() {
    $options = get_option('cl_settings');
    $bg_image = isset($options['cl_background_image']) ? esc_url($options['cl_background_image']) : '';
    ?>
    <input type='text' name='cl_settings[cl_background_image]' value='<?php echo esc_attr($bg_image); ?>' class="regular-text">
    <p class="description">Enter URL of background image</p>
    <?php if (!empty($bg_image)): ?>
        <div style="margin-top: 10px;">
            <p><strong>Background Preview:</strong></p>
            <div style="width: 100%; height: 100px; background-size: cover; background-repeat: no-repeat; background-position: center; background-image: url('<?php echo $bg_image; ?>'); border: 1px solid #ddd;"></div>
        </div>
        <div style="margin-top: 10px;">
            <input type='checkbox' name='cl_settings[cl_grayscale_image]' <?php checked(isset($options['cl_grayscale_image']) ? $options['cl_grayscale_image'] : 0, 1); ?> value='1'>
            <label>Convert to grayscale</label>
        </div>
        <div style="margin-top: 10px;">
            <label>Brightness: </label>
            <input type='range' name='cl_settings[cl_brightness]' min='0' max='200' value='<?php echo esc_attr(isset($options['cl_brightness']) ? $options['cl_brightness'] : 100); ?>' oninput="this.nextElementSibling.textContent=this.value+'%'">
            <span><?php echo esc_attr(isset($options['cl_brightness']) ? $options['cl_brightness'] : 100); ?>%</span>
        </div>
        <div style="margin-top: 10px;">
            <label>Blur: </label>
            <input type='range' name='cl_settings[cl_blur]' min='0' max='20' value='<?php echo esc_attr(isset($options['cl_blur']) ? $options['cl_blur'] : 0); ?>' oninput="this.nextElementSibling.textContent=this.value+'px'">
            <span><?php echo esc_attr(isset($options['cl_blur']) ? $options['cl_blur'] : 0); ?>px</span>
        </div>
    <?php endif; ?>
    <?php
}

// Customize login logo and styles
function cl_custom_login_styles() {
    $options = get_option('cl_settings');
    $enable_logo = isset($options['cl_enable_custom_logo']) ? $options['cl_enable_custom_logo'] : 0;
    $logo_url = isset($options['cl_custom_logo_url']) ? esc_url($options['cl_custom_logo_url']) : '';
    $bg_type = isset($options['cl_background_type']) ? $options['cl_background_type'] : 'color';
    $bg_color = isset($options['cl_background_color']) ? $options['cl_background_color'] : '#f1f1f1';
    $bg_image = isset($options['cl_background_image']) ? esc_url($options['cl_background_image']) : '';
    $custom_css = isset($options['cl_custom_login_css']) ? $options['cl_custom_login_css'] : '';
    
    echo '<style type="text/css">';
    
    // Background styles
    if ($bg_type === 'color') {
        echo 'body.login { background-color: ' . esc_attr($bg_color) . ' !important; }';
    } else {
        $enable_overlay = isset($options['cl_enable_overlay']) ? $options['cl_enable_overlay'] : 0;
        $overlay_color = isset($options['cl_overlay_color']) ? $options['cl_overlay_color'] : '#000000';
        $overlay_opacity = isset($options['cl_overlay_opacity']) ? $options['cl_overlay_opacity'] / 100 : 0.5;
        
        echo 'body.login { 
            position: relative;
        }
        body.login::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("' . esc_url($bg_image) . '") !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            z-index: -1;';
        
        $filters = [];
        if (isset($options['cl_grayscale_image']) && $options['cl_grayscale_image']) {
            $filters[] = 'grayscale(100%)';
        }
        if (isset($options['cl_brightness'])) {
            $filters[] = 'brightness(' . esc_attr($options['cl_brightness']) . '%)';
        }
        if (isset($options['cl_blur']) && $options['cl_blur'] > 0) {
            $filters[] = 'blur(' . esc_attr($options['cl_blur']) . 'px)';
        }
        if (!empty($filters)) {
            echo 'filter: ' . implode(' ', $filters) . ';
                -webkit-filter: ' . implode(' ', $filters) . ';';
        }
        echo '}';
        
        if ($enable_overlay) {
            echo 'body.login::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: ' . esc_attr($overlay_color) . ';
                opacity: ' . esc_attr($overlay_opacity) . ';
                z-index: 0;
            }';
            
            echo '#login {
                position: relative;
                z-index: 1;
            }';
        }
    }
    
    // Login form styling
    $position = isset($options['cl_login_position']) ? $options['cl_login_position'] : 'center';
    $form_bg_color = isset($options['cl_form_background_color']) ? $options['cl_form_background_color'] : '#ffffff';
    $form_bg_opacity = isset($options['cl_form_background_opacity']) ? $options['cl_form_background_opacity'] / 100 : 1;
    
    echo 'body.login div#login {';
    if ($position === 'left') {
        echo 'margin: 0 0 0 0px; float: left;';
    } elseif ($position === 'right') {
        echo 'margin: 0 0px 0 0; float: right;';
    } else {
        echo 'margin: 0 auto; float: none;';
    }
    echo 'background-color: ' . esc_attr($form_bg_color) . ';';
    echo 'opacity: ' . esc_attr($form_bg_opacity) . ';';
    echo 'min-height: 100vh;';
    echo 'width: 420px;';
    echo 'padding: 40px;';
    echo 'box-sizing: border-box;';
    echo '}';

    // Logo styles
    if ($enable_logo && !empty($logo_url)) {
        echo '#login h1 a, .login h1 a {
            background-image: url(' . esc_url($logo_url) . ');
            height: 100px;
            width: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            padding-bottom: 30px;
        }';
    }
    
    // Button styles
    $button_color = isset($options['cl_button_color']) ? $options['cl_button_color'] : '#2271b1';
    $button_hover_color = isset($options['cl_button_hover_color']) ? $options['cl_button_hover_color'] : '#135e96';
    
    echo '#loginform .button-primary {
        background-color: ' . esc_attr($button_color) . ' !important;
        border-color: ' . esc_attr($button_color) . ' !important;
        box-shadow: 0 1px 0 ' . esc_attr($button_color) . ' !important;
    }
    #loginform .button-primary:hover,
    #loginform .button-primary:focus {
        background-color: ' . esc_attr($button_hover_color) . ' !important;
        border-color: ' . esc_attr($button_hover_color) . ' !important;
    }';

    // Additional CSS
    if (!empty($custom_css)) {
        echo wp_strip_all_tags($custom_css);
    }
    
    echo '</style>';
}
add_action('login_enqueue_scripts', 'cl_custom_login_styles');

// Change login logo URL to site URL
function cl_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'cl_login_logo_url');

// Change login logo title to site name
function cl_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'cl_login_logo_url_title');
?>
