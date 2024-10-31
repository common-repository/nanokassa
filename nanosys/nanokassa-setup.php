<?php

final class setup_nanokassa {

	public static function checkAttr($slug){
		$attrList = wc_get_attribute_taxonomies();
		foreach ($attrList as $key => $attr) {
			if($attr->attribute_name == $slug){
				return true;
			}
		}
		return false;
	}

	public function process_add_attribute($attribute) {
	    global $wpdb;
	//      check_admin_referer( 'woocommerce-add-new_attribute' );

	    if (empty($attribute['attribute_type'])) { $attribute['attribute_type'] = 'text';}
	    if (empty($attribute['attribute_orderby'])) { $attribute['attribute_orderby'] = 'menu_order';}
	    if (empty($attribute['attribute_public'])) { $attribute['attribute_public'] = 0;}

	    if ( empty( $attribute['attribute_name'] ) || empty( $attribute['attribute_label'] ) ) {
	            return new WP_Error( 'error', __( 'Please, provide an attribute name and slug.', 'woocommerce' ) );
	    } elseif ( ( $valid_attribute_name = setup_nanokassa::valid_attribute_name( $attribute['attribute_name'] ) ) && is_wp_error( $valid_attribute_name ) ) {
	            return $valid_attribute_name;
	    } elseif ( taxonomy_exists( wc_attribute_taxonomy_name( $attribute['attribute_name'] ) ) ) {
	            return new WP_Error( 'error', sprintf( __( 'Slug "%s" is already in use. Change it, please.', 'woocommerce' ), sanitize_title( $attribute['attribute_name'] ) ) );
	    }

	    $wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );

	    do_action( 'woocommerce_attribute_added', $wpdb->insert_id, $attribute );

	    flush_rewrite_rules();
	    delete_transient( 'wc_attribute_taxonomies' );

