<?php
add_action( 'rest_api_init', 'pdp_register_rest_routes' );

function pdp_register_rest_routes() {
    $controller = new PDP_Core_Rest_Controller();
    $controller->register_routes();
}

class PDP_Core_Rest_Controller extends WP_REST_Controller{
    function __construct(){
        $this->namespace = 'pdp/v1';
    }

    function register_routes(){
        /**
         * Salons
         */

        register_rest_route( $this->namespace, '/salons/get_all', array(
            'methods'               => 'GET',
            'callback'              => 'PDP_Core_Salon::get_all',
            'permission_callback'   => '__return_true'
        ) );


        /**
         * Cart
         */

        register_rest_route( $this->namespace, '/get_cart/', array(
            'methods'               => 'GET',
            'callback'              => 'PDP_Core_Cart::get',
            'permission_callback'   => '__return_true'
        ) );

        register_rest_route( $this->namespace, '/update_cart/', array(
            'methods'               => 'POST',
            'callback'              => 'PDP_Core_Cart::update',
            'permission_callback'   => '__return_true'
        ) );


        /**
         * Services
         */

        register_rest_route( $this->namespace, '/services/get_categories', array(
            'methods'               => 'GET',
            'callback'              => 'PDP_Core_Salon::get_categories',
            'permission_callback'   => '__return_true'
        ) );

        register_rest_route( $this->namespace, '/services/(?P<salon>[\d]+)', array(
            'methods'               => 'GET',
            'callback'              => 'PDP_Core_Salon::get_pricelist',
            'permission_callback'   => '__return_true'
        ) );
    }
}