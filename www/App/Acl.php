<?php
namespace Framework\App;
/**
 * Class de Controle de Acesso
 * 
 * E controle de Configuracoes, e suas permissoes de acesso
 *
 * 
 * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
 * @version 0.4.2
 */
class Acl{
    // Informacoes de Login
    public  $logado;
    public  $logado_usuario;
    
    // Id de Usuario, Grupo e Permissoes
    private $_id;
    private $_grupo;
    private $_permissao = Array();
    
    // Permissoes do Sistema
    public static $Sis_Permissao = false;
    private static $Sis_Config_Publico = false;
    
    // Registro e banco de dados
    private $_Registro;
    private $_db;
    private $_Request;
    private $_Cache;
    
    // Configuracoes
    public      static  $config = false;   
    
    /**
     *
     * @var type 
     */
    public static $log_qnt_get_permissao = 0;
    
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @return boolean
     * @throws \Exception
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function __construct($id = false) {
        $tempo = new \Framework\App\Tempo('Acl - Construct');   
        
        // Recupera Registro
        $this->_Registro    = &\Framework\App\Registro::getInstacia();
        $this->_db          = &$this->_Registro->_Conexao;
        $this->_Request     = &$this->_Registro->_Request;
        $this->_Cache     = &$this->_Registro->_Cache;
        
        // Inicializa Classes caso ainda nao tenham sido
        if($this->_db===false){
            $this->_Registro->_Conexao = new \Framework\App\Conexao();
        }
        if($this->_Request===false){
            $this->_Registro->_Request = new \Framework\App\Request();
        }
        if($this->_Cache===false){
            $this->_Registro->_Cache = new \Framework\App\Cache();
        }
        
        if($id!==false){
            // Caso esteja carregando de outro usuario
            $this->_id = (int) $id;
        }else{
            // Caso LOGIN
            if(\Framework\App\Session::get(SESSION_ADMIN_ID)){
                $this->_id =  (int) \Framework\App\Session::get(SESSION_ADMIN_ID);
            }else{
                $this->_id = 0; // Nao esta logado

                // Esqueci Minha Senha
                if(isset($_GET['sistema_esquecisenha'])){
                    if($this->_Registro->_Visual===false) $this->_Registro->_Visual = new \Framework\App\Visual();
                    $this->_Registro->_Visual->Json_Info_Update('Titulo', __('Esqueci Senha'));
                    // Clicando no link do email
                    if(isset($_GET['sistema_esquecisenha_cod'])){
                        $novasenha = Sistema_Funcoes::Gerar_Senha();
                        $codPassado = Sistema_Funcoes::Seguranca_Gerar_Hash($_GET['sistema_esquecisenha_cod']);
                        $inscricao = $this->_db->Sql_Select('Sistema_Login_Esquecisenha','{sigla}usado=\'0\' AND {sigla}chave=\''.$codPassado.'\'',1);
                        if($inscricao===false){
                            // MEnsagem de Erro
                            $mensagens = array(
                                "tipo"              => 'erro',
                                "mgs_principal"     => __('Código Inválido'),
                                "mgs_secundaria"    => __('Verifique se a url foi digitada corretamente, ou se já foi usado ou expirada essa requisição.')
                            );
                            $this->_Registro->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                            $this->_Registro->_Visual->renderizar();
                            \Framework\App\Controle::Tema_Travar();
                        }
                        // Verifica se inscricao nao expiro
                        if((time()-(60*60*24))>$inscricao->time){
                            $inscricao->usado=2;
                            $this->_db->Sql_Update($inscricao);
                            // MEnsagem de Erro
                            $mensagens = array(
                                "tipo"              => 'erro',
                                "mgs_principal"     => __('Código Expirado'),
                                "mgs_secundaria"    => __('Já faz mais de 24 hrs que esse código foi gerado.')
                            );
                            $this->_Registro->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                            $this->_Registro->_Visual->renderizar();
                            \Framework\App\Controle::Tema_Travar();
                        }

                        // Verifica se usuario existe
                        $usuario = $this->_db->Sql_Select('Usuario','{sigla}status=\'1\' && {sigla}login=\''.$inscricao->login.'\'',1);
                        if($usuario===false){
                            // MEnsagem de Erro
                            $mensagens = array(
                                "tipo"              => 'erro',
                                "mgs_principal"     => __('Erro'),
                                "mgs_secundaria"    => __('Usuário não encontrado.')
                            );
                            $this->_Registro->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                            $this->_Registro->_Visual->renderizar();
                            \Framework\App\Controle::Tema_Travar();
                        }
                        $usuario->senha = $novasenha;
                        $this->_db->Sql_Update($usuario);
                        $inscricao->usado = '1';
                        $this->_db->Sql_Update($inscricao);


                        $this->_Registro->_Visual->Blocar('<b>'.__('Seu Login é:').'</b> '.$inscricao->login.'<br><b>'.__('Sua nova senha é:').'</b> '.$novasenha.'');
                        $this->_Registro->_Visual->Bloco_Unico_CriaJanela(__('Senha Atualizada com Sucesso'));
                        $this->_Registro->_Visual->renderizar();
                        \Framework\App\Controle::Tema_Travar();

                    }else
                    //Acabou de digitar o login
                    if(isset($_POST['sistema_esquecisenha_login'])){
                        \Framework\App\Session::destroy(false);
                        $this->logado           = false;
                        $loginPassado = anti_injection($_POST['sistema_esquecisenha_login']);

                        // Procura Login
                        $usuario = $this->_db->Sql_Select('Usuario','{sigla}login=\''.$loginPassado.'\'',1);
                        if($usuario===false){
                            // MEnsagem de Erro
                            $mensagens = array(
                                "tipo"              => 'erro',
                                "mgs_principal"     => __('Erro'),
                                "mgs_secundaria"    => __('Usuário não encontrado.')
                            );
                            $this->_Registro->_Visual->Json_IncluiTipo('Mensagens',$mensagens);

                            $form = new \Framework\Classes\Form('FormEsqueciSenha',SISTEMA_DIR_INT.'?sistema_esquecisenha=true'/*,'formajax'*/); //formajax /'.SISTEMA_MODULO.'/'.SISTEMA_SUB.'/'.SISTEMA_MET
                            $form->Input_Novo('Login','sistema_esquecisenha_login','','text', '',30, '');
                            $this->_Registro->_Visual->Blocar($form->retorna_form('Trocar a Senha'));
                            $this->_Registro->_Visual->Bloco_Unico_CriaJanela(__('Digite o Email'));
                            $this->_Registro->_Visual->renderizar();
                            \Framework\App\Controle::Tema_Travar();
                        }
                        // SE ja tiver inscricao desvale ela
                        $inscricao = $this->_db->Sql_Select('Sistema_Login_Esquecisenha','{sigla}usado=\'0\' AND {sigla}login=\''.$loginPassado.'\'',1);
                        if($inscricao!==false){
                            if((time()-(60*60*24))>$inscricao->time){
                                $inscricao->usado=2;
                                $this->_db->Sql_Update($inscricao);
                            }else{
                                // MEnsagem de Erro
                                $mensagens = array(
                                    "tipo"              => 'erro',
                                    "mgs_principal"     => __('Erro'),
                                    "mgs_secundaria"    => __('Vocẽ já fez sua requisição por uma nova senha. Verifique seu email.')
                                );
                                $this->_Registro->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                                $this->_Registro->_Visual->renderizar();
                                \Framework\App\Controle::Tema_Travar();
                            }
                        }

                        $codPassado = Sistema_Funcoes::Seguranca_Gerar_Token();
                        $inscricao = new \Sistema_Login_Esquecisenha_DAO();
                        $inscricao->ip = $_SERVER['REMOTE_ADDR'];
                        $inscricao->login = $loginPassado;
                        $inscricao->usado = '0';
                        $inscricao->time = time();
                        $inscricao->chave = Sistema_Funcoes::Seguranca_Gerar_Hash($codPassado);
                        $this->_db->Sql_Inserir($inscricao);


                        // Criar Email
                        $email = __('Clique no Link Abaixo para Gerar uma Nova senha para o seu Login')
                                .'<a href="'.URL_PATH.SISTEMA_DIR_INT.'?sistema_esquecisenha=true&sistema_esquecisenha_cod='.$codPassado.'">'.__('Clique Aqui').'</a>';
                        if(Controle::Enviar_Email($email, 'Alteração de Senha', $usuario->email, $usuario->nome)===false){
                            // MEnsagem de Erro
                            $mensagens = array(
                                "tipo"              => 'erro',
                                "mgs_principal"     => __('Erro'),
                                "mgs_secundaria"    => __('O email não pode ser enviado, contate o administrador do sistema.')
                            );
                            $this->_Registro->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                            $this->_Registro->_Visual->renderizar();
                            \Framework\App\Controle::Tema_Travar();
                        }

                        // Avisa na Tela
                        $this->_Registro->_Visual->Blocar('Clique no Link enviado por Email.<br>Por favor verifique seu email.');
                        $this->_Registro->_Visual->Bloco_Unico_CriaJanela(__('Enviado com Sucesso'));
                        $this->_Registro->_Visual->renderizar();
                        \Framework\App\Controle::Tema_Travar();
                    }
                    // Criar Formulario
                    else{
                        \Framework\App\Session::destroy(false);
                        $this->logado           = false;

                        $form = new \Framework\Classes\Form('FormEsqueciSenha',SISTEMA_DIR_INT.'?sistema_esquecisenha=true'/*,'formajax'*/); //formajax /'.SISTEMA_MODULO.'/'.SISTEMA_SUB.'/'.SISTEMA_MET
                        $form->Input_Novo('Login','sistema_esquecisenha_login','','text', '',30, '');
                        $this->_Registro->_Visual->Blocar($form->retorna_form('Trocar a Senha'));
                        $this->_Registro->_Visual->Bloco_Unico_CriaJanela(__('Digite o Email'));
                        $this->_Registro->_Visual->renderizar();
                        \Framework\App\Controle::Tema_Travar();
                    }
                }
            }
            
            
            // verifica se foi pedido o logout
            if(isset($_GET['logout'])) $logout = 'sair';
            else                       $logout = false;
            
            // Caso Tenha Pedido Pra sair, SAIR
            if($logout==='sair'){
                $this->Usuario_Login_Sair();
                $this->_Registro->_Visual = new \Framework\App\Visual();
                $this->_Registro->_Visual->renderizar_login(/*$this->calendario,$this->config_dia,$this->config_mes,$this->config_ano,$this->config_dataixi*/);
                \Framework\App\Controle::Tema_Travar();
            }else
            // CASO NAO TENHA FEITO LOGIN, E NEM PREENCHIDO O 
            if(!isset($_POST['sistema_login']) && !isset($_POST['sistema_senha']) && \Framework\App\Session::get(SESSION_ADMIN_LOG)===false && \Framework\App\Session::get(SESSION_ADMIN_SENHA)===false){
                $this->logado           = false;
            }else
            // Caso nao tenha sessao quer dizer que tem POST
            // se nao tiver sessao, verifica se o post foi acessado, caso contrario verifica se a sessao corresponde ao usuario e senha
            if(isset($_POST['sistema_login']) && isset($_POST['sistema_senha']) && (\Framework\App\Session::get(SESSION_ADMIN_LOG)===false || \Framework\App\Session::get(SESSION_ADMIN_SENHA)===false || \Framework\App\Session::get(SESSION_ADMIN_LOG)=='' || \Framework\App\Session::get(SESSION_ADMIN_SENHA)=='')){
                // Puxa Login E senha e verifica cadastro
                $login = \anti_injection($_POST['sistema_login']);
                // Tenta com a Nova Api de Senha mais Segura.
                $this->logado = $this->Usuario_Senha_Verificar($login, \Framework\App\Sistema_Funcoes::Form_Senha_Blindar($_POST['sistema_senha'],true));
                // Tenta com a VENHA Api de Senha menos seguras mas que usuários antigos ainda usam.
                if($this->logado===false) $this->logado = $this->Usuario_Senha_Verificar($login, \Framework\App\Sistema_Funcoes::Form_Senha_Blindar($_POST['sistema_senha'],false));
                
                // Avisa se login nao teve resultado
                if($this->logado===false){
                    // Verifica se Possui Modulo PRedial e corresponde a um Apartamento sem nenhum cadastro
                    if(\Framework\App\Sistema_Funcoes::Perm_Modulos('predial') && $senha == 'd41d8cd98f00b204e9800998ecf8427e' && strpos($login, '/')!==false){
                        $login = explode($login, '/');
                        if(!isset($login[1])){
                            // Deleta Sessoes e Puxa Erro
                            $this->Usuario_Login_Sair();
                            $this->Fluxo_Senha_Invalida();
                        }
                        $where = Array(
                            'nome'             => $login[1],
                        );
                        $bloco    = $this->_db->Sql_Select(  
                            'Predial_Bloco',            
                            $where,
                            1
                        );
                        if($bloco===false){
                            _Sistema_erroControle::Erro_Puro(5062);
                        }else{
                            $where = Array(
                                'num'            => $login[0],
                                'bloco'         => $bloco->id
                            );
                            $apartamento    = $this->_db->Sql_Select(  
                                'Predial_Bloco_Apart',            
                                $where,
                                1
                            );
                            if($apartamento!==false){
                                if($apartamento->morador==0){
                                    $Visual = new \Framework\App\Visual();
                                    $Visual->renderizar();
                                    \Framework\App\Controle::Tema_Travar();
                                }else{
                                    _Sistema_erroControle::Erro_Puro(5060);
                                }
                            }else{
                                _Sistema_erroControle::Erro_Puro(5061);
                            }
                        }
                    }
                    // Deleta Sessoes e Puxa Erro
                    \Framework\App\Session::destroy(SESSION_ADMIN_ID);
                    \Framework\App\Session::destroy(SESSION_ADMIN_LOG);
                    \Framework\App\Session::destroy(SESSION_ADMIN_SENHA);
                    $this->Fluxo_Senha_Invalida();
                }else{
                    $this->_id = \Framework\App\Session::get(SESSION_ADMIN_ID);
                }
            }else{
                $usuario = \Framework\App\Session::get(SESSION_ADMIN_LOG);
                $senha   = \Framework\App\Session::get(SESSION_ADMIN_SENHA);
                $this->logado = $this->Usuario_Senha_Verificar($usuario, $senha);

                // Avisa se login nao teve resultado
                if($this->logado===false ){
                    \Framework\App\Session::destroy(SESSION_ADMIN_ID);
                    \Framework\App\Session::destroy(SESSION_ADMIN_LOG);
                    \Framework\App\Session::destroy(SESSION_ADMIN_SENHA);
                    $this->Fluxo_Senha_Invalida();
                }
            }
            // SE A PAGINA FOR PROIBIDA PARA USUARIOS DESLOGADOS TRAVA
            if(TEMA_LOGIN===true && $this->logado===false && $this->_Request->getSubModulo()!=='erro' && $this->_Request->getSubModulo()!=='Recurso' && $this->_Request->getSubModulo()!=='localidades'){
                $this->_Registro->_Visual = new \Framework\App\Visual();
                $this->_Registro->_Visual->renderizar_login(/*$this->calendario,$this->config_dia,$this->config_mes,$this->config_ano,$this->config_dataixi*/);
                \Framework\App\Controle::Tema_Travar();
            }            
        }
        self::$Sis_Permissao = $this->_db->Sql_Select('Sistema_Permissao');
        if(self::$Sis_Permissao===false){
            $this->Sistema_Config_Permissoes_InserirPadrao();
            self::$Sis_Permissao = $this->_db->Sql_Select('Sistema_Permissao');
        }
        self::$Sis_Config_Publico = $this->_db->Sql_Select('Sistema_Config');
        if(self::$Sis_Config_Publico===false){
            $this->Sistema_Config_Publico_InserirPadrao();
            self::$Sis_Config_Publico = $this->_db->Sql_Select('Sistema_Config');
        }
        
        if($this->_id!==0){
            $this->_grupo       = $this->getGrupo();
            if($this->_grupo!==false){
                if(!is_int($this->_grupo) || $this->_grupo==0){
                    self::grupos_inserir();
                    throw new \Exception('Grupo não existente: '.$this->_grupo, 2901);
                }
                $this->_permissao   = $this->getPermissaoGrupo();
                $this->compilarAcl();
                // SE for nulo o Compilar, carrega permissoes
                //if(count($this->_permissao)==0){ 

                //}
            }
        }
        return true;
    }
    
