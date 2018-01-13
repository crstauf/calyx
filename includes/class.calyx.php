<?php

class Calyx {

    private static $_instance = null;

    private $_data = null;
    private $_admin = null;
    private $_front = null;
    private $_actions = null;
    private $_filters = null;
    private $_utilities = null;
    private $_woocommerce = null;

    private $_features = array();

    public static instance() {
        return (
            !is_null( self::$_instance ) )
            ? self::$_instance
            : self::$_instance = new self()
        );
    }

    function __construct() {

        class_exists( 'Calyx_Utilities' ) && $this->_utilities = new Calyx_Utilities;

        add_action( 'setup_theme_' . THEME_PREFIX, function() {

            class_exists( 'Calyx_Data'        ) && $this->_data        = new Calyx_Data;
            class_exists( 'Calyx_Admin'       ) && $this->_admin       = new Calyx_Admin;
            class_exists( 'Calyx_Front'       ) && $this->_front       = new Calyx_Front;
            class_exists( 'Calyx_Actions'     ) && $this->_actions     = new Calyx_Actions;
            class_exists( 'Calyx_Filters'     ) && $this->_filters     = new Calyx_Filters;
            class_exists( 'Calyx_WooCommerce' ) && $this->_woocommerce = new Calyx_WooCommerce;

        } );

    }

    function data()    { return $this->_data;        }
    function admin()   { return $this->_admin;       }
    function front()   { return $this->_front;       }
    function utils()   { return $this->_utilities;   }
    function actions() { return $this->_actions;     }
    function filters() { return $this->_filters;     }
    function wc()      { return $this->_woocommerce; }

    function add_feature( $name, $feature ) {
        if (
            doing_action( 'setup_theme_' . THEME_PREFIX )
            || did_action( 'setup_theme_' . THEME_PREFIX )
        ) {
            $this->_features[$name] = $feature;
            return $this->get_feature( $name );
        } else
            trigger_error( 'Features should be not be added before \'setup_theme_' . THEME_PREFIX . '\' action.' );
    }

    function get_feature( $name ) {
        return $this->_features[$name];
    }

    function has_feature( $name ) {
        return array_key_exists( $name, $this->_features );
    }

    function is_high_load() {
        return (
            (
                defined( THEME_PREFIX . '_HIGH_LOAD' )
                && constant( THEME_PREFIX . '_HIGH_LOAD' )
            )
            || !!get_transient( THEME_PREFIX . '_HIGH_LOAD' )
            || $this->is_extreme_load()
        );
    }

    function is_extreme_load() {
        return (
            (
                defined( THEME_PREFIX . '_EXTREME_LOAD' )
                && constant( THEME_PREFIX . '_EXTREME_LOAD' )
            )
            || !!get_transient( THEME_PREFIX . '_EXTREME_LOAD' )
        );
    }

    function is_woocommerce_active() {
        return is_plugin_active( 'woocommerce/woocommerce.php' );
    }

        function is_wc_active() {
            return $this->is_woocommerce_active();
        }

}

function Calyx() {
    return Calyx::instance();
}

?>
