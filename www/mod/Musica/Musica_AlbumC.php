<?php
class Musica_AlbumControle extends Musica_Controle
{
    public function __construct(){
        parent::__construct();
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses album_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
    */
    public function Main(){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Musica/Album/Albuns');
        return false;
    }
    static function Endereco_Album($true=true,$artista=false){
        if($artista==='false') $artista = false;
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        if($artista===false){
            $titulo = __('Todos os Albuns');
            $link   = 'Musica/Album/Albuns';
        }else{
            Musica_ArtistaControle::Endereco_Artista();
            $titulo = $artista->nome;
            $link   = 'Musica/Album/Albuns/'.$artista->id;
        }
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Albuns_Tabela(&$albuns,$artista=false){
        if($artista==='false') $artista = false;
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Modelo     = &$Registro->_Modelo;
        $Visual     = &$Registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($albuns)) $albuns = Array(0=>$albuns);
        reset($albuns);
        foreach ($albuns as &$valor) {
            if($artista===false || $artista==0){
                
                $tabela['Artista'][$i]   = $valor->artista2;
                $edit_url   = 'Musica/Album/Albuns_Edit/'.$valor->id.'/';
                $del_url    = 'Musica/Album/Albuns_Del/'.$valor->id.'/';
            }else{
                $edit_url   = 'Musica/Album/Albuns_Edit/'.$valor->id.'/'.$valor->artista.'/';
                $del_url    = 'Musica/Album/Albuns_Del/'.$valor->id.'/'.$valor->artista.'/';
            }
            if($valor->foto==='' || $valor->foto===false){
                $foto = WEB_URL.'img'.US.'icons'.US.'clientes.png';
            }else{
                $foto = $valor->foto;
            }
            $tabela['Foto'][$i]             = '<img alt="'.__('Foto de Album').' src="'.$foto.'" style="max-width:100px;" />';
            $tabela['Nome'][$i]             = $valor->nome;
            $tabela['Lançamento'][$i]       = $valor->lancamento;
            $tabela['Data Registrada no Sistema'][$i]  = $valor->log_date_add;
            $status                                 = $valor->status;
            if($status!=1){
                $status = 0;
                $texto = __('Desativado');
            }else{
                $status = 1;
                $texto = __('Ativado');
            }
            $tabela['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Visualizar' ,Array('Visualizar Musicas do Album'    ,'Musica/Musica/Musicas/'.$valor->artista.'/'.$valor->id.'/'    ,'')).
                                              '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$status     ,Array($texto        ,'Musica/Album/Status/'.$valor->id.'/'    ,'')).'</span>'.
                                              $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Album'        ,$edit_url    ,'')).
                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Album'       ,$del_url     ,'Deseja realmente deletar esse Album ?'));
            ++$i;
        }
        return Array($tabela,$i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Albuns($artista=false,$export=false){
        if($artista ==='false' || $artista ===0)  $artista    = false;
        if($artista!==false){
            $artista = (int) $artista;
            if($artista==0){
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array(),1,'id DESC');
                if($artista_registro===false){
                    throw new \Exception('Não existe nenhum artista:', 404);
                }
                $artista = $artista_registro->id;
            }else{
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$artista),1);
                if($artista_registro===false){
                    throw new \Exception('Esse Artista não existe:', 404);
                }
            }
            $where = Array(
                'artista'   => $artista,
            );
            self::Endereco_Album(false, $artista_registro);
        }else{
            $where = Array();
            self::Endereco_Album(false, false);
        }
        $i = 0;
        if($artista!==false){
            $titulo_add = 'Adicionar novo Album ao Artista: '.$artista_registro->nome;
            $url_add = '/'.$artista;
            $add_url = 'Musica/Album/Albuns_Add/'.$artista;
        }else{
            $titulo_add = __('Adicionar novo Album');
            $url_add = '/false';
            $add_url    = 'Musica/Album/Albuns_Add';
        }
        $add_url = 'Musica/Album/Albuns_Add'.$url_add;
        $i = 0;
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                $titulo_add,
                $add_url,
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Musica/Album/Albuns'.$url_add,
            )
        )));
        $albuns = $this->_Modelo->db->Sql_Select('Musica_Album',$where);
        if($artista!==false){
            $titulo = 'Listagem de Albuns: '.$artista_registro->nome;
        }else{
            $titulo = __('Listagem de Albuns em Todos os Artistas');
        }
        if($albuns!==false && !empty($albuns)){
            list($tabela,$i) = self::Albuns_Tabela($albuns,$artista);
            $titulo = $titulo.' ('.$i.')';
            if($export!==false){
                self::Export_Todos($export,$tabela, $titulo);
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
            $titulo = $titulo.' ('.$i.')';
            if($artista!==false){
                $erro = __('Nenhuma Album nesse Artista');
            }else{
                $erro = __('Nenhuma Album nos Artistas');
            }         
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$erro.'</font></b></center>');
        }
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo',$titulo);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Albuns_Add($artista = false){
        if($artista==='false') $artista = false;
        // Carrega Config
        $formid     = 'form_Sistema_Admin_Albuns';
        $formbt     = __('Salvar');
        $campos     = Musica_Album_DAO::Get_Colunas();
        if($artista===false){
            $formlink   = 'Musica/Album/Albuns_Add2';
            $titulo1    = __('Adicionar Album');
            $titulo2    = __('Salvar Album');
            self::Endereco_Album(true, false);
        }else{
            $artista = (int) $artista;
            if($artista==0){
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array(),1,'id DESC');
                if($artista_registro===false){
                    throw new \Exception('Não existe nenhuma artista:', 404);
                }
                $artista = $artista_registro->id;
            }else{
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$artista),1);
                if($artista_registro===false){
                    throw new \Exception('Esse Artista não existe:', 404);
                }
            }
            $formlink   = 'Musica/Album/Albuns_Add2/'.$artista;
            self::DAO_Campos_Retira($campos,'artista');
            $titulo1    = 'Adicionar Album de '.$artista_registro->nome ;
            $titulo2    = 'Salvar Album de '.$artista_registro->nome ;
            self::Endereco_Album(true, $artista_registro);
        }
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Albuns_Add2($artista=false){
        if($artista==='false') $artista = false;
        $titulo     = __('Album Adicionada com Sucesso');
        $dao        = 'Musica_Album';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Album cadastrada com sucesso.');
        if($artista===false){
            $funcao     = '$this->Albuns(0);';
            $alterar    = Array();
        }else{
            $artista = (int) $artista;
            $alterar    = Array('artista'=>$artista);
            $funcao     = '$this->Albuns('.$artista.');';
        }
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Albuns_Edit($id,$artista = false){
        if($artista==='false') $artista = false;
        if($id===false){
            throw new \Exception('Album não existe:'. $id, 404);
        }
        $id         = (int) $id;
        if($artista!==false){
            $artista    = (int) $artista;
        }
        // Carrega Config
        $titulo1    = 'Editar Album (#'.$id.')';
        $titulo2    = __('Alteração de Album');
        $formid     = 'form_Sistema_AdminC_AlbumEdit';
        $formbt     = __('Alterar Album');
        $campos = Musica_Album_DAO::Get_Colunas();
        if($artista!==false){
            $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$artista),1);
            if($artista_registro===false){
                throw new \Exception('Esse Artista não existe:', 404);
            }
            $formlink   = 'Musica/Album/Albuns_Edit2/'.$id.'/'.$artista;
            self::DAO_Campos_Retira($campos,'artista');
            self::Endereco_Album(true, $artista_registro);
        }else{
            $formlink   = 'Musica/Album/Albuns_Edit2/'.$id;
            self::Endereco_Album(true, false);
        }
        $editar     = Array('Musica_Album',$id);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Albuns_Edit2($id,$artista = false){
        if($artista==='false') $artista = false;
        if($id===false){
            throw new \Exception('Album não existe:'. $id, 404);
        }
        $id         = (int) $id;
        if($artista!==false){
            $artista    = (int) $artista;
        }
        $titulo     = __('Album Editada com Sucesso');
        $dao        = Array('Musica_Album',$id);
        if($artista!==false){
            $funcao     = '$this->Albuns('.$artista.');';
        }else{
            $funcao     = '$this->Albuns();';
        }
        $sucesso1   = __('Album Alterada com Sucesso.');
        $sucesso2   = ''.$_POST["nome"].' teve a alteração bem sucedida';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);   
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Albuns_Del($id = false,$artista=false){
        if($artista==='false') $artista = false;
        
        if($id===false){
            throw new \Exception('Album não existe:'. $id, 404);
        }
        // Antiinjection
    	$id = (int) $id;
        if($artista!==false){
            $artista    = (int) $artista;
            $where = Array('artista'=>$artista,'id'=>$id);
        }else{
            $where = Array('id'=>$id);
        }
        // Puxa album e deleta
        $album = $this->_Modelo->db->Sql_Select('Musica_Album', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($album);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Album deletada com sucesso')
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        // Recupera Albuns
        if($artista!==false){
            $this->Albuns($artista);
        }else{
            $this->Albuns();
        }
        
        $this->_Visual->Json_Info_Update('Titulo', __('Album deletada com Sucesso'));
        $this->_Visual->Json_Info_Update('Historico', false);
    }
    public function Status($id=false){
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $resultado = $this->_Modelo->db->Sql_Select('Musica_Album', Array('id'=>$id),1);
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        if($resultado->status=='1'){
            $resultado->status='0';
        }else{
            $resultado->status='1';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            if($resultado->status==1){
                $texto = __('Ativado');
            }else{
                $texto = __('Desativado');
            }
            $conteudo = array(
                'location' => '#status'.$resultado->id,
                'js' => '',
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Status'.$resultado->status     ,Array($texto        ,'Musica/Album/Status/'.$resultado->id.'/'    ,''))
            );
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
            $this->_Visual->Json_Info_Update('Titulo', __('Status Alterado')); 
        }else{
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => __('Erro'),
                "mgs_secundaria"    => __('Ocorreu um Erro.')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);

            $this->_Visual->Json_Info_Update('Titulo', __('Erro')); 
        }
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