	    return true;
	}

	public static function activation(){
		if(!setup_nanokassa::checkAttr("stavka_nds")){
			$insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'stavka_nds', 'attribute_label' => 'Ставка НДС', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
			if (is_wp_error($insert)) { do_something_for_error($insert); }
		}

		if(!setup_nanokassa::checkAttr("priznak_sposoba_rascheta")){
			$insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'priznak_sposoba_rascheta', 'attribute_label' => 'Признак способа расчёта', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
			if (is_wp_error($insert)) { do_something_for_error($insert); }
		}

		if(!setup_nanokassa::checkAttr("priznak_predmeta_rascheta")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'priznak_predmeta_rascheta', 'attribute_label' => 'Признак предмета расчёта', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
			if (is_wp_error($insert)) { do_something_for_error($insert); }
		}

		if(!setup_nanokassa::checkAttr("priznak_agenta")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'priznak_agenta', 'attribute_label' => 'Признак агента', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}
		  
		if(!setup_nanokassa::checkAttr("phone_oper_perevoda")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'phone_oper_perevoda', 'attribute_label' => 'Телефон опер. перевода', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}
		  
		if(!setup_nanokassa::checkAttr("operation_plat_agenta")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'operation_plat_agenta', 'attribute_label' => 'Операция плат. агента', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}
		  
		if(!setup_nanokassa::checkAttr("phone_oper_priem_plat")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'phone_oper_priem_plat', 'attribute_label' => 'Телефон опер. принимающего оплату', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}
		  
		if(!setup_nanokassa::checkAttr("name_oper_perevoda")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'name_oper_perevoda', 'attribute_label' => 'Имя опер. перевода', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}

		if(!setup_nanokassa::checkAttr("address_oper_perevoda")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'address_oper_perevoda', 'attribute_label' => 'Адресс опер. перевода', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}
		  
		if(!setup_nanokassa::checkAttr("inn_oper_perevoda")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'inn_oper_perevoda', 'attribute_label' => 'ИНН опер. перевода', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}

		if(!setup_nanokassa::checkAttr("phone_postavshika")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'phone_postavshika', 'attribute_label' => 'Телефон поставщика', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}
	
		if(!setup_nanokassa::checkAttr("name_postavshika")){
		    $insert = setup_nanokassa::process_add_attribute(array('attribute_name' => 'name_postavshika', 'attribute_label' => 'Имя поставщика', 'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => false));
		    if (is_wp_error($insert)) { do_something_for_error($insert); }
		}


		global $wpdb;

		$table_name = $wpdb->prefix . 'nanokassa_checki';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
				id int(11) NOT NULL  NOT NULL AUTO_INCREMENT,
				  order_number varchar(255) NOT NULL,
				  type int(1) NOT NULL,
				  status int(1) not null,
				  date datetime  not null,
				  PRIMARY KEY (id)
			) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$arr_terms1 = array(
							array( 'a0' => 'НДС 18', 
								   'a1' => 1, 
								   'a2' => 255
								),
							array( 'a0' => 'НДС 10', 
								   'a1' => 2, 
								   'a2' => 255
								),
							array( 'a0' => 'НДС 18/118', 
								   'a1' => 3, 
								   'a2' => 255
								),	
							array( 'a0' => 'НДС 10/110', 
								   'a1' => 4, 
								   'a2' => 255
								),				
							array( 'a0' => 'НДС 0', 
								   'a1' => 5, 
								   'a2' => 255
								),
							array( 'a0' => 'Без НДС', 
								   'a1' => 6, 
								   'a2' => 255
								)
						);

		$arr_terms2 = array(
							array( 'a0' => 'Полная предварительная оплата до момента передачи предмета расчета', 
								   'a1' => 1, 
								   'a2' => 256
								),
							array( 'a0' => 'Частичная предварительная оплата до момента передачи предмета расчета', 
								   'a1' => 2, 
								   'a2' => 256
								),
							array( 'a0' => 'Аванс', 
								   'a1' => 3, 
								   'a2' => 256
								),
							array( 'a0' => 'Полная оплата, в том числе с учетом аванса (предварительной оплаты) в момент передачи предмета расчета', 
								   'a1' => 4, 
								   'a2' => 256
								),
							array( 'a0' => 'Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит', 
								   'a1' => 5, 
								   'a2' => 256
								),
							array( 'a0' => 'Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит', 
								   'a1' => 6, 
								   'a2' => 256
								),
							array( 'a0' => 'Оплата предмета расчета после его передачи с оплатой в кредит (оплата кредита)', 
								   'a1' => 7, 
								   'a2' => 256
								)
						);

		$arr_terms3 = array(
							array( 'a0' => 'Реализуемый товар, за исключением подакцизного товара (1)', 
								   'a1' => 1, 
								   'a2' => 257
								),
							array( 'a0' => 'Реализуемый подакцизный товар (2)', 
								   'a1' => 2, 
								   'a2' => 257
								),
							array( 'a0' => 'Выполняемая работа (3)', 
								   'a1' => 3, 
								   'a2' => 257
								),
							array( 'a0' => 'Оказываемая услуга (4)', 
								   'a1' => 4, 
								   'a2' => 257
								),
							array( 'a0' => 'Прием ставок при осуществлении деятельности по проведению азартных игр (5)', 
								   'a1' => 5, 
								   'a2' => 257
								),
							array( 'a0' => 'Выплата денежных средств в виде выигрыша при осуществлении деятельности по проведению азартных игр (6)', 
								   'a1' => 6, 
								   'a2' => 257
								),
							array( 'a0' => 'Выплата денежных средств в виде выигрыша при осуществлении деятельности по проведению азартных игр (6)', 
								   'a1' => 7, 
								   'a2' => 257
								),
							array( 'a0' => 'Выплата денежных средств в виде выигрыша при осуществлении деятельности по проведению лотерей (8)', 
								   'a1' => 8, 
								   'a2' => 257
								),
							array( 'a0' => 'Предоставление прав на использование результатов интеллектуальной деятельности или средств индивидуализации (9)', 
								   'a1' => 9, 
								   'a2' => 257
								),
							array( 'a0' => 'Аванс, задаток, предоплата, кредит, взнос в счет оплаты, пеня, штраф, вознаграждение, бонус и иной аналогичный предмет расчета (10)', 
								   'a1' => 10, 
								   'a2' => 257
								),
							array( 'a0' => 'Вознаграждение пользователя, являющимся платежным агентом (11)', 
								   'a1' => 11, 
								   'a2' => 257
								),
							array( 'a0' => 'Предмет расчета, состоящем из предметов, каждому из которых может быть присвоено значение от «1» до «11» (12)', 
								   'a1' => 12, 
								   'a2' => 257
								),
							array( 'a0' => 'Предмет расчета, не относящемуся к предметам расчета, которым может быть присвоено значение от «1» до «12» (13)', 
								   'a1' => 13, 
								   'a2' => 257
								)
						);

		$arr_terms4 = array(
							array( 'a0' => 'Пользователь ККТ не является платежным агентом по всем предметам расчета', 
								   'a1' => 40, 
								   'a2' => 258
								),
							array( 'a0' => 'Оказание услуг покупателю (клиенту) пользователем, являющимся банковским платежным агентом', 
								   'a1' => 0, 
								   'a2' => 258
								),
							array( 'a0' => 'Оказание услуг покупателю (клиенту) пользователем, являющимся банковским платежным субагентом', 
								   'a1' => 1, 
								   'a2' => 258
								),
							array( 'a0' => 'Оказание услуг покупателю (клиенту) пользователем, являющимся платежным агентом', 
								   'a1' => 2, 
								   'a2' => 258
								),
							array( 'a0' => 'Оказание услуг покупателю (клиенту) пользователем, являющимся платежным субагентом', 
								   'a1' => 3, 
								   'a2' => 258
								),
							array( 'a0' => 'Осуществление расчета с покупателем (клиентом) пользователем, являющимся поверенным', 
								   'a1' => 4, 
								   'a2' => 258
								),
							array( 'a0' => 'Осуществление расчета с покупателем (клиентом) пользователем, являющимся комиссионером', 
								   'a1' => 5, 
								   'a2' => 258
								),
							array( 'a0' => 'Осуществление расчета с покупателем (клиентом) пользователем, являющимся агентом и не являющимся банковским платежным агентом', 
								   'a1' => 6, 
								   'a2' => 258
								)
						);


		$arr_mall = array(
				"pa_stavka_nds" => $arr_terms1, 
				"pa_priznak_sposoba_rascheta" => $arr_terms2,
				"pa_priznak_predmeta_rascheta" => $arr_terms3,
				"pa_priznak_agenta_list" => $arr_terms4
		);

		$table_name_terms = $wpdb->prefix . 'terms';
		$table_name_term_taxonomy = $wpdb->prefix . 'term_taxonomy';

		foreach ($arr_mall as $k0 => $v0) {
			$stt = $k0;
			foreach ($v0 as $k1 => $v1) {
				// $sql_term1 = $wpdb->prepare("INSERT INTO %s (name, slug, term_group) VALUES (%s);", $table_name_terms, $v1);
				// $wpdb->query($sql_term1);

				// $sql_term2 = $wpdb->prepare("SELECT LAST_INSERT_ID()");
				// $tmm = intval( $wpdb->get_results($sql_term2) );

				// $sql_term3 = $wpdb->prepare("INSERT INTO %s (term_id, taxonomy) VALUES (%s, %s)", $table_name_term_taxonomy, $tmm, $stt);
				// $wpdb->query($sql_term3);

				$aa0 = $v1['a0'];
				$aa1 = $v1['a1'];
				$aa2 = $v1['a2'];

				$sql_term0 = $wpdb->get_var("SELECT COUNT(*) FROM $table_name_terms WHERE slug='$aa1' AND term_group=$aa2");

				if ($sql_term0 > 0) {
				} else {
					$sql_term1 = $wpdb->insert(
						$table_name_terms,
						array (
							'name' => $aa0,
							'slug' => $aa1,
							'term_group' => $aa2
						),
						array (
							'%s',
							'%s',
							'%d'
						)
					);
				}

				$sql_term2 = $wpdb->get_var("SELECT term_id FROM $table_name_terms WHERE slug='$aa1' AND term_group=$aa2 LIMIT 1");
				$tmm2 = intval($sql_term2);

				if ($tmm2 > 0) {
					$sql_term3 = $wpdb->insert(
						$table_name_term_taxonomy,
						array (
							'term_id' => $tmm2,
							'taxonomy' => $stt,
							'description' => 'sss'
						),
						array (
							'%s',
							'%s',
							'%s'
						)
					);
				}


			}
		}

	}


	

	public function valid_attribute_name( $attribute_name ) {
	    if ( strlen( $attribute_name ) >= 28 ) {
	            return new WP_Error( 'error', sprintf( __( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) ) );
	    } elseif ( wc_check_if_attribute_name_is_reserved( $attribute_name ) ) {
	            return new WP_Error( 'error', sprintf( __( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) ) );
	    }

	    return true;
	}

}