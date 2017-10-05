<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

include './config.php';
include './ldap.php';
header('Content-Type: application/json');
if (!isset($_REQUEST['acao']) or (!isset($_REQUEST['hash']) or $_REQUEST['hash'] != _LDAP_HASH)) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$ldap = new acl_ldap();
if ($_REQUEST['acao'] == 'lista') {
    $user = $ldap->getUsers();
    echo json_encode($user);
}

if ($_REQUEST['acao'] == 'grupo') {
    $user = $ldap->getGrupos();
    echo json_encode($user);
}

if ($_REQUEST['acao'] == 'login') {

    $usuario = trim($_REQUEST['usuario']);
    $senha = trim($_REQUEST['senha']);

    $ad = $ldap->getLogin($usuario, $senha);
    echo json_encode($ad);
}