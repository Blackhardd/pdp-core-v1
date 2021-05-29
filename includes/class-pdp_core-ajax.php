<?php

class PDP_Core_Ajax{
    private $template_loader;
    private $mailer;

    function __construct(){
        $this->template_loader = new PDP_Core_Template_Loader();
        $this->mailer = new PDP_Core_Mailer();

        $this->register_public_actions();
        $this->register_admin_actions();
    }

    private function response( $status, $message = '', $redirect = false, $location = '' ){
        wp_die( json_encode( array(
        	'status'	=> $status,
	        'message'	=> $message,
			'redirect'  => $redirect,
	        'location'  => $location
        ) ) );
    }

    private function check_nonce( $action_name = false ){
		if( $action_name ){
			if( empty( $_POST ) || !wp_verify_nonce( $_POST['pdp_nonce'], 'pdp_' . $action_name . '_nonce' ) ){
				return false;
			}

			return true;
		}

		return;
    }

    private function register_public_actions(){
        $actions = array(
	        'booking',
            'simple_booking',
            'category_booking',
			'service_booking',
            'gift_card_order',
            'school_application',
            'vacancy_application',
            'add_post_like'
        );

        foreach( $actions as $action ){
            add_action( 'wp_ajax_' . $action, array( $this, $action ) );
            add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action ) );
        }
    }

    private function register_admin_actions(){
    	$actions = array(
    		'sync_pricelist',
    		'sync_pricelists'
	    );

    	foreach( $actions as $action ){
		    add_action( 'wp_ajax_' . $action, array( $this, $action ) );
	    }
    }

	public function booking(){
		$data = array(
			'name'              => $_POST['name'],
			'email'             => $_POST['email'],
			'phone'             => $_POST['phone'],
			'cart'              => json_decode( wp_unslash( $_POST['cart'] ), true ),
			'total'             => $_POST['total'],
			'is_hair_services'  => $_POST['is_hair_services']
		);

		if( get_option( '_thank_you_page_id' ) || get_option( '_thank_you_page_id_ua' ) ){
			$redirect_url = ( pll_current_language() == 'ru' ) ? get_permalink( get_option( '_thank_you_page_id' ) ) : get_permalink( get_option( '_thank_you_page_id_ua' ) );

			$this->response(
				$this->mailer->booking_notification( $data ),
				sprintf( '%s<br>%s', __( 'Спасибо за запись!', 'pdp_core' ), __( 'В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) ),
				true,
				$redirect_url
			);
		}
		else{
			$this->response(
				$this->mailer->booking_notification( $data ),
				sprintf( '%s<br>%s', __( 'Спасибо за запись!', 'pdp_core' ), __( 'В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) )
			);
		}
	}

    public function simple_booking(){
    	if( $this->check_nonce( 'simple_booking' ) ){
		    $data = pdp_get_post_data();

		    $this->response( $this->mailer->simple_booking_notification( $data ), sprintf( '%s<br>%s', __( 'Спасибо за запись!', 'pdp_core' ), __( 'В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) ) );
	    }
    }

	public function category_booking(){
		if( $this->check_nonce( 'category_booking' ) ) {
			$data = pdp_get_post_data();

			$this->response( $this->mailer->category_booking_notification( $data ), sprintf( '%s<br>%s', __( 'Спасибо за запись!', 'pdp_core' ), __( 'В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) ) );
		}
	}

	public function service_booking(){
		if( $this->check_nonce( 'service_booking' ) ) {
			$data = pdp_get_post_data();

			$this->response( $this->mailer->service_booking_notification( $data ), sprintf( '%s<br>%s', __( 'Спасибо за запись!', 'pdp_core' ), __( 'В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) ) );
		}
	}

	public function gift_card_order(){
		if( $this->check_nonce( 'gift_card_order' ) ){
	        $data = pdp_get_post_data();

			$this->response( $this->mailer->gift_card_notification( $data ), sprintf( '%s<br>%s', __( 'Спасибо за заказ!', 'pdp_core' ), __( 'В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) ) );
		}
	}

	public function school_application(){
		if( $this->check_nonce( 'school_application' ) ){
			$data = pdp_get_post_data();

			$this->response( $this->mailer->school_application_notification( $data ), sprintf( '%s<br>%s', __( 'Спасибо за заявку!', 'pdp_core' ), __( 'В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) ) );
		}
	}

	public function vacancy_application(){
		if( $this->check_nonce( 'vacancy_application' ) ){
			$data = pdp_get_post_data();

			if( $_FILES['attachment']['size'] != 0 ){
				if( !function_exists( 'wp_handle_upload' ) ){
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				}

				$attachment = wp_handle_upload( $_FILES['attachment'], array( 'test_form' => false ) );

				if( $attachment && empty( $attachment['error'] ) ){
					$this->response( $this->mailer->vacancy_application_notification( $data, $attachment['file'] ), __( 'Ваша заявка была отправлена', 'pdp_core' ) );
				}

				$this->response( false, __( 'Файл не был загружен.', 'pdp_core' ) );
			}

			$this->response( $this->mailer->vacancy_application_notification( $data, array() ), __( 'Ваша заявка была отправлена', 'pdp_core' ) );
		}
	}

	public function add_post_like(){
    	$current_likes = get_post_meta( $_POST['post_id'], '_likes', true );
		$updated_likes = $current_likes + 1;

		if( $current_likes ){
			update_post_meta( $_POST['post_id'], '_likes', $updated_likes );
		}
		else{
			update_post_meta( $_POST['post_id'], '_likes', '1' );
		}

		$this->response( true, $updated_likes );
	}

	/**
	 *  ADMIN
	 */

	public function sync_pricelist(){
		pdp_fetch_pricelists( $_POST['id'] );

		$this->response( true, 'Sync single.' );
	}

	public function sync_pricelists(){
		pdp_fetch_pricelists();

		$this->response( true, 'Sync all.' );
	}
}