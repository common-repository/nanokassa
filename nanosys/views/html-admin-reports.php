<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
$paged = isset($_GET['paged'])?intval($_GET['paged']):1;
$perpage = 25;
$from = ($paged-1)*$perpage;
$transactions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}nanokassa_checki limit $from,$perpage");
$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nanokassa_checki WHERE 1 = 1;" );
$total_items = $count;
$total_pages = ceil($total_items/$perpage);
$infinite_scroll = false;
echo "<div class='wrap'>";
echo "<h1>Онлайн-чеки</h1>";
if(!$count) {
    echo "На данный момент пока нет чеков";
    echo "</div>";
    return;
}

$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

$current = $paged;
$removable_query_args = wp_removable_query_args();
$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
$current_url = remove_query_arg( $removable_query_args, $current_url );
$page_links = array();
$total_pages_before = '<span class="paging-input">';
$total_pages_after  = '</span></span>';
$disable_first = $disable_last = $disable_prev = $disable_next = false;

if ( $current == 1 ) {
    $disable_first = true;
    $disable_prev = true;
}

if ( $current == 2 ) {
    $disable_first = true;
}

if ( $current == $total_pages ) {
    $disable_last = true;
    $disable_next = true;
}
        
if ( $current == $total_pages - 1 ) {
    $disable_last = true;
}

if ( $disable_first ) {
    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
} else {
    $page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( remove_query_arg( 'paged', $current_url ) ),
                __( 'First page' ),
                '&laquo;'
    );
}

if ( $disable_prev ) {
    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
} else {
    $page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
                __( 'Previous page' ),
                '&lsaquo;'
    );
}

$which = '123';
if ( 'bottom' === $which ) {
    $html_current_page  = $current;
    $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
} else {
    $html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                $current,
                strlen( $total_pages )
    );
}
        
$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
$page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

if ( $disable_next ) {
    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
} else {
    $page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
                __( 'Next page' ),
                '&rsaquo;'
    );
}

if ( $disable_last ) {
    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
} else {
    $page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
                __( 'Last page' ),
                '&raquo;'
    );
}

$pagination_links_class = 'pagination-links';
if ( ! empty( $infinite_scroll ) ) {
    $pagination_links_class = ' hide-if-js';
}
$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

if ( $total_pages ) {
    $page_class = $total_pages < 2 ? ' one-page' : '';
} else {
    $page_class = ' no-pages';
}
echo '<div class="tablenav top">';
echo "<div class='tablenav-pages{$page_class}'>$output</div>";
echo "</div>";
    
?>

<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th>№</th>
            <th>Номер заказа</th>
            <th>Тип чека</th>
            <th>Статус</th>
            <th>Дата</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($transactions as $data):
        ?>
        <tr class="transactionrow" >
            <td><?php echo $data->id?></td>
            <td><?php echo $data->order_number?></td>
            <td><?php echo (!$data->type)?'Приход':'Возврат'?></td>
            <td><?php echo (!$data->status)?'В процессе':'Фискализирован'?></td>
            <td><?php echo $data->date?></td>
        </tr>
    <?php endforeach;?>
        </tbody>
</table>
</div>