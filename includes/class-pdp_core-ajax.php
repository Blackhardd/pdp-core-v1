<?php

class PDP_Core_Ajax{
    private $template_loader;
    private $mailer;

    function __construct(){
        $this->template_loader = new PDP_Core_Template_Loader();
        $this->mailer = new PDP_Core_Mailer();

        $this->register_public_ajax_actions();
    }

    private function message( $status, $message ){
        wp_die(
            json_encode(
                array(
                    'status'	=> $status,
                    'message'	=> $message
                )
            )
        );
    }

    private function redirect( $location ){
        wp_die(
            json_encode(
                array(
                    'status'	=> true,
                    'redirect'  => $location
                )
            )
        );
    }

    private function check_nonce( $action_name = false ){
		if( $action_name ){
			if( empty( $_POST ) || !wp_verify_nonce( $_POST['pdp_nonce'], 'pdp_' . $action_name . '_nonce' ) ){
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
    }

    private function register_public_ajax_actions(){
        $actions = array(
	        'appointment',
            'appointment_quick',
            'appointment_service_category',
			'appointment_service',
			'appointment_master',
            'appointment_promotion',
            'gift_card',
            'cv_apply',
            'add_post_like'
        );

        foreach( $actions as $action ){
            add_action( 'wp_ajax_'.$action, array( $this, $action ) );
            add_action( 'wp_ajax_nopriv_'.$action, array( $this, $action ) );
        }
    }

	public function appointment(){
		$data = array(
			'name'              => $_POST['name'],
			'email'             => $_POST['email'],
			'phone'             => $_POST['phone'],
			'cart'              => json_decode( str_replace( '\\', '', $_POST['cart'] ) ),
			'total'             => $_POST['total'],
			'is_hair_services'  => $_POST['is_hair_services']
		);

		$this->message( $this->mailer->appointment_admins_notification( $data ), __( 'Спасибо за запись!<br>В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) );
	}

    public function appointment_quick(){
    	if( $this->check_nonce( 'appointment_quick' ) ){
		    $data = array(
			    'name'      => $_POST['name'],
			    'phone'     => $_POST['phone'],
			    'salon'     => $_POST['salon'],
			    'service'   => $_POST['service']
		    );

		    $this->message( $this->mailer->quick_appointment_admins_notification( $data ), __( 'Спасибо за запись!<br>В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) );
	    }
    }

	public function appointment_service_category(){
		if( $this->check_nonce( 'appointment_service_category' ) ) {
			$data = array(
				'name'      => $_POST['name'],
				'phone'     => $_POST['phone'],
				'service'   => $_POST['service']
			);

			$this->message( $this->mailer->service_category_appointment_admins_notification( $data ), __( 'Спасибо за запись!<br>В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) );
		}
	}

	public function appointment_service(){
		if( $this->check_nonce( 'appointment_service' ) ) {
			$data = array(
				'name'      => $_POST['name'],
				'phone'     => $_POST['phone'],
				'salon'     => $_POST['salon'],
				'service'   => $_POST['service']
			);

			$this->message( $this->mailer->service_appointment_admins_notification( $data ), __( 'Спасибо за запись!<br>В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) );
		}
	}

	public function appointment_master(){
		if( $this->check_nonce( 'appointment_master' ) ) {
			$data = array(
				'name'      => $_POST['name'],
				'phone'     => $_POST['phone'],
				'salon'     => $_POST['salon'],
				'service'   => $_POST['master']
			);

			$this->message( $this->mailer->master_appointment_admins_notification( $data ), __( 'Спасибо за запись!<br>В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) );
		}
	}

	public function appointment_promotion(){
		if( $this->check_nonce( 'appointment_promotion' ) ) {
			$data = array(
				'name'          => $_POST['name'],
				'phone'         => $_POST['phone'],
				'email'         => $_POST['email'],
				'promotion'     => $_POST['promotion']
			);

			$this->message( $this->mailer->promotion_appointment_admins_notification( $data ), __( 'Спасибо за запись!<br>В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) );
		}
	}

	public function gift_card(){
		if( $this->check_nonce( 'gift_card' ) ){
	        $data = array(
	            'name'      => $_POST['name'],
	            'phone'     => $_POST['phone'],
	            'email'     => $_POST['email'],
	            'card'      => $_POST['card']
		    );

			$this->message( $this->mailer->gift_card_order_admins_notification( $data ), __( 'Спасибо за заказ!<br>В ближайшее время с вами свяжется наш менеджер.', 'pdp_core' ) );
		}
	}

	public function cv_apply(){
		if( $this->check_nonce( 'cv_apply' ) ){
			$data = array(
				'name'      => $_POST['name'],
				'phone'     => $_POST['phone'],
				'email'     => $_POST['email'],
				'message'   => $_POST['message']
			);

			if( isset( $_FILES['attachment'] ) ){
				if( !function_exists( 'wp_handle_upload' ) ){
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				}

				$file = $_FILES['attachment'];

				$overrides = ['test_form' => false];

				$attachment = wp_handle_upload( $file, $overrides );

				if( $attachment && empty( $attachment['error'] ) ){
					$this->message( $this->mailer->vacancy_apply_admins_notification( $data, $attachment['file'] ), $attachment['file'] );
				}
				else{
					$this->message( false, __( 'Файл не был загружен.', 'pdp_core' ) );
				}
			}
			else{
				$this->message( $this->mailer->vacancy_apply_admins_notification( $data, array() ), __( 'Файл был успешно загружен.', 'pdp_core' ) );
			}
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

		$this->message( true, $updated_likes );
	}
}