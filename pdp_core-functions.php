<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Require Carbon Fields
 */
add_action( 'after_setup_theme', 'pdp_carbon_fields_load' );
function pdp_carbon_fields_load(){
	require_once( 'vendor/autoload.php' );
	\Carbon_Fields\Carbon_Fields::boot();
}


/**
 *  Admin Menu Pages
 */
add_action( 'admin_menu', function(){
	add_submenu_page(
		'crb_carbon_fields_container_pied-de-poule.php',
		'Синхронизация цен',
		'Синхронизация цен',
		'manage_options',
		'google-api-settings',
		'pdp_google_api_settings'
	);
}, 11 );

function pdp_google_api_settings(){
    require PDP_PLUGIN_PATH . 'templates/pricelists-sync.php';
}


/**
 *  Menus creation page fixes.
 */
add_action( 'load-nav-menus.php', 'pdp_init_menus_creation_page_fixes' );

function pdp_init_menus_creation_page_fixes(){
	add_action( 'pre_get_posts', 'pdp_disable_paging_for_hierarchical_post_types' );
	add_filter( 'get_terms_args', 'pdp_remove_limit_for_hierarchical_taxonomies', 10, 2 );
	add_filter( 'get_terms_fields', 'pdp_remove_page_links_for_hierarchical_taxonomies', 10, 3 );
}

function pdp_disable_paging_for_hierarchical_post_types( $query ){
	if( !is_admin() || 'nav-menus' !== get_current_screen()->id ){
		return;
	}

	if( !is_post_type_hierarchical( $query->get( 'post_type' ) ) ){
		return;
	}

	if( 50 == $query->get( 'posts_per_page' ) ){
		$query->set( 'nopaging', true );
	}
}

function pdp_remove_limit_for_hierarchical_taxonomies( $args, $taxonomies ){
	if( !is_admin() || 'nav-menus' !== get_current_screen()->id ){
		return $args;
	}

	if( !is_taxonomy_hierarchical( reset( $taxonomies ) ) ){
		return $args;
	}

	if( 50 == $args['number'] ){
		$args['number'] = '';
	}

	return $args;
}

function pdp_remove_page_links_for_hierarchical_taxonomies( $selects, $args, $taxonomies ){
	if( !is_admin() || 'nav-menus' !== get_current_screen()->id ){
		return $selects;
	}

	if( !is_taxonomy_hierarchical( reset( $taxonomies ) ) ){
		return $selects;
	}

	if( 'count' === $args['fields'] ){
		$selects = array( '1' );
	}

	return $selects;
}


function pdp_get_pricelists_id(){
	$salons = get_posts(
		array(
			'numberposts' => -1,
			'post_type' => 'salon'
		)
	);

	$data = [];

	foreach( $salons as $salon ) {
		$spreadsheet_id = carbon_get_post_meta($salon->ID, 'pricelist_sheet_id');
		if (!empty($spreadsheet_id)) {
			$data[] = array(
				'salon_id' => $salon->ID,
				'spreadsheet_id' => $spreadsheet_id
			);
		}
	}

	return $data;
}

function pdp_get_pricelist_id( $salon_id = false ){
	if( $salon_id ){
		return carbon_get_post_meta( $salon_id, 'pricelist_sheet_id' );
	}

	return false;
}

function pdp_get_salons( $order = 'ASC', $format = false ){
	$params = array(
		'numberposts'   => -1,
		'post_type'     => 'salon',
		'order'         => $order
	);

	if( !$format ){
		return get_posts( $params );
	}
	else if( $format == 'slider' ){
		$salons = array();

		foreach( get_posts( $params ) as $item ){
			$data = array();

			$data['title'] = $item->post_title;
			$city_terms = get_the_terms( $item->ID, 'city' );
			$data['city'] = array_pop( $city_terms )->name;
			$data['phone'] = carbon_get_post_meta( $item->ID, 'phone' );
			$data['image'] = get_the_post_thumbnail( $item->ID, 'salons-slider-thumb' );
			$data['link'] = get_permalink( $item->ID );

			$salons[] = $data;
		}

		return $salons;
	}
}

