<?php

class PDP_Core_Cart{
    public static function get(){
        if( isset( $_SESSION['cart'] ) ){
            return $_SESSION['cart'];
        }
        else{
            $_SESSION['cart'] = array(
                'master_option'     => 0,
                'hair_length'       => 0,
                'salon'             => false,
                'items'             => [],
                'total'             => 0
            );

            return $_SESSION['cart'];
        }
    }

    public static function update( WP_REST_Request $request ){
        $_SESSION['cart'] = json_decode( $request->get_body(), true )['cart'];
    }
}