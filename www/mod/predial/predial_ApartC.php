<?php
class predial_ApartControle extends predial_Controle
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
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'predial/Apart/Aparts');
        return FALSE;
    }
    static function Endereco_Apart($true= TRUE ) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        $titulo = __('Apartamentos');
        $link = 'predial/Apart/Aparts';
        if ($true === TRUE) {
            $_Controle->Tema_Endereco($titulo, $link);
        } else {
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Aparts_Tabela(&$apartamentos) {
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Visual     = &$Registro->_Visual;
        $table = Array();
        $i = 0;
        if (is_object($apartamentos)) $apartamentos = Array(0=>$apartamentos);
        reset($apartamentos);
        foreach ($apartamentos as &$valor) {
            $table['Bloco'][$i]            = $valor->bloco2;
            $table['Número'][$i]           = $valor->num;
            if ($valor->morador!=0 && $valor->morador2!=NULL) {
                $table['Morador'][$i]          = $valor->morador2;
            } else {
                $table['Morador'][$i]          = '<p class="text-error">Não Registrado</p>';
            }
            $table['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Apartamento'        ,'predial/Apart/Aparts_Edit/'.$valor->id.'/'    , '')).
                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Apartamento'       ,'predial/Apart/Aparts_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Apartamento ?'));
            ++$i;
        }
        return Array($table, $i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Aparts() {
        self::Endereco_Apart(FALSE);
        $i = 0;
        // Botao Add
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Apartamento',
                'predial/Apart/Aparts_Add',
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'predial/Apart/Aparts',
            )
        )));
        // Busca
        $apartamentos = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart');
        if ($apartamentos !== FALSE && !empty($apartamentos)) {
            list($table, $i) = self::Aparts_Tabela($apartamentos);
            $this->_Visual->Show_Tabela_DataTable($table);
            unset($table);
        } else {           
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Apartamento</font></b></center>');
        }
        $titulo = __('Listagem de Apartamentos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Administrar Apartamentos'));
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Aparts_Add() {
        self::Endereco_Apart();
        // Carrega Config
        $titulo1    = __('Adicionar Apartamento');
        $titulo2    = __('Salvar Apartamento');
        $formid     = 'form_Sistema_Admin_Aparts';
        $formbt     = __('Salvar');
        $formlink   = 'predial/Apart/Aparts_Add2/';
        $campos = Predial_Bloco_Apart_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Aparts_Add2() {
        $titulo     = __('Apartamento Adicionado com Sucesso');
        $dao        = 'Predial_Bloco_Apart';
        $function     = '$this->Aparts();';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Apartamento cadastrado com sucesso.');
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Aparts_Edit($id) {
        self::Endereco_Apart();
        // Carrega Config
        $titulo1    = 'Editar Apartamentoamento (#'.$id.')';
        $titulo2    = __('Alteração de Apartamento');
        $formid     = 'form_Sistema_AdminC_ApartEdit';
        $formbt     = __('Alterar Apartamento');
        $formlink   = 'predial/Apart/Aparts_Edit2/'.$id;
        $editar     = Array('Predial_Bloco_Apart', $id);
        $campos = Predial_Bloco_Apart_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, $editar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Aparts_Edit2($id) {
        $titulo     = __('Apartamento Editado com Sucesso');
        $dao        = Array('Predial_Bloco_Apart', $id);
        $function     = '$this->Aparts();';
        $sucesso1   = __('Apartamento Alterado com Sucesso.');
        $sucesso2   = ''.$_POST["num"].' teve a alteração bem sucedida';
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
    public function Aparts_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa apartamento e deleta
        $apartamento = $this->_Modelo->db->Sql_Select('Predial_Bloco_Apart', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($apartamento);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Apartamento deletado com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Aparts();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Apartamento deletado com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
}
?>
