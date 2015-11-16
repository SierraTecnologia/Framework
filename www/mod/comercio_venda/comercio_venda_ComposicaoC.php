<?php
class comercio_venda_ComposicaoControle extends comercio_venda_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses comercio_venda_rede_ComposicaoModelo::Carrega Rede Modelo
    * @uses comercio_venda_rede_ComposicaoView::Carrega Rede View
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
     * Main
     * 
     * FUNCAO PRINCIPAL, EXECUTA O PRIMEIRO PASSO 
     * 
     * @name Main
     * @access public
     * 
     * 
     * @return void
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Main() {
        return FALSE;
    }
    static function Endereco_Composicao($true= TRUE ) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        $titulo = __('Cardápio');
        $link = 'comercio_venda/Composicao/Composicoes';
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
    public function Composicoes($export = FALSE) {
        self::Endereco_Composicao(FALSE);
        $i = 0;
        // BOTAO IMPRIMIR / ADD
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Item ao Cardápio',
                'comercio_venda/Composicao/Composicoes_Add',
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'comercio_venda/Composicao/Composicoes',
            )
        )));
        // CONEXAO
        $composicoes = $this->_Modelo->db->Sql_Select('Comercio_Venda_Composicao');
        $produtos_usados = $this->_Modelo->db->Sql_Select('Comercio_Venda_Composicao_Produtos');
        $produtos_usados_array = Array();
        if ($produtos_usados !== FALSE && !empty($produtos_usados)) {
            if (is_object($produtos_usados)) $produtos_usados = Array(0=>$produtos_usados);
            reset($produtos_usados);
            foreach ($produtos_usados as $indice=>&$valor) {
                if (!isset($produtos_usados_array[$valor->composicao]) || $produtos_usados_array[$valor->composicao]==='') {
                    $produtos_usados_array[$valor->composicao] = '';
                } else {
                    $produtos_usados_array[$valor->composicao] .= '<br>';
                }
                $produtos_usados_array[$valor->composicao] .= '<b>'.$valor->produto2.'</b> (x'.$valor->qnt.')';
            }
        }
        if ($composicoes !== FALSE && !empty($composicoes)) {
            if (is_object($composicoes)) $composicoes = Array(0=>$composicoes);
            reset($composicoes);
            foreach ($composicoes as $indice=>&$valor) {
                $table['#Id'][$i]       = '#'.$valor->id;
                if ($valor->foto==='' || $valor->foto === FALSE) {
                    $foto = WEB_URL.'img'.US.'icons'.US.'clientes.png';
                } else {
                    $foto = $valor->foto;
                }
                $table['Foto'][$i]      = '<img alt="'.__('Foto do Item do Cardápio').'" src="'.$foto.'" style="max-width:100px;" />';
                $table['Nome'][$i]      = $valor->nome;
                $table['Descrição'][$i] = $valor->descricao;
                if (isset($produtos_usados_array[$valor->id])) {
                    $table['Produtos Usados'][$i] = $produtos_usados_array[$valor->id];
                } else {
                    $table['Produtos Usados'][$i] = __('Nenhum');
                }
                $table['Preço'][$i]     = $valor->preco;
                $table['Funções'][$i]   = $this->_Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Item do Cardápio'        ,'comercio_venda/Composicao/Composicoes_Edit/'.$valor->id.'/'    , '')).
                                           $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Item do Cardápio'       ,'comercio_venda/Composicao/Composicoes_Del/'.$valor->id.'/'     ,__('Deseja realmente deletar esse item do Cardápio ?')));
                ++$i;
            }
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Comercio Vendas - Cardápio');
            } else {
                $this->_Visual->Show_Tabela_DataTable($table);
            }
            unset($table);
        } else {          
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Cardápio</font></b></center>');
        }
        $titulo = __('Listagem de Itens do Cardápio').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Administrar Cardápio'));
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Composicoes_Add() {
        self::Endereco_Composicao(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Cardápio');
        $titulo2    = __('Salvar Cardápio');
        $formid     = 'form_Sistema_Admin_Composicoes';
        $formbt     = __('Salvar');
        $formlink   = 'comercio_venda/Composicao/Composicoes_Add2/';
        
        $campos = Comercio_Venda_Composicao_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Composicoes_Add2() {
        $titulo     = __('Cardápio Adicionado com Sucesso');
        $dao        = 'Comercio_Venda_Composicao';
        $function     = '$this->Composicoes();';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Cardápio cadastrado com sucesso.');
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Composicoes_Edit($id) {
        self::Endereco_Composicao(TRUE);
        // Carrega Config
        $titulo1    = 'Editar Cardápio (#'.$id.')';
        $titulo2    = __('Alteração de Cardápio');
        $formid     = 'form_Sistema_AdminC_ComposicaoEdit';
        $formbt     = __('Alterar Cardápio');
        $formlink   = 'comercio_venda/Composicao/Composicoes_Edit2/'.$id;
        $editar     = Array('Comercio_Venda_Composicao', $id);
        $campos = Comercio_Venda_Composicao_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, $editar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Composicoes_Edit2($id) {
        $titulo     = __('Cardápio Editado com Sucesso');
        $dao        = Array('Comercio_Venda_Composicao', $id);
        $function     = '$this->Composicoes();';
        $sucesso1   = __('Cardápio Alterado com Sucesso.');
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
    public function Composicoes_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $linha = $this->_Modelo->db->Sql_Select('Comercio_Venda_Composicao', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($linha);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletada'),
                "mgs_secundaria" => __('Cardápio Deletado com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Composicoes();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Cardápio deletado com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
}
?>