<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class PDP_Core_CPT {
    public function init_post_types(){
        $this->register_salon();
        $this->register_salon_taxonomies();
        $this->register_promotion();
        $this->register_master();
        $this->register_master_taxonomies();
        $this->register_vacancy();
    }

    public function init_post_types_meta(){
        $this->register_salon_meta();
        $this->register_promotion_meta();
        $this->register_master_meta();
        $this->register_vacancy_meta();
    }

    private function register_salon(){
        register_post_type( 'salon', array(
            'labels'                => array(
                'name'                  => __( 'Салоны', 'pdp_core' ),
                'singular_name'         => __( 'Салон', 'pdp_core' ),
                'add_new'               => __( 'Добавить новый', 'pdp_core' ),
                'add_new_item'          => __( 'Добавить новый салон', 'pdp_core' ),
                'edit_item'             => __( 'Редактировать салон', 'pdp_core' ),
                'new_item'              => __( 'Новый салон', 'pdp_core' ),
                'view_item'             => __( 'Посмотреть салон', 'pdp_core' ),
                'search_items'          => __( 'Найти салон', 'pdp_core' ),
                'not_found'             => __( 'Салонов не найдено', 'pdp_core' ),
                'not_found_in_trash'    => __( 'В корзине салонов не найдено', 'pdp_core' ),
                'menu_name'             => __( 'Салоны', 'pdp_core' )
            ),
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => true,
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => false,
            'menu_position'         => 4,
            'menu_icon'             => 'none',
            'supports'              => array( 'title', 'editor', 'thumbnail' )
        ) );
    }

    private function register_salon_taxonomies(){
        register_taxonomy( 'city', array( 'salon' ), array(
            'hierarchical'  => true,
            'labels'        => array(
                'name'              => __( 'Города', 'pdp_core' ),
                'singular_name'     => __( 'Город', 'pdp_core' ),
                'search_items'      => __( 'Найти город', 'pdp_core' ),
                'all_items'         => __( 'Все города', 'pdp_core' ),
                'parent_item'       => __( 'Родительский город', 'pdp_core' ),
                'parent_item_colon' => __( 'Родительский город:', 'pdp_core' ),
                'edit_item'         => __( 'Редактировать город', 'pdp_core' ),
                'update_item'       => __( 'Обновить город', 'pdp_core' ),
                'add_new_item'      => __( 'Добавить новый город', 'pdp_core' ),
                'new_item_name'     => __( 'Название нового города', 'pdp_core' ),
                'menu_name'         => __( 'Город', 'pdp_core' ),
            ),
            'show_ui'       => true,
            'query_var'     => true
        ) );
    }

    private function register_salon_meta(){
        Container::make( 'post_meta', __( 'Настройки салона', 'pdp_core' ) )
            ->where( 'post_type', '=', 'salon' )
            ->add_tab( __( 'Контакты и цены', 'pdp_core' ), array(
                Field::make( 'text', 'email', __( 'Электронная почта', 'pdp_core' ) )
                    ->set_attribute( 'type', 'email' )
                    ->set_width( 50 ),
                Field::make( 'text', 'phone', __( 'Номер телефона', 'pdp_core' ) )
                    ->set_attribute( 'type', 'tel' )
                    ->set_width( 50 ),
	            Field::make( 'text', 'google_maps', __( 'Ссылка на Google Maps', 'pdp_core' ) )
	                 ->set_width( 50 ),
	            Field::make( 'text', 'contacts_title', __( 'Заголовок для контактов', 'pdp_core' ) )
	                 ->set_width( 50 ),
                Field::make( 'text', 'pricelist_sheet_id', __( 'ID таблицы прайслиста', 'pdp_core' ) )
                    ->set_width( 100 ),
	            Field::make( 'textarea', 'notification_recipients', __( 'Email получателей уведомлений (через запятую)', 'pdp_core' ) )
            ) )
            ->add_tab( __( 'Информация', 'pdp_core' ), array(
                Field::make( 'complex', 'advantages', __( 'Список преимуществ', 'pdp_core' ) )
                    ->add_fields( array(
                        Field::make( 'text', 'advantage', __( 'Преимущество', 'pdp_core' ) )
                    ) )
                    ->set_collapsed( true ),
                Field::make( 'media_gallery', 'gallery', __( 'Галерея', 'pdp_core' ) )
                    ->set_type( 'image' )
            ) )
            ->add_tab( __( 'Команда', 'pdp_core' ), array(
            	Field::make( 'association', 'masters_term' )
                    ->set_types( array(
                    	array(
                    		'type'      => 'term',
		                    'taxonomy'  => 'salon'
	                    )
                    ) )
                    ->set_max( 1 ),
                Field::make( 'complex', 'team', __( 'Список преимуществ', 'pdp_core' ) )
                    ->add_fields( array(
                        Field::make( 'text', 'name', __( 'Имя', 'pdp_core' ) )
                            ->set_width( 30 ),
                        Field::make( 'text', 'title', __( 'Специальность', 'pdp_core' ) )
                            ->set_width( 40 ),
                        Field::make( 'text', 'expiriance', __( 'Опыт', 'pdp_core' ) )
                            ->set_width( 20 ),
                        Field::make( 'rich_text', 'description', __( 'Описание', 'pdp_core' ) )
                            ->set_width( 85 ),
                        Field::make( 'image', 'photo', __( 'Фото', 'pdp_core' ) )
                            ->set_width( 15 )
                    ) )
                    ->set_collapsed( true )
            ) );
    }


    /**
     * Promotions
     */
    private function register_promotion(){
        register_post_type( 'promotion', array(
            'labels'                => array(
                'name'                  => __( 'Акции', 'pdp_core' ),
                'singular_name'         => __( 'Акция', 'pdp_core' ),
                'add_new'               => __( 'Добавить новую', 'pdp_core' ),
                'add_new_item'          => __( 'Добавить новую акцию', 'pdp_core' ),
                'edit_item'             => __( 'Редактировать акцию', 'pdp_core' ),
                'new_item'              => __( 'Новая акция', 'pdp_core' ),
                'view_item'             => __( 'Посмотреть акцию', 'pdp_core' ),
                'search_items'          => __( 'Найти акцию', 'pdp_core' ),
                'not_found'             => __( 'Акций не найдено', 'pdp_core' ),
                'not_found_in_trash'    => __( 'В корзине акций не найдено', 'pdp_core' ),
                'menu_name'             => __( 'Акции', 'pdp_core' )
            ),
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => true,
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => false,
            'menu_position'         => 5,
            'menu_icon'             => 'none',
            'supports'              => array( 'title', 'editor', 'thumbnail' )
        ) );
    }

	private function register_promotion_meta(){
		Container::make( 'post_meta', __( 'Настройки акции', 'pdp_core' ) )
			->where( 'post_type', '=', 'promotion' )
			->add_fields( array(
				Field::make( 'radio', 'type', __( 'Тип акции', 'pdp_core' ) )
					->set_options( array(
						'permanent'     => __( 'Постоянная', 'pdp_core' ),
						'temporary' => __( 'Временная', 'pdp_core' )
					) ),
				Field::make( 'date', 'start_date', __( 'Дата начала', 'pdp_core' ) )
					->set_width( 50 )
					->set_conditional_logic( array(
						'relation' => 'AND',
						array(
							'field'     => 'type',
							'value'     => 'temporary',
							'compare'   => '='
						)
					) ),
				Field::make( 'date', 'end_date', __( 'Дата конца', 'pdp_core' ) )
					->set_width( 50 )
					->set_conditional_logic( array(
						'relation' => 'AND',
						array(
							'field'     => 'type',
							'value'     => 'temporary',
							'compare'   => '='
						)
					) )
			) );
	}


    /**
     * Masters
     */
    private function register_master(){
        register_post_type( 'master', array(
            'labels'                => array(
                'name'                  => __( 'Мастера', 'pdp_core' ),
                'singular_name'         => __( 'Мастер', 'pdp_core' ),
                'add_new'               => __( 'Добавить нового', 'pdp_core' ),
                'add_new_item'          => __( 'Добавить нового мастера', 'pdp_core' ),
                'edit_item'             => __( 'Редактировать мастера', 'pdp_core' ),
                'new_item'              => __( 'Новый мастер', 'pdp_core' ),
                'view_item'             => __( 'Посмотреть мастера', 'pdp_core' ),
                'search_items'          => __( 'Найти мастера', 'pdp_core' ),
                'not_found'             => __( 'Мастера не найдено', 'pdp_core' ),
                'not_found_in_trash'    => __( 'В корзине мастера не найдено', 'pdp_core' ),
                'menu_name'             => __( 'Мастера', 'pdp_core' )
            ),
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => true,
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => false,
            'menu_position'         => 6,
            'menu_icon'             => 'none',
            'supports'              => array( 'title', 'editor', 'thumbnail' )
        ) );
    }

    private function register_master_taxonomies(){
	    register_taxonomy( 'salon', array( 'master' ), array(
		    'hierarchical'  => true,
		    'labels'        => array(
			    'name'              => __( 'Салоны', 'pdp_core' ),
			    'singular_name'     => __( 'Салон', 'pdp_core' ),
			    'search_items'      => __( 'Найти салон', 'pdp_core' ),
			    'all_items'         => __( 'Все салоны', 'pdp_core' ),
			    'parent_item'       => __( 'Родительский салон', 'pdp_core' ),
			    'parent_item_colon' => __( 'Родительский салон:', 'pdp_core' ),
			    'edit_item'         => __( 'Редактировать салон', 'pdp_core' ),
			    'update_item'       => __( 'Обновить салон', 'pdp_core' ),
			    'add_new_item'      => __( 'Добавить новый салон', 'pdp_core' ),
			    'new_item_name'     => __( 'Название нового салона', 'pdp_core' ),
			    'menu_name'         => __( 'Салон', 'pdp_core' ),
		    ),
		    'show_ui'           => true,
		    'query_var'         => true,
		    'show_admin_column' => true,
	    ) );
    }

    private function register_master_meta(){
        Container::make( 'post_meta', __( 'Настройки мастера', 'pdp_core' ) )
            ->where( 'post_type', '=', 'master' )
            ->add_fields( array(
                Field::make( 'text', 'specialty', __( 'Специальность', 'pdp_core' ) ),
                Field::make( 'text', 'experience', __( 'Опыт работы', 'pdp_core' ) )
            ) );
    }


    /**
     * Vacancies
     */
    private function register_vacancy(){
        register_post_type( 'vacancy', array(
            'labels'                => array(
                'name'                  => __( 'Вакансии', 'pdp_core' ),
                'singular_name'         => __( 'Вакансия', 'pdp_core' ),
                'add_new'               => __( 'Добавить новую', 'pdp_core' ),
                'add_new_item'          => __( 'Добавить новую вакансию', 'pdp_core' ),
                'edit_item'             => __( 'Редактировать вакансию', 'pdp_core' ),
                'new_item'              => __( 'Новая вакансия', 'pdp_core' ),
                'view_item'             => __( 'Посмотреть вакансию', 'pdp_core' ),
                'search_items'          => __( 'Найти вакансию', 'pdp_core' ),
                'not_found'             => __( 'Вакансий не найдено', 'pdp_core' ),
                'not_found_in_trash'    => __( 'В корзине вакансий не найдено', 'pdp_core' ),
                'menu_name'             => __( 'Вакансии', 'pdp_core' )
            ),
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => true,
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => false,
            'menu_position'         => 6,
            'menu_icon'             => 'none',
            'supports'              => array( 'title', 'editor' )
        ) );
    }

    private function register_vacancy_meta(){
	    Container::make( 'post_meta', __( 'Настройки вакансии', 'pdp_core' ) )
	        ->where( 'post_type', '=', 'vacancy' )
	        ->add_fields( array(
	        	Field::make( 'radio', 'actual', __( 'Актуальность', 'pdp_core' ) )
		             ->set_options( array(
		             	'true'      => __( 'Актуально', 'pdp_core' ),
		             	'false'     => __( 'Не актуально', 'pdp_core' )
		             ) )
	        ) );
    }
}