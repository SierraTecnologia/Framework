<?php

class Transporte_PedidoControle extends Transporte_Controle
{
    public function __construct() {
        parent::__construct();
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Main() {
        //\Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Transporte/Pedido/Pedidos');
        return FALSE;
    }
    /**
     * Aceita um Pedido (->)
     * @param int $id Chave Primária (Id do Registro)
     * @param type $status
     * @throws \Exception
     */
    public function Arma_Ped_Novas_Aceitar($id = FALSE, $status=1) {
        if ($id === FALSE) {
            return FALSE;
        }
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}id=\''.$id.'\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if ($resultado === FALSE || !is_object($resultado)) {
            return FALSE;
        }
        if ($resultado->status==1 ||$resultado->status==2) {
            // Voce não pode alterar
            return FALSE;
        }
        if ($status==1) {
            $resultado->status='1';
        } else {
            $resultado->status='2';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if ($sucesso) {
            if ($resultado->status==1) {
                $texto = __('Aceito');
                $pedido = $resultado->pedido;
                
                // Caso Aceita o Resto ele Recusa
                $procurar = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}pedido=\''.$pedido.'\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'');
                if (is_object($procurar)) $procurar = array($procurar);
                if ($procurar !== FALSE) {
                    foreach($procurar as &$valor) {
                        $valor->status = '2';
                    }
                    $this->_Modelo->db->Sql_Update($procurar);
                }
                // Caso Aceita o Resto ele Recusa
                $procurar = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido', '{sigla}id=\''.$pedido.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
                if (is_object($procurar)) $procurar = array($procurar);
                if ($procurar !== FALSE) {
                    foreach($procurar as &$valor) {
                        $valor->status = '2';
                    }
                    $this->_Modelo->db->Sql_Update($procurar);
                }
                
            } else {
                $texto = __('Recusado');
            }
            $mensagens = array(
                "tipo"              => 'sucesso',
                "mgs_principal"     => __('Sucesso'),
                "mgs_secundaria"    => $texto.' com Sucesso'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            $this->_Visual->Json_Info_Update('Titulo', $texto.' com Sucesso'); 
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
    /**
     * Enviar Pedido (->)
     */
    public function Arma_Ped_Add() {
        //self::Endereco_Noticia(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Pedido');
        $titulo2    = __('Salvar Pedido');
        $formid     = 'formTransporte_Armazem_PEdido';
        $formbt     = __('Salvar');
        $formlink   = 'Transporte/Pedido/Arma_Ped_Add2/';
        $campos = Transporte_Armazem_Pedido_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * Enviar Pedido (->)
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Arma_Ped_Add2() {
        $titulo     = __('Pedido enviado com Sucesso');
        $dao        = 'Transporte_Armazem_Pedido';
        $function     = '$this->Arma_Ped_Novas();';
        $sucesso1   = __('Proposta enviada com Sucesso');
        $sucesso2   = __('Aguarde uma Resposta.');
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
    public function Arma_Ped_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido', '{sigla}id=\''.$id.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if ($pedido === FALSE || $pedido->status!='0') {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar esse Pedido')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            $this->_Visual->Json_Info_Update('Historico', FALSE);  
            return FALSE;
        }
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido);
        
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Pedido Cancelado com Sucesso')
            );
            
            $pedidos    =  $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}pedido=\''.$id.'\'');
            $sucesso =  $this->_Modelo->db->Sql_Delete($pedidos);
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Pedidos();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Proposta Cancelada com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Arma_Ped_Aceitas($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Arma_Ped_Aceitas',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}status=\'1\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionStatus = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Arma_Ped_Novas_Aceitar');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Armazem'][$i] = '#'.$valor->log_user_id;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Aceitar'        ,'Transporte/Pedido/Arma_Ped_Novas_Aceitar/'.$valor->id.'/1'    , ''), $permissionStatus).
                                            $this->_Visual->Tema_Elementos_Btn('Status0'     ,Array('Recusar'        ,'Transporte/Pedido/Arma_Ped_Novas_Aceitar/'.$valor->id.'/2'    , ''), $permissionStatus);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Aceitas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Aceita para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Aceita');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Propostas Aceitas dos Meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Aceitas dos Meus Pedidos'));
    }
    public function Arma_Ped_Novas($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Arma_Ped_Novas',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}status=\'0\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionStatus = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Arma_Ped_Novas_Aceitar');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Armazem'][$i] = '#'.$valor->log_user_id;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Aceitar'        ,'Transporte/Pedido/Arma_Ped_Novas_Aceitar/'.$valor->id.'/1'    , ''), $permissionStatus).
                                            $this->_Visual->Tema_Elementos_Btn('Status0'     ,Array('Recusar'        ,'Transporte/Pedido/Arma_Ped_Novas_Aceitar/'.$valor->id.'/2'    , ''), $permissionStatus);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Novas Propostas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma nova Proposta para exportar');
            } else {
                $mensagem = __('Nenhuma nova Proposta');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Novas Propostas dos Meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Novas Propostas dos Meus Pedidos'));
    }
    public function Arma_Ped_Minhas($export = FALSE) {
        $i = 0;
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Pedido',
                'Transporte/Pedido/Arma_Ped_Add',
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Arma_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido', '{sigla}status=\'0\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionDelete = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Arma_Ped_Del');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Descrição'][$i]       = $valor->descricao_carga;
                $table['Dimensões'][$i]       = '<b>Altura:</b>'.$valor->altura.
                                                ' cm<br><b>Comprimento:</b>'.$valor->comprimento.
                                                ' cm<br><b>Largura:</b>'.$valor->largura.' cm<br><b>Volume:</b>'.$valor->altura*$valor->comprimento*$valor->largura.' cm³';
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Cancelar Pedido'       ,'Transporte/Pedido/Arma_Ped_Del/'.$valor->id.'/'     ,'Deseja realmente Cancelar esse Pedido ?'), $permissionDelete);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Pedidos Cadastrados');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhum Pedido Cadastrado para exportar');
            } else {
                $mensagem = __('Nenhum Pedido Cadastrado');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem dos meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Meus Pedidos'));
    }
    public function Arma_Sol_Solicitacoes($export = FALSE) {
        $i = 0;
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Arma_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido', '{sigla}status=\'0\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $perm_add = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Arma_Sol_Add');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Descrição'][$i]       = $valor->descricao_carga;
                $table['Dimensões'][$i]       = '<b>Altura:</b>'.$valor->altura.
                                                ' cm<br><b>Comprimento:</b>'.$valor->comprimento.
                                                ' cm<br><b>Largura:</b>'.$valor->largura.' cm<br><b>Volume:</b>'.$valor->altura*$valor->comprimento*$valor->largura.' cm³';
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Fazer Proposta'        ,'Transporte/Pedido/Arma_Sol_Add/'.$valor->id.'/'    , ''), $perm_add);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Pedidos pendentes');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhum Pedido pendente para exportar');
            } else {
                $mensagem = __('Nenhum Pedido Pendente');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem dos Pedidos Pendentes').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Pedidos Pendentes'));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function Arma_Sol_PedAceitos($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Arma_Sol_PedAceitos',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}status=\'1\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Fornecedor'][$i] = '#'.$valor->fornecedor2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Aceitas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Aceita para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Aceita');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Aceitas').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Aceitas'));
    }
    public function Arma_Sol_PedRecusados($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Arma_Sol_PedRecusados',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}status=\'2\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Fornecedor'][$i] = '#'.$valor->fornecedor2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Recusadas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Recusada para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Recusada');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Recusadas').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Recusadas'));
    }
    public function Arma_Sol_PedPendente($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Arma_Sol_PedPendente',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}status=\'0\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionDelete = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Arma_Sol_Del');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Fornecedor'][$i] = '#'.$valor->fornecedor2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Cancelar Proposta'       ,'Transporte/Pedido/Arma_Sol_Del/'.$valor->id.'/'     ,'Deseja realmente Cancelar essa Proposta ?'), $permissionDelete);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Pendentes');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Pendente para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Pendente');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Pendentes').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Pendentes'));
    }
    
    
    
    
    
    
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Arma_Sol_Add($pedido = FALSE) {
        if ($pedido === FALSE) return FALSE;
        else{
            $pedido = (int) $pedido;
            
            $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido', '{sigla}id=\''.$pedido.'\'',1);
            if ($pedido === FALSE || $pedido->status!=0) {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Erro'),
                    "mgs_secundaria" => __('Você não pode Adicionar uma Solicitação a este pedido')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                return FALSE;
            }
        }
        //self::Endereco_Noticia(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Proposta de Pedido');
        $titulo2    = __('Salvar Proposta de Pedido');
        $formid     = 'formTransporte_Pedido_Arma_Sol_Add';
        $formbt     = __('Salvar');
        $formlink   = 'Transporte/Pedido/Arma_Sol_Add2/'.$pedido;
        $campos = Transporte_Armazem_Pedido_Lance_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos,'pedido');
        self::DAO_Campos_Retira($campos,'status');
        self::DAO_Campos_Retira($campos,'fornecedor');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Arma_Sol_Add2($pedido = FALSE) {
        if ($pedido === FALSE) return FALSE;
        else{
            $pedido = (int) $pedido;
            
            $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido', '{sigla}id=\''.$pedido.'\'',1);
            if ($pedido === FALSE || $pedido->status!=0) {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Erro'),
                    "mgs_secundaria" => __('Você não pode Adicionar uma Solicitação a este pedido')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                return FALSE;
            }
        }
        $titulo     = __('Proposta enviada com Sucesso');
        $dao        = 'Transporte_Armazem_Pedido_Lance';
        $function     = '$this->Arma_Sol_Solicitacoes();';
        $sucesso1   = __('Proposta enviada com Sucesso');
        $sucesso2   = __('Aguarde uma Resposta.');
        $alterar    = Array('status'=>'0', 'fornecedor'=>$pedido->log_user_add,'pedido'=>$pedido->id);
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Arma_Sol_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido_lance    =  $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido_Lance', '{sigla}id=\''.$id.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'',1);
        
        if ($pedido_lance === FALSE || $pedido_lance->status!=0) {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            return FALSE;
        }
        
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Armazem_Pedido', '{sigla}id=\''.$pedido_lance->pedido.'\'',1);
        if ($pedido === FALSE || $pedido->status!=0) {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            return FALSE;
        }
        
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido_lance);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Proposta Cancelada com Sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Arma_Sol_Solicitacoes();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Proposta Cancelada com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
    
    
    
    
    
    
    /**
     * Aceita um Pedido (->)
     * @param int $id Chave Primária (Id do Registro)
     * @param type $status
     * @throws \Exception
     */
    public function Trans_Ped_Novas_Aceitar($id = FALSE, $status=1) {
        if ($id === FALSE) {
            return FALSE;
        }
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}id=\''.$id.'\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if ($resultado === FALSE || !is_object($resultado)) {
            return FALSE;
        }
        if ($resultado->status==1 ||$resultado->status==2) {
            // Voce não pode alterar
            return FALSE;
        }
        if ($status==1) {
            $resultado->status='1';
        } else {
            $resultado->status='2';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if ($sucesso) {
            if ($resultado->status==1) {
                $texto = __('Aceito');
                $pedido = $resultado->pedido;
                
                // Caso Aceita o Resto ele Recusa
                $procurar = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}pedido=\''.$pedido.'\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'');
                if (is_object($procurar)) $procurar = array($procurar);
                if ($procurar !== FALSE) {
                    foreach($procurar as &$valor) {
                        $valor->status = '2';
                    }
                    $this->_Modelo->db->Sql_Update($procurar);
                }
                // Caso Aceita o Resto ele Recusa
                $procurar = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido', '{sigla}id=\''.$pedido.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
                if (is_object($procurar)) $procurar = array($procurar);
                if ($procurar !== FALSE) {
                    foreach($procurar as &$valor) {
                        $valor->status = '2';
                    }
                    $this->_Modelo->db->Sql_Update($procurar);
                }
                
            } else {
                $texto = __('Recusado');
            }
            $mensagens = array(
                "tipo"              => 'sucesso',
                "mgs_principal"     => __('Sucesso'),
                "mgs_secundaria"    => $texto.' com Sucesso'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            $this->_Visual->Json_Info_Update('Titulo', $texto.' com Sucesso'); 
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
    /**
     * Enviar Pedido (->)
     */
    public function Trans_Ped_Add() {
        //self::Endereco_Noticia(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Pedido');
        $titulo2    = __('Salvar Pedido');
        $formid     = 'formTransporte_Transportadora_PEdido';
        $formbt     = __('Salvar');
        $formlink   = 'Transporte/Pedido/Trans_Ped_Add2/';
        $campos = Transporte_Transportadora_Pedido_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * Enviar Pedido (->)
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Trans_Ped_Add2() {
        $titulo     = __('Pedido enviado com Sucesso');
        $dao        = 'Transporte_Transportadora_Pedido';
        $function     = '$this->Trans_Ped_Novas();';
        $sucesso1   = __('Proposta enviada com Sucesso');
        $sucesso2   = __('Aguarde uma Resposta.');
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
    public function Trans_Ped_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido', '{sigla}id=\''.$id.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if ($pedido === FALSE || $pedido->status!='0') {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar esse Pedido')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            $this->_Visual->Json_Info_Update('Historico', FALSE);  
            return FALSE;
        }
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido);
        
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Pedido Cancelado com Sucesso')
            );
            
            $pedidos    =  $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}pedido=\''.$id.'\'');
            $sucesso =  $this->_Modelo->db->Sql_Delete($pedidos);
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Pedidos();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Proposta Cancelada com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Trans_Ped_Aceitas($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Trans_Ped_Aceitas',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}status=\'1\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionStatus = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Trans_Ped_Novas_Aceitar');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Transportadora'][$i] = '#'.$valor->log_user_id;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Aceitar'        ,'Transporte/Pedido/Trans_Ped_Novas_Aceitar/'.$valor->id.'/1'    , ''), $permissionStatus).
                                            $this->_Visual->Tema_Elementos_Btn('Status0'     ,Array('Recusar'        ,'Transporte/Pedido/Trans_Ped_Novas_Aceitar/'.$valor->id.'/2'    , ''), $permissionStatus);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Aceitas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Aceita para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Aceita');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Propostas Aceitas dos Meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Aceitas dos Meus Pedidos'));
    }
    public function Trans_Ped_Novas($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Trans_Ped_Novas',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}status=\'0\' AND {sigla}fornecedor=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionStatus = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Trans_Ped_Novas_Aceitar');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Transportadora'][$i] = '#'.$valor->log_user_id;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Aceitar'        ,'Transporte/Pedido/Trans_Ped_Novas_Aceitar/'.$valor->id.'/1'    , ''), $permissionStatus).
                                            $this->_Visual->Tema_Elementos_Btn('Status0'     ,Array('Recusar'        ,'Transporte/Pedido/Trans_Ped_Novas_Aceitar/'.$valor->id.'/2'    , ''), $permissionStatus);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Novas Propostas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma nova Proposta para exportar');
            } else {
                $mensagem = __('Nenhuma nova Proposta');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Novas Propostas dos Meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Novas Propostas dos Meus Pedidos'));
    }
    public function Trans_Ped_Minhas($export = FALSE) {
        $i = 0;
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Pedido',
                'Transporte/Pedido/Trans_Ped_Add',
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Trans_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido', '{sigla}status=\'0\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionDelete = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Trans_Ped_Del');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Descrição'][$i]       = $valor->descricao_carga;
                $table['Dimensões'][$i]       = '<b>Altura:</b>'.$valor->altura.
                                                ' cm<br><b>Comprimento:</b>'.$valor->comprimento.
                                                ' cm<br><b>Largura:</b>'.$valor->largura.' cm<br><b>Volume:</b>'.$valor->altura*$valor->comprimento*$valor->largura.' cm³';
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Cancelar Pedido'       ,'Transporte/Pedido/Trans_Ped_Del/'.$valor->id.'/'     ,'Deseja realmente Cancelar esse Pedido ?'), $permissionDelete);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Pedidos Cadastrados');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhum Pedido Cadastrado para exportar');
            } else {
                $mensagem = __('Nenhum Pedido Cadastrado');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem dos meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Meus Pedidos'));
    }
    public function Trans_Sol_Solicitacoes($export = FALSE) {
        $i = 0;
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Trans_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido', '{sigla}status=\'0\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionStatus = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Trans_Sol_Add');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Descrição'][$i]       = $valor->descricao_carga;
                $table['Dimensões'][$i]       = '<b>Altura:</b>'.$valor->altura.
                                                ' cm<br><b>Comprimento:</b>'.$valor->comprimento.
                                                ' cm<br><b>Largura:</b>'.$valor->largura.' cm<br><b>Volume:</b>'.$valor->altura*$valor->comprimento*$valor->largura.' cm³';
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Fazer Proposta'        ,'Transporte/Pedido/Trans_Sol_Add/'.$valor->id.'/'    , ''), $permissionStatus);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Pedidos pendentes');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhum Pedido pendente para exportar');
            } else {
                $mensagem = __('Nenhum Pedido Pendente');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem dos Pedidos Pendentes').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Pedidos Pendentes'));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function Trans_Sol_PedAceitos($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Trans_Sol_PedAceitos',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}status=\'1\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Fornecedor'][$i] = '#'.$valor->fornecedor2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Aceitas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Aceita para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Aceita');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Aceitas').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Aceitas'));
    }
    public function Trans_Sol_PedRecusados($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Trans_Sol_PedRecusados',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}status=\'2\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Fornecedor'][$i] = '#'.$valor->fornecedor2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Recusadas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Recusada para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Recusada');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Recusadas').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Recusadas'));
    }
    public function Trans_Sol_PedPendente($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Trans_Sol_PedPendente',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}status=\'0\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionDelete = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Trans_Sol_Del');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Fornecedor'][$i] = '#'.$valor->fornecedor2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Cancelar Proposta'       ,'Transporte/Pedido/Trans_Sol_Del/'.$valor->id.'/'     ,'Deseja realmente Cancelar essa Proposta ?'), $permissionDelete);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Pendentes');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Pendente para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Pendente');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Pendentes').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Pendentes'));
    }
    
    
    
    
    
    
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Trans_Sol_Add($pedido = FALSE) {
        if ($pedido === FALSE) return FALSE;
        else{
            $pedido = (int) $pedido;
            
            $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido', '{sigla}id=\''.$pedido.'\'',1);
            if ($pedido === FALSE || $pedido->status!=0) {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Erro'),
                    "mgs_secundaria" => __('Você não pode Adicionar uma Solicitação a este pedido')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                return FALSE;
            }
        }
        //self::Endereco_Noticia(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Proposta de Pedido');
        $titulo2    = __('Salvar Proposta de Pedido');
        $formid     = 'formTransporte_Pedido_Trans_Sol_Add';
        $formbt     = __('Salvar');
        $formlink   = 'Transporte/Pedido/Trans_Sol_Add2/'.$pedido;
        $campos = Transporte_Transportadora_Pedido_Lance_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos,'pedido');
        self::DAO_Campos_Retira($campos,'status');
        self::DAO_Campos_Retira($campos,'fornecedor');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Trans_Sol_Add2($pedido = FALSE) {
        if ($pedido === FALSE) return FALSE;
        else{
            $pedido = (int) $pedido;
            
            $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido', '{sigla}id=\''.$pedido.'\'',1);
            if ($pedido === FALSE || $pedido->status!=0) {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Erro'),
                    "mgs_secundaria" => __('Você não pode Adicionar uma Solicitação a este pedido')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                return FALSE;
            }
        }
        $titulo     = __('Proposta enviada com Sucesso');
        $dao        = 'Transporte_Transportadora_Pedido_Lance';
        $function     = '$this->Trans_Sol_Solicitacoes();';
        $sucesso1   = __('Proposta enviada com Sucesso');
        $sucesso2   = __('Aguarde uma Resposta.');
        $alterar    = Array('status'=>'0', 'fornecedor'=>$pedido->log_user_add,'pedido'=>$pedido->id);
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Trans_Sol_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido_lance    =  $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance', '{sigla}id=\''.$id.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'',1);
        
        if ($pedido_lance === FALSE || $pedido_lance->status!=0) {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            return FALSE;
        }
        
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido', '{sigla}id=\''.$pedido_lance->pedido.'\'',1);
        if ($pedido === FALSE || $pedido->status!=0) {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            return FALSE;
        }
        
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido_lance);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Proposta Cancelada com Sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Trans_Sol_Solicitacoes();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Proposta Cancelada com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }


    /**
     * Aceita um Pedido (->)
     * @param int $id Chave Primária (Id do Registro)
     * @param type $status
     * @throws \Exception
     */
    public function Caminho_Ped_Novas_Aceitar($id = FALSE, $status=1) {
        if ($id === FALSE) {
            return FALSE;
        }
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}id=\''.$id.'\' AND {sigla}transportadora=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if ($resultado === FALSE || !is_object($resultado)) {
            return FALSE;
        }
        if ($resultado->status==1 ||$resultado->status==2) {
            // Voce não pode alterar
            return FALSE;
        }
        if ($status==1) {
            $resultado->status='1';
        } else {
            $resultado->status='2';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if ($sucesso) {
            if ($resultado->status==1) {
                $texto = __('Aceito');
                $pedido = $resultado->pedido;
                
                // Caso Aceita o Resto ele Recusa
                $procurar = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}pedido=\''.$pedido.'\' AND {sigla}transportadora=\''.$this->_Acl->Usuario_GetID().'\'');
                if (is_object($procurar)) $procurar = array($procurar);
                if ($procurar !== FALSE) {
                    foreach($procurar as &$valor) {
                        $valor->status = '2';
                    }
                    $this->_Modelo->db->Sql_Update($procurar);
                }
                // Caso Aceita o Resto ele Recusa
                $procurar = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido', '{sigla}id=\''.$pedido.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
                if (is_object($procurar)) $procurar = array($procurar);
                if ($procurar !== FALSE) {
                    foreach($procurar as &$valor) {
                        $valor->status = '2';
                    }
                    $this->_Modelo->db->Sql_Update($procurar);
                }
                
            } else {
                $texto = __('Recusado');
            }
            $mensagens = array(
                "tipo"              => 'sucesso',
                "mgs_principal"     => __('Sucesso'),
                "mgs_secundaria"    => $texto.' com Sucesso'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            $this->_Visual->Json_Info_Update('Titulo', $texto.' com Sucesso'); 
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
    /**
     * Enviar Pedido (->)
     */
    public function Caminho_Ped_Add() {
        //self::Endereco_Noticia(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Pedido');
        $titulo2    = __('Salvar Pedido');
        $formid     = 'formTransporte_Caminhoneiro_PEdido';
        $formbt     = __('Salvar');
        $formlink   = 'Transporte/Pedido/Caminho_Ped_Add2/';
        $campos = Transporte_Caminhoneiro_Pedido_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * Enviar Pedido (->)
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Caminho_Ped_Add2() {
        $titulo     = __('Pedido enviado com Sucesso');
        $dao        = 'Transporte_Caminhoneiro_Pedido';
        $function     = '$this->Caminho_Ped_Novas();';
        $sucesso1   = __('Proposta enviada com Sucesso');
        $sucesso2   = __('Aguarde uma Resposta.');
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
    public function Caminho_Ped_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido', '{sigla}id=\''.$id.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'',1);
        if ($pedido === FALSE || $pedido->status!='0') {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar esse Pedido')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            $this->_Visual->Json_Info_Update('Historico', FALSE);  
            return FALSE;
        }
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido);
        
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Pedido Cancelado com Sucesso')
            );
            
            $pedidos    =  $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}pedido=\''.$id.'\'');
            $sucesso =  $this->_Modelo->db->Sql_Delete($pedidos);
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Pedidos();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Proposta Cancelada com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Caminho_Ped_Aceitas($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Caminho_Ped_Aceitas',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}status=\'1\' AND {sigla}transportadora=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionStatus = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Caminho_Ped_Novas_Aceitar');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Caminhoneiro'][$i] = '#'.$valor->log_user_id;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Aceitar'        ,'Transporte/Pedido/Caminho_Ped_Novas_Aceitar/'.$valor->id.'/1'    , ''), $permissionStatus).
                                            $this->_Visual->Tema_Elementos_Btn('Status0'     ,Array('Recusar'        ,'Transporte/Pedido/Caminho_Ped_Novas_Aceitar/'.$valor->id.'/2'    , ''), $permissionStatus);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Aceitas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Aceita para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Aceita');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Propostas Aceitas dos Meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Aceitas dos Meus Pedidos'));
    }
    public function Caminho_Ped_Novas($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Caminho_Ped_Novas',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}status=\'0\' AND {sigla}transportadora=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionStatus = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Caminho_Ped_Novas_Aceitar');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Caminhoneiro'][$i] = '#'.$valor->log_user_id;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Aceitar'        ,'Transporte/Pedido/Caminho_Ped_Novas_Aceitar/'.$valor->id.'/1'    , ''), $permissionStatus).
                                            $this->_Visual->Tema_Elementos_Btn('Status0'     ,Array('Recusar'        ,'Transporte/Pedido/Caminho_Ped_Novas_Aceitar/'.$valor->id.'/2'    , ''), $permissionStatus);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Novas Propostas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma nova Proposta para exportar');
            } else {
                $mensagem = __('Nenhuma nova Proposta');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Novas Propostas dos Meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Novas Propostas dos Meus Pedidos'));
    }
    public function Caminho_Ped_Minhas($export = FALSE) {
        $i = 0;
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Pedido',
                'Transporte/Pedido/Caminho_Ped_Add',
                ''
            ),
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Caminho_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido', '{sigla}status=\'0\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionDelete = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Caminho_Ped_Del');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Descrição'][$i]       = $valor->descricao_carga;
                $table['Dimensões'][$i]       = '<b>Altura:</b>'.$valor->altura.
                                                ' cm<br><b>Comprimento:</b>'.$valor->comprimento.
                                                ' cm<br><b>Largura:</b>'.$valor->largura.' cm<br><b>Volume:</b>'.$valor->altura*$valor->comprimento*$valor->largura.' cm³';
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Cancelar Pedido'       ,'Transporte/Pedido/Caminho_Ped_Del/'.$valor->id.'/'     ,'Deseja realmente Cancelar esse Pedido ?'), $permissionDelete);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Pedidos Cadastrados');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhum Pedido Cadastrado para exportar');
            } else {
                $mensagem = __('Nenhum Pedido Cadastrado');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem dos meus Pedidos').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Meus Pedidos'));
    }
    public function Caminho_Sol_Solicitacoes($export = FALSE) {
        $i = 0;
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Caminho_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido', '{sigla}status=\'0\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $perm_add = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Caminho_Sol_Add');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Descrição'][$i]       = $valor->descricao_carga;
                $table['Dimensões'][$i]       = '<b>Altura:</b>'.$valor->altura.
                                                ' cm<br><b>Comprimento:</b>'.$valor->comprimento.
                                                ' cm<br><b>Largura:</b>'.$valor->largura.' cm<br><b>Volume:</b>'.$valor->altura*$valor->comprimento*$valor->largura.' cm³';
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Status1'     ,Array('Fazer Proposta'        ,'Transporte/Pedido/Caminho_Sol_Add/'.$valor->id.'/'    , ''), $perm_add);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Pedidos pendentes');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhum Pedido pendente para exportar');
            } else {
                $mensagem = __('Nenhum Pedido Pendente');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Listagem dos Pedidos Pendentes').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Pedidos Pendentes'));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function Caminho_Sol_PedAceitos($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Caminho_Sol_PedAceitos',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}status=\'1\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Transportadora'][$i] = '#'.$valor->transportadora2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Aceitas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Aceita para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Aceita');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Aceitas').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Aceitas'));
    }
    public function Caminho_Sol_PedRecusados($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Caminho_Sol_PedRecusados',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}status=\'2\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Transportadora'][$i] = '#'.$valor->transportadora2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Recusadas');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Recusada para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Recusada');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Recusadas').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Recusadas'));
    }
    public function Caminho_Sol_PedPendente($export = FALSE) {
        
        //self::Endereco_Noticia(FALSE);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            FALSE,
            Array(
                'Print'     => TRUE,
                'Pdf'       => TRUE,
                'Excel'     => TRUE,
                'Link'      => 'Transporte/Pedido/Caminho_Sol_PedPendente',
            )
        )));
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}status=\'0\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'');
        if (is_object($pedido)) $pedido = Array(0=>$pedido);
        if ($pedido !== FALSE && !empty($pedido)) {
            $i = 0;
            reset($pedido);
            $permissionDelete = $this->_Registro->_Acl->Get_Permissao_Url('Transporte/Pedido/Caminho_Sol_Del');
            foreach ($pedido as &$valor) {                
                $table['Id'][$i]           = '#'.$valor->id;
                $table['Pedido'][$i] = '#'.$valor->pedido2;
                $table['Transportadora'][$i] = '#'.$valor->transportadora2;
                $table['Valor'][$i]       = $valor->valor;
                $table['Observação'][$i]       = $valor->obs;
                $table['Funções'][$i]      = $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Cancelar Proposta'       ,'Transporte/Pedido/Caminho_Sol_Del/'.$valor->id.'/'     ,'Deseja realmente Cancelar essa Proposta ?'), $permissionDelete);
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if ($export !== FALSE) {
                self::Export_Todos($export, $table, 'Propostas Pendentes');
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
            if ($export !== FALSE) {
                $mensagem = __('Nenhuma Proposta Pendente para exportar');
            } else {
                $mensagem = __('Nenhuma Proposta Pendente');
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = __('Proposta Pendentes').' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo', __('Propostas Pendentes'));
    }
    
    
    
    
    
    
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Caminho_Sol_Add($pedido = FALSE) {
        if ($pedido === FALSE) return FALSE;
        else{
            $pedido = (int) $pedido;
            
            $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido', '{sigla}id=\''.$pedido.'\'',1);
            if ($pedido === FALSE || $pedido->status!=0) {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Erro'),
                    "mgs_secundaria" => __('Você não pode Adicionar uma Solicitação a este pedido')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                return FALSE;
            }
        }
        //self::Endereco_Noticia(TRUE);
        // Carrega Config
        $titulo1    = __('Adicionar Proposta de Pedido');
        $titulo2    = __('Salvar Proposta de Pedido');
        $formid     = 'formTransporte_Pedido_Caminho_Sol_Add';
        $formbt     = __('Salvar');
        $formlink   = 'Transporte/Pedido/Caminho_Sol_Add2/'.$pedido;
        $campos = Transporte_Caminhoneiro_Pedido_Lance_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos,'pedido');
        self::DAO_Campos_Retira($campos,'status');
        self::DAO_Campos_Retira($campos,'transportadora');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1, $titulo2, $formlink, $formid, $formbt, $campos);
    }
    /**
     * 
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Caminho_Sol_Add2($pedido = FALSE) {
        if ($pedido === FALSE) return FALSE;
        else{
            $pedido = (int) $pedido;
            
            $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido', '{sigla}id=\''.$pedido.'\'',1);
            if ($pedido === FALSE || $pedido->status!=0) {
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => __('Erro'),
                    "mgs_secundaria" => __('Você não pode Adicionar uma Solicitação a este pedido')
                );
                $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
                return FALSE;
            }
        }
        $titulo     = __('Proposta enviada com Sucesso');
        $dao        = 'Transporte_Caminhoneiro_Pedido_Lance';
        $function     = '$this->Caminho_Sol_Solicitacoes();';
        $sucesso1   = __('Proposta enviada com Sucesso');
        $sucesso2   = __('Aguarde uma Resposta.');
        $alterar    = Array('status'=>'0', 'transportadora'=>$pedido->log_user_add,'pedido'=>$pedido->id);
        $this->Gerador_Formulario_Janela2($titulo, $dao, $function, $sucesso1, $sucesso2, $alterar);
    }
    /**
     * 
     * 
     * @param int $id Chave Primária (Id do Registro)
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     */
    public function Caminho_Sol_Del($id) {
        
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido_lance    =  $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido_Lance', '{sigla}id=\''.$id.'\' AND {sigla}log_user_add=\''.$this->_Acl->Usuario_GetID().'\'',1);
        
        if ($pedido_lance === FALSE || $pedido_lance->status!=0) {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            return FALSE;
        }
        
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Caminhoneiro_Pedido', '{sigla}id=\''.$pedido_lance->pedido.'\'',1);
        if ($pedido === FALSE || $pedido->status!=0) {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Você não pode deletar')
            );
            $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
            return FALSE;
        }
        
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido_lance);
        // Mensagem
    	if ($sucesso === TRUE) {
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => __('Deletado'),
                "mgs_secundaria" => __('Proposta Cancelada com Sucesso')
            );
    	} else {
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => __('Erro'),
                "mgs_secundaria" => __('Erro')
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens', $mensagens);
        
        $this->Caminho_Sol_Solicitacoes();
        
        $this->_Visual->Json_Info_Update('Titulo', __('Proposta Cancelada com Sucesso'));  
        $this->_Visual->Json_Info_Update('Historico', FALSE);  
    }
}
?>
