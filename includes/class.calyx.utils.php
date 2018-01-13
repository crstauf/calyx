<?php

if ( !class_exists( 'Calyx_Utilities' ) ) {

    class Calyx_Utilities {

        function version_monitor( array $customization, $dependency ) {
        }

        function benchmark() {
        }

    }

}

if ( !function_exists( 'get_theme_file_path' ) ) {

    function get_theme_file_path( string $after ) {
        return __DIR__ . '/' . $after;
    }

}

?>
