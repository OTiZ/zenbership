<?php

/**
 *    Zenbership
 *    http://www.zenbership.com/
 *    (c) 2012, Castlamp.
 *
 *    Purpose: User management page:
 *    -> Update Account
 *
 *    WARNING!
 *    DO NOT EDIT THIS FILE!
 *    To change the calendar's
 *    apperance, please edit the
 *    program templates from the
 *    "Integration" section of the
 *    admin control panel.
 *
 */
// Load the basics
require "../admin/sd-system/config.php";
// Check a user's session
$session = new session;
$ses     = $session->check_session();
if ($ses['error'] == '1') {
    $session->reject('login', $ses['ecode']);
    exit;
} else {
    // Member
    $user   = new user;
    $member = $user->get_user($ses['member_id']);
    /**
     * Pagination
     */
    $add_get = array();
    $filters = array(
        'ppSD_cart_sessions.member_id' => array('scope' => 'AND', 'value' => $ses['member_id'], 'eq' => 'eq'),
        'ppSD_cart_sessions.status'    => array('scope' => 'AND', 'value' => '1', 'eq' => 'eq'),
    );
    $join    = array(
        'table'    => 'ppSD_cart_session_totals',
        'on'       => 'id',
        'table_id' => 'id'
    );
    if (!empty($_GET['organize'])) {
        if ($_GET['organize'] == 'date_rl') {
            $_GET['order'] = 'ppSD_cart_sessions.date_completed';
            $_GET['dir']   = 'ASC';
        } else if ($_GET['organize'] == 'total_low') {
            $_GET['order'] = 'ppSD_cart_session_totals.total';
            $_GET['dir']   = 'ASC';
        } else if ($_GET['organize'] == 'total_high') {
            $_GET['order'] = 'ppSD_cart_session_totals.total';
            $_GET['dir']   = 'DESC';
        } else {
            $_GET['order'] = 'ppSD_cart_sessions.date_completed';
            $_GET['dir']   = 'DESC';
        }
        $add_get['organize'] = $_GET['organize'];
    }
    if (empty($_GET['organize'])) {
        $_GET['organize'] = '';
    }
    if (empty($_GET['order'])) {
        $_GET['order'] = 'ppSD_cart_sessions.date_completed';
    }
    if (empty($_GET['dir'])) {
        $_GET['dir'] = 'DESC';
    }
    if (empty($_GET['display'])) {
        $_GET['display'] = '24';
    }
    $paginate  = new pagination('ppSD_cart_sessions', 'manage/billing_history.php', $add_get, $_GET, $filters, $join);
    $cart      = new cart;
    $formatted = '';
    $STH       = $db->run_query($paginate->{'query'});
    while ($row = $STH->fetch()) {
        $order   = $cart->get_order($row['id'], '0');
        $changes = array(
            'data'    => $order['data'],
            'pricing' => $order['pricing'],
        );
        $formatted .= new template('manage_billing_history_entry', $changes, '0');
        //pa($order);
    }
    // Billing History
    //pa($pagination);
    // Template
    $changes = array(
        'sales'      => $formatted,
        'pagination' => $paginate->{'rendered_pages'}
    );
    $wrapper = new template('manage_billing_history', $changes, '1');
    echo $wrapper;
    exit;
}

