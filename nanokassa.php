<?php
/*
Plugin Name: nanokassa
Plugin URI: https://nanokassa.ru
Description: Kassa plugin for Nanokassa.ru
Version: 1.0.2
Author: "Nanokassa.ru"
Author URI: https://nanokassa.ru
Copyright: © 2018 "Nanokassa"
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

require( ABSPATH . WPINC . '/pluggable.php' );
// $folder = ABSPATH.'/wp-content/plugins/nanokassa/';

// include ($folder.'adminpages.php');
// include ($folder.'cheki.php');
// include ($folder.'setup.php');

// require_once($folder.'params.php');
// require_once($folder.'fns.php');

use Nanokassa\F\NanoFunctions as nanoF;
use Nanokassa\P\NanoParams as nanoP;


final class NanokassaMainClass {

	public $version = '1.0.1';

	private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function __construct() {
		$this->define('NANOKASSA_ABSPATH', plugin_dir_path( __FILE__));
		$this->define('NANOKASSA_ABSPATH_VIEWS', plugin_dir_path( __FILE__) . 'nanosys/views/');

		$this->includes();
		$this->load_options();
		$this->hooks();
		$this->wp_hooks();
		
	}

	public function wp_hooks()
    {
        register_activation_hook( __FILE__, array('setup_nanokassa', 'activation') );

		add_action( 'woocommerce_order_status_changed', array($this, 'wc_status_changed'), 10, 4);
		add_action( 'wpsc_purchase_log_save', array($this, 'wpec_status_changed'));
    }

    public function hooks()
    {
        add_action( 'nanokassa_settings_general' , array($this, 'settings_general_html') );
		add_action( 'nanokassa_settings_woocommerce' , array($this, 'settings_woocommerce_html') );
		add_action( 'nanokassa_settings_wpec' , array($this,  'settings_wpec_html') );
    }

    public function includes() {
    	require_once(NANOKASSA_ABSPATH . 'nanosys/nanokassa-setup.php');
        require_once(NANOKASSA_ABSPATH . 'nanosys/nanofunctions.php');
        require_once(NANOKASSA_ABSPATH . 'nanosys/nanoparams.php');

        if (is_admin()) {
            require_once(NANOKASSA_ABSPATH . 'nanosys/class-nanokassa-admin.php');
            add_action('init', array( 'Nanokassa_Admin', 'init'));
        }
    }

    private function define($name, $value)
    {
        if (!defined( $name )) {
            define( $name, $value );
        }
    }

    public function load_options() {

    	$this->kassaid = get_option('kassaid');
    	$this->kassatoken = get_option('kassatoken');
    	$this->rezhim_nalog = get_option('rezhim_nalog');
    	$this->product_nds = get_option('product_nds');
    	$this->dostavka_nds = get_option('dostavka_nds');
    	$this->priznak_sposoba_rascheta = get_option('priznak_sposoba_rascheta');
    	$this->priznak_predmeta_rascheta = get_option('priznak_predmeta_rascheta');
    	$this->priznak_agenta = get_option('priznak_agenta');
    	$this->log = get_option('log');
    	$this->type_check = get_option('type_check');
    	$this->phone_oper = get_option('phone_oper');
    	$this->oper_plat_agenta = get_option('oper_plat_agenta');
    	$this->phone_plat_agenta = get_option('phone_plat_agenta');
    	$this->phone_oper_plat = get_option('phone_oper_plat');
    	$this->name_oper_per = get_option('name_oper_per');
    	$this->addr_oper_per = get_option('addr_oper_per');
    	$this->inn_oper_perevoda = get_option('inn_oper_perevoda');
    	$this->phone_post = get_option('phone_post');
    	$this->name_post = get_option('name_post');
    	$this->fio_upoln = get_option('fio_upoln');
    	$this->inn_upoln = get_option('inn_upoln');
    	$this->type_corr = get_option('type_corr');
    	$this->name_doc_vozvrat = get_option('name_doc_vozvrat');
    	$this->num_doc_vozvrat = get_option('num_doc_vozvrat');
    	$this->woocommerce_status = get_option('woocommerce_status');
    	$this->woocommerce_status_return = get_option('woocommerce_status_return');
    	$this->woocommerce_payment = json_decode( get_option('woocommerce_payment'), true);
    	$this->wpec_status = get_option('wpec_status');
    	$this->wpec_status_return = get_option('wpec_status_return');
    	$this->wpec_payment = get_option('wpec_payment');

		
		if ( empty($this->rezhim_nalog) ) {
			$this->rezhim_nalog = nanoP::NALOG_TYPE_OSN;
		}
		if ( empty($this->product_nds) ) {
			$this->product_nds = nanoP::STAVKA_NDS_18;
			// $this->product_nds = 70;
		}
		if ( empty($this->dostavka_nds) ) {
			$this->dostavka_nds = nanoP::STAVKA_NDS_18;
			// $this->dostavka_nds = 70;
		}
		if ( empty($this->priznak_sposoba_rascheta) ) {
			$this->priznak_sposoba_rascheta = nanoP::OPLATA_POLN_DO;
		}
		if ( empty($this->priznak_predmeta_rascheta) ) {
			$this->priznak_predmeta_rascheta = nanoP::PREDMET_TOVAR_MAIN;
		}
		if ( empty($this->priznak_agenta) ) {
			$this->priznak_agenta = nanoP::NO_AGENT;
		}
		if ( empty($this->log) ) {
			$this->log = 0;
		}

		if ( empty($this->type_check) ) {
			$this->type_check = 1;
		}
		
		if ( empty($this->fio_upoln) ) {
			$this->fio_upoln = nanoP::TEXT_AGENT_FIO;
		}
		if ( empty($this->inn_upoln) ) {
			$this->inn_upoln = nanoP::TEXT_AGENT_INN;
		}
		if ( empty($this->type_corr) ) {
			$this->type_corr = nanoP::VOZVRAT_SAM;
		}
		if ( empty($this->name_doc_vozvrat) ) {
			$this->name_doc_vozvrat = nanoP::TEXT_DOCUM_OSNOV;
		}
		if ( empty($this->num_doc_vozvrat) ) {
 			$this->num_doc_vozvrat = nanoP::TEXT_DOCUM_NUM;
		}

		if ( empty($this->woocommerce_status) ) {
			$this->woocommerce_status = 'wc-completed';
		}
		if ( empty($this->woocommerce_status_return) ) {
			$this->woocommerce_status_return = 'wc-refunded';
		}
		if ( empty($this->woocommerce_payment) ) {
			$this->woocommerce_payment = array();
		}
		if ( empty($this->wpec_status) ) {
			$this->wpec_status = '3';
		}
		if ( empty($this->wpec_status_return) ) {
			$this->wpec_status_return = '7';
		}
		if ( empty($this->wpec_payment) ) {
			$this->wpec_payment = array();
		}
    }

	public function settings_general_html() {
		self::load_options();
		?>
		<table class="form-table">

			<tr>
				<th scope="row"><label for="kassaid">Уникальный номер вашей онлайн-кассы (kassaid)</label></th>
				<td><input type="text" name="kassaid" id="kassaid" value="<?php echo esc_attr($this->kassaid); ?>"  class="regular-text" ></td>
			</tr>

			<tr>
				<th scope="row"><label for="kassatoken">Пароль для кассы (kassatoken)</label></th>
				<td><input type="text" name="kassatoken" id="kassatoken" value="<?php echo esc_attr($this->kassatoken); ?>"  class="regular-text" ></td>
			</tr>

			<tr>
				<th scope="row"><label for="rezhim_nalog">Система налогообложения</label></th>
				<td>
				<select name="rezhim_nalog" id="rezhim_nalog"  style="font-size:12px;">
					<?php
						nanoF::dropdown_list(nanoF::nalog_type_list(), $this->rezhim_nalog);
					?>
				</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="product_nds">НДС на товары</label></th>
				<td>
				<select name="product_nds" id="product_nds"  style="font-size:12px;">
					<?php
						nanoF::dropdown_list(nanoF::nds_list_wordpress_wc(), $this->product_nds);
					?>
				</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="dostavka_nds">НДС на доставку</label></th>
				<td>
				<select name="dostavka_nds" id="dostavka_nds" style="font-size:12px;">
					<?php
					nanoF::dropdown_list(nanoF::nds_dostavka_list_wordpress_wc(), $this->dostavka_nds);
					?>
				</select>
				</td>
			</tr>



			<tr>
				<th scope="row"><label for="priznak_sposoba_rascheta">Выберите признак способа расчета</label></th>
				<td>
				<select name="priznak_sposoba_rascheta" id="priznak_sposoba_rascheta" style="font-size:12px;">
				<?php
					nanoF::dropdown_list(nanoF::priznak_sposoba_rascheta_list(), $this->priznak_sposoba_rascheta);
				?>
				</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="priznak_predmeta_rascheta">Выберите признак предмета расчета</label></th>
				<td>
				<select name="priznak_predmeta_rascheta" id="priznak_predmeta_rascheta" style="font-size:12px;">
				<?php
					nanoF::dropdown_list(nanoF::priznak_predmeta_rascheta_list(), $this->priznak_predmeta_rascheta);
				?>
				</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="priznak_agenta">Выберите признак агента</label></th>
				<td>
				<select name="priznak_agenta" id="priznak_agenta" style="font-size:12px;">
				<?php
					nanoF::dropdown_list(nanoF::priznak_agenta_list(), $this->priznak_agenta);
				?>
				</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="log">Логирование запросов</label></th>
				<td>
				<select name="log" id="log" style="font-size:12px;">
					<?php
						nanoF::dropdown_list(nanoF::dropdown_yesno_list(), $this->log);
					?>
				</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="type_check">Отправлять чеки посредством</label></th>
				<td>
				<select name="type_check" id="type_check" style="font-size:12px;">
					<?php
						nanoF::dropdown_list(nanoF::dropdown_type_check_list(), $this->type_check);
					?>
				</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="phone_oper">Телефон оператора перевода</label></th>
				<td><input type="text" name="phone_oper" id="phone_oper" value="<?php echo esc_attr($this->phone_oper); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (БПА, БПСА)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="oper_plat_agenta">Операция платежного агента</label></th>
				<td><input type="text" name="oper_plat_agenta" id="oper_plat_agenta" value="<?php echo esc_attr($this->oper_plat_agenta); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (БПА, БПСА)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="phone_plat_agenta">Телефон платежного агента</label></th>
				<td><input type="text" name="phone_plat_agenta" id="phone_plat_agenta" value="<?php echo esc_attr($this->phone_plat_agenta); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (ПА, ПСА)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="phone_oper_plat">Телефон оператора по приему платежей</label></th>
				<td><input type="text" name="phone_oper_plat" id="phone_oper_plat" value="<?php echo esc_attr($this->phone_oper_plat); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (ПА, ПСА)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="name_oper_per">Наименование оператора перевода</label></th>
				<td><input type="text" name="name_oper_per" id="name_oper_per" value="<?php echo esc_attr($this->name_oper_per); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (БПА, БПСА)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="addr_oper_per">Адрес оператора перевода</label></th>
				<td><input type="text" name="addr_oper_per" id="addr_oper_per" value="<?php echo esc_attr($this->addr_oper_per); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (БПА, БПСА)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="inn_oper_perevoda">ИНН оператора перевода</label></th>
				<td><input type="text" name="inn_oper_perevoda" id="inn_oper_perevoda" value="<?php echo esc_attr($this->inn_oper_perevoda); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (БПА, БПСА)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="phone_post">Телефон поставщика</label></th>
				<td><input type="text" name="phone_post" id="phone_post" value="<?php echo esc_attr($this->phone_post); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (для всех типов агента)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="name_post">Наименование поставщика</label></th>
				<td><input type="text" name="name_post" id="name_post" value="<?php echo esc_attr($this->name_post); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в атрибуте товара. Заполнять нужно ТОЛЬКО если пользователь ККТ является платежным агентом (для всех типов агента)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="fio_upoln">ФИО уполномоченного лица</label></th>
				<td><input type="text" name="fio_upoln" id="fio_upoln" value="<?php echo esc_attr($this->fio_upoln); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в произвольном поле заказа. Заполнять нужно ТОЛЬКО для чеков коррекции (Это ФИО будет фигурировать в формировании чека коррекции при возврате прихода
	)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="inn_upoln">ИНН уполномоченного лица</label></th>
				<td><input type="text" name="inn_upoln" id="inn_upoln" value="<?php echo esc_attr($this->inn_upoln); ?>"  class="regular-text" >
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в произвольном поле заказа. Заполнять нужно ТОЛЬКО для чеков коррекции (Это ИНН будет фигурировать в формировании чека коррекции при возврате прихода)</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="type_corr">Тип коррекции</label></th>
				<td>
				<select name="type_corr" id="type_corr">
				<?php
					nanoF::dropdown_list(nanoF::dropdown_type_corr_list(), $this->type_corr);
				?>
				</select>
				<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в произвольном поле заказа. Этот тип корреции будет фигурировать в формировании чека коррекции при возврате прихода</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="name_doc_vozvrat">Наименование документа основания</label></th>
					<td><input type="text" name="name_doc_vozvrat" id="name_doc_vozvrat" value="<?php echo esc_attr($this->name_doc_vozvrat); ?>"  class="regular-text" >
					<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в произвольном поле заказа. Заполнять нужно ТОЛЬКО для чеков коррекции (Именно это наименование будет фигурировать в формировании чека коррекции при возврате прихода
		)</p>
					</td>
			</tr>

			<tr>
				<th scope="row"><label for="num_doc_vozvrat">Номер документа основания</label></th>
					<td><input type="text" name="num_doc_vozvrat" id="num_doc_vozvrat" value="<?php echo esc_attr($this->num_doc_vozvrat); ?>"  class="regular-text" >
					<p style="font-size:12px;">Это значение будет задано по умолчанию, если другое не было заполнено в произвольном поле заказа. Заполнять нужно ТОЛЬКО для чеков коррекции (Именно этот номер документа будет фигурировать в формировании чека коррекции при возврате прихода
	)</p>
					</td>
			</tr>

		</table>
		<?php
	}

	public function dropdown_woocommerce_status_list($value) {
		$list = wc_get_order_statuses();
		nanoF::dropdown_list($list, $value);
	}

	public function dropdown_woocommerce_payment_list($value) {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		$list = array();
		foreach($payment_gateways as $gateway) {
			$list[$gateway->id] = $gateway->get_title();
		}
		nanoF::dropdown_list($list, $value);
	}

	public function settings_woocommerce_html() {
		?>
		<table class="form-table">
		<tr>
			<th scope="row"><label for="woocommerce_status">Статус прихода</label></th>
			<td><select name="woocommerce_status" id="woocommerce_status">
				<?php $this->dropdown_woocommerce_status_list($this->woocommerce_status);?>
			</select></td>
		</tr>
		<tr>
			<th scope="row"><label for="woocommerce_status_return">Статус возврата</label></th>
			<td><select name="woocommerce_status_return" id="woocommerce_status_return">
				<?php $this->dropdown_woocommerce_status_list($this->woocommerce_status_return);?>
			</select></td>
		</tr>
		<tr>
			<th scope="row"><label for="woocommerce_payment">Платежные системы</label></th>
			<td><select name="woocommerce_payment[]" id="woocommerce_payment"  multiple="multiple" size="3">
				<?php $this->dropdown_woocommerce_payment_list($this->woocommerce_payment);?>
			</select></td>
		</tr>
		</table>
		<?php

	}

	public function dropdown_wpec_status_list($value) {
		global $wpsc_purchlog_statuses;
		$list = array();
		foreach ( $wpsc_purchlog_statuses as $status ) {
			$list[$status['order']] = $status['label'];
		}
		nanoF::dropdown_list($list, $value);
	}

	public function dropdown_wpec_payment_list($value) {
		$gateways = apply_filters( 'wpsc_settings_get_gateways', array() );
		$list = array();
		foreach($gateways as $gateway) {
			$list[$gateway['id']] = esc_html( $gateway['name'] );
		}
		nanoF::dropdown_list($list, $value);
	}

	public function settings_wpec_html() {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="wpec_status">Статус прихода</label></th>
				<td><select name="wpec_status" id="wpec_status">
					<?php $this->dropdown_wpec_status_list($this->wpec_status);?>
				</select></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpec_status_return">Статус возврата</label></th>
				<td><select name="wpec_status_return" id="wpec_status_return">
					<?php $this->dropdown_wpec_status_list($this->wpec_status_return);?>
				</select></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpec_payment">Платежные системы</label></th>
				<td><select name="wpec_payment[]" id="wpec_payment" multiple="multiple" size="3">
					<?php $this->dropdown_wpec_payment_list($this->wpec_payment);?>
				</select></td>
			</tr>
		</table>
		<?php
	}

	public static function getProdAttr($prodID, $attr){
		$tmp_30 = 'pa_'.$attr;
		$tmp_31 = array('fields' => 'names');
		$tmp_32 = wc_get_product_terms($prodID, $tmp_30, $tmp_31);

		$fromProd = array_shift($tmp_32);
			// wc_get_product_terms( $prodID, 'pa_'.$attr, array( 'fields' => 'names' ) ) 
		if($fromProd){
			return $fromProd;
		} 
		return "";
	}

	public function wpec_status_changed($order) {
		global $wpdb;
		require_once( WPSC_FILE_PATH . '/wpsc-includes/purchaselogs-items.class.php' );
		$order_data = $order->get_data();
		$order->init_items();
		$items = $order->get_items();

		if (
			(
				$order_data['processed'] == $this->wpec_status
				|| $order_data['processed'] == $this->wpec_status_return
				)
			&&
				in_array($order_data['gateway'], $this->wpec_payment)
		) {
			if($order_data['processed'] == $this->wpec_status) {
				$type = 0;
			} else {
				$type = 1;
			}

			if ($type == 0) {
				$documentType = nanoP::OPERATION_PRIHOD;
			} else {
				$documentType = nanoP::OPERATION_VOZVRAT_PRIHODA;
			}

			$checkId = $this->getCheckId($order_data['id'], $type);

			$shipping = $order_data['base_shipping'];
			$discount = $order_data['discount_value'];
			if($discount) {
				$discount = $discount/($order_data['totalprice']+$discount);
			}

			$total_sum = 0;
			$iterator = 0;
			$allItemsArray = array();

			foreach($items as $item) {
				$iterator++;
				if($iterator == count($items)){
                    $subtotal = $order_data['totalprice'] - round($shipping * (1 + $discount),2) - $total_sum;
                    $final = round($subtotal/$item->quantity,2);
                } else {
                    $final = round($item->price * (1 + $discount),2);
                    $subtotal = $final * $item->quantity;
                }
                $total_sum += $subtotal;

                if ($this->product_nds == 70) {
					// $nds = $this->getProdAttr($item->get_product()->!id, "stavka_nds");
					$nds = $this->getProdAttr($item->get_product()->get_ID(), "stavka_nds");
				} else {
					$nds = $this->product_nds;
				}


				if ($this->priznak_sposoba_rascheta == 70) {
					$priznak_sposoba_rascheta = $this->getProdAttr($item->get_product()->get_ID(), "priznak_sposoba_rascheta");
				} else {
					$nds = intval($this->priznak_sposoba_rascheta);
				}


				if ($this->priznak_sposoba_rascheta == 70) {
					$priznak_predmeta_rascheta = $this->getProdAttr($item->get_product()->get_ID(), "priznak_predmeta_rascheta");
				} else {
					$priznak_predmeta_rascheta = intval($this->priznak_predmeta_rascheta);
				}

				if ($this->priznak_agenta == 70) {
					$priznak_agenta = $this->getProdAttr($item->get_product()->get_ID(), "priznak_agenta");
				} else {
					$priznak_agenta = intval($this->priznak_agenta);
				}

				$phone_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "phone_oper_perevoda");
				if (empty($phone_oper_perevoda)) {
					if (isset($this->phone_oper_perevoda)) {
                		$phone_oper_perevoda = $this->phone_oper_perevoda;
                	} else {
                		$phone_oper_perevoda = '';
                	}
                }

                $operation_plat_agenta = $this->getProdAttr($item->get_product()->get_ID(), "operation_plat_agenta");
				if (empty($operation_plat_agenta)) {
					if (isset($this->operation_plat_agenta)) {
                		$operation_plat_agenta = $this->operation_plat_agenta;
                	} else {
                		$operation_plat_agenta = '';
                	}
                }


                $phone_oper_priem_plat = $this->getProdAttr($item->get_product()->get_ID(), "phone_oper_priem_plat");
				if (empty($phone_oper_priem_plat)) {
					if (isset($this->phone_oper_priem_plat)) {
                		$phone_oper_priem_plat = $this->phone_oper_priem_plat;
                	} else {
                		$phone_oper_priem_plat = '';
                	}
                }
                
                $name_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "name_oper_perevoda");
                if (empty($name_oper_perevoda)) {
                	if (isset($this->name_oper_perevoda)) {
                		$name_oper_perevoda = $this->name_oper_perevoda;
                	} else {
                		$name_oper_perevoda = '';
                	}
                }

                $address_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "address_oper_perevoda");
                if (empty($address_oper_perevoda)) {
                	if (isset($this->address_oper_perevoda)) {
                		$address_oper_perevoda = $this->address_oper_perevoda;
                	} else {
                		$address_oper_perevoda = '';
                	}
                }

                $inn_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "inn_oper_perevoda");
                if (empty($inn_oper_perevoda)) {
                	if (isset($this->inn_oper_perevoda)) {
                		$inn_oper_perevoda = $this->inn_oper_perevoda;
                	} else {
                		$inn_oper_perevoda = '';
                	}
                }

                $phone_postavshika = $this->getProdAttr($item->get_product()->get_ID(), "phone_postavshika");
                if (empty($phone_postavshika)) {
                	if (isset($this->phone_postavshika)) {
                		$phone_postavshika = $this->phone_postavshika;
                	} else {
                		$phone_postavshika = '';
                	}
                }

                $name_postavshika = $this->getProdAttr($item->get_product()->get_ID(), "name_postavshika");
                if (empty($name_postavshika)) {
                	if (isset($this->name_postavshika)) {
                		$name_postavshika = $this->name_postavshika;
                	} else {
                		$name_postavshika = '';
                	}                	
                }                

                $kolvo = $item_data['quantity'];
                // $price_piece = round($final*100);
                // $summa = round($subtotal * 100);

                $price_piece_bez_skidki = round( ($item_data['subtotal'] + $item_data['subtotal_tax'])/$item_data['quantity'], 2)*100;
                $price_piece = round( ($item_data['total'] + $item_data['total_tax']) /$item_data['quantity'], 2)*100;
                $skidka = $price_piece_bez_skidki-$price_piece;
                $summa = round( ($item_data['total'] + $item_data['total_tax']),2) * 100;

                $itemArray = [
                	"name_tovar" => "Доставка: ".$item->name,
                    "price_piece_bez_skidki" => $price_piece_bez_skidki,
                    "skidka" => $skidka,
                    "kolvo" => $kolvo,
                    "price_piece" => $price_piece,
                    "summa" => $summa,
                    "stavka_nds" => $nds,
                    "priznak_sposoba_rascheta" => $priznak_sposoba_rascheta,
                    "priznak_predmeta_rascheta" => $priznak_predmeta_rascheta,
                    "priznak_agenta" => $priznak_agenta,
                    "phone_oper_perevoda" => $phone_oper_perevoda,
                    "operation_plat_agenta" => $operation_plat_agenta,
                    "phone_oper_priem_plat" => $phone_oper_priem_plat,
                    "name_oper_perevoda" => $name_oper_perevoda,
                    "address_oper_perevoda" => $address_oper_perevoda,
                    "inn_oper_perevoda" => $inn_oper_perevoda,
                    "phone_postavshika" => $phone_postavshika,
                    "name_postavshika" => $name_postavshika,
                    "discount" => array('type' => "amount", "value" => $skidka)
                ];

                $allItemsArray[] = $itemArray;

			}

			if ($shipping > 0) {

				$priznak_sposoba_rascheta_dost = $this->priznak_sposoba_rascheta;
				if ($priznak_sposoba_rascheta_dost == 70) {
					$priznak_sposoba_rascheta_dost = 4;
				}

				$priznak_predmeta_rascheta_dost = $this->priznak_predmeta_rascheta;
				if ($priznak_predmeta_rascheta_dost == 70) {
					$priznak_predmeta_rascheta_dost = 4;
				}

				$ship_tot_tax = $order_data['shipping_total']+$order_data['shipping_tax'];

				$itemArray = [
					"name_tovar" => $item_data['name'],
                    "price_piece_bez_skidki" => round($ship_tot_tax * 100),
                    "skidka" => 0,
                    "kolvo" => 1,
                    "price_piece" => round($ship_tot_tax * 100),
                    "summa" => round($ship_tot_tax * 100),
                    "stavka_nds" => intval($this->dostavka_nds),
                    "priznak_sposoba_rascheta" => $priznak_sposoba_rascheta,
                    "priznak_predmeta_rascheta" => $priznak_predmeta_rascheta,
                    "priznak_agenta" => 'none',
                    "phone_oper_perevoda" => '',
                    "operation_plat_agenta" => '',
                    "phone_oper_priem_plat" => '',
                    "name_oper_perevoda" => '',
                    "address_oper_perevoda" => '',
                    "inn_oper_perevoda" => '',
                    "phone_postavshika" => '',
                    "name_postavshika" => '',
                    "discount" => array('type' => "amount", "value" => 0)
                ];

                $allItemsArray[] = $itemArray;

			}

			if (isset($this->type_check)) {
				$type_check = $this->type_check;
			} else {
				$type_check = 1;
			}

			if ($type_check == 1) {
				$type_check_txt = 'email';
			} elseif ($type_check == 2) {
				$type_check_txt = 'phone';
			} else {
				$type_check_txt = 'email';
			}

			if ( isset($order_data['id']) ) {
				$name_zakaz = '#'.intval($order_data['id']);
			} else {
				$name_zakaz = '';
			}

    		$request_part1 = '{
				"kassaid": "'.$this->kassaid.'",
				"kassatoken": "'.$this->kassatoken.'",
				"cms": "wordpress",
				"check_send_type": "'.$type_check_txt.'",
				"name_zakaz": "'.$name_zakaz.'",
				"oplata_arr": {
					"rezhim_nalog": "'.$this->rezhim_nalog.'",
					"money_nal": 0,
					"money_electro": '.($order_data["total"]*100).',
					"money_predoplata": 0,
					"money_postoplata": 0,
					"money_vstrecha": 0,
					"client_email": "'.$order_data['billing']['email'].'",
					"client_phone": "'.$order_data['billing']['phone'].'"
					},
				"itog_arr": {
					"priznak_rascheta": '.$documentType.',
					"itog_cheka": '.($order_data["total"]*100).'
					},
        		"products_arr": '.json_encode($allItemsArray, JSON_UNESCAPED_UNICODE).'';
			

			if ($documentType == nanoP::OPERATION_PRIHOD) {
				$request_part2 = '}';
			} elseif ($documentType == nanoP::OPERATION_VOZVRAT_PRIHODA) {
				$date_paid = $order_data->get_date_paid();
				$date_paid_timestamp = $date_paid->getTimestamp();
				$date_paid_day = date("Y-m-d", $date_paid_timestamp);
				$date_paid_day_timestamp = strtotime($date_paid_day);

				$meta_order = $order_data['meta_data'];

				if ( !empty($meta_order->custom_name_doc_korr) ) {
					$meta_name_doc_korr = $meta_order->custom_name_doc_korr;
				}

				if ( !empty($meta_order->custom_number_doc_korr) ) {
					$meta_num_doc_korr = $meta_order->custom_number_doc_korr;
				}

				if ( empty($meta_name_doc_korr) ) {
					if ( isset($this->$name_doc_vozvrat) ) {
						$meta_name_doc_korr = $this->$name_doc_vozvrat;
					}
				}

				if ( empty($meta_num_doc_korr) ) {
					if ( isset($this->$num_doc_vozvrat) ) {
						$meta_num_doc_korr = $this->$num_doc_vozvrat;
					}
				}

				$request_part2 = ', "korr_arr": {
					"name_doc_korr" : "'.$meta_name_doc_korr.'",
					"date_doc_korr" : "'.$date_paid_day_timestamp.'",
					"num_doc_korr" : "'.$meta_num_doc_korr.'"
				}}';
			}
			$request = $request_part1.$request_part2;

			$this->sendData($request, $checkId);

		}

	}


	public function wc_status_changed($id, $from, $to, $order) {
		global $wpdb;
		$order_data = $order->get_data();

		if (
			(
				$this->woocommerce_status == 'wc-'.$to
				|| $this->woocommerce_status_return == 'wc-'.$to
			)
			&& in_array($order_data['payment_method'],$this->woocommerce_payment)
			) {
			if($this->woocommerce_status == 'wc-'.$to) {
				$type = 0;
			} else {
				$type = 1;
			}

			if ($type == 0) {
				$documentType = nanoP::OPERATION_PRIHOD;
			} else {
				$documentType = nanoP::OPERATION_VOZVRAT_PRIHODA;
			}
			$order_number = $order->get_order_number();

			$checkId = $this->getCheckId($order_number, $type);

			$total_sum = 0;
			$iterator = 0;
			$discount = 0;

			// add1
			$allItemsArray = array();

			// mod1
			foreach($order_data['line_items'] as $key => $item) {
				$item_data = $item->get_data();
				$iterator++;
				if($iterator == count($order_data['line_items'])){
                    $subtotal = round($order_data['total'],2) - round($order_data['shipping_total'] * (1 + $discount),2) - $total_sum;
                    $final = round($subtotal/$item_data['quantity'],2);
                } else {
                	$subtotal = $item_data['total'];
                    $final = round($item_data['total']/$item_data['quantity'],2);
                }
                $total_sum += $subtotal;

                // add2

                if ($this->product_nds == 70) {
					$nds = $this->getProdAttr($item->get_product()->get_ID(), "stavka_nds");
				} else {
					$nds = $this->product_nds;
				}


				if ($this->priznak_sposoba_rascheta == 70) {
					$priznak_sposoba_rascheta = $this->getProdAttr($item->get_product()->get_ID(), "priznak_sposoba_rascheta");
				} else {
					$priznak_sposoba_rascheta = intval($this->priznak_sposoba_rascheta);
				}


				if ($this->priznak_sposoba_rascheta == 70) {
					$priznak_predmeta_rascheta = $this->getProdAttr($item->get_product()->get_ID(), "priznak_predmeta_rascheta");
				} else {
					$priznak_predmeta_rascheta = intval($this->priznak_predmeta_rascheta);
				}

				if ($this->priznak_agenta == 70) {
					$priznak_agenta = $this->getProdAttr($item->get_product()->get_ID(), "priznak_agenta");
				} else {
					$priznak_agenta = intval($this->priznak_agenta);
				}

                $phone_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "phone_oper_perevoda");
                if (empty($phone_oper_perevoda)) {
                	if (isset($this->phone_oper_perevoda)) {
                		$phone_oper_perevoda = $this->phone_oper_perevoda;
                	} else {
                		$phone_oper_perevoda = '';
                	}
                }

                $operation_plat_agenta = $this->getProdAttr($item->get_product()->get_ID(), "operation_plat_agenta");
                if (empty($operation_plat_agenta)) {
                	if (isset($this->operation_plat_agenta)) {
                		$operation_plat_agenta = $this->operation_plat_agenta;
                	} else {
                		$operation_plat_agenta = '';
                	}
                	
                }

                $phone_oper_priem_plat = $this->getProdAttr($item->get_product()->get_ID(), "phone_oper_priem_plat");
                if (empty($phone_oper_priem_plat)) {
                	if (isset($this->phone_oper_priem_plat)) {
                		$phone_oper_priem_plat = $this->phone_oper_priem_plat;
                	} else {
                		$phone_oper_priem_plat = '';
                	}
                }

                $name_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "name_oper_perevoda");
                if (empty($name_oper_perevoda)) {
                	if (isset($this->name_oper_perevoda)) {
                		$name_oper_perevoda = $this->name_oper_perevoda;
                	} else {
                		$name_oper_perevoda = '';
                	}
                	
                }

                $address_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "address_oper_perevoda");
                if (empty($address_oper_perevoda)) {
                	if (isset($this->address_oper_perevoda)) {
                		$address_oper_perevoda = $this->address_oper_perevoda;
                	} else {
                		$address_oper_perevoda = '';
                	}
                }

                $inn_oper_perevoda = $this->getProdAttr($item->get_product()->get_ID(), "inn_oper_perevoda");
                if (empty($inn_oper_perevoda)) {
                	if (isset($this->inn_oper_perevoda)) {
                		$inn_oper_perevoda = $this->inn_oper_perevoda;
                	} else {
                		$inn_oper_perevoda = '';
                	}
                }

                $phone_postavshika = $this->getProdAttr($item->get_product()->get_ID(), "phone_postavshika");
                if (empty($phone_postavshika)) {
                	if (isset($this->phone_postavshika)) {
                		$phone_postavshika = $this->phone_postavshika;
                	} else {
                		$phone_postavshika = '';
                	}
                }

                $name_postavshika = $this->getProdAttr($item->get_product()->get_ID(), "name_postavshika");
                if (empty($name_postavshika)) {
                	if (isset($this->name_postavshika)) {
                		$name_postavshika = $this->name_postavshika;
                	} else {
                		$name_postavshika = '';
                	}
                }

				$kolvo = $item_data['quantity'];
                //$price_piece = round($final*100);
                

				$price_piece_bez_skidki = round( ($item_data['subtotal'] + $item_data['subtotal_tax'])/$item_data['quantity'], 2)*100;
                $price_piece = round( ($item_data['total'] + $item_data['total_tax']) /$item_data['quantity'], 2)*100;
                $skidka = $price_piece_bez_skidki-$price_piece;
                $summa = round( ($item_data['total'] + $item_data['total_tax']),2) * 100;


                $itemArray = [
                	"name_tovar" => $item_data['name'],
                    "price_piece_bez_skidki" => $price_piece_bez_skidki,
                    "skidka" => $skidka,
                    "kolvo" => $kolvo,
                    "price_piece" => $price_piece,
                    "summa" => $summa,
                    "stavka_nds" => $nds,
                    "priznak_sposoba_rascheta" => $priznak_sposoba_rascheta,
                    "priznak_predmeta_rascheta" => $priznak_predmeta_rascheta,
                    "priznak_agenta" => $priznak_agenta,
                    "phone_oper_perevoda" => $phone_oper_perevoda,
                    "operation_plat_agenta" => $operation_plat_agenta,
                    "phone_oper_priem_plat" => $phone_oper_priem_plat,
                    "name_oper_perevoda" => $name_oper_perevoda,
                    "address_oper_perevoda" => $address_oper_perevoda,
                    "inn_oper_perevoda" => $inn_oper_perevoda,
                    "phone_postavshika" => $phone_postavshika,
                    "name_postavshika" => $name_postavshika,
                    "discount" => array('type' => "amount", "value" => $skidka)
                ];

                $allItemsArray[] = $itemArray;
			}

			if ($order_data['shipping_total'] > 0) {

				$priznak_sposoba_rascheta_dost = $this->priznak_sposoba_rascheta;
				if ($priznak_sposoba_rascheta_dost == 70) {
					$priznak_sposoba_rascheta_dost = 4;
				}

				$priznak_predmeta_rascheta_dost = $this->priznak_predmeta_rascheta;
				if ($priznak_predmeta_rascheta_dost == 70) {
					$priznak_predmeta_rascheta_dost = 4;
				}

				$ship_tot_tax = $order_data['shipping_total'] + $order_data['shipping_tax'];

				$itemArray = [
					"name_tovar" => $item_data['name'],
                    "price_piece_bez_skidki" => round($ship_tot_tax * 100),
                    "skidka" => 0,
                    "kolvo" => 1,
                    "price_piece" => round($ship_tot_tax * 100),
                    "summa" => round($ship_tot_tax * 100),
                    "stavka_nds" => intval($this->dostavka_nds),
                    "priznak_sposoba_rascheta" => $priznak_sposoba_rascheta_dost,
                    "priznak_predmeta_rascheta" => $priznak_predmeta_rascheta_dost,
                    "priznak_agenta" => 'none',
                    "phone_oper_perevoda" => '',
                    "operation_plat_agenta" => '',
                    "phone_oper_priem_plat" => '',
                    "name_oper_perevoda" => '',
                    "address_oper_perevoda" => '',
                    "inn_oper_perevoda" => '',
                    "phone_postavshika" => '',
                    "name_postavshika" => '',
                    "discount" => array('type' => "amount", "value" => 0)
                ];

                $allItemsArray[] = $itemArray;

			}

			if (isset($this->type_check)) {
				$type_check = $this->type_check;
			} else {
				$type_check = 1;
			}

			if ($type_check == 1) {
				$type_check_txt = 'email';
			} elseif ($type_check == 2) {
				$type_check_txt = 'phone';
			} else {
				$type_check_txt = 'email';
			}

			if ( isset($order_data['id']) ) {
				$name_zakaz = '#'.intval($order_data['id']);
			} else {
				$name_zakaz = '';
			}

			$request_part1 = '{
					"kassaid": "'.$this->kassaid.'",
					"kassatoken": "'.$this->kassatoken.'",
					"cms": "wordpress",
					"check_send_type": "'.$type_check_txt.'",
					"name_zakaz": "'.$name_zakaz.'",
					"oplata_arr": {
						"rezhim_nalog": "'.$this->rezhim_nalog.'",
						"money_nal": 0,
						"money_electro": '.($order_data["total"]*100).',
						"money_predoplata": 0,
						"money_postoplata": 0,
						"money_vstrecha": 0,
						"client_email": "'.$order_data['billing']['email'].'",
						"client_phone": "'.$order_data['billing']['phone'].'"
						},
					"itog_arr": {
						"priznak_rascheta": '.$documentType.',
						"itog_cheka": '.($order_data["total"]*100).'
						},
	        		"products_arr": '.json_encode($allItemsArray, JSON_UNESCAPED_UNICODE).'';
			

			if ($documentType == nanoP::OPERATION_PRIHOD) {
				$request_part2 = '}';
			} elseif ($documentType == nanoP::OPERATION_VOZVRAT_PRIHODA) {
				$date_paid = $order_data['date_paid'];
				$date_paid_timestamp = $date_paid->getTimestamp();
				$date_paid_day = date("Y-m-d", $date_paid_timestamp);
				$date_paid_day_timestamp = strtotime($date_paid_day);

				$meta_order = $order_data['meta_data'];
				$order77 = wc_get_order($order_data['id']);

				if ( !empty($order77->get_meta('custom_name_doc_korr')) ) {
					$meta_name_doc_korr = $order77->get_meta('custom_name_doc_korr');
				}

				if ( !empty($order77->get_meta('custom_number_doc_korr')) ) {
					$meta_num_doc_korr = $order77->get_meta('custom_number_doc_korr');
				}


				if ( empty($meta_name_doc_korr) ) {
					if ( isset($this->$name_doc_vozvrat) ) {
						$meta_name_doc_korr = $this->$name_doc_vozvrat;
					} else {
						$meta_name_doc_korr = 'Коррекция';
					}
				}

				if ( empty($meta_num_doc_korr) ) {
					if ( isset($this->$num_doc_vozvrat) ) {
						$meta_num_doc_korr = $this->$num_doc_vozvrat;
					} else {
						$meta_num_doc_korr = '1';
					}
				}

				$request_part2 = ', "korr_arr": {
					"name_doc_korr" : "'.$meta_name_doc_korr.'",
					"date_doc_korr" : "'.$date_paid_day_timestamp.'",
					"num_doc_korr" : "'.$meta_num_doc_korr.'"
				}}';
			}
			$request = $request_part1.$request_part2;

			$this->sendData($request,$checkId);
		}
	}

	public function sendData($request, $chid) {
		global $wpdb;

		$first_request = json_decode($request, true);
		$first_request['kassaid'] = 'removed_from_logs';
		$first_request['kassatoken'] = 'removed_from_logs';
		$first_request['oplata_arr']['client_email'] = 'removed_from_logs';
		$first_request['oplata_arr']['client_phone'] = 'removed_from_logs';

    	$firstcrypt = nanoF::crypt_nanokassa_first($request);
    	$returnDataAB = $firstcrypt[0];
    	$returnDataDE = $firstcrypt[1];
    	
    	$request2 = '{
    		"ab":"'.$returnDataAB.'",
    		"de":"'.$returnDataDE.'",
    		"kassaid":"'.$this->kassaid.'",
    		"kassatoken":"'.$this->kassatoken.'",
    		"test":"0"}';

    	$secondcrypt = nanoF::crypt_nanokassa_second($request2);
    	$returnDataAAB = $secondcrypt[0];
    	$returnDataDE2 = $secondcrypt[1];

    	$request = '{
		    "aab":"'.$returnDataAAB.'",
		    "dde":"'.$returnDataDE2.'",
		    "test":"0"
		}';

		// $ur_to = "https://nanokassa.ru/srv/igd.php";
		$ur_to = nanoP::URL_TO_SEND_TO_NANOKASSA;

		$args_curl = array(
			'body'=> $request,
			'timeout' => '20',
			'redirection' => '20',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
				'Content-type' => 'application/json'
			),
			'cookies' => array()
		);

		$response = wp_remote_post($ur_to, $args_curl);

/*
		$curl = curl_init();
		@curl_setopt($curl, CURLOPT_URL, $ur_to);
	    @curl_setopt($curl, CURLOPT_HTTPHEADER,
	                                array("Content-type: application/json"));
	    @curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
	    @curl_setopt($curl, CURLOPT_TIMEOUT, 20);
	    @curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    @curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    @curl_setopt($curl, CURLOPT_POST, true);
	    @curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
	    // Ответ от сервера
	    $response = @curl_exec($curl);
	    curl_close($curl);
*/


	    if($this->log) {
			$this->writeLog('request', $first_request);
			$this->writeLog('response', $response);
		}

		$response_js = json_decode($response, true);

		if ( isset($response_js['status']) ) {
			$response_status = $response_js['status'];
			if ($response_status == 'error') {
				$response_ans = $response_js['error'];
			}
			if ($response_status == 'success') {
				$response_ans = $response_js['success'];
			}
		}

		// if($response == '{"status":"success","success":"added army record to nanokassa.ru"}') {
		if ( isset($response_status) ) {
			if($response_status == 'success') {
				$wpdb->query("update {$wpdb->prefix}nanokassa_checki set status=1 where id=".$chid);
				$this->writeLog('success', $response_ans);
			} else {
				$this->writeLog('error', $response_ans);
			}
		}
		

	}

	public function writeLog($type, $data) {
		$now = time();
		@file_put_contents(__DIR__.'/logs/'.$type.'_'.date('d_m_y',$now).'.txt', date("y-m-d H:i:s\n").var_export($data, true)."\n", FILE_APPEND);
	}

	public function getCheckId($order_number, $type = 0) {
		global $wpdb;
		$result = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}nanokassa_checki where order_number='".esc_sql($order_number)."' and type='".esc_sql($type)."'");
		if($result){
			return strval($result->id);
		} else {
		$wpdb->query("
				INSERT INTO {$wpdb->prefix}nanokassa_checki (order_number, type, status, date) values ('".esc_sql($order_number)."','".esc_sql($type)."',0,NOW())");
			return strval($wpdb->insert_id);
		}
	}

}


function Nanokassa() {
	return NanokassaMainClass::instance();
}

$GLOBALS['nanokassa'] = Nanokassa();