<?php

class Transporte_CaminhoneiroControle extends Transporte_Controle
{
    public function __construct() {
        parent::__construct();
    }
    static function Endereco_Caminhoneiro($true= true ) {
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        $titulo = __('Autonômos');
        $link = 'Transporte/Caminhoneiro/Caminhoneiros';
        if ($true === true) {
            $_Controle->Tema_Endereco($titulo, $link);
        } else {
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.24
     */
    public function Main() {
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Transporte/Caminhoneiro/Caminhoneiros');
        return false;
    }
    static function Caminhoneiros_Tabela(&$caminhoneiro) {
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Visual     = &$Registro->_Visual;
        $table = Array();
        $i = 0;
        if (is_object($caminhoneiro)) $caminhoneiro = Array(0=>$caminhoneiro);reset($caminhoneiro);
        $perm_view = $Registro->_Acl->Get_Permissao_Url('Transporte/Caminhoneiro/Visualizar');
        foreach ($caminhoneiro as &$valor) {                
            $table[__('Id')][$i]           = '#'.$valor->id;
            $table[__('Categoria')][$i]    = $valor->categoria2;
            $table[__('Nome')][$i]         = $valor->usuario2;
            $table[__('Capacidade')][$i]   = $valor->capacidade;
            $table[__('Telefone')][$i]     = $valor->telefone;
            $table[__('Visualizar')][$i]   = $Visual->Tema_Elementos_Btn('Visualizar'     ,Array(__('Visualizar')        ,'Transporte/Caminhoneiro/Visualizar/'.$valor->id    , ''), $perm_view);
            
            ++$i;
        }
        return Array($table, $i);
    }
    public function Visualizar($id, $export = false) {

        $caminhoneiro = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro', 'TC.id=\''.((int) $id).'\'',1);
        
        $this->Gerador_Visualizar_Unidade($caminhoneiro, 'Visualizar Autônomo #'.$id);
        
    }
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.24
     */
    public function Caminhoneiros($export = false) {
        $i = 0;
        self::Endereco_Caminhoneiro(false);
        $caminhoneiro = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro');
        if (is_object($caminhoneiro)) $caminhoneiro = Array(0=>$caminhoneiro);
        if ($caminhoneiro !== false && !empty($caminhoneiro)) {
            list($table, $i) = self::Caminhoneiros_Tabela($caminhoneiro);
            // SE exportar ou mostra em tabela
            if ($export !== false) {
                self::Export_Todos($export, $table, 'Autonômos');
            } else {
                $this->_Visual->Show_Tabela_DataTable(
                    $table,     // Array Com a Tabela
                    '',          // style extra
                    true,        // true -> Add ao Bloco, false => Retorna html
                    false,        // Apagar primeira coluna ?
                    Array(       // Ordenacao
                        Array(
                            0,'desc'
                        )
                    )
                );
            }
            unset($table);
        } else {
            if ($export !== false) {
                $mensagem = __('Nenhum Autonômo Cadastrado para exportar');
            } else {
                $mensagem = __('Nenhum Autonômo Cadastrado');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem de Autonômos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Listagem de Autonômos'));
    }
    /**
     * Painel Adminstrativo de Caminhoneiros
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.24
     */
    public function Painel() {
        return true;
    }
    static function Painel_Caminhoneiro($camada, $retornar= true ) {
        $existe = false;
        if ($retornar==='false') $retornar = false;
        // Verifica se Existe Conexao, se nao tiver abre o adicionar conexao, se nao, abre a pasta!
        $Registro = &\Framework\App\Registro::getInstacia();
        $resultado = $Registro->_Modelo->db->Sql_Select('Transporte_Caminhoneiro', '{sigla}usuario=\''.$Registro->_Acl->Usuario_GetID().'\'',1);
        if (is_object($resultado)) {
            $existe = true;
        }
        
        // Dependendo se Existir Cria Formulario ou Lista arquivos
        if ($existe === false) {
            $html = '<b>'.__('Ainda faltam insformações sobre você').'</b><br>'.self::Painel_Caminhoneiro_Add($camada);
        } else {
            $html = __('Painel');
        }
        
        if ($retornar === true) {
            return $html;
        } else {
            $conteudo = array(
                'location'  =>  '#'.$camada,
                'js'        =>  '',
                'html'      =>  $html
            );
            $Registro->_Visual->Json_IncluiTipo('Conteudo', $conteudo);
        }
        return true;
    }
    static protected function Painel_Caminhoneiro_Add($camada) {
        // Carrega Config
        $titulo1    = __('Salvar Dados');
        $titulo2    = __('Salvar Dados');
        $formid     = 'form_Transporte_Caminhoneiro_Add';
        $formbt     = __('Salvar Dados');
        $formlink   = 'Transporte/Caminhoneiro/Painel_Caminhoneiro_Add2/'.$camada;
        $campos = Transporte_Caminhoneiro_DAO::Get_Colunas();
        // Remove Essas Colunas
        self::DAO_Campos_Retira($campos, 'usuario');
        // Chama Formulario
       return \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, false,'html', false);
    }
    public function Painel_Caminhoneiro_Add2($camada) {
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro', '{sigla}usuario=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if (is_object($resultado)) {
            self::Painel_Caminhoneiro($camada, false);
            return true;
        }
        $titulo     = __('Dados Atualizados com Sucesso');
        $dao        = 'Transporte_Caminhoneiro';
        $function     = 'Transporte_CaminhoneiroControle::Painel_Caminhoneiro(\''.$camada.'\',\'false\');';
        $sucesso1   = __('Atualização bem sucedida');
        $sucesso2   = __('Dados Atualizados com sucesso.');
        $alterar    = Array(
            'usuario'        =>  $this->_Acl->Usuario_GetID(),
        );
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
}
?>