function pdp_get_promotions( $order = 'ASC' ){
	$params = array(
		'numberposts'   => -1,
		'post_type'     => 'promotion',
		'order'         => $order
	);

	return get_posts( $params );
}

function pdp_get_service_categories(){
	return carbon_get_theme_option( 'service_categories' );
}


/**
 * Meta fields used in theme.
 */

add_action('carbon_fields_register_fields', 'pdp_attach_meta_fields');

function pdp_attach_meta_fields(){
	Container::make('post_meta', __('Настройки страницы', 'pdp_core'))
	         ->where('post_template', '=', 'service-category.php')
	         ->add_tab(__('Шапка страницы', 'pdp_core'), array(
		         Field::make('rich_text', 'hero_content', __('Текст', 'pdp_core'))
		              ->set_width(75),
		         Field::make('image', 'hero_img', __('Изображение', 'pdp_core'))
		              ->set_width(25)
	         ))
	         ->add_tab( __( 'Секции', 'pdp_core' ), array(
		         Field::make( 'complex', 'sections', __( 'Список секций', 'pdp_core' ) )
		              ->set_collapsed( true )
		              ->add_fields( array(
			              Field::make( 'text', 'title', __('Заголовок', 'pdp_core' ) ),
			              Field::make( 'image', 'image', __('Изображение', 'pdp_core' ) ),
			              Field::make( 'rich_text', 'content', __('Контент', 'pdp_core' ) ),
			              Field::make( 'text', 'details', __( 'Страница подробностей', 'pdp_core' ) ),
			              Field::make( 'complex', 'pricelist', __( 'Список цен', 'pdp_core' ) )
			                   ->set_collapsed( true )
			                   ->add_fields( 'basic', array(
				                   Field::make( 'text', 'category', __( 'Категория', 'pdp_core') ),
				                   Field::make( 'complex', 'services', __( 'Список услуг', 'pdp_core' ) )
				                        ->set_collapsed( true )
				                        ->add_fields( array(
					                        Field::make( 'text', 'name', __( 'Название', 'pdp_core' ) ),
					                        Field::make( 'text', 'price_from', __( 'Цена от', 'pdp_core' ) ),
					                        Field::make( 'text', 'price_to', __( 'Цена до', 'pdp_core' ) )
				                        ) )
			                   ))
			                   ->add_fields( 'complex', array(
				                   Field::make( 'text', 'category', __( 'Категория', 'pdp_core' ) ),
				                   Field::make( 'complex', 'services', __( 'Список услуг', 'pdp_core' ) )
				                        ->set_collapsed( true )
				                        ->add_fields( array(
					                        Field::make( 'text', 'name', __( 'Название', 'pdp_core' ) ),
					                        Field::make( 'text', 'price_first_from', __( '1 длина - от', 'pdp_core' ) )
					                             ->set_width( 25 ),
					                        Field::make('text', 'price_first_to', __( '1 длина - до', 'pdp_core' ) )
					                             ->set_width( 25 ),
					                        Field::make('text', 'price_second_from', __( '2 длина - от', 'pdp_core' ) )
					                             ->set_width( 25 ),
					                        Field::make('text', 'price_second_to', __( '2 длина - до', 'pdp_core' ) )
					                             ->set_width( 25 ),
					                        Field::make('text', 'price_third_from', __( '3 длина - от', 'pdp_core' ) )
					                             ->set_width( 25 ),
					                        Field::make('text', 'price_third_to', __( '3 длина - до', 'pdp_core' ) )
					                             ->set_width( 25 ),
					                        Field::make('text', 'price_fourth_from', __( '4 длина - от', 'pdp_core' ) )
					                             ->set_width( 25 ),
					                        Field::make('text', 'price_fourth_to', __( '4 длина - до', 'pdp_core' ) )
					                             ->set_width( 25 ),
				                        ))
			                   )),
			              Field::make( 'text', 'form_title', __( 'Заголовок для формы', 'pdp_core' ) ),
			              Field::make( 'text', 'form_service', __( 'Название услуги для формы', 'pdp_core' ) ),
			              Field::make( 'rich_text', 'after_content', __( 'Контент после секции', 'pdp_core' ) )
		              ) )
	         ));
}

