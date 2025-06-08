<?php

namespace Vladyslav10111\MarkedMap;

class MarkedMapAdmin
{
    const OPTION_KEY = 'marked_map_api_key';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_notices', [$this, 'show_missing_key_notice']);
    }

    public function add_settings_page()
    {
        add_options_page(
            'Marked Map Settings',
            'Marked Map',
            'manage_options',
            'marked-map',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings()
    {
        register_setting('marked_map_settings_group', self::OPTION_KEY);
    }

    public function render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1>Marked Map</h1>
            <p>This plugin allows you to embed a Google Map with markers using latitude and longitude pairs via a simple shortcode.</p>

            <h2>How to use:</h2>
            <p>Use the following shortcode in your posts or pages:</p>
            <code>[marked_map points="49.8397,24.0297|50.4501,30.5234|48.3794,31.1656" height="500px"]</code>

            <form method="post" action="options.php">
                <?php settings_fields('marked_map_settings_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Google Maps API Key</th>
                        <td>
                            <input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>" value="<?php echo esc_attr(get_option(self::OPTION_KEY)); ?>" class="regular-text" />
                            <p class="description">Enter your Google Maps JavaScript API key.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function show_missing_key_notice()
    {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        $screen = get_current_screen();
        if ($screen && $screen->base === 'settings_page_marked-map') {
            return; // Не показуємо на сторінці налаштувань самого плагіна
        }

        $api_key = get_option(self::OPTION_KEY);
        if (empty($api_key)) {
            echo '<div class="notice notice-warning"><p><strong>Marked Map:</strong> Google Maps API key is not set. Please <a href="' . esc_url(admin_url('options-general.php?page=marked-map')) . '">enter your key</a> to enable map display.</p></div>';
        }
    }

    // Статичний метод, який можна викликати у Shortcode-класі
    public static function get_api_key(): string
    {
        return esc_attr(get_option(self::OPTION_KEY, ''));
    }
}
