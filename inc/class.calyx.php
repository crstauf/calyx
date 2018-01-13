<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

$Calyx;

class Calyx {

    var $cpts = null,
        $admin = null,
        $front = null,
        $actions = null,
        $filters = null,
        $customizer = null,
        $shortcodes = null,

        $debug = array(
            'scripts' => false,
            'styles' => false,
            'css' => false,
            'js' => false,
        );

        function __construct() {

            if ( SCRIPT_DEBUG ) {

                $this->debug = array(
                    'scripts' => SCRIPT_DEBUG,
                    'styles' => SCRIPT_DEBUG,
                    'css' => SCRIPT_DEBUG,
                    'js' => SCRIPT_DEBUG,
                );

            } else {

                if ( defined( 'COMPRESS_SCRIPTS' ) )
                    $this->debug['scripts']
                        = $this->debug['js']
                        = !COMPRESS_SCRIPTS;

                if ( defined( 'COMPRESS_CSS' ) )
                    $this->debug['styles']
                        = $this->debug['css']
                        = !COMPRESS_CSS;

            }

        }

        function doing_ajax() {
            return defined( 'DOING_AJAX' ) && DOING_AJAX;
        }

        function minify_js( $buffer ) {
            $buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);
            $buffer = str_replace(["\r\n","\r","\t","\n",'  ','    ','     '], '', $buffer);
            return preg_replace(['(( )+\))','(\)( )+)'], ')', $buffer);
        }

        function minify_css( $css ) {
    		// remove comments
    		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

    		// backup values within single or double quotes
    		preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);
    		for ($i=0; $i < count($hit[1]); $i++) {
    			$css = str_replace($hit[1][$i], '##########' . $i . '##########', $css);
    		}

    		// remove trailing semicolon of selector's last property
    		$css = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css);

    		// remove space between empty definition and next definition
    		$css = preg_replace('/}[\s\r\n\t]*/ims', "}\r\n", $css);

    		// remove space around plus sign
    		$css = preg_replace('/[\s\r\n\t]*\+[\s\r\n\t]*?([^\s\r\n\t])/ims', '+$1', $css);

    		// remove any whitespace between semicolon and property-name
    		$css = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css);

    		// remove any whitespace surrounding property-colon
    		$css = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css);

    		// remove any whitespace surrounding selector-comma
    		$css = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css);

    		// remove any whitespace surrounding opening parenthesis
    		$css = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css);

    		// remove any whitespace between numbers and units
    		$css = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css);

    		// shorten zero-values
    		$css = preg_replace('/([^\d\.]0)(px|em|pt)/ims', '$1', $css);

    		// remove tabs
    		$css = str_replace("\t",' ',$css);

    		// constrain multiple whitespaces
    		$css = preg_replace('/\p{Zs}+/ims',' ', $css);

    		// remove newlines
    		$css = str_replace(array("\r\n", "\r", "\n"), '', $css);

    		// shorten #aabbcc to #abc
    		$css = preg_replace("/#([0-9a-fA-F])\\1([0-9a-fA-F])\\2([0-9a-fA-F])\\3/", "#$1$2$3", $css);

    		// Restore backupped values within single or double quotes
    		for ($i=0; $i < count($hit[1]); $i++) {
    			$css = str_replace('##########' . $i . '##########', $hit[1][$i], $css);
    		}

    		return $css;
    	}

        function benchmark( $label, $switch = 1 ) {
            if (
                $switch
                && (
                    !defined( 'DOING_AJAX' )
                    || !DOING_AJAX
                )
                && (
                    !defined( 'QM_CALYX_DISABLED' )
                    || !QM_CALYX_DISABLED
                )
                && WP_DEBUG
                && function_exists( 'QMX_Benchmark' )
            )
                QMX_Benchmark( $label );
        }

}

function Calyx() {
    global $Calyx;

    if (
        !isset( $Calyx )
        || !( $Calyx instanceof Calyx )
    )
        $Calyx = new Calyx;

    return $Calyx;
}

Calyx()->benchmark( basename( __FILE__ ) . ':' . __LINE__ );

?>
