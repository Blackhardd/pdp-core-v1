<?php

class PDP_Core_Google{
    protected $token_path;
    protected $client_id;
    protected $client_secret;
    protected $auth_code;
    protected $client;

    public function __construct(){
        $this->token_path = plugin_dir_path(__FILE__) . 'token.json';
        $this->client_id = carbon_get_theme_option( 'google_client_id' );
        $this->client_secret = carbon_get_theme_option( 'google_secret' );
        $this->auth_code = isset( $_GET['code'] ) ? $_GET['code'] : false;
        $this->client = new Google_Client();
        $this->config_client();
    }

    public function config_client(){
        $this->client->setClientId( $this->client_id );
        $this->client->setClientSecret( $this->client_secret );
        $this->client->addScope( Google_Service_Sheets::SPREADSHEETS_READONLY );
        $this->client->setRedirectUri( 'https://new.p-de-p.com/wp-admin/admin.php?page=google-api-settings' );
        $this->client->setAccessType( 'offline' );
        $this->client->setPrompt( 'select_account' );

        if( $this->get_token() ){
            $this->client->setAccessToken( $this->get_token() );
        }

        if( $this->client->isAccessTokenExpired() ){
            if( $this->client->getRefreshToken() ){
                $this->client->fetchAccessTokenWithRefreshToken( $this->client->getRefreshToken() );
            }
            else{
                $auth_url = $this->client->createAuthUrl();

                echo ( !$this->auth_code ) ? '<div class="pdp-infobox alert"><div class="pdp-infobox__message">Для синхронизации цен нужно авторизовать в Google</div><div class="pdp-infobox__action"><a href="' . $auth_url . '" class="pdp-btn">' . __('Авторизоваться в Google', 'pdp_core') . '</a></div></div>' : '';

                if( $this->auth_code ){
                    $access_token = $this->client->fetchAccessTokenWithAuthCode( $this->auth_code );
                    $this->client->setAccessToken( $access_token );
                }
            }

            $this->save_token_to_file();
        }
    }

    public function get_client(){
        return $this->client;
    }

    private function get_token(){
        if( file_exists( $this->token_path ) ){
            return json_decode( file_get_contents( $this->token_path ), true );
        }
        else{
            return false;
        }
    }

    private function save_token_to_file(){
        return file_put_contents( $this->token_path, json_encode( $this->client->getAccessToken() ) );
    }
}