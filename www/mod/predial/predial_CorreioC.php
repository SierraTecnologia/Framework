<?php
class predial_CorreioControle extends predial_Controle
{
    public function __construct() {
        parent::__construct();
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses predial_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
    */
    public function Main() {
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'predial/Correio/Correios');
        return FALSE;
    }
    static function Endereco_Correio($true= TRUE ) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        $titulo = __('Corrêios');
        $link = 'predial/Correio/Correios';
        if ($true === TRUE) {
            $_Controle->Tema_Endereco($titulo, $link);
        } else {
            $_Controle->Tema_Endereco($titulo);
        }
    }
    public function Correios_Baixar($correio = FALSE) {
        
        self::Endereco_Correio();
        if ($correio === FALSE || $correio == 0) return FALSE;
        $correio = (int) $correio;
        $where = Array(
            'id' => $correio
        );
        $correios = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Correio', $where);
        $correios->data_recebido = date('d/m/Y H:i:s');
        $sucesso = $this->_Modelo->db->Sql_Update($correios);
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Sucesso'),
                "mgs_secundaria" => __('Correio declarado Recebido com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        // Recupera pagina atualiza
        $this->Correios();
        $this->_Visual->Json_Info_Update('Titulo', __('Correio declarado Recebido com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
    static function Correios_Tabela(&$correios, $recebido = FALSE) {
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Modelo     = &$Registro->_Modelo;
        $Visual     = &$Registro->_Visual;
        $table = Array();
        $i = 0;
        if (is_object($correios)) $correios = Array(0=>$correios);
        reset($correios);
        foreach ($correios as &$valor) {
            $email = FALSE;
            $enviar = FALSE;
            $apartamento  = $Modelo->db->Sql_Select(
                'Predial_Bloco_Apart', 
                Array(
                    'id'        =>  $valor->apart,
                    'bloco'        =>  $valor->bloco
                ),
                1,
                'id DESC'
            );
            if ($apartamento !== FALSE && is_int($apartamento->morador) && $apartamento->morador!=0) {
                $usuario  = $Modelo->db->Sql_Select(
                    'Usuario', 
                    Array('id'=>$apartamento->morador),
                    1
                );
                if ($usuario !== FALSE) {
                    $email = '';
                    if ($usuario->email!='' && \Framework\App\Sistema_Funcoes::Control_Layoult_Valida_Email($usuario->email)) {
                        $email .= $usuario->email;
                    }
                    if ($usuario->email2!='' && \Framework\App\Sistema_Funcoes::Control_Layoult_Valida_Email($usuario->email2)) {
                        if ($email!='') $email .= '<br>';
                        $email .= $usuario->email2;
                    }
                    if ($email=='') {
                        $email = '<p class="text-error">Morador sem nenhum email válido</p>';
                    }
                }
            }
            // Avisa que nao foi, ou manda 
            if ($email === FALSE || $email=='') {
                $email = '<p class="text-error">Morador não registrado</p>';
            }
            $table['Bloco'][$i]                        = $valor->bloco2;
            $table['Apartamento'][$i]                  = $valor->apart2;
            $table['Tipo de Correio'][$i]              = $valor->categoria2;
            $table['Responsável'][$i]                  = $valor->responsavel;
            $table['Email para Avisos'][$i]                  = $email;
            $table['Data Recebida Adm/Portaria'][$i]   = $valor->data_entregue;
            if ($recebido !== FALSE) {
                $table['Data Entregue ao Morador'][$i]     = $valor->data_recebido;
                $table['Funções'][$i]                      = '';
            } else {
                $table['Funções'][$i]                      = $Visual->Tema_Elementos_Btn('Baixar'     ,Array('Declarar Recebido Pelo Morador'        ,'predial/Correio/Correios_Baixar/'.$valor->id.'/'    , ''));
            }
            $table['Funções'][$i]                      .=  $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Correio'        ,'predial/Correio/Correios_Edit/'.$valor->id.'/'    , '')).
                                                            $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Correio'       ,'predial/Correio/Correios_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Correio ?'));
            ++$i;
        }
        return Array($table, $i);
    }
    public function Correios() {
        self::Endereco_Correio(FALSE);
        $this->Correios_Bloco(FALSE, 10);
        $this->Correios_Bloco(TRUE);
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo', __('Correios')); 
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    protected function Correios_Bloco($recebido = FALSE, $gravidade=0) {
        $i = 0;
        if ($recebido === FALSE) {
            $titulo = __('Correios recebidos e não entregues');
            $where = Array(
                'data_recebido' => '0000-00-00 00:00:00'
            );
            // Botao Add
            $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
                Array(
                    'Adicionar Correio',
                    'predial/Correio/Correios_Add',
                    ''
                ),
                Array(
                    'Print'     => TRUE,
                    'Pdf'       => TRUE,
                    'Excel'     => TRUE,
                    'Link'      => 'predial/Correio/Correios',
                )
            )));
        } else {
            $titulo = __('Histórico de Correios Entregues');
            $where = Array(
                '!data_recebido' => '0000-00-00 00:00:00'
            );
        }
        $correios = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Correio', $where);
        if ($correios !== FALSE && !empty($correios)) {
            list($table, $i) = self::Correios_Tabela($correios, $recebido);
            $this->_Visual->Show_Tabela_DataTable($table);
        } else {     
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Correio</font></b></center>');
        }
        $titulo = 'Listagem de '.$titulo.' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo, '', $gravidade);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Administrar ').$titulo);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Correios_Add() {
        self::Endereco_Correio();
        // Carrega Config
        $titulo1    = __('Adicionar Correio');
        $titulo2    = __('Salvar Correio');
        $formid     = 'form_Sistema_Admin_Correios';
        $formbt     = __('Salvar');
        $formlink   = 'predial/Correio/Correios_Add2/';
        $campos = Predial_Bloco_Apart_Correio_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'data_recebido');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Correios_Add2() {
        //Validar_Email
        $titulo     = __('Correio Adicionado com Sucesso');
        $dao        = 'Predial_Bloco_Apart_Correio';
        $function     = '$this->Correios();';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Correio cadastrado com sucesso.');
        $alterar    = Array();
        $sucesso = $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
        if ($sucesso === TRUE) {
            // Pega o Correio
            $identificador  = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Correio', FALSE,1,'id DESC');
            // Captura Apartamento Responsavel
            $enviar = FALSE;
            $apartamento  = $this->_Modelo->db->Sql_Select(
                'Predial_Bloco_Apart', 
                Array(
                    'id'        =>  $identificador->apart,
                    'bloco'        =>  $identificador->bloco
                ),
                1,
                'id DESC'
            );
            if (!is_object($apartamento)) {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Erro'),
                    "mgs_secundaria" => __('Apartamento não existe.')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                return FALSE;
            }
            if (is_int($apartamento->morador) && $apartamento->morador!=0) {
                $usuario  = $this->_Modelo->db->Sql_Select(
                    'Usuario', 
                    Array('id'=>$apartamento->morador),
                    1
                );
                if ($usuario !== FALSE) {
                    $nome = $usuario->nome;
                    $enviar = '';
                    if ($usuario->email!='' && \Framework\App\Sistema_Funcoes::Control_Layoult_Valida_Email($usuario->email)) {
                        $enviar .= '->setTo(\''.$usuario->email.'\', \''.$nome.'\')';
                    }
                    if ($usuario->email2!='' && \Framework\App\Sistema_Funcoes::Control_Layoult_Valida_Email($usuario->email2)) {
                        $enviar .= '->setTo(\''.$usuario->email2.'\', \''.$nome.'\')';
                    }
                }
            }
            // Avisa que nao foi, ou manda 
            if ($enviar === FALSE || $enviar=='') {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Aviso não Enviado'),
                    "mgs_secundaria" => __('Verifique se o Morador está registrado no sistema e com um email válido.')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            } else {
                // Mandar Mensagem
                $mensagem   =   'Informamos que encontra-se na administração corrreio registrado<br>'.
                                '<b>Bloco / Apart:</b>'.$identificador->bloco.' / '.$identificador->apart.'<br>'.
                                '<b>Responsável:</b>'.$identificador->responsavel.'<br>'.
                                '<b>Data Entregue:</b>'.$identificador->data_entregue.'<br>';
                // Cadastra Aviso
                $aviso = new \Predial_Bloco_Apart_Correio_Aviso_DAO();
                $aviso->correio = $identificador->id;
                $aviso->mensagem = $mensagem;
                $this->_Modelo->bd->Sql_Insert($aviso);
                // Manda Email
                eval('$send	= $mailer'.$enviar.'
                ->setSubject(\'Nova Encomenda (1Â° Aviso) - \'.SISTEMA_NOME)
                ->setFrom(SISTEMA_EMAIL, SISTEMA_NOME)
                ->addGenericHeader(\'X-Mailer\', \'PHP/\' . phpversion())
                ->addGenericHeader(\'Content-Type\', \'text/html; charset="utf-8"\')
                ->setMessage(\'<strong>'.$mensagem.'</strong>\')
                ->setWrap(78)->send();');
                if (!$send) {
                    $mensagens = array(
                        "tipo" => 'erro',
                        "mgs_principal" => __('Aviso não Enviado'),
                        "mgs_secundaria" => __('Verifique se o Morador está registrado no sistema e com um email válido.')
                    );
                    $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                }
            }
        }
    }
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Correios_Edit($id) {
        self::Endereco_Correio();
        // Carrega Config
        $titulo1    = 'Editar Correio (#'.$id.')';
        $titulo2    = __('Alteração de Correio');
        $formid     = 'form_Sistema_AdminC_CorreioEdit';
        $formbt     = __('Alterar Correio');
        $formlink   = 'predial/Correio/Correios_Edit2/'.$id;
        $editar     = Array('Predial_Bloco_Apart_Correio', $id);
        $campos = Predial_Bloco_Apart_Correio_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, $editar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Correios_Edit2($id) {
        $titulo     = __('Correio Editado com Sucesso');
        $dao        = Array('Predial_Bloco_Apart_Correio', $id);
        $function     = '$this->Correios();';
        $sucesso1   = __('Correio Alterado com Sucesso.');
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
    public function Correios_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa setor e deleta
        $correio = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Correio', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($correio);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Correio deletado com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        // Recupera pagina atualiza
        $this->Correios();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Correio deletado com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    static function Personalizados_Tabela(&$correios, $recebido = FALSE) {
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Modelo     = &$Registro->_Modelo;
        $Visual     = &$Registro->_Visual;
        $table = Array();
        $i = 0;
        if (is_object($correios)) $correios = Array(0=>$correios);
        reset($correios);
        foreach ($correios as &$valor) {
            $table['Tipo de Correio'][$i]              = $valor->categoria2;
            $table['Responsável'][$i]                  = $valor->responsavel;
            $table['Data Recebida Adm/Portaria'][$i]   = $valor->data_entregue;
            if ($recebido !== FALSE) {
                $table['Data Entregue ao Morador'][$i]     = $valor->data_recebido;
            }
            ++$i;
        }
        return Array($table, $i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    static function Personalizados($apartamento, $recebido = FALSE, $gravidade=0, $adicionar= TRUE ) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $i = 0;
        $html = '';
        if ($recebido === FALSE) {
            $titulo = __('Correios recebidos e não entregues');
            $where = Array(
                'data_recebido' => '0000-00-00 00:00:00',
                'apart'         => $apartamento
            );
            // Botao Add
            $html .= $Registro->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
                FALSE,
                Array(
                    'Print'     => TRUE,
                    'Pdf'       => TRUE,
                    'Excel'     => TRUE,
                    'Link'      => 'predial/Correio/Correios',
                )
            ));
        } else {
            $titulo = __('Histórico de Correios Entregues');
            $where = Array(
                '!data_recebido' => '0000-00-00 00:00:00',
                'apart'         => $apartamento
            );
        }
        $correios = $Registro->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Correio', $where);
        if ($correios !== FALSE && !empty($correios)) {
            list($table, $i) = self::Correios_Tabela($correios, $recebido);
            $html .= $Registro->_Visual->Show_Tabela_DataTable($table, '', FALSE);
        } else {     
            $html .= '<center><b><font color="#FF0000" size="3">Nenhum Correio para Você</font></b></center>';
        }
        $titulo = 'Listagem de '.$titulo.' ('.$i.')';
        return Array($titulo, $html);
    }
    
    
}
?>
