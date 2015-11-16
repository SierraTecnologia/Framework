<?php
class usuario_Modelo extends \Framework\App\Modelo
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
    */
    public function __construct() {
        parent::__construct();
    } 
    /**
    * Retorna todos os usuarios
    * 
    * @name retorna_usuarios
    * @access public
    * 
    * @uses MYSQL_USUARIOS
    * @uses \Framework\App\Modelo::$usuario
    * @uses \Framework\App\Modelo::$db
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
     * #update
    */
    
    /**
     * Insere usuarios no Banco de Dados
     * 
     * @name usuarios_inserir
     * @access public
     * 
     * @return int
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function usuarios_inserir($tipo = 'cliente') {
        GLOBAL $config;
        $this->db->query('INSERT INTO '.MYSQL_USUARIOS.' '.$this->mysqlInsertCampos($this->campos));

        
       if ($tipo!='cliente') {
            $sql = $this->db->query('SELECT id FROM '.MYSQL_USUARIOS.' WHERE email=\''.\Framework\App\Conexao::anti_injection($_POST['email']).'\' ORDER BY id DESC LIMIT 1');
            while ($campo = $sql->fetch_object()) {
               $id = $campo->id;
            }
            $dt_vencimento = date("Y-m-d", time() + (FINANCEIRO_DIASVENCIMENTO * 86400));
            Financeiro_Modelo::MovInt_Inserir($this, $id, $valor,0,'usuario',\Framework\App\Conexao::anti_injection($_POST['grupo']), $dt_vencimento);
        }
        return 1;
    }
    /**
     * 
     * @param type $Modelo
     * @param type $email
     * @return int
     */
    static function VerificaExtEmail(&$Modelo, $email) {
        if (!is_object($Modelo)) {
            throw new \Exception('Classe Modelo não Recebida',2800);
        }
        $sql = $Modelo->db->query(' SELECT id
        FROM '.MYSQL_USUARIOS.'
        WHERE deletado!=1 AND email=\''.$email.'\''); //P.categoria
        while ($campo = $sql->fetch_object()) {
            return TRUE;
        }
        return FALSE;
    }
    /**
     * 
     * @param type $Modelo
     * @param type $login
     * @return int
     */
    static function VerificaExtLogin(&$Modelo, $login) {
        if (!is_object($Modelo)) {
            throw new \Exception('Classe Modelo não Recebida',2800);
        }
        $sql = $Modelo->db->query(' SELECT id
        FROM '.MYSQL_USUARIOS.'
        WHERE deletado!=1 AND login=\''.$login.'\''); //P.categoria
        while ($campo = $sql->fetch_object()) {
            return TRUE;
        }
        return FALSE;
    }
    /**
     * 
     * @param type $Modelo
     * @param type $usuarioid
     * @param type $motivoid
     * @return int
     */
    static function Financeiro($usuarioid, $motivoid) {
        $usuarioid = (int) $usuarioid;
        $Registro = &\Framework\App\Registro::getInstacia();
        $Modelo = &$Registro->_Modelo;
        if (!isset($usuarioid) || !is_int($usuarioid) || $usuarioid==0) return 0;
        return 1;
    }
    /**
     * 
     * @param type $Modelo
     * @param type $usuarioid
     * @param type $motivoid
     */
    static function Financeiro_Motivo_Exibir($motivoid) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $Modelo = &$Registro->_Modelo;
        $text = 'CONFIG_CLI_'.$motivoid.'_NOME';
        return Array('Pagamento do Plano', $text);
    }
    
    
    
    
    
    
    /****
     * Funcoes mais Perfomaticas - 2015
     */
    
    /**
     * Listragem de Usuarios
     * @param type $grupo
     * @param type $ativado
     * @param type $gravidade
     * @param type $inverter
     * @param type $export
     * @throws \Exception
     */
    protected function Usuario_Listagem($grupo = FALSE, $ativado = FALSE, $gravidade=0, $inverter = FALSE) {
        $url_ver = 'usuario/Perfil/Perfil_Show';
        $url_editar='usuario/Admin/Usuarios_Edit';
        $url_deletar='usuario/Admin/Usuarios_Del';
        if ($grupo === FALSE) {
            $categoria = 0;
            if ($inverter) {
                $where = 'ativado!='.$ativado;
            } else {
                $where = 'ativado='.$ativado;
            }
            if ($ativado === FALSE) {
                $where = '';
            }
            $nomedisplay        = __('Usuários ');
            $nomedisplay_sing   = __('Usuário ');
            $nomedisplay_tipo   = __('Usuario');
        } else {
            $categoria = (int) $grupo[0];
            
            // Pega GRUPOS VALIDOS
            //#update - Comer essa Query, nao há necessidade
            $sql_grupos = $this->db->Sql_Select('Sistema_Grupo', 'categoria='.$categoria,0, '', 'id');
            $grupos_id = Array();
            if (is_object($sql_grupos)) $sql_grupos = Array(0=>$sql_grupos);
            if ($sql_grupos !== FALSE && !empty($sql_grupos)) {
                foreach ($sql_grupos as &$valor) {
                    $grupos_id[] = $valor->id;
                }
            }
            
            if (empty($grupos_id)) return _Sistema_erroControle::Erro_Fluxo('Grupos não existe',404);
            
            // cria where de acordo com parametros
            if ($inverter) {
                $where = 'grupo NOT IN ('.implode(', ', $grupos_id).') AND ativado='.$ativado;
            } else {
                $where = 'grupo IN ('.implode(', ', $grupos_id).') AND ativado='.$ativado;
            }
            
            if ($ativado === FALSE) {
                $where = explode(' AND ', $where);
                $where = $where[0];
            }
        
            $nomedisplay        = $grupo[1].' ';
            $nomedisplay_sing   = Framework\Classes\Texto::Transformar_Plural_Singular($grupo[1]);
            $nomedisplay_tipo   = Framework\Classes\Texto::Transformar_Plural_Singular($grupo[1]);
        }
        
        $linkextra = '';
        if ($grupo !== FALSE && $grupo[0]==CFG_TEC_CAT_ID_CLIENTES && $inverter === FALSE) {
            $linkextra = '/cliente';
            $link = 'usuario/Admin/ListarCliente';
            $link_editar = 'usuario/Admin/Cliente_Edit';
            $link_deletar = 'usuario/Admin/Cliente_Del';
            $link_add = 'usuario/Admin/Cliente_Add/'.$categoria;
        }
        else if ($grupo !== FALSE && $grupo[0]==CFG_TEC_CAT_ID_FUNCIONARIOS && $inverter === FALSE) {
            $linkextra = '/funcionario';
            $link = 'usuario/Admin/ListarFuncionario';
            $link_editar = 'usuario/Admin/Funcionario_Edit';
            $link_deletar = 'usuario/Admin/Funcionario_Del';
            $link_add = 'usuario/Admin/Funcionario_Add/'.$categoria;
        } else {
            $link = 'usuario/Admin/ListarUsuario';
            $link_editar = 'usuario/Admin/Usuarios_Edit';
            $link_deletar = 'usuario/Admin/Usuarios_Del';
            $link_add = 'usuario/Admin/Usuarios_Add/'.$categoria;
        }
        
        // Table's primary key
        $primaryKey = 'id';
        $table = 'Usuario';
        
        // Permissoes (Fora Do LOOPING por performace)
        $usuario_Admin_Ativado_Listar   = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Admin_Ativado_Listar');
        $usuario_Admin_Foto             = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Admin_Foto');
        $Financeiro_User_Saldo          = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('Financeiro_User_Saldo');
        $usuario_mensagem_EmailSetor    = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_mensagem_EmailSetor');
        $usuario_Admin_Grupo            = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Grupo_Mostrar');

        // Get Permissoes (Fora Do LOOPING por performace)
        $perm_view          = $this->_Registro->_Acl->Get_Permissao_Url($url_ver);
        $perm_comentario    = $this->_Registro->_Acl->Get_Permissao_Url('usuario/Admin/Usuarios_Comentario');
        $perm_anexo         = $this->_Registro->_Acl->Get_Permissao_Url('usuario/Anexo/Anexar');
        $perm_email         = $this->_Registro->_Acl->Get_Permissao_Url('usuario/Admin/Usuarios_Email');
        $permissionStatus        = $this->_Registro->_Acl->Get_Permissao_Url('usuario/Admin/Status');
        $permissionEdit        = $this->_Registro->_Acl->Get_Permissao_Url($url_editar);
        $permissionDelete           = $this->_Registro->_Acl->Get_Permissao_Url($url_deletar);

        // Verifica Grupo
        $Ativado_Grupo = FALSE;
        if (is_array($usuario_Admin_Grupo)) {
            if ($grupo === FALSE || (is_array($grupo) && in_array($grupo[0], $usuario_Admin_Grupo))) {
                $Ativado_Grupo = TRUE;
            }
        } else {
            if ($usuario_Admin_Grupo === TRUE) {
                $Ativado_Grupo = TRUE;
            }
        }

        // Verifica foto
        $Ativado_Foto = FALSE;
        if (is_array($usuario_Admin_Foto)) {
            if ($grupo === FALSE || (is_array($grupo) && in_array($grupo[0], $usuario_Admin_Foto))) {
                $Ativado_Foto = TRUE;
            }
        } else {
            if ($usuario_Admin_Foto === TRUE) {
                $Ativado_Foto = TRUE;
            }
        }
        
        $columns = Array();
        $numero = -1;
        
        
        
        
        
        
        
        
        
        //// ORIGINAL
        
        ++$numero;
        $columns[] = array( 'db' => 'id', 'dt' => $numero); //'Id';
        
        

        if ($Ativado_Grupo === TRUE) {
            ++$numero;
            $columns[] = array( 'db' => 'grupo2', 'dt' => $numero); //'Grupo';
        }
        if ($Ativado_Foto === TRUE) {
            ++$numero;
            $columns[] = array( 'db' => 'foto', 'dt' => $numero,
                'formatter' => function($d, $row) {
                    if ($d==='' || $d === FALSE) {
                        $foto = WEB_URL.'img'.US.'icons'.US.'clientes.png';
                    } else {
                        $foto = $d;
                    }
                    return '<img src="'.$foto.'" style="max-width:100px;" alt="'.__('Foto de Usuário').' />';
                }
            ); //'Foto';
        }
        
        
        //NOME #UPDATE
        ++$numero;
        $columns[] = array( 'db' => 'nome', 'dt' => $numero); //'Nome';
        // Atualiza Nome
        /*if ($valor->nome!='') {
            $nome .= $valor->nome;
        }
        // Atualiza Razao Social
        if ($valor->razao_social!='') {
            if ($nome!='') $nome .= '<br>';
            $nome .= $valor->razao_social;
        }
        // Se tiver Mensagens
        if (\Framework\App\Sistema_Funcoes::Perm_Modulos('usuario_mensagem')) {
            $nome = '<a href="'.URL_PATH.'usuario_mensagem/Suporte/Mostrar_Cliente/'.$valor->id.'/">'.$nome.' ('.usuario_mensagem_SuporteModelo::Suporte_MensagensCliente_Qnt($valor->id).')</a>';
        }
        // Mostra Nome
        $table['Nome'][$i]             = $nome;
         * 
         */
        
        // TELEFONE #UPDATE
        ++$numero;
        $columns[] = array( 'db' => 'telefone', 'dt' => $numero); //'Telefone';
        /*$telefone = '';
        if ($valor->telefone!='') {
            $telefone .= $valor->telefone;
        }
        if ($valor->telefone2!='') {
            if ($telefone!='') $telefone .= '<br>';
            $telefone .= $valor->telefone1;
        }
        if ($valor->celular!='') {
            if ($telefone!='') $telefone .= '<br>';
            $telefone .= $valor->celular;
        }
        if ($valor->celular1!='') {
            if ($telefone!='') $telefone .= '<br>';
            $telefone .= $valor->celular1;
        }
        if ($valor->celular2!='') {
            if ($telefone!='') $telefone .= '<br>';
            $telefone .= $valor->celular2;
        }
        if ($valor->celular3!='') {
            if ($telefone!='') $telefone .= '<br>';
            $telefone .= $valor->celular3;
        }
        $table['Contato'][$i]         = $telefone;*/
        
        // EMAIL #UPDATE
        
        ++$numero;
        $columns[] = array( 'db' => 'email', 'dt' => $numero); //'Email';
        /*
        $email = '';
        if ($valor->email!='') {
            $email .= $valor->email;
        }
        if ($valor->email2!='') {
            if ($email!='') $email .= '<br>';
            $email .= $valor->email2;
        }
        $table['Email'][$i]      =  $email;*/
        
        
        
        // para MOdulos que contem banco
        if (\Framework\App\Sistema_Funcoes::Perm_Modulos('Financeiro') && $Financeiro_User_Saldo) {
            ++$numero;
            $columns[] = array( 'db' => 'id', 'dt' => $numero,
                'formatter' => function($d, $row) {
                    return Financeiro_Modelo::Carregar_Saldo(Framework\App\Registro::getInstacia()->_Modelo, $d, TRUE);
                }
            ); //'Saldo';
        }
        
        
        
        // Data de Cadastro
        ++$numero;
        $columns[] = array( 'db' => 'log_date_add', 'dt' => $numero,
            'formatter' => function($d, $row) {
                if (strpos($d, APP_DATA_BR) !== FALSE) {
                    return '<b>'.$d.'</b>';
                } else {
                    return $d;
                }
            }
        ); //'Data de Cadastro';

        
        
        
        
        // Funcoes
        $function = '';
        $funcoes_qnt = 0;
        
        // Visualizar
        if ($perm_view) {
            ++$funcoes_qnt;
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Visualizar\'     ,Array(\'Visualizar '.$nomedisplay_sing.'\'        ,\''.$url_ver.'/\'.$row[\'id\'].\''.$linkextra.'/\'    ,\'\'),TRUE);';
        }
        
        // Comentario de Usuario
        if (\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Comentarios') && $perm_comentario) {
            if ($funcoes_qnt>2) {
                $table['Funções'][$i]     .=   '<br>';
                $funcoes_qnt = 0;
            }
            ++$funcoes_qnt;
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Personalizado\'     ,Array(\'Histórico\'        ,\'usuario/Admin/Usuarios_Comentario/\'.$row[\'id\'].\''.$linkextra.'/\'    ,\'\',\'file\',\'inverse\'),TRUE);';
        }
        // Anexo de Usuario
        if (\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Anexo') && $perm_anexo) {
            if ($funcoes_qnt>2) {
                $table['Funções'][$i]     .=   '<br>';
                $funcoes_qnt = 0;
            }
            ++$funcoes_qnt;
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Personalizado\'     ,Array(\'Anexos\'        ,\'usuario/Anexo/Anexar/\'.$row[\'id\'].\''.$linkextra.'/\'    ,\'\',\'file\',\'inverse\'),TRUE);';
        }
        // Email para Usuario
        if (\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Admin_Email') && $perm_email) {
            if ($funcoes_qnt>2) {
                $function .= ' $html .= \'<br>\';';
                $funcoes_qnt = 0;
            }
            ++$funcoes_qnt;
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Email\'     ,Array(\'Enviar email para '.$nomedisplay_sing.'\'        ,\'usuario/Admin/Usuarios_Email/\'.$row[\'id\'].\''.$linkextra.'/\'    ,\'\'),TRUE);';
        }
        // Email para Setor
        if (\Framework\App\Sistema_Funcoes::Perm_Modulos('usuario_mensagem') && $usuario_mensagem_EmailSetor && $perm_email) {
            if ($funcoes_qnt>2) {
                $function .= ' $html .= \'<br>\';';
                $funcoes_qnt = 0;
            }
            ++$funcoes_qnt;
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Email\'     ,Array(\'Enviar email para Setor\'        ,\'usuario/Admin/Usuarios_Email/\'.$row[\'id\'].\''.$linkextra.'/Setor/\'    ,\'\',\'envelope\',\'danger\'),TRUE);';
        }
        // Verifica se Possue Status e Mostra
        if ($usuario_Admin_Ativado_Listar !== FALSE && $permissionStatus) {
            if ($funcoes_qnt>2) {
                $function .= ' $html .= \'<br>\';';
                $funcoes_qnt = 0;
            }
            ++$funcoes_qnt;
            $function .= 'if ($d===1 || $d===\'1\') {';
                $function .= '$texto = \''.$usuario_Admin_Ativado_Listar[1].'\';';
                $function .= '$ativado=\'1\';';
            $function .= ' } else {';
                $function .= ' $ativado = \'0\';';
                $function .= ' $texto = \''.$usuario_Admin_Ativado_Listar[0].'\';';
            $function .= ' }';
            $function .= ' $html .= \'<span id="status\'.$row[\'id\'].\'">\'.Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Status\'.$ativado     ,Array($texto        ,\'usuario/Admin/Status/\'.$row[\'id\'].\'/\'    ,\'\'),TRUE).\'</span>\';';

        }
        if ($funcoes_qnt>2) {
            $function .= ' $html .= \'<br>\';';
            $funcoes_qnt = 0;
        }
        
        // Editar e Deletar
        $funcoes_qnt = $funcoes_qnt+2;
        if ($permissionEdit) {
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Editar\'     ,Array(\'Editar '.$nomedisplay_sing.'\'        ,\''.$url_editar.'/\'.$row[\'id\'].\''.$linkextra.'/\'    ,\'\'),TRUE);';
        }
        if ($permissionDelete) {
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Deletar\'    ,Array(\'Deletar '.$nomedisplay_sing.'\'       ,\''.$url_deletar.'/\'.$row[\'id\'].\''.$linkextra.'/\'     ,\'Deseja realmente deletar essa '.$nomedisplay_sing.'?\'),TRUE);';
        }

        ++$numero;
        eval('$function = function( $d, $row ) { $html = \'\'; '.$function.' return $html; };');       
        $columns[] = array( 'db' => 'ativado', 'dt' => $numero,
            'formatter' => $function
        ); //'Funções';
                
        echo json_encode(
            \Framework\Classes\Datatable::complex( $_GET, Framework\App\Registro::getInstacia()->_Conexao, $table, $primaryKey, $columns, null, $where)
        );
    }
}
?>
