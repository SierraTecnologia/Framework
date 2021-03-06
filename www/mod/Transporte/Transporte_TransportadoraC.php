<?php

class Transporte_TransportadoraControle extends Transporte_Controle
{
    public function __construct(){
        parent::__construct();
    }
    static function Endereco_Transportadora($true=true){
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        $titulo = __('Transportadoras');
        $link = 'Transporte/Transportadora/Transportadoras';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Main(){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Transporte/Transportadora/Transportadoras');
        return false;
    }
    static function Transportadoras_Tabela(&$transportadora){
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Visual     = &$Registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($transportadora)) $transportadora = Array(0=>$transportadora);reset($transportadora);
        $perm_view = $Registro->_Acl->Get_Permissao_Url('Transporte/Transportadora/Visualizar');
        foreach ($transportadora as &$valor) {                
            $tabela['Id'][$i]           = '#'.$valor->id;
            $tabela['Razão Social'][$i] = $valor->usuario2;
            $tabela['Categoria'][$i]    = $valor->categoria2;
            $tabela['Observação'][$i]   = $valor->obs;
            $tabela['Visualizar'][$i]   = $Visual->Tema_Elementos_Btn('Visualizar'     ,Array('Visualizar'        ,'Transporte/Transportadora/Visualizar/'.$valor->id    ,''),$perm_view);
            
            ++$i;
        }
        return Array($tabela,$i);
    }
    public function Visualizar($id,$export=false){
        
        
        $transportadora = $this->_Modelo->db->Sql_Select('Transporte_Transportadora','TT.id=\''.((int) $id).'\'',1);
        
        $this->Gerador_Visualizar_Unidade($transportadora, 'Visualizar Transportadora #'.$id);
        
    }
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Transportadoras($export=false){
        $i = 0;
        self::Endereco_Transportadora(false);
        $transportadora = $this->_Modelo->db->Sql_Select('Transporte_Transportadora');
        if(is_object($transportadora)) $transportadora = Array(0=>$transportadora);
        if($transportadora!==false && !empty($transportadora)){
            list($tabela,$i) = self::Transportadoras_Tabela($transportadora);
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Transportadoras');
            }else{
                $this->_Visual->Show_Tabela_DataTable(
                    $tabela,     // Array Com a Tabela
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
            unset($tabela);
        }else{
            if($export!==false){
                $mensagem = __('Nenhum Transportadora Cadastrada para exportar');
            }else{
                $mensagem = __('Nenhum Transportadora Cadastrada');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem de Transportadoras').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Listagem de Transportadoras'));
    }
    /**
     * Painel Adminstrativo de Transportadoras
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Painel(){
        return true;
    }
    static function Painel_Transportadora($camada,$retornar=true){
        $existe = false;
        if($retornar==='false') $retornar = false;
        // Verifica se Existe Conexao, se nao tiver abre o adicionar conexao, se nao, abre a pasta!
        $Registro = &\Framework\App\Registro::getInstacia();
        $resultado = $Registro->_Modelo->db->Sql_Select('Transporte_Transportadora','{sigla}usuario=\''.$Registro->_Acl->Usuario_GetID().'\'',1);
        if(is_object($resultado)){
            $existe = true;
        }
        
        // Dependendo se Existir Cria Formulario ou Lista arquivos
        if($existe===false){
            $html = '<b>Ainda faltam insformações sobre vocês</b><br>'.self::Painel_Transportadora_Add($camada);
        }else{
            $html = __('Painel');
        }
        
        if($retornar===true){
            return $html;
        }else{
            $conteudo = array(
                'location'  =>  '#'.$camada,
                'js'        =>  '',
                'html'      =>  $html
            );
            $Registro->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
        }
        /*
                $this->_Visual->Bloco_Customizavel(Array(
                    Array(
                        'span'      =>      5,
                        'conteudo'  =>  Array(Array(
                            'div_ext'   =>      false,
                            'title_id'  =>      false,
                            'title'     =>      $titulo.' #'.$identificador->id,
                            'html'      =>      $html,
                        ),),
                    ),
                    Array(
                        'span'      =>      7,
                        'conteudo'  =>  Array(Array(
                            'div_ext'   =>      false,
                            'title_id'  =>      false,
                            'title'     =>      'Pasta da '.$titulo.' #'.$identificador->id.' na Transportadora',
                            'html'      =>      '<span id="proposta_'.$identificador->id.'">'.self::Painel_Transportadora('comercio_Proposta',$identificador->id,'proposta_'.$identificador->id).'</span>',
                        )/*,Array(
                            'div_ext'   =>      false,
                            'title_id'  =>      false,
                            'title'     =>      'Sub-'.$tema,
                            'html'      =>      ' Aqui tem !',
                        ),*//*),
                    )
                ));*/
        return true;
    }
    static protected function Painel_Transportadora_Add($camada){
        // Carrega Config
        $titulo1    = __('Salvar Dados');
        $titulo2    = __('Salvar Dados');
        $formid     = 'form_Transporte_Transportadora_Add';
        $formbt     = __('Salvar Dados');
        $formlink   = 'Transporte/Transportadora/Painel_Transportadora_Add2/'.$camada;
        $campos = Transporte_Transportadora_DAO::Get_Colunas();
        // Remove Essas Colunas
        self::DAO_Campos_Retira($campos, 'usuario');
        // Chama Formulario
       return \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,false,'html',false);
    }
    public function Painel_Transportadora_Add2($camada){
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Transportadora','{sigla}usuario=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if(is_object($resultado)){
            self::Painel_Transportadora($camada,false);
            return true;
        }
        $titulo     = __('Dados Atualizados com Sucesso');
        $dao        = 'Transporte_Transportadora';
        $funcao     = 'Transporte_TransportadoraControle::Painel_Transportadora(\''.$camada.'\',\'false\');';
        $sucesso1   = __('Atualização bem sucedida');
        $sucesso2   = __('Dados Atualizados com sucesso.');
        $alterar    = Array(
            'usuario'        =>  $this->_Acl->Usuario_GetID(),
        );
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
}
?>
