<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpsc_purchlog_statuses;
$tabs = array(
    'general'       => 'Основные',
);

if(function_exists("wc_get_order_statuses")) {
    $tabs['woocommerce'] = 'WooCommerce';
}

if (isset($wpsc_purchlog_statuses)) {
    $tabs['wpec'] = 'WP eCommerce';
}
        
$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );
?>
    
<div class="wrap">
    <form method="POST" id="mainform" action="" enctype="multipart/form-data">
            <nav class="nav-tab-wrapper">
                <?php
                    foreach ( $tabs as $name => $label ) {
                        echo '<a href="' . admin_url( 'admin.php?page=nanokassa-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
                    }
                ?>
            </nav>
        <h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
            <?php do_action( 'nanokassa_settings_' . $current_tab ); ?>
        <p class="submit">
            <?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
            <input name="save" class="button-primary" type="submit" value="Сохранить" />
            <?php endif; ?>
            <?php wp_nonce_field( 'nanokassa-settings' ); ?>
        </p>
    </form>
</div>