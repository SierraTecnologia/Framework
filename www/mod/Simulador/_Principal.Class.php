<?php
class tag_Principal implements \Framework\PrincipalInterface
{
    /**
     * Função Home para o modulo mensagem aparecer na pagina HOME
     * 
     * @name Home
     * @access public
     * @static
     * 
     * @param Class &$controle Classe Controle Atual passada por Ponteiro
     * @param Class &$modelo Modelo Passado por Ponteiro
     * @param Class &$Visual Visual Passado por Ponteiro
     *
     * @uses tag_Controle::$num_Indicados
     * 
     * @return void 
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 3.1.1
     */
    static function Home(&$controle, &$modelo, &$Visual){
        self::Widgets();
        return true;
    }
    /**
     * 
     * @return boolean
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 3.1.1
     */
    static function Config(){
        return false;
    }
    
    static function Relatorio($data_inicio,$data_final,$filtro=false){
        return false;
    }
    
    static function Estatistica($data_inicio,$data_final,$filtro=false){
        return false;
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 3.1.1
     */
    public static function Widgets(){
        $Registro = &\Framework\App\Registro::getInstacia();
        $modelo = $Registro->_Modelo;
        $Visual = $Registro->_Visual;
        // Tags
        $tag_qnt = $modelo->db->Sql_Contar('Simulador_Tag');
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Tags/Caracteristicas', 
            'Simulador/Tag/Tags', 
            'folder-open',
            $tag_qnt, 
            'light-green', 
            false, 
            300
        );
    }
    
    
    /***********************
     * BUSCAS
     */
    static function Busca(&$controle, &$modelo, &$Visual,$busca){
        $i = 0;
        // Busca Tags
        $result = self::Busca_Tags($controle, $modelo, $Visual, $busca);
        if($result!==false){
            $i = $i + $result;
        }
        if(is_int($i) && $i>0){
            return $i;
        }else{
            return false;
        }
    }
    static function Busca_Tags($controle, $modelo, $Visual, $busca){
        $where = Array(Array(
          'nome'                    => '%'.$busca.'%',
          'obs'                     => '%'.$busca.'%',
          'arquivo'                 => '%'.$busca.'%'
        ));
        $i = 0;
        $tags = $modelo->db->Sql_Select('Simulador_Tag',$where);
        if($tags===false) return false;
        // add botao
        $Visual->Blocar('<a title="Adicionar Tag" class="btn btn-success lajax explicar-titulo" acao="" href="'.URL_PATH.'Simulador/Tag/Tags_Add">Adicionar nova Tag</a><div class="space15"></div>');
        if(is_object($tags)) $tags = Array(0=>$tags);
        if($tags!==false && !empty($tags)){
            $funcao = '';
            $tabela = Array();
            $i = 0;
            if($raiz!==false && $raiz!=0){
                $resultado_pasta = $Modelo->db->Sql_Select('Tag', Array('id'=>$raiz),1);
                if($resultado_pasta===false){
                    throw new \Exception('Essa Pasta não existe:'. $raiz, 404);
                }
                $tabela['Tipo'][$i]             = '<a href="'.URL_PATH.'tag/Tag/Tags/'.$resultado_pasta->parent.'" border="1" class="lajax" acao=""><img src="'.WEB_URL.'img'.US.'arquivos'.US.'pastavoltar.png" alt="0" /></a>';
                $tabela['Nome'][$i]             = '<a href="'.URL_PATH.'tag/Tag/Tags/'.$resultado_pasta->parent.'" border="1" class="lajax" acao="">Voltar para a Pasta Anterior</a>';
                $tabela['Descrição'][$i]        = '';
                $tabela['Tamanho'][$i]          = '';
                $tabela['Criador'][$i]          = '';
                $tabela['Data'][$i]  = '';
                $tabela['Funções'][$i]          = '';
                ++$i;
            }
            if($tags!==false){
                // Percorre Tags
                if(is_object($tags)) $tags = Array(0=>$tags);
                reset($tags);
                if(!empty($tags)){
                    $perm_download = \Framework\App\Registro::getInstacia()->_Acl->Get_Permissao_Url('tag/Tag/Download');
                    $perm_editar = \Framework\App\Registro::getInstacia()->_Acl->Get_Permissao_Url('tag/Tag/Tags_Edit');
                    $perm_del = \Framework\App\Registro::getInstacia()->_Acl->Get_Permissao_Url('tag/Tag/Tags_Del');

                    foreach ($tags as &$valor) {
                        if($valor->tipo==1){
                            $tipo       =   'pasta';
                            $foto = WEB_URL.'img'.US.'arquivos'.US.$tipo.'.png';
                        }else{
                            $tipo  = \Framework\App\Sistema_Funcoes::Control_Arq_Ext($valor->ext);
                            $endereco = ARQ_PATH.'tags'.DS.strtolower($valor->arquivo).'.'.$tipo;
                            if(!file_exists($endereco)){
                                continue;
                            }
                            if(file_exists(WEB_PATH.'img'.US.'arquivos'.US.$tipo.'.png')){
                                $foto = WEB_URL.'img'.US.'arquivos'.US.$tipo.'.png';
                            }else{
                                $foto = WEB_URL.'img'.US.'arquivos'.US.'desconhecido.png';
                            }
                        }

                        // Tamanho
                        $tamanho = (int) $valor->tamanho;
                        if($tamanho === 0){
                            if($valor->tipo==1){
                                $tamanho = tag_Controle::Tags_AtualizaTamanho_Pai($valor);
                            }else{
                                $tamanho = filesize($endereco);
                                $Modelo->db->Sql_Update($valor);
                            }
                        }

                        if($valor->tipo==1){
                            $tabela['Tipo'][$i]             = '<a href="'.URL_PATH.'tag/Tag/Tags/'.$valor->id.'/" border="1" class="lajax" acao=""><img src="'.$foto.'" alt="1" /></a>';
                            $tabela['Nome'][$i]             = '<a href="'.URL_PATH.'tag/Tag/Tags/'.$valor->id.'/" border="1" class="lajax" acao="">'.$valor->nome.'</a>';
                        }else{
                            $tabela['Tipo'][$i]             = '<a href="'.URL_PATH.'tag/Tag/Download/'.$valor->id.'/" border="1" target="_BLANK"><img src="'.$foto.'" alt="'.$tipo.'" /></a>';
                            $tabela['Nome'][$i]             = '<a href="'.URL_PATH.'tag/Tag/Download/'.$valor->id.'/" border="1" target="_BLANK">'.$valor->nome.'</a>';
                        }
                        $tabela['Descrição'][$i]        = $valor->obs;
                        $tabela['Tamanho'][$i]          = \Framework\App\Sistema_Funcoes::Tranf_Byte_Otimizado($tamanho);
                        $tabela['Criador'][$i]          = $valor->usuario2;
                        $tabela['Data'][$i]             = $valor->log_date_add;

                        if($valor->tipo==1){
                            $tabela['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pasta'        ,'tag/Tag/Tags_Edit/'.$valor->id.'/'.$raiz    ,''),$perm_editar).
                                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pasta'       ,'tag/Tag/Tags_Del/'.$valor->id.'/'.$raiz     ,'Deseja realmente deletar essa pasta ?'),$perm_del);
                        }else{
                            $tabela['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Baixar'     ,Array('Download de Arquivo'   ,'tag/Tag/Download/'.$valor->id    ,''),$perm_download).
                                                              $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Arquivo'        ,'tag/Tag/Tags_Edit/'.$valor->id.'/'.$raiz    ,''),$perm_editar).
                                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Arquivo'       ,'tag/Tag/Tags_Del/'.$valor->id.'/'.$raiz     ,'Deseja realmente deletar esse arquivo ?'),$perm_del);
                        }
                        $funcao .= $tabela['Funções'][$i];
                        ++$i;
                    }
                }
            }
            if($funcao===''){
                unset($tabela['Funções']);
            }
            // Desconta Primeiro Registro
            if($raiz!==false && $raiz!=0){
                $i = $i-1;
            }
            // Retorna List
            $Visual->Show_Tabela_DataTable($tabela);
        }else{        
            $Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Arquivo/Pasta na Busca '.$busca.'</font></b></center>');
        }
        $titulo = 'Busca de Pasta/Arquivo na Tag: '.$busca.' ('.$i.')';
        $Visual->Bloco_Unico_CriaJanela($titulo);
        return $i;
    }
    /**
     * Classe de Manutenção do Sistema
     * #update Acabar de Fazer
     * 
     * @param Array $log Sempre será Adicionado Novos Arrays com Indice ['Nome'] e ['Descricao']
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 3.1.1
     */
    public static function Manutencao(&$log){
        $Registro = &\Framework\App\Registro::getInstacia();
        $modelo = $Registro->_Modelo;
        $Visual = $Registro->_Visual;
        // Endereços dos arquivos $tags_chaves['endereco'] = 'ID';
        $tags_chaves = Array();
        
        // Carrega Tags
        $tag = $modelo->db->Sql_Select('Tag',false,0, '', '*', true, '*'); // Pega Todos os Dados de Tags -> Até os Deletados.
        if(is_object($tag))  $tag = Array($tag);
        if($tag===false)     $tag = Array();
        
        // Verifica se Todos tem Download Válido
        if(!empty($tag)){
            foreach($tag as $valor){

            }
        }
        
        // Percorre Todos os Arquivos da Tag no SERVIDOR.
        //E verifica se Algum não está sendo usado.
    }
}
?>