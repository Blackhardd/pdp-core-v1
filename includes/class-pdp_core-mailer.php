<?php

class PDP_Core_Mailer{
	private $site_logo;
	private $admin_emails;
	private $hair_lengths;

	public function __construct(){
		$this->init();
	}

	private function init(){
		$this->admin_emails = array(
			get_option( 'admin_email' )
		);

		add_filter( 'wp_mail_content_type', function( $content_type ){
			return 'text/html';
		} );
	}

	private function send_to_admins( $subject, $message, $attachments = array() ){
		return wp_mail( $this->admin_emails, $subject, $message, '', $attachments );
	}

	private function get_template_base( $title, $content ){
		ob_start();
		pdp_get_template( 'emails/base.php', ['title' => $title,'content' => $content] );
		return ob_get_clean();
	}

	private function get_template_booking( $data, $is_simple = false ){
		$salon_name = ( !$is_simple ) ? PDP_Core_Salon::get_by_id( $data['cart']->salon )->post_title : $data['salon'];
		ob_start();
		pdp_get_template( 'emails/booking/body.php', ['data' => $data, 'salon_name' => $salon_name] );

		if( $is_simple ){
			echo $this->get_template_cart( $data['cart'] );
		}
		else{
			echo $this->get_template_simple_cart( $data['service'] );
		}

		$template = ob_get_clean();

		return $this->get_template_base( '', $template );
	}

	private function get_template_simple_cart( $service ){
		ob_start();
		pdp_get_template( 'emails/booking/simple-cart.php' );
		return ob_get_clean();
	}

	private function get_template_cart( $cart ){
		ob_start();
		pdp_get_template( 'emails/booking/cart.php' );
		return ob_get_clean();
	}

	private function get_template_gift_card( $data ){
		ob_start();
		pdp_get_template( 'emails/gift-card.php' );
		$template = ob_get_clean();

		return $this->get_template_base( '', $template );
	}

	private function get_template_vacancy_application( $data ){
		$data = $data;
		ob_start();
		pdp_get_template( 'emails/vacancy-application.php' );
		$template = ob_get_clean();

		return $this->get_template_base( '', $template );
	}

	public function booking_notification( $data ){
		$recipients = $this->admin_emails;
		//$recipients[] = carbon_get_post_meta( $data['cart']->salon, 'email' );

		return $this->send_to_admins( __( 'Новая заявка', 'pdp_core' ) , $this->get_template_booking( $data ) );
	}

	public function simple_booking_notification( $data ){
		$recipients = $this->admin_emails;
		//$recipients[] = carbon_get_post_meta( $data['salon'], 'email' );

		return $this->send_to_admins( __( 'Новая заявка', 'pdp_core' ) , $this->get_template_booking( $data, true ) );
	}

	public function service_booking_notification( $data ){
		$recipients = $this->admin_emails;
		//$recipients[] = carbon_get_post_meta( $data['salon'], 'email' );

		return $this->send_to_admins( __( 'Новая заявка', 'pdp_core' ) , $this->get_template_booking( $data, true ) );
	}

	public function category_booking_notification( $data ){
		return $this->send_to_admins( __( 'Новая заявка', 'pdp_core' ) , $this->get_template_booking( $data, true ) );
	}

	public function gift_card_notification( $data ){
		return $this->send_to_admins( __( 'Заказ подарочного сертификата', 'pdp_core' ) , $this->get_template_gift_card( $data ) );
	}

	public function vacancy_application_notification( $data, $attachment ){
		return $this->send_to_admins( __( 'Отклик на вакансию', 'pdp_core' ), $this->get_template_vacancy_application( $data ), $attachment );
	}
}