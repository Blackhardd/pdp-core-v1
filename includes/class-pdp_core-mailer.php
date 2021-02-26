<?php

class PDP_Core_Mailer{
	private $site_url;
	private $admin_emails;
	private $hair_lengths;

	public function __construct(){
		$this->init();
	}

	private function init(){
		$this->site_url = get_option( 'siteurl' );

		$this->admin_emails = array(
			get_option( 'admin_email' )
		);

		$this->hair_lengths = array(
			__( 'от 5-15 см', 'pdp' ),
			__( 'от 15 - 25 см (выше плеч, каре, боб)', 'pdp' ),
			__( 'от 25 - 40 см (ниже плеч/выше лопаток)', 'pdp' ),
			__( 'от 40 - 60 см (ниже лопаток)', 'pdp' )
		);

		add_filter( 'wp_mail_content_type', function( $content_type ){
			return 'text/html';
		} );
	}

	private function send( $recipients, $subject, $message, $attachments = array() ){
		return wp_mail( $recipients, $subject, $message, '', $attachments );
	}

	private function get_appointment_template( $data ){
		$salon = PDP_Core_Salon::get_by_id( $data['cart']->salon );

		$services_html = '';

		foreach( $data['cart']->items as $service ){
			$name = $service->name;
			$price = 0;

			if( count( $service->prices ) > 1 && $service->master ){
				$price = $service->prices[$data['cart']->hair_length][$data['cart']->master_option];
			}
			else if( count( $service->prices ) > 1 && !$service->master ){
				$price = $service->prices[$data['cart']->hair_length][0];
			}
			else if( count( $service->prices ) == 1 && $service->master ){
				$price = $service->prices[0][$data['cart']->master_option];
			}
			else{
				$price = $service->prices[0][0];
			}

			$services_html .= "
				<div style='display: flex;'>
					<div>{$name}</div>
					<div style='margin-left: auto;'>{$price} грн</div>
				</div>
			";
		}

		$services_detail = "
			<h3 style='margin-bottom: 8px;'>" . __( 'Дополнительная информация', 'pdp_core' ) . ":</h3>
		";

		if( $data['cart']->master_option ){
			$services_detail .= '<div>Старший мастер.</div>';
		}

		if( $data['is_hair_services'] ){
			$services_detail .= "<div>" . __( 'Длина волос', 'pdp_core' ) . " {$this->hair_lengths[$data['cart']->hair_length]}.</div>";
		}

		return '
            <div style="padding: 40px; font-family: Helvetica, Arial, sans-serif; font-size: 16px; background-color: #f3f3f3;">
                <div style="max-width: 500px; margin: 0 auto; padding: 40px; background-color: white; border-radius: 4px;">
                	<div style="text-align: center">
	                    <a href="' . $this->site_url . '"><img src="https://new.p-de-p.com/wp-content/uploads/2020/10/logo.png" alt="PIED-DE-POULE"></a>
	                    <h1 style="color: black; text-align: center;">' . __( 'Заявка на запись', 'pdp_core' ) . ':</h1>
                    </div>
        
                    <div>
                    	<div style="margin-bottom: 32px;">
                    		<h3 style="margin-bottom: 8px;">' . __( 'Салон', 'pdp_core' ) . ':</h3>
                    		<div>' . $salon->post_title . '</div>
						</div>
                    	
                        <div style="margin-bottom: 32px;">
                            <h3 style="margin-bottom: 8px;">' . __( 'Контактные данные', 'pdp_core' ) . ':</h3>
                            <div>' . $data['name'] . '</div>
                            <div>' . $data['email'] . '</div>
                            <div><a href="tel:' . $data['phone'] . '">' . $data['phone'] . '</a></div>
                        </div>
                        
                        <div style="margin-bottom: 32px;">
                        	' . $services_detail . '
						</div>
        
                        <div>
                            <h3 style="margin: 0; padding: 20px; background: #fafafa;">' . __( 'Список услуг', 'pdp_core' ) . '</h3>
                            <div style="padding: 20px; background: #f3f3f3;">' . $services_html . '</div>
                            <div style="display: flex; padding: 20px; font-size: 20px; background: #fafafa;">
	                            <div>Итого:</div>
	                            <div style="margin-left: auto;">' . $data['total'] . ' грн</div>
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
		array_push( $receivers, carbon_get_post_meta( $data['cart']->salon, 'email' ) );

		return $this->send( $receivers, __( 'Заявка', 'pdp_core' ) , $this->get_appointment_template( $data ) );
	}

	public function quick_appointment_admins_notification( $data ){
		$receivers = $this->admin_emails;
		array_push( $receivers, carbon_get_post_meta( $data['salon'], 'email' ) );

		return $this->send( $receivers, __( 'Заявка', 'pdp_core' ) , $this->get_quick_appointment_template( $data ) );
	}

	public function service_category_appointment_admins_notification( $data ){
		return $this->send( $this->admin_emails, __( 'Заявка', 'pdp_core' ) , $this->get_service_category_appointment_template( $data ) );
	}

	public function service_appointment_admins_notification( $data ){
		$receivers = $this->admin_emails;
		array_push( $receivers, carbon_get_post_meta( $data['salon'], 'email' ) );
		
		return $this->send( $receivers, __( 'Заявка', 'pdp_core' ) , $this->get_service_appointment_template( $data ) );
    }
    
    public function master_appointment_admins_notification( $data ){
		$receivers = $this->admin_emails;
		array_push( $receivers, carbon_get_post_meta( $data['salon'], 'email' ) );
		
		return $this->send( $receivers, __( 'Заявка', 'pdp_core' ) , $this->get_master_appointment_template( $data ) );
	}

	public function promotion_appointment_admins_notification( $data ){
		return $this->send( $this->admin_emails, __( 'Заявка', 'pdp_core' ) , $this->get_promotion_appointment_template( $data ) );
	}

	public function gift_card_order_admins_notification( $data ){
		return $this->send( $this->admin_emails, __( 'Заказ подарочного сертификата', 'pdp_core' ) , $this->get_gift_card_order_template( $data ) );
	}

	public function vacancy_apply_admins_notification( $data, $attachment ){
		return $this->send( $this->admin_emails, __( 'Отклик на вакансию', 'pdp_core' ), $this->get_vacancy_apply_template( $data ), $attachment );
	}
}