<?php

// print_r($_SERVER);

// add_action( 'wp', 'dev_wp' );
function dev_wp() {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
    print_r(WC()->session);
    if ( !is_front_page() ) return;
    echo basename(__FILE__) . ':' . __LINE__;
    WC()->cart->add_discount( 'test' );
    WC()->session->set( 'mbsy', 'Caleb' );
    print_r(WC()->session);
}

?>
