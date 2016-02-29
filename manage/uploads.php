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
        'ppSD_uploads.item_id' => array('scope' => 'AND', 'value' => $ses['member_id'], 'eq' => 'eq'),
        'ppSD_uploads.cp_only' => array('scope' => 'AND', 'value' => '1', 'eq' => 'neq'),
    );
    if (!empty($_GET['label'])) {
        $filters['ppSD_uploads.label'] = array('scope' => 'AND', 'value' => $_GET['label'], 'eq' => 'eq');
    }
    $join = array();
    if (!empty($_GET['organize'])) {
        if ($_GET['organize'] == 'date_rl') {
            $_GET['order'] = 'ppSD_uploads.date';
            $_GET['dir']   = 'DESC';
        } else if ($_GET['organize'] == 'date_rf') {
            $_GET['order'] = 'ppSD_uploads.date';
            $_GET['dir']   = 'ASC';
        } else if ($_GET['organize'] == 'name_za') {
            $_GET['order'] = 'ppSD_uploads.name';
            $_GET['dir']   = 'DESC';
        } else {
            $_GET['order'] = 'ppSD_uploads.name';
            $_GET['dir']   = 'ASC';
        }
        $add_get['organize'] = $_GET['organize'];
    }
    if (empty($_GET['organize'])) {
        $_GET['organize'] = '';
    }
    if (empty($_GET['order'])) {
        $_GET['order'] = 'ppSD_uploads.date';
    }
    if (empty($_GET['dir'])) {
        $_GET['dir'] = 'DESC';
    }
    if (empty($_GET['display'])) {
        $_GET['display'] = '24';
    }
    $paginate  = new pagination('ppSD_uploads', 'manage/uploads.php', $add_get, $_GET, $filters, $join);
    $uploads   = new uploads;
    $formatted = '';
    $STH       = $db->run_query($paginate->{'query'});
    while ($row = $STH->fetch()) {
        $changes = $uploads->get_upload($row['id']);
        $formatted .= new template('manage_uploads_entry', $changes, '0');
    }
    // Template
    $changes = array(
        'files'      => $formatted,
        'pagination' => $paginate->{'rendered_pages'}
    );
    $wrapper = new template('manage_uploads', $changes, '1');
    echo $wrapper;
    exit;

}

