<?php

namespace Vladyslav10111\MarkedMap;

class MarkedMapShortcode
{
    public function __construct()
    {
        add_shortcode('marked_map', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_google_maps_script']);
    }

    public function enqueue_google_maps_script()
    {
        $api_key = MarkedMapAdmin::get_api_key();

        if (!empty($api_key)) {
            wp_enqueue_script(
                'google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($api_key),
                [],
                null,
                true
            );
        }
    }

    public function render_shortcode($atts)
    {
        $atts = shortcode_atts([
            'points' => '',
            'height' => '500px',
        ], $atts);

        $api_key = MarkedMapAdmin::get_api_key();

        if (empty($api_key)) {
            return '<p><strong>Error:</strong> Google Maps API key is not set. Please configure it in <a href="' . esc_url(admin_url('options-general.php?page=marked-map')) . '">Marked Map settings</a>.</p>';
        }

        $map_id = 'map_' . uniqid();
        $points = array_map('trim', explode('|', $atts['points']));

        ob_start();
        ?>

        <div id="<?php echo esc_attr($map_id); ?>" style="height: <?php echo esc_attr($atts['height']); ?>; width: 100%;"></div>

        <script>
            window.gm_authFailure = function () {
                alert("Invalid Google Maps API key. Please check your settings in the admin panel.");
            };
        </script>

        <script>
            function initMarkedMap_<?php echo $map_id; ?>() {
                const map = new google.maps.Map(document.getElementById("<?php echo $map_id; ?>"), {
                    zoom: 8,
                    center: { lat: 49.0, lng: 32.0 }
                });

                const points = <?php echo json_encode($points); ?>;
                const bounds = new google.maps.LatLngBounds();

                points.forEach(function (point) {
                    const match = point.match(/^([-+]?[0-9]*\.?[0-9]+),\s*([-+]?[0-9]*\.?[0-9]+)$/);
                    if (match) {
                        const lat = parseFloat(match[1]);
                        const lng = parseFloat(match[2]);
                        const position = new google.maps.LatLng(lat, lng);

                        new google.maps.Marker({
                            position: position,
                            map: map
                        });

                        bounds.extend(position);
                    }
                });

                if (!bounds.isEmpty()) {
                    map.fitBounds(bounds);
                }
            }

            document.addEventListener("DOMContentLoaded", initMarkedMap_<?php echo $map_id; ?>);
        </script>

        <?php
        return ob_get_clean();
    }
}
