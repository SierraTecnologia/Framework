<?php
class comercio_FornecedorControle extends comercio_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses comercio_rede_PerfilModelo::Carrega Rede Modelo
    * @uses comercio_rede_PerfilVisual::Carrega Rede Visual
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
    */
    public function __construct() {
        parent::__construct();
    }
    static function Campos_Deletar(&$campos) {
        if (!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Fornecedor_Categoria')) {
            self::DAO_Campos_Retira($campos, 'categoria');
        }
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses comercio_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
    */
    public function Main() {
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'comercio/Fornecedor/Fornecedores');
        return FALSE;
    }
    static function Endereco_Fornecedor($true= TRUE, $produto = FALSE) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        $titulo = __('Fornecedores');
        $link = 'comercio/Fornecedor/Fornecedores';
        if ($true === TRUE) {
            $_Controle->Tema_Endereco($titulo, $link);
        } else {
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores($export = FALSE) {
        self::Endereco_Fornecedor(FALSE);
        
        $table_colunas = Array();

        $table_colunas[] = __('Nome');
        // Coloca Preco
        if (\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Fornecedor_Categoria')) {
            $table_colunas[] = __('Tipo de Fornecimento');
        }
        
        $table_colunas[] = __('Telefone');
        $table_colunas[] = __('Email');
        $table_colunas[] = __('Funções');

        $this->_Visual->Show_Tabela_DataTable_Massiva($table_colunas,'comercio/Fornecedor/Fornecedores');
        $titulo = __('Listagem de Fornecedores').' (<span id="DataTable_Contador">0</span>)';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo, '',10,Array("link"=>"comercio/Fornecedor/Fornecedores_Add",'icon'=>'add', 'nome'=>'Adicionar Fornecedor'));
        
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Administrar Fornecedores'));
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Add() {
        self::Endereco_Fornecedor(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Fornecedor');
        $titulo2    = __('Salvar Fornecedor');
        $formid     = 'form_Sistema_Admin_Fornecedores';
        $formbt     = __('Salvar');
        $formlink   = 'comercio/Fornecedor/Fornecedores_Add2/';
        $campos = Comercio_Fornecedor_DAO::Get_Colunas();
        self::Campos_Deletar($campos);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Add2() {
        $titulo     = __('Fornecedor Adicionado com Sucesso');
        $dao        = 'Comercio_Fornecedor';
        $function     = '$this->Fornecedores();';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Fornecedor cadastrado com sucesso.');
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
     
    }
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Edit($id) {
        self::Endereco_Fornecedor(TRUE);
        // Carrega Config
        $titulo1    = 'Editar Fornecedor (#'.$id.')';
        $titulo2    = __('Alteração de Fornecedor');
        $formid     = 'form_Sistema_AdminC_FornecedorEdit';
        $formbt     = __('Alterar Fornecedor');
        $formlink   = 'comercio/Fornecedor/Fornecedores_Edit2/'.$id;
        $editar     = Array('Comercio_Fornecedor', $id);
        $campos = Comercio_Fornecedor_DAO::Get_Colunas();
        self::Campos_Deletar($campos);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, $editar);  
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Edit2($id) {
        $titulo     = __('Fornecedor Editado com Sucesso');
        $dao        = Array('Comercio_Fornecedor', $id);
        $function     = '$this->Fornecedores();';
        $sucesso1   = __('Fornecedor Alterado com Sucesso.');
        $sucesso2   = ''.$_POST["nome"].' teve a alteração bem sucedida';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);      
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa fornecedor e deleta
        $fornecedor = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($fornecedor);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Fornecedor Deletado com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Fornecedores();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Fornecedor deletado com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
    public function Fornecedores_View($fornecedor_id = FALSE) {
        if ($fornecedor_id === FALSE || $fornecedor_id==0 || !isset($fornecedor_id)) return _Sistema_erroControle::Erro_Fluxo('Fornecedor não informado',404);
        // Inclui Endereco
        self::Endereco_Fornecedor(TRUE);
        $this->Tema_Endereco('Visualizar Comentários do Fornecedor #'.$fornecedor_id);
        // Chama Popup e COmentarios
        $this->Fornecedores_Popup(      $fornecedor_id, FALSE);
        $this->Fornecedores_Comentario( $fornecedor_id);
        $this->_Visual->Json_Info_Update('Titulo', __('Visualizar Comentários do Fornecedor'));
    }
    public function Fornecedores_Popup($fornecedor_id = FALSE, $popup = TRUE) {
        if ($fornecedor_id === FALSE || $fornecedor_id==0 || !isset($fornecedor_id)) {
            return _Sistema_erroControle::Erro_Fluxo('Fornecedor não informado',404);
        }
        // mostra todas as suas mensagens
        $where = Array(
            'id'    =>  $fornecedor_id,
        );
        $fornecedor = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor', $where, 1);
        $html  = '<div class="col-sm-4">';
        $html .= '<b>Razão Social:</b> '.           $fornecedor->nome.'<br>'; 
        $html .= '<b>CNPJ:</b> '.                   $fornecedor->cnpj.'<br>'; 
        $html .= '<b>Cpf:</b> '.                    $fornecedor->cpf.'<br>'; 
        $html .= '<b>Banco:</b> '.                  $fornecedor->banco.'<br>'; 
        $html .= '<b>Agencia:</b> '.                $fornecedor->agencia.'<br>';
        $html .= '<b>Conta:</b> '.                  $fornecedor->conta.'<br>';
        $html .= '<b>Site:</b> '.                   $fornecedor->site.'<br>'; 
        $html .= '<b>Ie:</b> '.                     $fornecedor->ie.'<br>';  
        $html .= '</div><div class="col-sm-4">';  
        $html .= '<b>Email Principal:</b> '.        $fornecedor->email.'<br>';  
        $html .= '<b>Fax:</b> '.                    $fornecedor->fax.'<br>';  
        $html .= '<b>Cep:</b> '.                    $fornecedor->cep.'<br>';  
        $html .= '<b>País:</b> '.                   $fornecedor->pais2.'<br>';  
        $html .= '<b>Estado:</b> '.                 $fornecedor->estado2.'<br>';  
        $html .= '<b>Bairro:</b> '.                 $fornecedor->bairro2.'<br>';  
        $html .= '<b>Endereço:</b> '.               $fornecedor->endereco.'<br>';  
        $html .= '<b>Número:</b> '.                 $fornecedor->numero.'<br>';    
        $html .= '<b>Complemento:</b> '.            $fornecedor->complemento.'<br>';    
        $html .= '</div><div class="col-sm-4">';
        $html .= '<b>Telefone de Contato 1:</b> '.  $fornecedor->telefone1.'<br>'; 
        $html .= '<b>Email de Contato 1:</b> '.     $fornecedor->email1.'<br>';  
        $html .= '<b>Celular de Contato 1:</b> '.   $fornecedor->celular1.'<br>'; 
        $html .= '<b>Telefone de Contato 2:</b> '.  $fornecedor->telefone2.'<br>';  
        $html .= '<b>Email de Contato 2:</b> '.     $fornecedor->email2.'<br>';  
        $html .= '<b>Celular de Contato 2:</b> '.   $fornecedor->celular2.'<br>';  
        $html .= '<b>Observação:</b> '.             $fornecedor->obs;
        $html .= '</div>';            
        $titulo = 'Informações do Fornecedor '.$fornecedor->nome.' ('.$fornecedor_id.')';
        if ($popup) {
            $conteudo = array(
                'id' => 'popup',
                'title' => 'Visualizar Fornecedor',
                'botoes' => array(
                    array(
                        'text' => 'Fechar',
                        'clique' => '$( this ).dialog( "close" );'
                    )
                ),
                'html' => $html
            );
            $this->_Visual->Json_IncluiTipo('Popup', $conteudo);
        } else {
            $this->_Visual->Blocar('<div class="row">'.$html.'</div>');
            $this->_Visual->Bloco_Unico_CriaJanela($titulo, '',20);
            $this->_Visual->Json_Info_Update('Titulo', __('Visualizar Fornecedor'));
        }
    }
    /**
     * Comentarios dos Fornecedores
     */
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Comentario($fornecedor_id = FALSE, $export = FALSE) {
        
        $erro = FALSE;
        if ($fornecedor_id === FALSE) {
            $where = Array();
        } else {
            $where = Array('fornecedor'=>$fornecedor_id);
        }
        $i = 0;
        // BOTAO IMPRIMIR / ADD
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Comentário de Fornecedor',
                'comercio/Fornecedor/Fornecedores_Comentario_Add/'.$fornecedor_id,
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'comercio/Fornecedor/Fornecedores_Comentario/'.$fornecedor_id,
            )
        )));
        // CONEXAO
        $linhas = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor_Comentario', $where);
        if ($linhas !== FALSE && !empty($linhas)) {
            if (is_object($linhas)) $linhas = Array(0=>$linhas);
            reset($linhas);
            foreach ($linhas as $indice=>&$valor) {
                //$table['#Id'][$i]        = '#'.$valor->id;
                $table['Comentário'][$i]   =   $valor->comentario;
                $table['Data'][$i]         =   $valor->log_date_add;
                $table['Funções'][$i]      =   $this->_Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Comentário de Fornecedor'        ,'comercio/Fornecedor/Fornecedores_Comentario_Edit/'.$fornecedor_id.'/'.$valor->id.'/'    , '')).
                                                $this->_Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Comentário de Fornecedor'       ,'comercio/Fornecedor/Fornecedores_Comentario_Del/'.$fornecedor_id.'/'.$valor->id.'/'     ,'Deseja realmente deletar esse Comentário desse Fornecedor ?'));
                ++$i;
            }
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Comercio - Fornecedor (#'.$fornecedor_id.') Comentários');
            } else {
                $this->_Visual->Show_Tabela_DataTable($table);
            }
            unset($table);
        } else {   
            if ($export !== FALSE) {
                $erro = __('Nenhum Comentário desse Fornecedor para Exportar');
            } else {
                $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Comentário do Fornecedor</font></b></center>');
            }
        }
        if ($erro === FALSE) {
            $titulo = __('Comentários do Fornecedor').' ('.$i.')';
            $this->_Visual->Bloco_Unico_CriaJanela($titulo, '',10);

            //Carrega Json
            $this->_Visual->Json_Info_Update('Titulo', __('Administrar Comentários do Fornecedor'));
        } else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => $erro
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Comentario_Add($fornecedor_id = FALSE) {
        if ($fornecedor_id === FALSE) return _Sistema_erroControle::Erro_Fluxo('Fornecedor não informado',404);
        // Carrega Config
        $titulo1    = __('Adicionar Comentário do Fornecedor');
        $titulo2    = __('Salvar Comentário do Fornecedor');
        $formid     = 'form_Sistema_Admin_Fornecedores_Comentario';
        $formbt     = __('Salvar');
        $formlink   = 'comercio/Fornecedor/Fornecedores_Comentario_Add2/'.$fornecedor_id.'/';
        $campos = Comercio_Fornecedor_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'fornecedor');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Comentario_Add2($fornecedor_id = FALSE) {
        if ($fornecedor_id === FALSE) return _Sistema_erroControle::Erro_Fluxo('Fornecedor não informado',404);
        $titulo     = __('Comentário do Fornecedor Adicionado com Sucesso');
        $dao        = 'Comercio_Fornecedor_Comentario';
        $function     = '$this->Fornecedores_View('.$fornecedor_id.');';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Comentário do Fornecedor cadastrado com sucesso.');
        $alterar    = Array('fornecedor'=>$fornecedor_id);
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Comentario_Edit($fornecedor_id = FALSE, $id = 0) {
        if ($fornecedor_id === FALSE) return _Sistema_erroControle::Erro_Fluxo('Fornecedor não informado',404);
        if ($id            == 0   ) return _Sistema_erroControle::Erro_Fluxo('Comentário não informado',404);
        // Carrega Config
        $titulo1    = 'Editar Comentário do Fornecedor (#'.$id.')';
        $titulo2    = __('Alteração de Comentário do Fornecedor');
        $formid     = 'form_Sistema_AdminC_FornecedorEdit';
        $formbt     = __('Alterar Comentário do Fornecedor');
        $formlink   = 'comercio/Fornecedor/Fornecedores_Comentario_Edit2/'.$fornecedor_id.'/'.$id;
        $editar     = Array('Comercio_Fornecedor_Comentario', $id);
        $campos = Comercio_Fornecedor_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'fornecedor');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, $editar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Comentario_Edit2($fornecedor_id = FALSE, $id = 0) {
        if ($fornecedor_id === FALSE) return _Sistema_erroControle::Erro_Fluxo('Fornecedor não informado',404);
        if ($id            == 0   ) return _Sistema_erroControle::Erro_Fluxo('Comentário não informado',404);
        $titulo     = __('Comentário de Fornecedor Editado com Sucesso');
        $dao        = Array('Comercio_Fornecedor_Comentario', $id);
        $function     = '$this->Fornecedores_View('.$fornecedor_id.');';
        $sucesso1   = __('Comentário de Fornecedor Alterado com Sucesso.');
        $sucesso2   = ''.$_POST["nome"].' teve a alteração bem sucedida';
        $alterar    = Array('fornecedor'=>$fornecedor_id);
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);      
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Fornecedores_Comentario_Del($fornecedor_id = FALSE, $id = 0) {
        if ($fornecedor_id === FALSE) return _Sistema_erroControle::Erro_Fluxo('Fornecedor não informado',404);
        if ($id            == 0   ) return _Sistema_erroControle::Erro_Fluxo('Comentário não informado',404);
        
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $where = Array('id'=>$id);
        $comentario = $this->_Modelo->db->Sql_Select('Comercio_Fornecedor_Comentario', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($comentario);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Comentário do Fornecedor Deletado com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Fornecedores_View($fornecedor_id);
        
        $this->_Visual->Json_Info_Update('Titulo', __('Comentário de Fornecedor deletado com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
}
?>
