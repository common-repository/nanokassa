<?php

final class Nanokassa_AdminSettings {

    private static $options = array(
        'kassaid' => 'string',
        'kassatoken' => 'string',
        'rezhim_nalog' => 'integer',
        'product_nds' => 'integer',
        'dostavka_nds' => 'integer',
        'priznak_sposoba_rascheta' => 'integer',
        'priznak_predmeta_rascheta' => 'integer',
        'priznak_agenta' => 'integer',
        'log' => 'integer',
        'type_check' => 'integer',
        'phone_oper_perevoda' => 'string',
        'oper_plat_agenta' => 'string',
        'phone_plat_agenta' => 'string',
        'phone_oper_plat' => 'string',
        'name_oper_per' => 'string',
        'addr_oper_per' => 'string',
        'inn_oper_per' => 'string',
        'phone_post' => 'string',
        'name_post' => 'string',
        'fio_upoln' => 'string',
        'inn_upoln' => 'string',
        'type_corr' => 'integer',
        'name_doc_vozvrat' => 'string',
        'num_doc_vozvrat' => 'string',
        'woocommerce_status' => 'string',
        'woocommerce_status_return' => 'string',
        // 'woocommerce_payment' => 'array',
        'wpec_status' => 'string',
        'wpec_status_return' => 'string',
        'wpec_payment' => 'string'
    );

    public static function checklisthtml() {
        if (!empty($_POST)) {
            self::save();
        }
        include(NANOKASSA_ABSPATH_VIEWS . 'html-admin-reports.php');
    }

    public static function mainsettings() {
        if (!empty($_POST)) {
            self::save();
        }
        include(NANOKASSA_ABSPATH_VIEWS . 'html-admin-settings.php');
    }

    public static function save() {

        foreach (self::$options as $key => $type) {
            $value = filter_input(INPUT_POST, $key);

            if(isset($_POST['woocommerce_payment'])) {
                $js_woo = json_encode($_POST['woocommerce_payment']);
                update_option('woocommerce_payment', $js_woo);
            }
                
            if ($value != NULL) {
                if ($type == 'string') {
                    update_option($key, $value);
                } else if ($type == 'bool') {
                    update_option($key, $value === "1" ? "1" : "0");
                } else if ($type == 'integer') {
                    update_option($key, intval($value));
                }
            } 
               
        }

    }

}