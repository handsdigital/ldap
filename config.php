<?php
/*
 * Informações de acesso ao LDAP
 */
define("_LDAP_DN", "dc=teste,dc=contaminima,dc=com,dc=br");
define("_LDAP_SERVER", "192.168.1.14");
define("_LDAP_PORT", "389");
define("_LDAP_OS", "LINUX"); //WINDOWS ou LINUX
define("_LDAP_USER", "cn=admin,dc=teste,dc=contaminima,dc=com,dc=br");
define("_LDAP_PASS", "senha");

/*
 * Filtro para Buscar Colaboradores
 */
define("_LDAP_FILTRO_USER", "objectClass=person");

/*
 * Campos dos dados do colaborador
 */
define("_LDAP_EMAIL", "mail");
define("_LDAP_USUARIO", "uid");
define("_LDAP_NOME", "displayname");
define("_LDAP_GRUPO", "title");