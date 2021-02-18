<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'pdp_attach_theme_options' );
function pdp_attach_theme_options(){
	$theme_options = Container::make( 'theme_options', __( 'Настройки', 'pdp_core' ) )
	    ->set_icon('none')
        ->set_page_parent('pdp-options')
        ->add_tab( __( 'Общие', 'pdp_core' ), array(
            Field::make( 'association', 'phones_list_hero_city', __( 'Основной город', 'pdp_core' ) )
                ->set_types( array(
                    array(
                        'type'      => 'term',
                        'taxonomy'  => 'city'
                    )
                ) )
                ->set_max( 1 ),
            Field::make( 'textarea', 'analytics_code', __( 'Коды аналитик', 'pdp_core' ) )
        ) )
        ->add_tab(__('Google API', 'pdp_core'), array(
            Field::make( 'text', 'google_client_id', __( 'ID клента', 'pdp_core' ) ),
            Field::make( 'text', 'google_secret', __( 'Секретный код клента', 'pdp_core' ) )
        ) )
        ->add_tab( __( 'Категории услуг', 'pdp_core' ), array(
            Field::make( 'complex', 'service_categories', __( 'Список категорий', 'pdp_core' ) )
                ->set_collapsed( true )
                ->add_fields( array(
                    Field::make( 'text', 'title', __( 'Имя категории', 'pdp_core' ) )
                         ->set_width( 40 ),
                    Field::make( 'text', 'slug', __( 'Ярлык', 'pdp_core' ) )
                         ->set_width( 40 ),
                    Field::make( 'image', 'cover', __( 'Обложка', 'pdp_core' ) )
                         ->set_width( 20 )
                ) )
        ) )
        ->add_tab( __( 'Социальные сети', 'pdp_core' ), array(
            Field::make('text', 'email', __('Email', 'pdp_core')),
            Field::make('text', 'telegram', __('Telegram', 'pdp_core')),
            Field::make('text', 'instagram', __('Instagram', 'pdp_core')),
            Field::make('text', 'facebook', __('Facebook', 'pdp_core')),
            Field::make('text', 'youtube', __('YouTube', 'pdp_core')),
        ) )
        ->add_tab( __( 'Контактные данные', 'pdp_core' ), array(
            Field::make( 'text', 'phone_qa', __( 'Номер отдела контроля качества', 'pdp_core' ) )
                 ->set_attribute( 'type', 'tel' ),
		    Field::make( 'text', 'phone_marketing', __( 'Номер отдела маркетинга', 'pdp_core' ) )
                 ->set_attribute( 'type', 'tel' ),
        ) );
}

/**
 * Require Carbon Fields
 */
add_action('after_setup_theme', 'pdp_carbon_fields_load');
function pdp_carbon_fields_load()
{
    require_once('vendor/autoload.php');
    \Carbon_Fields\Carbon_Fields::boot();
}

add_action('admin_menu', function () {
    add_menu_page(
        'Настройки сайта',
        'Pied-De-Poule',
        'manage_options',
        'pdp-options',
        'pdp_options_page',
        'none',
        4
    );

    add_submenu_page(
        'pdp-options',
        'Синхронизация цен',
        'Синхронизация цен',
        'manage_options',
        'google-api-settings',
        'pdp_google_api_settings'
    );
});