    /**
     * 
     */
    private function Fluxo_Senha_Invalida(){    
        $_Registro = &Registro::getInstacia();
        if(!$_Registro->_Visual) $_Registro->_Visual = new \Framework\App\Visual();
        $mensagens = array(
            "tipo"              => 'erro',
            "mgs_principal"     => __('Senha Inválida'),
            "mgs_secundaria"    => __('Verifique se o Login ou a senha foram colocadas com sucesso.')
        );
        $_Registro->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
    }
    /**
     * CONFIG PUBLICAS DO SISTEMA
     * Serve pra Recuperar algum valor de acordo com a chave
     * 
     * @param type $chave
     * @param type $campo
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Get_Config_Publico($chave,$campo='valor') {
        $array = &self::$Sis_Config_Publico;
        reset($array);
        while(key($array)!==NULL){
            $objeto = current($array);
            if($objeto->chave===$chave){
                return $objeto->$campo;
                
            }
            next($array);
        }
        return $chave;
    }
    /**
     * PERMISSOES DO SISTEMA
     * Serve pra Recuperar algum valor do objeto permissao que quiser
     * 
     * @param type $chave
     * @param type $campo
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Get_Permissao_Nome($chave,$campo='Nome') {
        $array = &self::$Sis_Permissao;
        reset($array);
        while(key($array)!==NULL){
            $objeto = current($array);
            if($objeto->chave===$chave){
                return $objeto->$campo;
                
            }
            next($array);
        }
        return $chave;
    }
    /**
     * Recupera Permissoes do Usuario
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function getPermissao(){
        if(isset($this->_permissao) && count($this->_permissao)){
            return $this->_permissao;
        }
    }    
    /**
     * Retorna o valor de alguma chave de alguma Permissão de Modulo
     * 
     * @param string $chave Chave da Permissão
     * @param string $campo Campo da Permissão, Opcional
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Get_Permissao_Chave($chave,$campo='Nome') {
        $array = $this->getPermissao();
        if(isset($array[$chave])){
            return $array[$chave]['valor'];
        }else{
            return false;
        }
    }
    /**
     * Retorna se o Usuario tem permissao de acesso a URL ou nao
     * 
     * @param string $url Url que irá conferir se tem acesso.
     * @return boolean Verdadeiro ou Falso
     */
    public function Get_Permissao_Url($url) {
        // Economiza Tempo quando a URL é obvia que é permitida
        if($url===SISTEMA_URL.SISTEMA_DIR.'#' || $url===SISTEMA_URL.SISTEMA_DIR.'#/'){
            return true;
        }
        // Começa Tratamento
        $tempo = new \Framework\App\Tempo('Acl Get Permissao');
        
        
        // Faz Controle de Excesso de Permissao, para Performace
        ++static::$log_qnt_get_permissao;
        //var_dump($url);echo "<br><br>\n\n";
        if(SISTEMA_DEBUG && static::$log_qnt_get_permissao>60){
            throw new \Exception('Permissão Requisitada mais de 60 vezes em uma mesma pagina.',2808);
        }
        
        //echo "\n\n<br><br>".$url;
        
        $permissoes_quepossuem = Array();
        $array = &self::$Sis_Permissao;
        reset($array);
        while(key($array)!==NULL){
            $objeto = current($array);
            
            // CAso nao Vazio
            if($objeto->end!=''){
            
                // Verifica se permissao inclue a url de entrada
                $consta = strpos(strtolower($url), strtolower($objeto->end));
                if($consta!==false){
                    $permissoes_quepossuem[] = Array(
                        'Perm'      => $objeto,
                        'Gravidade' => strlen($objeto->end)); // tamanho da url vira a gravidade
                }
                
            }
            next($array);
        }
        // Ordena Array Multi indices
        orderMultiDimensionalArray($permissoes_quepossuem, 'Gravidade', true);
        
        //var_dump($permissoes_registro,$permissoes_quepossuem);
        // Percorre Verificando Permissoes
        $permissoes_registro = $this->getPermissao();
        reset($permissoes_quepossuem);
        while(key($permissoes_quepossuem)!==NULL){
            $objeto = current($permissoes_quepossuem);
            if(!isset($permissoes_registro[$objeto['Perm']->chave]) || $permissoes_registro[$objeto['Perm']->chave]['valor']===false){
                return false;
            }
            next($permissoes_quepossuem);
        }
        return true;
    }
    /**
     * Compilar as Permissões, Junta as Permissoes de Usuario com as de Grupo
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    private function compilarAcl(){
        $usuario_perm = $this->getUsuarioPermissao();
        $this->_permissao = array_merge(
                $this->_permissao,
                $usuario_perm
        );
    }
    /**
     * Retorna Grupos, se nao houver ele cria automaticamente
     * 
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    private function getGrupo(){
        $sql = $this->_db->query(
            'SELECT U.grupo,SG.categoria FROM '.MYSQL_USUARIOS.' U LEFT JOIN '.MYSQL_SIS_GRUPO.' SG '.
            'ON U.grupo=SG.id WHERE U.deletado!=1 AND U.id='.$this->_id
        );
        /*echo 'SELECT U.grupo,SG.categoria FROM '.MYSQL_USUARIOS.' U LEFT JOIN '.MYSQL_SIS_GRUPO.' SG '.
            'ON U.grupo=SG.id WHERE U.deletado!=1 AND U.id='.$this->_id; exit;*/
        $grupo = $sql->fetch_object();
        if($grupo==NULL) return false;
        if($grupo->categoria!==NULL && $grupo->categoria!=0){
            return (int) $grupo->grupo;    
        }
        // Caso Categoria Não existe, continua
        $sql = $this->_db->query(
            'SELECT * FROM '.MYSQL_CAT.' C LEFT JOIN '.MYSQL_CAT_ACESSO.' CA '.
            'ON C.id WHERE CA.mod_acc=\'usuario_grupo\''
        ,true);
        $categoria = $sql->fetch_object();
        // Caso nao Exista, cria as categorias
        if($categoria===NULL){
            $manutencao = new \Framework\Classes\SierraTec_Manutencao();
            
            // Gerais
            $this->_db->query(
                'INSERT INTO '.MYSQL_CAT.' (log_date_add,servidor,parent,nome,deletado)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\',0,\'Gerais\',0);'
            ,true);
            $id = (int) $this->_db->ultimo_id();
            $cadastrar = $this->_db->query(
                'INSERT INTO '.MYSQL_CAT_ACESSO.' (log_date_add,servidor,categoria,mod_acc)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\','.$id.',\'usuario_grupo\');'
            ,true);
            $manutencao->Alterar_Config('CFG_TEC_CAT_ID_ADMIN',$id);
            // Atualiza GRUPOs REFErentes
            $this->_db->query('UPDATE '.MYSQL_SIS_GRUPO.' SET categoria='.$id.' WHERE id!='.CFG_TEC_IDFUNCIONARIO.' AND id!='.CFG_TEC_IDCLIENTE,true,false);
            
            // Funcionarios
            $this->_db->query(
                'INSERT INTO '.MYSQL_CAT.' (log_date_add,servidor,parent,nome,deletado)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\',0,\'Clientes\',0);'
            ,true);
            $id = (int) $this->_db->ultimo_id();
            $cadastrar = $this->_db->query(
                'INSERT INTO '.MYSQL_CAT_ACESSO.' (log_date_add,servidor,categoria,mod_acc)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\','.$id.',\'usuario_grupo\');'
            ,true);
            $manutencao->Alterar_Config('CFG_TEC_CAT_ID_CLIENTES',$id);
            // Atualiza GRUPOs REFErentes
            $this->_db->query('UPDATE '.MYSQL_SIS_GRUPO.' SET categoria='.$id.' WHERE id='.CFG_TEC_IDCLIENTE,true,false);
            
            // Funcionarios
            $this->_db->query(
                'INSERT INTO '.MYSQL_CAT.' (log_date_add,servidor,parent,nome,deletado)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\',0,\'Funcionários\',0);'
            ,true);
            $id = (int) $this->_db->ultimo_id();
            $cadastrar = $this->_db->query(
                'INSERT INTO '.MYSQL_CAT_ACESSO.' (log_date_add,servidor,categoria,mod_acc)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\','.$id.',\'usuario_grupo\');'
            ,true);
            $manutencao->Alterar_Config('CFG_TEC_CAT_ID_FUNCIONARIOS',$id);
            // Atualiza GRUPOs REFErentes
            $this->_db->query('UPDATE '.MYSQL_SIS_GRUPO.' SET categoria='.$id.' WHERE id='.CFG_TEC_IDCLIENTE,true,false);
            
            // Clientes 
            $this->_db->query(
                'INSERT INTO '.MYSQL_CAT.' (log_date_add,servidor,parent,nome,deletado)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\',0,\'Clientes\',0);'
            ,true);
            $id = (int) $this->_db->ultimo_id();
            $cadastrar = $this->_db->query(
                'INSERT INTO '.MYSQL_CAT_ACESSO.' (log_date_add,servidor,categoria,mod_acc)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\','.$id.',\'usuario_grupo\');'
            ,true);
            $manutencao->Alterar_Config('CFG_TEC_CAT_ID_CLIENTES',$id);
            // Atualiza GRUPOs REFErentes
            $this->_db->query('UPDATE '.MYSQL_SIS_GRUPO.' SET categoria='.$id.' WHERE id='.CFG_TEC_IDFUNCIONARIO,true,false);

        }else{
            // tenta botar em grupos ja existentes
            if($grupo->grupo=='1'){
                $this->_db->query('UPDATE '.MYSQL_USUARIOS.' SET grupo='.CFG_TEC_IDADMINDEUS.' WHERE grupo='.$grupo->grupo,true,false);
            }else if($grupo->grupo=='2'){
                $this->_db->query('UPDATE '.MYSQL_USUARIOS.' SET grupo='.CFG_TEC_IDADMIN.' WHERE grupo='.$grupo->grupo,true,false);
            }else if($grupo->grupo=='4' || $grupo->grupo=='3'){
                $this->_db->query('UPDATE '.MYSQL_USUARIOS.' SET grupo='.CFG_TEC_IDCLIENTE.' WHERE grupo='.$grupo->grupo,true,false);
            }else if($grupo->grupo=='5'){
                $this->_db->query('UPDATE '.MYSQL_USUARIOS.' SET grupo='.CFG_TEC_IDFUNCIONARIO.' WHERE grupo='.$grupo->grupo,true,false);
            }
        }
        // REEXECUTA QUERY
        $sql = $this->_db->query(
            'SELECT U.grupo,SG.categoria FROM '.MYSQL_USUARIOS.' U LEFT JOIN '.MYSQL_SIS_GRUPO.' SG '.
            'ON U.grupo=SG.id WHERE U.deletado!=1 AND U.id='.$this->_id
        );
        $grupo = $sql->fetch_object();
        if($grupo==NULL) return false;
        if($grupo->categoria!==NULL && $grupo->categoria!=0){
            return (int) $grupo->grupo;    
        }
    }/*
    private function getPermissaoGrupoId(){
        $id = Array();
        $i = 0;
        $sql_ids = $this->_db->query(
                'SELECT permissao FROM '.MYSQL_SIS_GRUPO_PERMISSAO.
                ' WHERE deletado!=1 AND grupo = '.$this->_grupo
                );
        while ($campo = $sql_ids->fetch_object()) {
            $id[$i] = '\''.$campo->permissao.'\'';
            ++$i;
        }
        return $id;
    }*/
    /**
     * Retorna a Permissão do Grupo
     * 
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    private function getPermissaoGrupo(){
        $data = Array();
        $sql_permissoes = $this->_db->query(
            'SELECT SP.chave,SP.modulo,SP.submodulo,SP.metodo,SP.end,SP.nome,SGP.valor FROM '.MYSQL_SIS_GRUPO_PERMISSAO.' SGP, '.MYSQL_SIS_PERMISSAO.' SP'.
            ' WHERE SGP.deletado!=1 AND SGP.permissao = SP.chave AND SGP.grupo = '.$this->_grupo
        );
        while ($campo = $sql_permissoes->fetch_object()) {
            
            if($campo->valor=='1'){
                $v = true;
            }else{
                $v = false;
            }
            
            $data[$campo->chave] = Array(
                'chave'     => $campo->chave,
                'mod'       => $campo->modulo,
                'sub'       => $campo->submodulo,
                'met'       => $campo->metodo,
                'end'       => $campo->end,
                'permissao' => $campo->nome,
                'valor'     => $v,
                'herdado'   => true
            );
        }  
        return $data;
    }
    /**
     * Retorna as Permissoes de CErto usuario
     * #update
     * @return array
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    private function getUsuarioPermissao(){
        $data = Array();
        /*$ids = $this->getPermissaoGrupoId();
        if(empty($ids))     return $data;
        // verifica consistencia do ids
        $teste = '';
        foreach($ids as $valor){
            $teste .= $valor;
        }
        if($teste=='')      return $data;*/
        

        $permissao = $this->_db->query('SELECT SP.chave,SP.modulo,SP.submodulo,SP.metodo,SP.end,SP.nome,UP.valor FROM '.MYSQL_USUARIO_PERMISSAO.' UP, '.MYSQL_SIS_PERMISSAO.' SP'.
                ' WHERE UP.deletado!=1 AND UP.permissao = SP.chave AND UP.servidor = \''.SRV_NAME_SQL.'\' AND UP.usuario = '.$this->_id
                //.' AND UP.permissao in ( '.implode(',',$ids).' )'
                ,true);
        while ($campo = $permissao->fetch_object()) {            
            if($campo->valor==1 || $campo->valor=='1'){
                $v = true;
            }else{
                $v = false;
            }
            
            $data[$campo->chave] = Array(
                'chave'     => $campo->chave,
                'mod'       => $campo->modulo,
                'sub'       => $campo->submodulo,
                'met'       => $campo->metodo,
                'end'       => $campo->end,
                'permissao' => $campo->nome,
                'valor'     => $v,
                'herdado'   => false,
            );
        }
        return $data;
    }
    /**
     * Retorna id do Usuario
     * 
     * @name Usuario_GetID
     * @access public
     * 
     * @uses \Framework\App\Controle::$usuario
     * 
     * @return int 
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Usuario_GetID(){
        if(!isset($this->logado_usuario) || !is_numeric($this->logado_usuario->id) || $this->logado===false){
            return 0;
        }
        $id = (int) $this->logado_usuario->id;
        return $id;
    }
    /**
     * 
     * @return int
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Usuario_GetNome(){
        if(!isset($this->logado_usuario) || $this->logado===false){
            return 0;
        }
        $nome = $this->logado_usuario->nome;
        return $nome;
    }
    /**
     * 
     * @return int
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Usuario_GetEmail(){
        if(!isset($this->logado_usuario) || $this->logado===false){
            return 0;
        }
        $email = $this->logado_usuario->email;
        return $email;
    }
    
    /**
     * Retorna id do Usuario
     * 
     * @name Usuario_GetID
     * @access public
     * 
     * @uses \Framework\App\Controle::$usuario
     * 
     * @return int 
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public static function Usuario_GetID_Static(){
        $Registro = &\Framework\App\Registro::getInstacia();
        $Acl = $Registro->_Acl;
        return ($Acl)?$Acl->Usuario_GetID():0;
    }
    /**
    * Verifica o usuario e senha do usuario logado e retorna
    * 
    * @name Usuario_Senha_Verificar
    * @access public
    * 
    * @param string $email
    * @param string $senha
    * @param Array $usuario Carrega Ponteiro da variavel Usuario para Controle
    * @param Array $usuario2 Carrega Ponteiro da variavel Usuario para Visual
    * 
    * @uses \Framework\App\Modelo::$usuario
    * @uses \Framework\App\Modelo::$bd
    * @uses \Framework\App\Conexao::$query
    * 
    * @return int 1
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2 
    * Revisão:
    *  - 0.1.1 2013-05-13 Sessao Automatizada por Constante
    */
    public function Usuario_Senha_Verificar($email=false,$senha=false)
    {
        if($email===false){
                $email = \anti_injection($_POST['sistema_login']);
        }
        if($senha===false){
            $senha = $_POST['sistema_senha'];
        }
        
        $query = $this->_db->query('SELECT id,senha,grupo,nome,foto,email,cpf,telefone,celular,endereco,numero,complemento,'.
                'cidade,bairro,cep,foto_cnh,foto_res,foto_cnh_apv,foto_res_apv'
                . ' FROM '.MYSQL_USUARIOS.' WHERE servidor=\''.SRV_NAME_SQL.'\' AND (login=\''.$email.'\' OR email=\''.$email.'\') AND ativado=1 AND deletado=0 LIMIT 1');
        
        // #update Nao deixar que os logins configurados cmo nao login, façam login
        // Procura Resultado
        while($this->logado_usuario = $query->fetch_object()){
            // Se for Igual Passa
            if($this->logado_usuario->senha===$senha){
                $this->Usuario_Logar($email,$senha,$this->logado_usuario->id);
                return true;
            }
            // Se nao, verifica o time da senha, pra poder compilar para comparação
            $recuperaca_time = explode('\?',$this->logado_usuario->senha);
            if(isset($recuperaca_time[2]) && $this->logado_usuario->senha===\Framework\App\Sistema_Funcoes::Form_Senha_Blindar($senha,true,$recuperaca_time[2])){
                // Se a senha tiver passado de 3 meses, pedir pra trocar a senha, dessa forma, mesmo se
                // pegarem a senha criptografada, quando conseguirem descobrir uma chave que abre.
                // O usuário já irá ter trocado de senha #update
                $this->Usuario_Logar($email,$senha,$this->logado_usuario->id);
                return true;
            }else if($this->logado_usuario->senha===\Framework\App\Sistema_Funcoes::Form_Senha_Blindar($senha)){
                // Necessita Trocar Senha
                $this->Usuario_Logar($email,$senha,$this->logado_usuario->id);
                return true;
            }
            // Senha Errada
        }
        // CAso nao AChe
        $this->logado_usuario = new \Usuario_DAO();
        $this->logado_usuario->id = 0;
        return false;
        
    }
    /**
     * Responsavel por Fazer LOGOUT do sistema
     */
    private function Usuario_Login_Sair(){
        \Framework\App\Session::destroy(SESSION_ADMIN_ID);
        \Framework\App\Session::destroy(SESSION_ADMIN_LOG);
        \Framework\App\Session::destroy(SESSION_ADMIN_SENHA);
        \Framework\App\Session::destroy(false);
        $this->logado           = false;
    }
    /**
     * 
     * @param type $login
     * @param type $senha
     * @param int $id Chave Primária (Id do Registro)
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Usuario_Logar($login='',$senha='',$id=0){
        // #update -> Aplicar Segurança de Roubo de Sessao, Gerar Token e 
        // Configurar opcao de manter logado
        if($login!=='') \Framework\App\Session::set(SESSION_ADMIN_LOG,   $login);
        if($senha!=='') \Framework\App\Session::set(SESSION_ADMIN_SENHA, $senha);
        if($senha!==0) \Framework\App\Session::set(SESSION_ADMIN_ID,    $id);
        return true;
    }
    /**
     * Retorna se ta logado
     * 
     * @name Usuario_GetLogado
     * @access public
     * 
     * @return int Id de Usuario logado ou zero 
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Usuario_GetLogado(){
        return $this->logado;
    }
    /**
     * Retorna o Id do Usuario de uma forma Estatica
     * 
     * @return int Id de Usuario logado ou zero
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    static public function Usuario_GetLogado_Static(){
        return \Framework\App\Registro::getInstacia()->_Acl->logado;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Inseri Novos Grupos (Grupos Basicos) no Sistema
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    static function grupos_inserir(){
        $Registro   = &Registro::getInstacia();
        $db         = &$Registro->_Conexao;
        // Caso Categoria Não existe, continua
        $sql = $db->query(
            'SELECT * FROM '.MYSQL_CAT.' C LEFT JOIN '.MYSQL_CAT_ACESSO.' CA '.
            'ON C.id WHERE CA.mod_acc=\'usuario_grupo\'' 
       ,true);
        $categoria = $sql->fetch_object();
        // Caso nao Exista, cria as categorias
        if($categoria===NULL){
            $manutencao = new \Framework\Classes\SierraTec_Manutencao();
            
            // Gerais
            $db->query(
                'INSERT INTO '.MYSQL_CAT.' (log_date_add,servidor,parent,nome,deletado)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\',0,\'Gerais\',0);'
            ,true);
            $gerais_id = (int) $db->ultimo_id();
            $cadastrar = $db->query(
                'INSERT INTO '.MYSQL_CAT_ACESSO.' (log_date_add,servidor,categoria,mod_acc)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\','.$gerais_id.',\'usuario_grupo\');'
            ,true);
            $manutencao->Alterar_Config('CFG_TEC_CAT_ID_ADMIN',$gerais_id);
            
            // Clientes
            $db->query(
                'INSERT INTO '.MYSQL_CAT.' (log_date_add,servidor,parent,nome,deletado)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\',0,\'Clientes\',0);'
            ,true);
            $clientes_id = (int) $db->ultimo_id();
            $cadastrar = $db->query(
                'INSERT INTO '.MYSQL_CAT_ACESSO.' (log_date_add,servidor,categoria,mod_acc)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\','.$clientes_id.',\'usuario_grupo\');'
            ,true);
            $manutencao->Alterar_Config('CFG_TEC_CAT_ID_CLIENTES',$clientes_id);
            
            // Funcionarios
            $db->query(
                'INSERT INTO '.MYSQL_CAT.' (log_date_add,servidor,parent,nome,deletado)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\',0,\'Funcionários\',0);'
            ,true);
            $funcionarios_id = (int) $db->ultimo_id();
            $cadastrar = $db->query(
                'INSERT INTO '.MYSQL_CAT_ACESSO.' (log_date_add,servidor,categoria,mod_acc)
                VALUES (\''.APP_HORA.'\',\''.SRV_NAME_SQL.'\','.$funcionarios_id.',\'usuario_grupo\');'
            ,true);
            $manutencao->Alterar_Config('CFG_TEC_CAT_ID_FUNCIONARIOS',$funcionarios_id);
        }else{
            
            $gerais_id = (int) CFG_TEC_CAT_ID_ADMIN;
            $clientes_id = (int) CFG_TEC_CAT_ID_CLIENTES;
            $funcionarios_id = (int) CFG_TEC_CAT_ID_FUNCIONARIOS;
        }
        
        
        $grupos = $db->Sql_Select('Sistema_Grupo');
        if($grupos===false){
            
            // Admin Master
            $grupo = new \Sistema_Grupo_DAO();
            //$grupo->id = 1;
            $grupo->nome = __('Admin Master');
            $grupo->categoria = $gerais_id;
            $db->Sql_Inserir($grupo);
            
            // Admin
            $grupo = new \Sistema_Grupo_DAO();
            //$grupo->id = 2;
            $grupo->nome = __('Admin');
            $grupo->categoria = $gerais_id;
            $db->Sql_Inserir($grupo);
            
            // Cliente
            $grupo = new \Sistema_Grupo_DAO();
            //$grupo->id = 3;
            $grupo->nome = \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('usuario_Cliente_nome');
            $grupo->categoria = $clientes_id;
            $db->Sql_Inserir($grupo);
            
            // Funcionario
            $grupo = new \Sistema_Grupo_DAO();
            //$grupo->id = 3;
            $grupo->nome = __('Funcionário');
            $grupo->categoria = $funcionarios_id;
            $db->Sql_Inserir($grupo);
            
            // Newsletter
            $grupo = new \Sistema_Grupo_DAO();
            //$grupo->id = 4;
            $grupo->nome = __('Newsletter');
            $grupo->categoria = $funcionarios_id;
            $db->Sql_Inserir($grupo);
        }
        
        
    }
    
    /**
     * Em cima de uma Chave, retorna o Valor desse Config nesse Servidor
     * 
     * @param varchar $chave
     * @return boolean Se o Valor desse Config for um Array, Retornara um Array
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public static function Sistema_Modulos_Configs_Funcional($chave=false){
        if($chave===false || $chave=='') return false;
        if(self::$config===false){
            self::$config     = &self::Sistema_Modulos_Carregar_Funcional();
        }
        $percorrer  = &self::$config[$chave]['Valor'];
        // Percorre Funcional
        /*if(empty($percorrer)) return false;
        foreach($percorrer as &$valor){
            if($valor['chave']==$chave){
                return $valor['Valor'];
            }
        }*/
        if(isset($percorrer))
            return $percorrer;
        return false;
    }
    /**
     * Em cima de uma Chave, retorna o Valor desse Config nesse Servidor
     * 
     * @param varchar $chave
     * @return boolean Se o Valor desse Config for um Array, Retornara um Array
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public static function Sistema_Modulos_Configs_Publico($chave=false){
        if($chave===false || $chave=='') return false;
        $db          = &$this->_Registro->_Conexao;
        if(self::$Sis_Config_Publico===false){
            self::$Sis_Config_Publico = $db->Sql_Select('Sistema_Config');
            if(self::$Sis_Config_Publico===false){
                $this->Sistema_Config_Publico_InserirPadrao();
                self::$Sis_Config_Publico = $db->Sql_Select('Sistema_Config');
            }
        }
        $percorrer  = &self::$Sis_Config_Publico[$chave]['Valor'];
        
        // Percorre Publico
        /*if(empty($percorrer)) return false;
        foreach($percorrer as &$valor){
            if($valor['chave']==$chave){
                return $valor['Valor'];
            }
        }*/
        if(isset($percorrer))
            return $percorrer;
        return false;
    }
    /**
     * 
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public static function &Sistema_Modulos_Carregar_Menu(){
        $tempo = new \Framework\App\Tempo('\Framework\App\Acl::Sistema_Modulos_Configs->Menu');
        // Le todos arquivos Menus dos modulos permitidos
        $ponteiro   = Array('_Sistema' => '_Sistema');
        if(function_exists('config_modulos')){
            $ponteiro   = array_merge($ponteiro,config_modulos());
        }
        $config     = Array();
        reset($ponteiro);
        while (key($ponteiro) !== null) {
            $current = current($ponteiro);
            if (is_dir(MOD_PATH.''.$current)) {
                // SE existe arquivo config
                if(file_exists(MOD_PATH.''.$current.'/_Config.php')){
                    // Puxa
                    include MOD_PATH.''.$current.'/_Config.php';
                    // Realiza Merge para Indexir Configuracoes
                    $config         = array_merge_recursive($config     ,$config_Menu()         );
                } 
            }
            next($ponteiro);
        }
        return $config;
    }
    /**
     * 
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public static function &Sistema_Modulos_Carregar_Permissoes(){
        $tempo = new \Framework\App\Tempo('\Framework\App\Acl::Sistema_Modulos_Configs->Permissoes');
        // Le todos arquivos Menus dos modulos permitidos
        $ponteiro   = Array('_Sistema' => '_Sistema');
        if(function_exists('config_modulos')){
            $ponteiro   = array_merge($ponteiro,config_modulos());
        }
        $config     = Array();
        reset($ponteiro);
        while (key($ponteiro) !== null) {
            $current = current($ponteiro);
            if (is_dir(MOD_PATH.''.$current)) {
                // SE existe arquivo config
                if(file_exists(MOD_PATH.''.$current.'/_Config.php')){
                    // Puxa
                    include MOD_PATH.''.$current.'/_Config.php';
                    // Realiza Merge para Indexir Configuracoes
                    $config    = array_merge_recursive($config,$config_Permissoes()   );
                } 
            }
            next($ponteiro);
        }
        return $config;
    }
    /**
     * 
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public static function &Sistema_Modulos_Carregar_Funcional(){
        $tempo = new \Framework\App\Tempo('\Framework\App\Acl::Sistema_Modulos_Configs->Funcional');
        // ordena na ordem correta
        $Registro = &\Framework\App\Registro::getInstacia();
        // Inicializa Caso nao Tenha Sido
        if($Registro->_Cache===false){
            $Registro->_Cache = new \Framework\App\Cache();
        }
        // Verifica se Tem Cache
        $funcional = $Registro->_Cache->Ler('Config_Funcional');
        if (!$funcional) {
            $ponteiro   = Array('_Sistema' => '_Sistema');
            if(function_exists('config_modulos')){
                $ponteiro   = array_merge($ponteiro,config_modulos());
            }
            $funcional     = Array();
            reset($ponteiro);
            while (key($ponteiro) !== null) {
                $current = current($ponteiro);
                if (is_dir(MOD_PATH.''.$current)) {
                    // SE existe arquivo config
                    if(file_exists(MOD_PATH.''.$current.'/_Config.php')){
                        // Puxa
                        include MOD_PATH.''.$current.'/_Config.php';
                        // Merge Com Config Funcional DO SERVIDOR se Existir
                        if(file_exists(INI_PATH.SRV_NAME.'/'.$current.'.php')){
                            include INI_PATH.SRV_NAME.'/'.$current.'.php';
                            if(isset($Funcional)){
                                // Pega Arrays com configs
                                $config_funciona = $config_Funcional();
                                
                                // variavel $Funcional vem do Include do Config de cada servidor
                                // Substitui o valor
                                reset($Funcional);
                                while (key($Funcional) !== null) {
                                    $current2 = current($Funcional);
                                    if(isset($current2['Valor'])){
                                        $config_funciona[key($Funcional)]['Valor'] = $current2['Valor'];
                                    }
                                    next($Funcional);
                                }
                            }else{
                                $config_funciona = $config_Funcional();
                            }
                        }else{
                            $config_funciona = $config_Funcional();
                        }
                        // Realiza Merge para Indexir Configuracoes
                        $funcional    = array_merge_recursive($funcional,$config_funciona       );
                    } 
                }
                next($ponteiro);
            }
            $Registro->_Cache->Salvar('Config_Funcional', $funcional);
        }
        return $funcional;
    }
    /**
     * Carrega os COnfig Publicos Padroes para poder Inserir no Banco de Dados para futura alteracao !
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public static function &Sistema_Modulos_Carregar_Publico(){
        $tempo = new \Framework\App\Tempo('\Framework\App\Acl::Sistema_Modulos_Configs->Publico');
        // ordena na ordem correta
        $ponteiro   = Array('_Sistema' => '_Sistema');
        if(function_exists('config_modulos')){
            $ponteiro   = array_merge($ponteiro,config_modulos());
        }
        $publico     = Array();
        reset($ponteiro);
        while (key($ponteiro) !== null) {
            $current = current($ponteiro);
            if (is_dir(MOD_PATH.''.$current)) {
                // SE existe arquivo config
                if(file_exists(MOD_PATH.''.$current.'/_Config.php')){
                    // Puxa
                    include MOD_PATH.''.$current.'/_Config.php';
                    // Merge Com Config Publico DO SERVIDOR se Existir
                    if(file_exists(INI_PATH.SRV_NAME.'/'.$current.'.php')){
                        include INI_PATH.SRV_NAME.'/'.$current.'.php';
                        if(isset($Publico)){
                            // Pega Arrays com configs
                            $config_publico_temp = $config_Publico();
                            $config_Publico = $Publico;

                            // Merge só valor
                            reset($config_Publico);
                            while (key($config_Publico) !== null) {
                                $current2 = current($config_Publico);
                                if(isset($current2['Valor'])){
                                    $config_publico_temp[key($config_Publico)]['Valor'] = $current2['Valor'];
                                }
                                next($config_Publico);
                            }
                        }else{
                            $config_publico_temp = $config_Publico();
                        }
                    }else{
                        $config_publico_temp = $config_Publico();
                    }
                    // Realiza Merge para Indexir Configuracoes
                    $publico    = array_merge_recursive($publico,$config_publico_temp       );
                } 
            }
            next($ponteiro);
        }
        return $publico;
    }
    /**
     * 
     * VErifica se Nao existe realmente no banco de dados e se for verdade insere o padrao
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    private function Sistema_Config_Permissoes_InserirPadrao(){
        $configPermissoes = self::Sistema_Modulos_Carregar_Permissoes();
        if(!empty($configPermissoes)){
            foreach($configPermissoes as &$valor){
                if($valor['Chave']!=''){
                    // Verifica se ja existe
                    $where = Array(
                        'chave' => $valor['Chave'],
                    );
                    $retorno = $this->_db->Sql_Select('Sistema_Permissao',$where);
                    if($retorno===false){
                        
                        
                        // Se nao tiver Permissao funcional requerida, entao passa direto
                        $trava = false;
                        if(isset($valor['Permissao_Func']) && is_array($valor['Permissao_Func'])){
                            foreach($valor['Permissao_Func'] as $indicepermfunc=>&$permfunc){
                                if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional($indicepermfunc)!==$permfunc){
                                    $trava = true;
                                    continue;
                                }
                            }
                        }
                        if($trava) continue;
                        
                        // Faz as Paradas Cria Permissao e Grava no Banco
                        $endereco   = explode('/',$valor['End']);
                        $modulo     = $endereco[0];
                        if(isset($endereco[1])){
                            $submodulo  = $endereco[1];
                        }else{
                            $submodulo  = '*';
                        }
                        $inserir = new \Sistema_Permissao_DAO();
                        $inserir->nome        = $valor['Nome'];
                        $inserir->descricao   = $valor['Desc'];
                        $inserir->modulo      = $modulo;
                        $inserir->submodulo   = $submodulo;
                        $inserir->end         = $valor['End'];
                        $inserir->chave       = $valor['Chave'];
                        $this->_db->Sql_Inserir($inserir);
                    }
                }
            }
        }
    }
    /**
     * VErifica se Nao existe realmente no banco de dados e se for verdade insere o padrao
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    private function Sistema_Config_Publico_InserirPadrao(){
        $configPublicos = self::Sistema_Modulos_Carregar_Publico();
        if(!empty($configPublicos)){
            foreach($configPublicos as &$valor){
                if($valor['Chave']!=''){
                    // Verifica se ja existe
                    $where = '{sigla}chave=\''.$valor['Chave'].'\'';
                    $retorno = $this->_db->Sql_Select('Sistema_Config',$where);
                    if($retorno===false){
                        
                        
                        // Se nao tiver Permissao funcional requerida, entao passa direto
                        $trava = false;
                        if(isset($valor['Permissao_Func']) && is_array($valor['Permissao_Func'])){
                            foreach($valor['Permissao_Func'] as $indicepermfunc=>&$permfunc){
                                if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional($indicepermfunc)!==$permfunc){
                                    $trava = true;
                                    continue;
                                }
                            }
                        }
                        if($trava) continue;
                        // Grava no Banco
                        $inserir = new \Sistema_Config_DAO();
                        $inserir->nome        = $valor['Nome'];
                        $inserir->descricao   = $valor['Desc'];
                        $inserir->chave       = $valor['Chave'];
                        $inserir->valor       = $valor['Valor'];
                        if(isset($valor['Mascara'])){
                            $inserir->mascara       = $valor['Mascara'];
                        }else{
                            $inserir->mascara       = '';
                        }
                        if(isset($valor['Max'])){
                            $inserir->max       = $valor['Max'];
                        }else{
                            $inserir->max       = '100';
                        }
                        
                        $this->_db->Sql_Inserir($inserir);
                    }
                }
            }
        }
    }
}
?>
