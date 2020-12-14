<?php

class PDP_Core_Shortcodes {
    private $shortcodes;

    public function __construct(){
        $this->shortcodes = array(
            'logos_grid',
            'logos_grid_item'
        );

        $this->init();
    }

    private function init(){
        foreach( $this->shortcodes as $shortcode ){
            add_shortcode( $shortcode, array( $this, $shortcode . '_func' ) );
        }
    }

    public function logos_grid_func( $atts, $content ){
        return '<div class="logos-grid logos-grid_style-01">'. do_shortcode( $content ) . '</div>';
    }

    public function logos_grid_item_func( $atts ){
        $atts = shortcode_atts( array(
            'image'     => '',
            'link'      => ''
        ), $atts );

        if( $atts['image'] ){
            $image_tag = wp_get_attachment_image( $atts['image'], 'full' );

            $html = '<div class="logos-grid__item">';

            if( $atts['link'] ) {
                $html .= "<a href='{$atts['link']}' target='_blank'>{$image_tag}</a>";
            }
            else{
                $html .= "<a>{$image_tag}</a>";
            }

            $html .= '</div>';

            return $html;
        }
        else{
            return false;
        }
    }
}