<?php

class acl_ldap {

    private $ldapOS;  // WINDOWS ou LINUX
    private $baseDN; // DN do LDAP
    private $ldapHost;  // IP ou host do LDAP
    private $ldapPort;                 // Porta do LDAP
    private $ldapMasterPassword;
    private $ldapMasterUser;
    private $username;
    private $mail;
    private $conn;
    private $erro;

    public function __construct() {

        $this->baseDN = _LDAP_DN;
        $this->ldapHost = _LDAP_SERVER;
        $this->ldapOS = _LDAP_OS;
        $this->ldapPort = _LDAP_PORT;
        $this->ldapMasterUser = _LDAP_USER;
        $this->ldapMasterPassword = _LDAP_PASS;
        
        $this->erro = "";

        $this->username = _LDAP_USUARIO;
        $this->mail = _LDAP_EMAIL;
        $this->nome = _LDAP_NOME;
        $this->grupo = _LDAP_GRUPO;
        $this->ldapOS = _LDAP_OS;


        $this->connect();
    }

    protected function connect() {
        $conn = ldap_connect($this->ldapHost, $this->ldapPort);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

        $this->conn = $conn;
        $this->bind();
    }

    public function getUsers($filtro = _LDAP_FILTRO_USER) {
        $buscar = array(_LDAP_EMAIL,_LDAP_GRUPO, _LDAP_NOME,_LDAP_USUARIO);
        $busca = ldap_search($this->conn, _LDAP_DN, $filtro,$buscar);
        $users = ldap_get_entries($this->conn, $busca);
        $retorno = array();
        
        foreach ($users as $v) {
            if(!isset($v[_LDAP_NOME][0]) or !isset($v[_LDAP_EMAIL][0])){
                
                continue;
            }
            $retorno[] = array(
                'nome' => $v[_LDAP_NOME][0],
                'email' => $v[_LDAP_EMAIL][0],
                'usuario' => $v[_LDAP_USUARIO][0],
                'grupo' => $v[_LDAP_GRUPO][0],
            );
        }
        
        return $retorno;
    }
    
    public function getGrupos(){
        $users = $this->getUsers();
        $grupos = array();
        foreach ($users as $v) {
            $grupos[$v['grupo']] = $v['grupo'];
        }
        $retorno = array();
        foreach ($grupos as $v) {
            $retorno[]['nome'] = $v;
        }
        
        return $retorno;
    }

    protected function bind() {
        ldap_bind($this->conn, $this->ldapMasterUser, $this->ldapMasterPassword)
                or ( $this->erro .= "Não foi possível conectar no LDAP, solicite ao ADMINISTRADOR de sua rede para verificar.\n" . ldap_error($this->conn));
    }

    public function getLogin($user, $password) {
        $filter = "$this->username=$user";
        $result = ldap_search($this->conn, $this->baseDN, $filter);

        if (isset($_REQUEST['ldap']))
            print_r($result);

        $info = ldap_get_entries($this->conn, $result);

        /*
         * Verifica se existe a conta
         */
        $retorno = array();
        $retorno['logged'] = false;

        if ($info["count"] > 0) {
            /*
             * Verifica se a senha e usuário são validos
             */
            $retorno['exist'] = true;
            $retorno['usuario'] = $info[0]["$this->username"][0];
            $retorno['email'] = $info[0]["$this->mail"][0]; //displayname
            $retorno['nome'] = $info[0]["$this->nome"][0];
            $retorno['grupo'] = $info[0]["$this->grupo"][0];

            $userLogin = ($this->ldapOS == "WINDOWS") ? $info[0]["$this->mail"][0] : $info[0]["dn"];

            if (ldap_bind($this->conn, $userLogin, $password)) {

                $retorno['logged'] = true;
            } else {
                $this->erro .= "Senha ou usuário invalido";
                $retorno['logged'] = false;
            }
        } else {
            $retorno['logged'] = false;
            $retorno['exist'] = false;
            $this->erro .= 'Não achou usuário';
        }

        $retorno['erro'] = $this->erro;
        return $retorno;
    }

    public function getMembers($group = FALSE, $inclusive = FALSE) {
        
        // Begin building query
        if ($group)
            $query = "(&";
        else
            $query = "";

        $query .= "(&(objectCategory=person))";

        // Filter by memberOf, if group is set
        if (is_array($group)) {
            // Looking for a members amongst multiple groups
            if ($inclusive) {
                // Inclusive - get users that are in any of the groups
                // Add OR operator
                $query .= "(|";
            } else {
                // Exclusive - only get users that are in all of the groups
                // Add AND operator
                $query .= "(&";
            }

            // Append each group
            foreach ($group as $g)
                $query .= "(memberOf=CN=$g,$this->baseDN)";

            $query .= ")";
        } elseif ($group) {
            // Just looking for membership of one group
            $query .= "(memberOf=CN=$group,$this->baseDN)";
        }

        // Close query
        if ($group)
            $query .= ")";
        else
            $query .= "";

        // Uncomment to output queries onto page for debugging
        // print_r($query);
        // Search AD
        $results = ldap_search($this->conn, $this->baseDN, $query);
        $entries = ldap_get_entries($this->conn, $results);

        // Remove first entry (it's always blank)
        array_shift($entries);

        $output = array(); // Declare the output array

        $i = 0; // Counter
        // Build output array
        foreach ($entries as $u) {
            foreach ($keep as $x) {
                // Check for attribute
                if (isset($u[$x][0]))
                    $attrval = $u[$x][0];
                else
                    $attrval = NULL;

                // Append attribute to output array
                $output[$i][$x] = $attrval;
            }
            $i++;
        }

        return $output;
    }

}
