<?php

include './config.php';
include './ldap.php';
header('Content-Type: application/json');
if (!isset($_REQUEST['acao'])) {
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