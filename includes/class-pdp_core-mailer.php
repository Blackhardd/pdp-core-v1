<?php

class PDP_Core_Mailer{
	private $admin_emails;
	private $site_url;

	public function __construct(){
		$this->init();
	}

	private function init(){
		$this->admin_emails = array(
			get_option( 'admin_email' ),
			'egorkolchenkode@gmail.com',
			'Shadyanett@gmail.com',
		);

		$this->site_url = get_option( 'siteurl' );

		add_filter( 'wp_mail_content_type', function( $content_type ){
			return 'text/html';
		} );
	}

	private function send( $recipients, $subject, $message, $attachments = array() ){
		return wp_mail( $recipients, $subject, $message, '', $attachments );
	}

	private function get_appointment_template( $data ){
		$salon = PDP_Core_Salon::get_by_id( $data['salon'] );

		$services_html = '';

		foreach( $data['cart']->items as $service ){
			$services_html .= "<div>{$service->name}</div>";
		}

		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; background-color: white; border-radius: 4px;">
                	<div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Заявка на запись', 'pdp_core' ) . ':</h1>
                    </div>
        
                    <div>
                    	<div style="margin-bottom: 32px;">
                    		<h3>' . __( 'Салон', 'pdp_core' ) . ':</h3>
                    		<div>
                                ' . $salon->post_title . '
                            </div>
						</div>
                    	
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 8px;">
                                ' . $data['email'] . '
                            </div>
                            <div style="margin-bottom: 8px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                        </div>
        
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Список услуг', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $services_html . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
	}

	private function get_quick_appointment_template( $data ){
		$salon = PDP_Core_Salon::get_by_id( $data['salon'] );

		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; background-color: white; border-radius: 4px;">
                	<div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Заявка на запись', 'pdp_core' ) . ':</h1>
                    </div>
        
                    <div>
                    	<div style="margin-bottom: 32px;">
                    		<h3>' . __( 'Салон', 'pdp_core' ) . ':</h3>
                    		<div>
                                ' . $salon->post_title . '
                            </div>
						</div>
                    	
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 8px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                        </div>
        
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Категория услуг', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['service'] . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
	}

	private function get_service_appointment_template( $data ){
		$salon = PDP_Core_Salon::get_by_id( $data['salon'] );

		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; background-color: white; border-radius: 4px;">
                	<div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Заявка на запись', 'pdp_core' ) . ':</h1>
                    </div>
        
                    <div>
                    	<div style="margin-bottom: 32px;">
                    		<h3>' . __( 'Салон', 'pdp_core' ) . ':</h3>
                    		<div>
                                ' . $salon->post_title . '
                            </div>
						</div>
                    	
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 8px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                        </div>
        
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Услуга', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['service'] . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
	}