function pdp_cyr_to_lat( $str ){
	$cyr = [
		'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
		'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
		'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
		'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'
	];

	$lat = [
		'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p',
		'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sht', 'a', 'i', 'y', 'e', 'yu', 'ya',
		'A', 'B', 'V', 'G', 'D', 'E', 'Io', 'Zh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P',
		'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Sht', 'A', 'I', 'Y', 'e', 'Yu', 'Ya'
	];

	return str_replace( $cyr, $lat, $str );
}

function pdp_service_slug_to_key( $str ){
	return str_replace( [' ', '/'], '-', mb_strtolower( pdp_cyr_to_lat( $str ) ) );
}

/**
 *  Fetch Price List
 */

function pdp_fetch_pricelists( $salon = false ){
	$google_api = new PDP_Core_Google();
	$client = $google_api->get_client();
	$service = new Google_Service_Sheets( $client );

	$prielists = ( $salon ) ? [pdp_get_pricelist_id( $salon )] : pdp_get_pricelists_id();

	foreach( $prielists as $pricelist ){
		$ranges = [];
		$titles = [];
		$spreadsheet_id = ( $salon ) ? $pricelist : $pricelist['spreadsheet_id'];
		$salon_id = ( $salon ) ? $salon : $pricelist['salon_id'];

		$spreadsheet = $service->spreadsheets->get( $spreadsheet_id );

		foreach( $spreadsheet->getSheets() as $sheet ){
			$ranges[] = $sheet['properties']['title'] . '!A:J';
			$titles[] = rtrim( $sheet['properties']['title'] );
		}

		$response = $service->spreadsheets_values->batchGet( $spreadsheet_id, array( 'ranges' => $ranges ) )->getValueRanges();

		update_post_meta( $salon_id, '_pdp_pricelist', pdp_parse_pricelist( $titles, $response ) );
		update_post_meta( $salon_id, '_pdp_pricelist_last_update', current_time( "Y-m-d H:i:s" ) );
	}
}

/**
 *  Price List Parser
 */

function pdp_parse_pricelist( $categories, $data ){
	$parsed_data = [];

	foreach( $data as $key => $range ){
		$category_names = [];
		$services = [];
		$subcategory_services = [];
		$subcategories = [];
		$subcategory_title = [];

		$is_subcategory = false;
		$is_master_option = false;
		$is_variable_price = false;

		foreach( $range as $row ){
			if( isset( $row[0] ) && $row[0] != '' ){
				$row = array_values( array_filter( $row ) );

				if( strpos( $row[0], '[category]' ) !== false ){
					$category_names['ru'] = str_replace( '[category]', '', rtrim( array_shift( $row ) ) );
					$category_names['ua'] = rtrim( array_shift( $row ) );
				}
				else if( $row[0] == '[subcategory-begin]' ){
					$is_subcategory = true;
				}
				else if( $row[0] == '[subcategory-end]' ){
					$subcategories[] = array(
						'name'      => $subcategory_title,
						'services'  => $subcategory_services
					);

					$is_subcategory = false;

					$subcategory_services = [];
				}
				else{
					if( strpos( $row[0], '[subcategory-title]' ) !== false ){
						$subcategory_title['ru'] = str_replace( '[subcategory-title]', '', $row[0] );
						$subcategory_title['ua'] = $row[1];
					}
					else{
						$current_service = [];
						$is_pro = false;

						if( strpos( $row[0], '[pro]' ) !== false ){
							$current_service['name']['ru'] = str_replace( '[pro]', '', rtrim( array_shift( $row ) ) );
							$current_service['name']['ua'] = str_replace( '[pro]', '', rtrim( array_shift( $row ) ) );
							$is_pro = true;
						}
						else{
							$current_service['name']['ru'] = rtrim( array_shift( $row ) );
							$current_service['name']['ua'] = rtrim( array_shift( $row ) );
						}

						$current_service['id'] = md5( $categories[$key] . '_' . $current_service['name']['ru'] );

						switch( count( $row ) ){
							case 1:
								$current_service['master'] = false;
								if( strpos( $row[0], '[from]' ) !== false ){
									$current_service['prices'] = [[str_replace( '[from]', '', $row[0] )]];
									$current_service['variable'] = true;
									$is_variable_price = true;
								}
								else{
									$current_service['prices'] = [$row];
								}
								break;
							case 3:
							case 4:
								$current_service['master'] = false;
								$current_service['prices'] = array_chunk( $row, 1 );
								break;
							case 2:
							case 6:
							case 8:
								$current_service['master'] = true;
								$current_service['prices'] = array_chunk( $row, 2 );
								break;
						}

						if( $current_service['master'] ){
							$is_master_option = true;
						}

						if( !isset( $current_service['variable'] ) ){
							$current_service['variable'] = false;
						}

						$current_service['pro'] = $is_pro;


						if( $is_subcategory ){
							$subcategory_services[] = $current_service;
						}
						else{
							$services[] = $current_service;
						}
					}
				}
			}
		}

		$category = array(
			'name'                  => $categories[$key],
			'is_master_option'      => $is_master_option,
			'is_variable_price'     => $is_variable_price
		);

		if( $categories[$key] == 'стрижки/укладки/прически' ||
		    $categories[$key] == 'свадебные прически' ||
		    $categories[$key] == 'уходы для волос' ||
		    $categories[$key] == 'все виды окрашиваний' ||
		    $categories[$key] == 'уходы после окрашиваний' ){
			$category['is_hair_services'] = true;
		}
		else{
			$category['is_hair_services'] = false;
		}

		if( $subcategories ){
			$category['services'] = $subcategories;
		}
		else{
			$category['services'][] = array(
				'name'      => $category_names,
				'services'  => $services
			);
		}

		$parsed_data[pdp_service_slug_to_key( $categories[$key] )] = $category;
	}

	return $parsed_data;
}


