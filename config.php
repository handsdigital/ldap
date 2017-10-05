<?php
/*
 * Informações de acesso ao LDAP
 */
define("_LDAP_DN", "dc=contaminima,dc=com");
define("_LDAP_SERVER", "192.168.25.210");
define("_LDAP_PORT", "389");
define("_LDAP_OS", "WINDOWS"); //WINDOWS ou LINUX
/*
 * _LDAP_USER
 * Padrão Linux: cn=admin,dc=teste,dc=contaminima,dc=com,dc=br
 * Padrão Windows: admin@contaminima.com.br
 */
define("_LDAP_USER", "teste@contaminima.com");
define("_LDAP_PASS", "senha");
define("_LDAP_HASH", "75c9c484b4c04d3a4af8ef64611c3bfc");

/*
 * Filtro para Buscar Colaboradores
 */
define("_LDAP_FILTRO_USER", "(objectClass=person)");

/*
 * Campos dos dados do colaborador
 */
define("_LDAP_EMAIL", _LDAP_OS == 'WINDOWS'?"userprincipalname":"mail");
define("_LDAP_USUARIO", _LDAP_OS == 'WINDOWS'?"samaccountname":"uid");
define("_LDAP_NOME", "displayname");
define("_LDAP_GRUPO", "title");