// функция отвечает за вывод страницы настроек
// подробнее смотрите API Настроек: http://wp-kama.ru/id_3773/api-optsiy-nastroek.html
function pdp_google_api_settings(){ ?>
    <div class="wrap">
        <div class="pdp-admin-page">
            <header class="pdp-admin-page__header">
                <h2 class="pdp-admin-heading"><?=get_admin_page_title(); ?></h2>
            </header>

            <main class="pdp-admin-page__body">
                <?php
                $google_api = new PDP_Core_Google();
                $client = $google_api->get_client();

                if( !$client->isAccessTokenExpired() ){
                    $salons = pdp_get_salons(); ?>
                    <div class="salons-list">
                        <div class="salons-list__header">
                            <h3><?=__( 'Салоны', 'pdp_core' ); ?></h3>
                        </div>
                        <div class="salons-list__body">
                            <?php foreach( $salons as $salon ){ ?>
                                <div class="salons-list__row <?php echo ( !$salon->_pricelist_sheet_id ) ? 'disabled' : ''; ?>">
                                    <div class="salons-list__col salons-list__col_title">
                                        <?=$salon->post_title; ?>
                                    </div>

                                    <div class="salons-list__col salons-list__col_id">
                                        <?php if( $salon->_pricelist_sheet_id ){ ?>
                                            <?=$salon->_pricelist_sheet_id; ?>
                                        <?php } else { ?>
                                            <?=__( 'Прайслист не подвязан', 'pdp_core' ); ?>
                                            ( <a href="<?=get_edit_post_link( $salon->ID ); ?>"><?=__( 'подвязать', 'pdp_core' ); ?></a> )
                                        <?php } ?>
                                    </div>

                                    <div class="salons-list__col salons-list__col_date">
                                        <?php if( $salon->_pdp_pricelist_last_update ){ ?>
                                            <stron><?=__( 'Последнее обновление:', 'pdp_core' ); ?></stron> <?=$salon->_pdp_pricelist_last_update; ?>
                                        <?php } else { ?>
                                            <?=__( 'Не обновлялся', 'pdp_core' ); ?>
                                        <?php } ?>
                                    </div>

                                    <div class="salons-list__col salons-list__col_btns">
                                        <button class="pdp-btn" data-update-pricelist="<?=$salon->ID; ?>" <?php echo ( !$salon->_pricelist_sheet_id ) ? 'disabled' : ''; ?>><?=__( 'Синхронизировать', 'pdp_core' ); ?></button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="salons-list__footer">
                            <button class="pdp-btn" data-update-pricelists><?=__( 'Синхронизировать все цены', 'pdp_core' ); ?></button>
                        </div>
                    </div>
                </div>
            </div>
                <?php } ?>
            </main>
        </div>
    <script>
        jQuery(function($){
            $(document).ready(function(){
                $('[data-update-pricelists]').click(function(){
                    let $self = $(this);
                    let data = {
                        action: 'update_pricelists'
                    };

                    $.post(ajaxurl, data, function(response){
                        alert('Ответ сервера: ' + response);
                    });
                });

                $('[data-update-pricelist]').click(function(){
                    let $self = $(this);
                    let data = {
                        action:     'update_pricelist',
                        id:         $self.data('update-pricelist')
                    };

                    $.post(ajaxurl, data, function(response){
                        alert('Ответ сервера: ' + response);
                    });
                });
            });
        });
    </script>
    <?php
}

function pdp_parse_pricelist( $categories, $data ){
    $parsed_data = [];

	foreach( $data as $key => $range ){
		$category = [];
		$services = [];
		$subcategory_services = [];
		$subcategories = [];
		$subcategory_title = '';

		$is_subcategory = false;
		$is_master_option = false;
		$is_variable_price = false;

		foreach( $range as $row ){
			if( $row[0] != '' ){
				$row = array_values( array_filter( $row ) );

				if( $row[0] == '[subcategory-begin]' ){
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
					    $subcategory_title = str_replace( '[subcategory-title]', '', $row[0] );
                    }
				    else{
					    $current_service = [];
				        $is_pro = false;

				        if( strpos( $row[0], '[pro]' ) !== false ){
					        $current_service['name'] = str_replace( '[pro]', '', rtrim( array_shift( $row ) ) );
					        $is_pro = true;
                        }
				        else{
					        $current_service['name'] = rtrim( array_shift( $row ) );
                        }

					    $current_service['id'] = md5( $categories[$key] . '_' . $current_service['name'] );

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
			    'name'      => $categories[$key],
			    'services'  => $services
            );
        }

		$parsed_data[pdp_service_slug_to_key( $categories[$key] )] = $category;
	}

	return $parsed_data;
}

function pdp_get_pricelists_id(){
    $salons = get_posts(
        array(
            'numberposts' => -1,
            'post_type' => 'salon'
        )
    );

    $data = [];

    foreach ($salons as $salon) {
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
    else{
        return false;
    }
}

function pdp_get_salons( $sort = 'ASC' ){
	return get_posts(
        array(
            'numberposts'   => -1,
            'post_type'     => 'salon',
            'order'         => $sort
        )
    );
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

add_action('wp_ajax_update_pricelist', 'pdp_ajax_update_pricelist');
function pdp_ajax_update_pricelist(){
    $google_api = new PDP_Core_Google();
    $client = $google_api->get_client();
    $sheets_service = new Google_Service_Sheets($client);

    $spreadsheet_id = pdp_get_pricelist_id($_POST['id']);

    if ($spreadsheet_id) {
        $ranges = [];
        $titles = [];

        $spreadsheet = $sheets_service->spreadsheets->get($spreadsheet_id);

        foreach ($spreadsheet->getSheets() as $sheet) {
            $ranges[] = $sheet['properties']['title'] . '!A:I';
            $titles[] = rtrim( $sheet['properties']['title'] );
        }

        $response = $sheets_service->spreadsheets_values->batchGet($spreadsheet_id, array('ranges' => $ranges))->getValueRanges();

        $is_updated = update_post_meta($_POST['id'], '_pdp_pricelist', pdp_parse_pricelist( $titles, $response ));

        wp_die(json_encode($is_updated));

        if ($is_updated) {
            update_post_meta($_POST['id'], '_pdp_pricelist_last_update', date("Y-m-d H:i:s"));
            wp_die($is_updated);
        } else {
            wp_die('Что-то пошло не так или прайслист не изменился.');
        }
    }
}

add_action('wp_ajax_update_pricelists', 'pdp_ajax_update_pricelists');
function pdp_ajax_update_pricelists(){
    $google_api = new PDP_Core_Google();
    $client = $google_api->get_client();
    $service = new Google_Service_Sheets($client);

    $pricelists = pdp_get_pricelists_id();

    foreach ($pricelists as $pricelist) {
        $ranges = [];
        $titles = [];
        $spreadsheet_id = $pricelist['spreadsheet_id'];

        $spreadsheet = $service->spreadsheets->get($spreadsheet_id);

        foreach ($spreadsheet->getSheets() as $sheet) {
            $ranges[] = $sheet['properties']['title'] . '!A:I';
            $titles[] = rtrim( $sheet['properties']['title'] );
        }

        $response = $service->spreadsheets_values->batchGet($spreadsheet_id, array('ranges' => $ranges))->getValueRanges();

        update_post_meta($pricelist['salon_id'], '_pdp_pricelist', pdp_parse_pricelist( $titles, $response ));
        update_post_meta($pricelist['salon_id'], '_pdp_pricelist_last_update', date("Y-m-d H:i:s"));
    }

    wp_die();
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