/**
 *  Get related posts
 */

function pdp_get_related_posts( $id, $amount ){
	$terms = get_the_terms( $id, 'category' );

	if( empty( $terms ) ) $terms = array();

	$term_list = wp_list_pluck( $terms, 'slug' );

	$related_args = array(
		'post_type'         => 'post',
		'posts_per_page'    => $amount,
		'posts_status'      => 'publish',
		'post__not_in'      => array( $id ),
		'orderby'           => 'rand',
		'tax_query' => array(
			array(
				'taxonomy'  => 'category',
				'field'     => 'slug',
				'terms'     => $term_list
			)
		)
	);

	return new WP_Query( $related_args );
}

function pdp_get_template( $path = '', $data = [] ){
	if( $path && is_string( $path ) ) {
		require( PDP_PLUGIN_PATH . 'templates/' . $path );
	}
}

function pdp_get_hair_length_title( $id = false ){
	if( $id !== false ){
		$lengths = array(
			__( 'от 5-15 см', 'pdp' ),
			__( 'от 15 - 25 см (выше плеч, каре, боб)', 'pdp' ),
			__( 'от 25 - 40 см (ниже плеч/выше лопаток)', 'pdp' ),
			__( 'от 40 - 60 см (ниже лопаток)', 'pdp' )
		);

		return $lengths[$id];
	}
}

function pdp_get_salon_recipients( $id ){
	return explode( ',', get_post_meta( $id, '_notification_recipients', true ) );
}

function pdp_get_post_data(){
	$data = array();

	foreach( $_POST as $key => $value ){
		$data[$key] = $value;
	}

	return $data;
}

function pdp_utm_fields(){
	if( isset( $_GET['utm_source'] ) ){
		$utm_values = array(
			'utm_source'    => $_GET['utm_source'],
			'utm_medium'    => $_GET['utm_medium'],
			'utm_campaign'  => $_GET['utm_campaign'],
			'utm_content'   => $_GET['utm_content'],
			'utm_term'      => $_GET['utm_term']
		);

		foreach( $utm_values as $key => $value ){
			echo "<input type='hidden' name='{$key}' value='{$value}'>";
		}
	}
}

if( !function_exists( 'write_log' ) ){
	function write_log( $log ){
		if( true === WP_DEBUG ){
			if( is_array( $log ) || is_object( $log ) ){
				error_log( print_r( $log, true ) );
			}
			else{
				error_log( $log );
			}
		}
	}
}