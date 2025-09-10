<?php
/*
Plugin Name: Jednostavne Google Karte
Description: Jednostavni plugin za ugrađivanje Google karata u vaš WordPress site.
Version: 1.0
Author: Vaše Ime
*/

// Učitaj Google Maps API skriptu
function sgm_enqueue_google_maps() {
    wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBVnRf8vNQm3AgYJkMO7yqu-yGp1AAVZjA', [], null, true);
}
add_action('wp_enqueue_scripts', 'sgm_enqueue_google_maps');

// Shortcode za prikaz karte
// Shortcode za prikaz karte s fiksnom lokacijom
function sgm_display_fixed_map() {
    $latitude = '45.84184646606445'; // Fiksna širina (latitude)
    $longitude = '17.38709831237793'; // Fiksna dužina (longitude)
    $zoom = '14'; // Fiksni zoom
    $height = '400px'; // Visina karte
    $width = '100%'; // Širina karte

    $map_id = uniqid('sgm_map_');

    $output = "<div id='{$map_id}' style='height: {$height}; width: {$width};'></div>";
    $output .= "<script>
        function initMap_{$map_id}() {
            var location = { lat: parseFloat({$latitude}), lng: parseFloat({$longitude}) };
            var map = new google.maps.Map(document.getElementById('{$map_id}'), {
                zoom: {$zoom},
                center: location
            });
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                title: 'Moja lokacija'
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            initMap_{$map_id}();
        });
    </script>";

    return $output;
}
add_shortcode('simple_google_map_fixed', 'sgm_display_fixed_map');