	private function get_service_category_appointment_template( $data ){
		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; background-color: white; border-radius: 4px;">
                	<div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Заявка на запись', 'pdp_core' ) . ':</h1>
                    </div>
        
                    <div>
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 8px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                        </div>
        
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Услуга', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['service'] . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
    
    private function get_master_appointment_template( $data ){
        $salon = PDP_Core_Salon::get_by_id( $data['salon'] );

		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; background-color: white; border-radius: 4px;">
                	<div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Заявка на запись', 'pdp_core' ) . ':</h1>
                    </div>
        
                    <div>
                        <div style="margin-bottom: 32px;">
                    		<h3>' . __( 'Салон', 'pdp_core' ) . ':</h3>
                    		<div>
                                ' . $salon->post_title . '
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 8px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                        </div>
        
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Мастер', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['master'] . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
	}

	private function get_promotion_appointment_template( $data ){
		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; background-color: white; border-radius: 4px;">
                	<div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Заявка на запись', 'pdp_core' ) . ':</h1>
                    </div>
        
                    <div>
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 8px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                            <div style="margin-bottom: 8px;">
                                <a href="tel:' . $data['email'] . '">' . $data['email'] . '</a>
                            </div>
                        </div>
        
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Акция', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 8px;">
                                ' . $data['promotion'] . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
	}

	private function get_gift_card_order_template( $data ){
		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; text-align: center; background-color: white; border-radius: 4px;">
                    <div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Заказ подарочного сертификата', 'pdp_core' ) . '</h1>
                    </div>
        
                    <div style="text-align: left;">
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 4px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 4px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                            <div>
                                <a href="mailto:' . $data['email'] . '">' . $data['email'] . '</a>
                            </div>
                        </div>
        
                        <div>
                        	<h3>' . __( 'Заказ', 'pdp_core' ) . ':</h3>
                        	Подарочный сертификат на сумму ' . $_POST['card'] . ' грн.
                        </div>
                    </div>
                </div>
            </div>
        ';
	}

	private function get_vacancy_apply_template( $data ){
		return '
            <div style="padding: 40px; font-family: Arial; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; text-align: center; background-color: white; border-radius: 4px;">
                    <div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="Pied-De-Poule"></a>
	                    <h1 style="text-align: center;">' . __( 'Отклик на вакансию', 'pdp_core' ) . '</h1>
                    </div>
        
                    <div style="text-align: left;">
                        <div style="margin-bottom: 32px;">
                            <h3>' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div style="margin-bottom: 4px;">
                                ' . $data['name'] . '
                            </div>
                            <div style="margin-bottom: 4px;">
                                <a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a>
                            </div>
                            <div>
                                <a href="mailto:' . $data['email'] . '">' . $data['email'] . '</a>
                            </div>
                        </div>
        
                        <div>
                        	<h3>' . __( 'Сообщение', 'pdp_core' ) . ':</h3>
                        	' . $data['message'] . '
                        </div>
                    </div>
                </div>
            </div>
        ';
	}

	public function appointment_admins_notification( $data ){
		$receivers = $this->admin_emails;
		array_push( $receivers, carbon_get_post_meta( $data['salon'], 'email' ) );

		return $this->send( $receivers, __( 'Новая заявка', 'pdp_core' ) , $this->get_appointment_template( $data ) );
	}

	public function quick_appointment_admins_notification( $data ){
		$receivers = $this->admin_emails;
		array_push( $receivers, carbon_get_post_meta( $data['salon'], 'email' ) );

		return $this->send( $receivers, __( 'Новая заявка', 'pdp_core' ) , $this->get_quick_appointment_template( $data ) );
	}

	public function service_category_appointment_admins_notification( $data ){
		return $this->send( $this->admin_emails, __( 'Новая заявка', 'pdp_core' ) , $this->get_service_category_appointment_template( $data ) );
	}

	public function service_appointment_admins_notification( $data ){
		$receivers = $this->admin_emails;
		array_push( $receivers, carbon_get_post_meta( $data['salon'], 'email' ) );
		
		return $this->send( $receivers, __( 'Новая заявка', 'pdp_core' ) , $this->get_service_appointment_template( $data ) );
    }
    
    public function master_appointment_admins_notification( $data ){
		$receivers = $this->admin_emails;
		array_push( $receivers, carbon_get_post_meta( $data['salon'], 'email' ) );
		
		return $this->send( $receivers, __( 'Новая заявка', 'pdp_core' ) , $this->get_master_appointment_template( $data ) );
	}

	public function promotion_appointment_admins_notification( $data ){
		return $this->send( $this->admin_emails, __( 'Новая заявка', 'pdp_core' ) , $this->get_promotion_appointment_template( $data ) );
	}

	public function gift_card_order_admins_notification( $data ){
		return $this->send( $this->admin_emails, __( 'Заказ подарочного сертификата', 'pdp_core' ) , $this->get_gift_card_order_template( $data ) );
	}

	public function vacancy_apply_admins_notification( $data, $attachment ){
		return $this->send( $this->admin_emails, __( 'Новый отклик на вакансию', 'pdp_core' ), $this->get_vacancy_apply_template( $data ), $attachment );
	}
}