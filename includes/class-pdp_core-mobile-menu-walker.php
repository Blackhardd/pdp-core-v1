<?php

class PDP_Core_Mobile_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl( &$output, $depth = 0, $args = NULL ){
        $output .= '<ul class="sub-menu">';

        if( $depth == 0 ){
	        $output .= '<li class="mobile-navigation__back"><button class="btn-icon"><svg width="22" height="16" fill="none"><path d="M.3 7.3a1 1 0 000 1.4l6.4 6.4A1 1 0 008 13.7L2.4 8l5.7-5.7A1 1 0 006.7 1L.3 7.3zM22 7H1v2h21V7z" fill="#000"/></svg><span>назад</span></button></li>';
        }
    }

    function start_el( &$output, $item, $depth = 0, $args = NULL, $id = 0 ) {
        global $wp_query;
        /*
         * Некоторые из параметров объекта $item
         * ID - ID самого элемента меню, а не объекта на который он ссылается
         * menu_item_parent - ID родительского элемента меню
         * classes - массив классов элемента меню
         * post_date - дата добавления
         * post_modified - дата последнего изменения
         * post_author - ID пользователя, добавившего этот элемент меню
         * title - заголовок элемента меню
         * url - ссылка
         * attr_title - HTML-атрибут title ссылки
         * xfn - атрибут rel
         * target - атрибут target
         * current - равен 1, если является текущим элементом
         * current_item_ancestor - равен 1, если текущим (открытым на сайте) является вложенный элемент данного
         * current_item_parent - равен 1, если текущим (открытым на сайте) является родительский элемент данного
         * menu_order - порядок в меню
         * object_id - ID объекта меню
         * type - тип объекта меню (таксономия, пост, произвольно)
         * object - какая это таксономия / какой тип поста (page /category / post_tag и т д)
         * type_label - название данного типа с локализацией (Рубрика, Страница)
         * post_parent - ID родительского поста / категории
         * post_title - заголовок, который был у поста, когда он был добавлен в меню
         * post_name - ярлык, который был у поста при его добавлении в меню
         */
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        /*
         * Генерируем строку с CSS-классами элемента меню
         */
        $class_names = $value = '';
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // функция join превращает массив в строку
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        /*
         * Генерируем ID элемента
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';


        /**
         * Генерируем атрибуты Vue
         */
        $vue = '';
	    if( $args->walker->has_children && $depth == 0 ){
			$vue .= " @click='showSubMenu({$item->ID})' :class='{ active: activeSubMenu == {$item->ID} }'";
	    }

        /**
         * Генерируем элемент меню
         */
        $output .= $indent . '<li' . $id . $value . $class_names . $vue . '>';

        $attributes  = !empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= !empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= !empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';

        if( $item->url != '#' ){
	        $attributes .= !empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
        }

        // ссылка и околоссылочный текст
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}