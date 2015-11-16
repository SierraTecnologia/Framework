<?php
class Simulador_PerguntaControle extends Simulador_Controle
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
    * @uses pergunta_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
    */
    public function Main() {
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Simulador/Pergunta/Perguntas');
        return FALSE;
    }
    static function Endereco_Pergunta($true= TRUE, $simulador = FALSE) {
        if ($simulador==='false') $simulador = FALSE;
        $Registro = &\Framework\App\Registro::getInstacia();
        $_Controle = $Registro->_Controle;
        if ($simulador === FALSE) {
            $titulo = __('Todos os Perguntas');
            $link   = 'Simulador/Pergunta/Perguntas';
        } else {
            Simulador_SimuladorControle::Endereco_Simulador();
            $titulo = $simulador->nome;
            $link   = 'Simulador/Pergunta/Perguntas/'.$simulador->id;
        }
        if ($true === TRUE) {
            $_Controle->Tema_Endereco($titulo, $link);
        } else {
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Perguntas_Tabela(&$perguntas, $simulador = FALSE) {
        if ($simulador==='false') $simulador = FALSE;
        $Registro   = &\Framework\App\Registro::getInstacia();
        $Modelo     = &$Registro->_Modelo;
        $Visual     = &$Registro->_Visual;
        $table = Array();
        $i = 0;
        if (is_object($perguntas)) $perguntas = Array(0=>$perguntas);
        reset($perguntas);
        foreach ($perguntas as &$valor) {
            if ($simulador === FALSE || $simulador==0) {
                
                $table['Simulador'][$i]   = $valor->simulador2;
                $edit_url   = 'Simulador/Pergunta/Perguntas_Edit/'.$valor->id.'/';
                $del_url    = 'Simulador/Pergunta/Perguntas_Del/'.$valor->id.'/';
            } else {
                $edit_url   = 'Simulador/Pergunta/Perguntas_Edit/'.$valor->id.'/'.$valor->simulador.'/';
                $del_url    = 'Simulador/Pergunta/Perguntas_Del/'.$valor->id.'/'.$valor->simulador.'/';
            }
            
            $table['Nome'][$i]             = $valor->nome;
            $table['Data Registrada no Sistema'][$i]  = $valor->log_date_add;
            $status                                 = $valor->status;
            if ($status!=1) {
                $status = 0;
                $texto = __('Desativado');
            } else {
                $status = 1;
                $texto = __('Ativado');
            }
            $table['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Visualizar' ,Array('Visualizar Simuladores da Pergunta'    ,'Simulador/Resposta/Respostas/'.$valor->simulador.'/'.$valor->id.'/'    , '')).
                                              '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$status     ,Array($texto        ,'Simulador/Pergunta/Status/'.$valor->id.'/'    , '')).'</span>'.
                                              $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pergunta'        , $edit_url    , '')).
                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pergunta'       , $del_url     ,'Deseja realmente deletar esse Pergunta ?'));
            ++$i;
        }
        return Array($table, $i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Perguntas($simulador = FALSE, $export = FALSE) {
        if ($simulador ==='false' || $simulador ===0)  $simulador    = FALSE;
        if ($simulador !== FALSE) {
            $simulador = (int) $simulador;
            if ($simulador==0) {
                $simulador_registro = $this->_Modelo->db->Sql_Select('Simulador',Array(),1,'id DESC');
                if ($simulador_registro === FALSE) {
                    return _Sistema_erroControle::Erro_Fluxo('Não existe nenhum simulador:',404);
                }
                $simulador = $simulador_registro->id;
            } else {
                $simulador_registro = $this->_Modelo->db->Sql_Select('Simulador',Array('id'=>$simulador),1);
                if ($simulador_registro === FALSE) {
                    return _Sistema_erroControle::Erro_Fluxo('Esse Simulador não existe:',404);
                }
            }
            $where = Array(
                'simulador'   => $simulador,
            );
            self::Endereco_Pergunta(FALSE, $simulador_registro);
        } else {
            $where = Array();
            self::Endereco_Pergunta(FALSE, FALSE);
        }
        $i = 0;
        if ($simulador !== FALSE) {
            $titulo_add = 'Adicionar nova Pergunta ao Simulador: '.$simulador_registro->nome;
            $url_add = '/'.$simulador;
            $add_url = 'Simulador/Pergunta/Perguntas_Add/'.$simulador;
        } else {
            $titulo_add = __('Adicionar nova Pergunta');
            $url_add = '/false';
            $add_url    = 'Simulador/Pergunta/Perguntas_Add';
        }
        $add_url = 'Simulador/Pergunta/Perguntas_Add'.$url_add;
        $i = 0;
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                $titulo_add,
                $add_url,
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Simulador/Pergunta/Perguntas'.$url_add,
            )
        )));
        $perguntas = $this->_Modelo->db->Sql_Select('Simulador_Pergunta', $where);
           
        if ($simulador !== FALSE) {
            $titulo = 'Listagem de Perguntas: '.$simulador_registro->nome;
        } else {
            $titulo = __('Listagem de Perguntas em Todos os Simuladores');
        }
        if ($perguntas !== FALSE && !empty($perguntas)) {
            list($table, $i) = self::Perguntas_Tabela($perguntas, $simulador);
            $titulo = $titulo.' ('.$i.')';
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, $titulo);
            } else {
                $this->_Visual->Show_Tabela_DataTable(
                    $table,     // Array Com a Tabela
                    '',          // style extra
                    true,        // true -> Add ao Bloco, false => Retorna html
                    FALSE,        // Apagar primeira coluna ?
                    Array(       // Ordenacao
                        Array(
                            0,'desc'
                        )
                    )
                );
            }
            unset($table);
        } else {
            $titulo = $titulo.' ('.$i.')';
            if ($simulador !== FALSE) {
                $erro = __('Nenhuma Pergunta nesse Simulador');
            } else {
                $erro = __('Nenhuma Pergunta nos Simuladores');
            }         
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$erro.'</font></b></center>');
        }
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', $titulo);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Perguntas_Add($simulador = FALSE) {
        if ($simulador==='false') $simulador = FALSE;
        // Carrega Config
        $formid     = 'form_Simulador_Pergunta_Perguntas';
        $formbt     = __('Salvar');
        $campos     = Simulador_Pergunta_DAO::Get_Colunas();
        if ($simulador === FALSE) {
            $formlink   = 'Simulador/Pergunta/Perguntas_Add2';
            $titulo1    = __('Adicionar Pergunta');
            $titulo2    = __('Salvar Pergunta');
            self::Endereco_Pergunta(true, FALSE);
        } else {
            $simulador = (int) $simulador;
            if ($simulador==0) {
                $simulador_registro = $this->_Modelo->db->Sql_Select('Simulador',Array(),1,'id DESC');
                if ($simulador_registro === FALSE) {
                    return _Sistema_erroControle::Erro_Fluxo('Não existe nenhuma simulador:',404);
                }
                $simulador = $simulador_registro->id;
            } else {
                $simulador_registro = $this->_Modelo->db->Sql_Select('Simulador',Array('id'=>$simulador),1);
                if ($simulador_registro === FALSE) {
                    return _Sistema_erroControle::Erro_Fluxo('Esse Simulador não existe:',404);
                }
            }
            $formlink   = 'Simulador/Pergunta/Perguntas_Add2/'.$simulador;
            self::DAO_Campos_Retira($campos,'simulador');
            $titulo1    = 'Adicionar Pergunta de '.$simulador_registro->nome ;
            $titulo2    = 'Salvar Pergunta de '.$simulador_registro->nome ;
            self::Endereco_Pergunta(true, $simulador_registro);
        }
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Perguntas_Add2($simulador = FALSE) {
        if ($simulador==='false') $simulador = FALSE;
        $titulo     = __('Pergunta Adicionada com Sucesso');
        $dao        = 'Simulador_Pergunta';
        $sucesso1   = __('Inserção bem sucedida');
        $sucesso2   = __('Pergunta cadastrada com sucesso.');
        if ($simulador === FALSE) {
            $function     = '$this->Perguntas(0);';
            $alterar    = Array();
        } else {
            $simulador = (int) $simulador;
            $alterar    = Array('simulador'=>$simulador);
            $function     = '$this->Perguntas('.$simulador.');';
        }
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
    /**
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Perguntas_Edit($id, $simulador = FALSE) {
        if ($simulador==='false') $simulador = FALSE;
        if ($id === FALSE) {
            return _Sistema_erroControle::Erro_Fluxo('Pergunta não existe:'. $id,404);
        }
        $id         = (int) $id;
        if ($simulador !== FALSE) {
            $simulador    = (int) $simulador;
        }
        // Carrega Config
        $titulo1    = 'Editar Pergunta (#'.$id.')';
        $titulo2    = __('Alteração de Pergunta');
        $formid     = 'form_Simulador_PerguntaC_PerguntaEdit';
        $formbt     = __('Alterar Pergunta');
        $campos = Simulador_Pergunta_DAO::Get_Colunas();
        if ($simulador !== FALSE) {
            $simulador_registro = $this->_Modelo->db->Sql_Select('Simulador',Array('id'=>$simulador),1);
            if ($simulador_registro === FALSE) {
                return _Sistema_erroControle::Erro_Fluxo('Esse Simulador não existe:',404);
            }
            $formlink   = 'Simulador/Pergunta/Perguntas_Edit2/'.$id.'/'.$simulador;
            self::DAO_Campos_Retira($campos,'simulador');
            self::Endereco_Pergunta(true, $simulador_registro);
        } else {
            $formlink   = 'Simulador/Pergunta/Perguntas_Edit2/'.$id;
            self::Endereco_Pergunta(true, FALSE);
        }
        $editar     = Array('Simulador_Pergunta', $id);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos, $editar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Perguntas_Edit2($id, $simulador = FALSE) {
        if ($simulador==='false') $simulador = FALSE;
        if ($id === FALSE) {
            return _Sistema_erroControle::Erro_Fluxo('Pergunta não existe:'. $id,404);
        }
        $id         = (int) $id;
        if ($simulador !== FALSE) {
            $simulador    = (int) $simulador;
        }
        $titulo     = __('Pergunta Editada com Sucesso');
        $dao        = Array('Simulador_Pergunta', $id);
        if ($simulador !== FALSE) {
            $function     = '$this->Perguntas('.$simulador.');';
        } else {
            $function     = '$this->Perguntas();';
        }
        $sucesso1   = __('Pergunta Alterada com Sucesso.');
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
    public function Perguntas_Del($id = FALSE, $simulador = FALSE) {
        if ($simulador==='false') $simulador = FALSE;
        
        if ($id === FALSE) {
            return _Sistema_erroControle::Erro_Fluxo('Pergunta não existe:'. $id,404);
        }
        // Antiinjection
    	$id = (int) $id;
        if ($simulador !== FALSE) {
            $simulador    = (int) $simulador;
            $where = Array('simulador'=>$simulador,'id'=>$id);
        } else {
            $where = Array('id'=>$id);
        }
        // Puxa pergunta e deleta
        $pergunta = $this->_Modelo->db->Sql_Select('Simulador_Pergunta', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($pergunta);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Pergunta deletada com sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        // Recupera Perguntas
        if ($simulador !== FALSE) {
            $this->Perguntas($simulador);
        } else {
            $this->Perguntas();
        }
        
        $this->_Visual->Json_Info_Update('Titulo', __('Pergunta deletada com Sucesso'));
        $this->_Visual->Json_Info_Update('Historico', FALSE);
    }
    public function Status($id = FALSE) {
        if ($id === FALSE) {
            return FALSE;
        }
        $resultado = $this->_Modelo->db->Sql_Select('Simulador_Pergunta', Array('id'=>$id),1);
        if ($resultado === FALSE || !is_object($resultado)) {
            return FALSE;
        }
        if ($resultado->status=='1') {
            $resultado->status='0';
        } else {
            $resultado->status='1';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if ($sucesso) {
            if ($resultado->status==1) {
                $texto = __('Ativado');
            } else {
                $texto = __('Desativado');
            }
            $conteudo = array(
                'location' => '#status'.$resultado->id,
                'js' => '',
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Status'.$resultado->status     ,Array($texto        ,'Simulador/Pergunta/Status/'.$resultado->id.'/'    , ''))
            );
            $this->_Visual->Json_IncluiTipo('Conteudo', $conteudo);
            $this->_Visual->Json_Info_Update('Titulo', __('Status Alterado')); 
        } else {
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => __('Erro'),
                "mgs_secundaria"    => __('Ocorreu um Erro.')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);

            $this->_Visual->Json_Info_Update('Titulo', __('Erro')); 
        }
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
}
?>
