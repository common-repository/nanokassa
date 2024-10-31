<?php

final class Nanokassa_Admin {

	public static function init() {
		add_action( 'admin_head', array(__CLASS__, 'menu_correction') );
		add_action( 'admin_menu', array(__CLASS__, 'add_menu') );
		add_action( 'admin_menu', array(__CLASS__, 'add_sub_menu') );

		require_once(NANOKASSA_ABSPATH . 'nanosys/class-nanokassa-admin-settings.php');
    }

    public static function add_menu() {
		add_menu_page( 
			'Nanokassa', 
			'Nanokassa', 
			'manage_options', 
			'nanokassa', 
			null, 
			null,  
			'55.6' 
		);
	}

	public static function add_sub_menu() {
		global $submenu;

		add_submenu_page(
			'nanokassa', 
			'Онлайн-чеки', 
			'Онлайн-чеки', 
			'manage_options', 
			'nanokassa-checki', 
			array(__CLASS__, 'nanokassa_check_list')
		);

		add_submenu_page( 
			'nanokassa', 
			'Настройки',  
			'Настройки' , 
			'manage_options', 
			'nanokassa-settings', 
			array(__CLASS__, 'nanokassa_settings' )
		);
	}

	public static function menu_correction() {
        global $submenu;

        if (isset($submenu['nanokassa'])) {
            unset($submenu['nanokassa'][0]);
        }
    }

    public static function nanokassa_check_list() {
    	Nanokassa_AdminSettings::checklisthtml();
	}

	public static function nanokassa_settings() {
		Nanokassa_AdminSettings::mainsettings();
	}

}