<?php
class predial_InformativoControle extends predial_Controle
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
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'predial/Informativo/Informativos');
        return FALSE;
    }
    static function Endereco_Informativo($true= TRUE ) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        $titulo = __('Informativos');
        $link = 'predial/Informativo/Informativos';
        if ($true === TRUE) {
            $_Controle->Tema_Endereco($titulo, $link);
        } else {
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Informativos_Tabela(&$informativos) {
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Visual     = &$Registro->_Visual;
        $table = Array();
        $i = 0;
        if (is_object($informativos)) $informativos = Array(0=>$informativos);
        reset($informativos);
        foreach ($informativos as &$valor) {
            $table['Bloco'][$i]            = $valor->bloco2;
            $table['Apartamento'][$i]      = $valor->apart2;
            $table['Nome'][$i]             = $valor->nome;
            $table['Descrição'][$i]        = $valor->descricao;
            $table['Data Inicio'][$i]      = $valor->data_inicio;
            $table['Data Fim'][$i]      = $valor->data_fim;
            $table['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Informativo'        ,'predial/Informativo/Informativos_Edit/'.$valor->id.'/'    , '')).
                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Informativo'       ,'predial/Informativo/Informativos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Informativo ?'));
            ++$i;
        }
        return Array($table, $i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Informativos() {
        self::Endereco_Informativo(FALSE);
        $i = 0;
        // Botao Add
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Informativo',
                'predial/Informativo/Informativos_Add',
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'predial/Informativo/Informativos',
            )
        )));
        // Busca
        $informativos = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Informativo');
        if ($informativos !== FALSE && !empty($informativos)) {
            list($table, $i) = self::Informativos_Tabela($informativos);
            $this->_Visual->Show_Tabela_DataTable($table);
            unset($table);
        } else {          
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Informativo</font></b></center>');
        }
        $titulo = __('Listagem de Informativos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Administrar Informativos'));
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Informativos_Add() {
        self::Endereco_Informativo();
        // Chama funcao js
        $this->_Visual->Javascript_Executar('Sierra.Control_Layoult_CalendarioHorario_Intervalo(\'#data_inicio\',\'#data_fim\',\''.APP_HORA_BR.'\',\''.APP_HORA_BR.'\');');

        // Carrega Config
        $titulo1    = __('Adicionar Informativo');
        $titulo2    = __('Salvar Informativo');
        $formid     = 'form_Sistema_Admin_Informativos';
        $formbt     = __('Salvar');
        $formlink   = 'predial/Informativo/Informativos_Add2/';
        $campos = Predial_Bloco_Apart_Informativo_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Informativos_Add2() {
        $titulo     = __('Informativo Adicionado com Sucesso');
        $dao        = 'Predial_Bloco_Apart_Informativo';
        $function     = '$this->Informativos();';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Informativo cadastrado com sucesso.');
        $alterar    = Array();
        $sucesso = $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
        if ($sucesso === TRUE) {
            // Pega o Informativo
            $identificador  = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Informativo', Array(),1,'id DESC');
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
                    "mgs_principal" => __('Informativo não Enviado'),
                    "mgs_secundaria" => __('Verifique se o Morador está registrado no sistema e com um email válido.')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            } else {
                // Mandar Mensagem
                $mensagem   =   'Chegou uma novo informativo para Você<br>'.
                                '<b>Bloco / Apart:</b>'.$identificador->bloco.' / '.$identificador->apart.'<br>';
                // Cadastra Aviso
                $aviso = new \Predial_Bloco_Apart_Informativo_Aviso_DAO();
                $aviso->informativo = $identificador->id;
                $aviso->mensagem = $mensagem;
                $this->_Modelo->bd->Sql_Insert($aviso);
                // Manda Email
                eval('$send	= $mailer'.$enviar.'
                ->setSubject(\'Novo Informativo - \'.SISTEMA_NOME)
                ->setFrom(SISTEMA_EMAIL, SISTEMA_NOME)
                ->addGenericHeader(\'X-Mailer\', \'PHP/\' . phpversion())
                ->addGenericHeader(\'Content-Type\', \'text/html; charset="utf-8"\')
                ->setMessage(\'<strong>'.$mensagem.'</strong>\')
                ->setWrap(78)->send();');
                if (!$send) {
                    $mensagens = array(
                        "tipo" => 'erro',
                        "mgs_principal" => __('Informativo não Enviado'),
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
    public function Informativos_Edit($id) {
        self::Endereco_Informativo();
        // Chama funcao js
        $this->_Visual->Javascript_Executar(
            'Sierra.Control_Layoult_Calendario_Intervalo(\'data_inicio\',\'data_fim\',\''.$datainicial.'\',\''.$datafinal.'\');'
        );

        // Carrega Config
        $titulo1    = 'Editar Informativo (#'.$id.')';
        $titulo2    = __('Alteração de Informativo');
        $formid     = 'form_Sistema_AdminC_InformativoEdit';
        $formbt     = __('Alterar Informativo');
        $formlink   = 'predial/Informativo/Informativos_Edit2/'.$id;
        $editar     = Array('Predial_Bloco_Apart_Informativo', $id);
        $campos = Predial_Bloco_Apart_Informativo_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, $editar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Informativos_Edit2($id) {
        $titulo     = __('Informativo Editado com Sucesso');
        $dao        = Array('Predial_Bloco_Apart_Informativo', $id);
        $function     = '$this->Informativos();';
        $sucesso1   = __('Informativo Alterado com Sucesso.');
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
    public function Informativos_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa informativo e deleta
        $informativo = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart_Informativo', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($informativo);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Informativo deletado com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Informativos();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Informativo deletado com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
}
?>
