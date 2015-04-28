<?php
class Financeiro_UsuarioControle extends Financeiro_Controle
{
    /**
     * 
     * Classe Para Ver oq Foi pago e oq precisa pagar no sistema
     * 
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses financeiro_ListarModelo Carrega financeiro Modelo
    * @uses financeiro_ListarVisual Carrega financeiro Visual
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        parent::__construct();
    }
    static function Endereco_Financeiro($true=true){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Financeiro';
        $link = '_Sistema/Principal/Home';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Endereco_Pagar($true=true){
        self::Endereco_Financeiro();
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'À Pagar';
        $link = 'Financeiro/Usuario/Pagar';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Endereco_Receber($true=true){
        self::Endereco_Financeiro();
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'À Receber';
        $link = 'Financeiro/Usuario/Receber';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Endereco_Pago($true=true){
        self::Endereco_Financeiro();
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Pagas';
        $link = 'Financeiro/Usuario/Pago';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Endereco_Recebido($true=true){
        self::Endereco_Financeiro();
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Recebidos';
        $link = 'Financeiro/Usuario/Recebido';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses Financeiro_Controle::$financeiroPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main(){
        return false;
    }
    /**
     * Contas a Pagar
     */
    public function Pagar($export = false){
        self::Endereco_Pagar(false);
        
        // Exportar
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            false,
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Financeiro/Usuario/Pagar',
            )
        )));
        
        // Parametros
        $titulo = 'Listagem de Contas à pagar';
        $where  = Array(
            'entrada_motivo'     => 'Servidor',
            'entrada_motivoid'   => SRV_NAME_SQL,
        );
        list($tabela,$i) = $this->Movimentacao_Interna($where,'Mini');
        $titulo = $titulo.' ('.$i.')';
        if($i==0){          
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Conta à pagar</font></b></center>');
        }else{
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Contas à pagar');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        $this->_Visual->Json_Info_Update('Titulo',$titulo); 
    }
    /**
     * Contas a Receber
     */
    public function Receber($export = false){
        self::Endereco_Receber(false);
        
        // Exportar
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            false,
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Financeiro/Usuario/Receber',
            )
        )));
        
        $titulo = 'Listagem de Contas à Receber';
        $where  = Array(
            'saida_motivo'     => 'Servidor',
            'saida_motivoid'   => SRV_NAME_SQL,
        );
        
        list($tabela,$i) = $this->Movimentacao_Interna($where,'Mini');
        $titulo = $titulo.' ('.$i.')';
        if($i==0 ){
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Conta à Receber</font></b></center>');
        }else{
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Contas à Receber');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        $this->_Visual->Json_Info_Update('Titulo',$titulo); 
    }
    public function Pago($export = false){
        self::Endereco_Pago(false);
        
        // Exportar
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            false,
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Financeiro/Usuario/Pago',
            )
        )));
        
        // Parametros
        $titulo = 'Listagem de Contas Pagas';
        $where  = Array(
            'entrada_motivo'     => 'Servidor',
            'entrada_motivoid'   => SRV_NAME_SQL,
        );
        list($tabela,$i) = $this->Movimentacao_Interna_Pago($where,'Mini');
        $titulo = $titulo.' ('.$i.')';
        if($i==0){          
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Conta Paga</font></b></center>');
        }else{
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Contas Pagas');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        $this->_Visual->Json_Info_Update('Titulo',$titulo); 
    }
    /**
     * Contas a Receber
     */
    public function Recebido($export = false){
        self::Endereco_Recebido(false);
        
        // Exportar
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            false,
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Financeiro/Usuario/Recebido',
            )
        )));
        
        $titulo = 'Listagem de Contas Recebidas';
        $where  = Array(
            'saida_motivo'     => 'Servidor',
            'saida_motivoid'   => SRV_NAME_SQL,
        );
        
        list($tabela,$i) = $this->Movimentacao_Interna_Pago($where,'Mini');
        $titulo = $titulo.' ('.$i.')';
        if($i==0 ){
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Conta Recebida</font></b></center>');
        }else{
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Contas Recebidas');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        $this->_Visual->Json_Info_Update('Titulo',$titulo); 
    }
    static function Financeiros_Campos_Retirar(&$campos){
        //self::DAO_Campos_Retira($campos, 'dt_pago');
        
        self::DAO_Campos_Retira($campos, 'categoria');
        self::DAO_Campos_Retira($campos, 'pago');
        self::DAO_Campos_Retira($campos, 'motivo');
        self::DAO_Campos_Retira($campos, 'motivoid');
        self::DAO_Campos_Retira($campos, 'entrada_motivo');
        self::DAO_Campos_Retira($campos, 'entrada_motivoid');
        self::DAO_Campos_Retira($campos, 'saida_motivo');
        self::DAO_Campos_Retira($campos, 'saida_motivoid');
        self::DAO_Campos_Retira($campos, 'forma_pagar');
        self::DAO_Campos_Retira($campos, 'forma_condicao');
        self::mysql_MudaLeitura($campos, Array('valor','dt_vencimento','num_parcela'));
        return true;
    }
    /**
     * Altera Usuario pra Pago ou pra Nao pago !
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    static function Financeiro_Usuario($motivo,$motivo_id,$pago){
        // Carrega Modelo
        $registro = \Framework\App\Registro::getInstacia();
        $_Modelo = $registro->_Modelo;
        // Prepara Update ClasseDAO|SET|WHERE
        $string = 'Financeiro_Pagamento_Interno|pago=\''.$pago.'\'| motivo=\''.$motivo.'\' AND motivoid=\''.$motivo_id.'\'';
        $_Modelo->db->Sql_Update($string);
    }
    public function Financeiros_Pagar($id=false, $localizacao=false, $dataini=false,$datafin=false){
        // Faz Protecao, e Linguagem apropriada
        if($id===false){
            throw new \Exception('Financeiro não especificado: '.$id,404);
        }
        $where = Array('id'=>  $id, 'pago'=>'0');
        $editar = $this->_Modelo->db->Sql_Select('Financeiro_Pagamento_Interno',$where);
        if($editar===false){
            throw new \Exception('Financeiro não existe: '.$id,404);
        }
        if($localizacao!==false){
            Financeiro_RelatorioControle::Endereco_Financeiro();
            if($editar->saida_motivo==='Servidor' && $editar->saida_motivoid===SRV_NAME_SQL){
                $pago = 'Recebido';
            }else{
                $pago = 'Pago';
            }
            $link_extra = '/'.$localizacao.'/'.$dataini.'/'.$datafin;
        }else if($editar->saida_motivo==='Servidor' && $editar->saida_motivoid===SRV_NAME_SQL){
            $pago = 'Recebido';
            self::Endereco_Receber();
            $link_extra = '';
        }else{
            $pago = 'Pago';
            self::Endereco_Pagar();
            $link_extra = '';
        }
        // Carrega Config
        $titulo1    = 'Declarar '.$pago.' (#'.$id.')';
        $titulo2    = $titulo1;
        $formid     = 'form_Sistema_AdminC_UsuarioEdit';
        $formbt     = $titulo1;
        $formlink   = 'Financeiro/Usuario/Financeiros_Pagar2/'.$id.$link_extra;
        $campos = Financeiro_Pagamento_Interno_DAO::Get_Colunas();
        // Retira Desnecessarios
        self::Financeiros_Campos_Retirar($campos);
        // Modifica Resultados
        if($editar->num_parcela=='0' || $editar->num_parcela==0){
            $editar->num_parcela = 'Parcela Unica';
        }else{
            $editar->num_parcela = $editar->num_parcela.'º Parcela';
        }
        $editar->valor_juros = $editar->valor;
        $editar->dt_pago = APP_DATA_BR;
        // Puxa controler
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Financeiros_Pagar2($id=false, $localizacao=false, $dataini=false,$datafin=false){
        // Verifica Existencia
        if($id===false){
            throw new \Exception('Financeiro não especificado: '.$id,404);
        }
        $where = Array('id'=>  $id);
        $financeiros = $this->_Modelo->db->Sql_Select('Financeiro_Pagamento_Interno',$where);
        if($financeiros===false){
            throw new \Exception('Financeiro não existe: '.$id,404);
        }
        if($financeiros->saida_motivo==='Servidor' && $financeiros->saida_motivoid===SRV_NAME_SQL){
            $pago = 'Recebido';
        }else{
            $pago = 'Pago';
        }
        if(!isset($_POST['valor_juros']) || !isset($_POST['dt_pago'])|| !isset($_POST['obs'])){
            throw new \Exception('Campos Imcompletos: ',404);
        }
        // Captura Valores
        $valor_juros    = \Framework\App\Sistema_Funcoes::Tranf_Real_Float(\anti_injection($_POST['valor_juros']));
        $dt_pago        = \anti_injection($_POST['dt_pago']);
        //$forma_pagar    = (int) $_POST['forma_pagar'];
        $obs            = \anti_injection($_POST['obs']);
        // Mensagens
        $titulo     = 'Declarado '.$pago.' com Sucesso';
        $dao        = Array('Financeiro_Pagamento_Interno',$id);
        $sucesso1   = $titulo;
        $sucesso2   = ''.$_POST["valor"].' '.$pago.' com sucesso.';
        // Verificação e Atualizacao
        $financeiros->valor = \Framework\App\Sistema_Funcoes::Tranf_Real_Float($financeiros->valor);
        /*if($financeiros->valor>$valor_juros){
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => 'Valor Pago Incorreto',
                "mgs_secundaria"    => 'O valor pago é inferior ao valor do Pagamento'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
            $this->_Visual->Json_Info_Update('Titulo', 'Erro');
        }else{*/
            // Altera Valores Manualmente e grava
            $financeiros->pago = '1';
            //$financeiros->forma_pagar = $forma_pagar;
            $financeiros->dt_pago = $dt_pago;
            $financeiros->obs = $obs;
            $financeiros->valor_juros = \Framework\App\Sistema_Funcoes::Tranf_Float_Real($valor_juros-$financeiros->valor);
            
            $financeiros->valor = \Framework\App\Sistema_Funcoes::Tranf_Float_Real($valor_juros);

            $sucesso = $this->_Modelo->db->Sql_Update($financeiros);
            if($sucesso){
                $mensagens = array(
                    "tipo"              => 'sucesso',
                    "mgs_principal"     => $sucesso1,
                    "mgs_secundaria"    => $sucesso2
                ); 
                $this->_Visual->Json_Info_Update('Titulo', $titulo);
                // SE vier do Relatorio, volta pra la
                if($localizacao!==false){
                    \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Financeiro/Relatorio/Relatorio/'.$localizacao.'/'.$dataini.'/'.$datafin);
                }else if($financeiros->saida_motivo==='Servidor' && $financeiros->saida_motivoid===SRV_NAME_SQL){
                    $this->Receber();
                }else{
                    $this->Pagar();
                }
            }else{
                $mensagens = array(
                    "tipo"              => 'erro',
                    "mgs_principal"     => 'Ocorreu um Erro',
                    "mgs_secundaria"    => 'Tente Novamente, algo deu errado.'
                );
                $this->_Visual->Json_Info_Update('Titulo', 'Erro');
            }
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        //}
        $this->_Visual->Json_Info_Update('Historico', false);
        $this->layoult_zerar = false;
        //
    }
    /**
     * 
     * @param type $id
     * @param type $tema (Pagar,Pago,Receber,Recebido)
     * @param type $layoult
     * @throws Exception
     */
    public function Financeiro_View($id,$layoult='Unico'){
        $html = '<span style="text-transform:uppercase;">';


        // Puxca Financeiro
        $identificador = $this->_Modelo->db->Sql_Select('Financeiro_Pagamento_Interno',Array('id'=>$id),1); // Banco DAO, Condicao e LIMITE
        // Verifica se Existe e Continua
        if($identificador===false){
            throw new \Exception('Pagamento não Existe',404);
        }
        $id = $identificador->id;
        
        
        if($identificador->entrada_motivo==='Servidor' && $identificador->entrada_motivoid===SRV_NAME_SQL){
            if($identificador->pago==1){
                $tema = 'Pago';
            }else{
                $tema = 'Pagar';
            }
        }else{            
            if($identificador->pago==1){
                $tema = 'Receber';
            }else{
                $tema = 'Recebido';
            }
        }
        
        // Nomes
        if($tema==='Pagar'){
            $titulo             = 'Conta a Pagar';
            $titulo_plural      = 'Contas a Pagar';
            $titulo_unico       = 'contasapagar';
        }else if($tema==='Pago'){
            $titulo             = 'Conta Paga';
            $titulo_plural      = 'Contas Pagas';
            $titulo_unico       = 'contaspagas';
        }else if($tema==='Receber'){
            $titulo             = 'Conta a Pagar';
            $titulo_plural      = 'Contas a Pagar';
            $titulo_unico       = 'contasareceber';
        }else{
            $tema               = 'Recebido';
            $titulo             = 'Conta Recebida';
            $titulo_plural      = 'Contas Recebidas';
            $titulo_unico       = 'contasrecebidas';
        }
        
        
        
        /*$cliente = $this->_Modelo->db->Sql_Select('Usuario',Array('id'=>$identificador->cliente),1); // Banco DAO, Condicao e LIMITE
        // Verifica se Existe e Continua
        if($identificador===false){
            throw new \Exception('Proposta não Existe',404);
        }
        if($cliente===false){
            throw new \Exception('Cliente não existe',404);
        }
        */
        
        
        if($layoult!=='Imprimir'){            
            $html .= $this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
                false,
                Array(
                    'Print'     => true,
                    'Pdf'       => false,
                    'Excel'     => false,
                    'Link'      => 'Financeiro/Usuario/Financeiro_View/'.$id.'/'.$tema,
                )
            ));
        }
        
        //DADOS
        if($identificador->valor!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Valor:</label>'.$identificador->valor.'</p>';
        if($identificador->obs!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Observação:</label>'.$identificador->obs.'</p>';
        
        // Bota Espaço
        $html .= '<div class="space15"></div>';
        
        $html .= '</span>';
        // Caso seja pra Imprimir
        if($layoult==='Imprimir'){
            self::Export_Todos($layoult,$html, $titulo.' #'.$identificador->id);
        }else{
            // Identifica tipo e cria conteudo
            if(LAYOULT_IMPRIMIR=='AJAX'){
                // Coloca Conteudo em Popup
                $popup = array(
                    'id'        => 'popup',
                    'title'     => $titulo.' #'.$identificador->id,
                    /*'botoes'    => array(
                        '0'         => array(
                            'text'      => 'Fechar Janela',
                            'clique'    => '$(this).dialog(\'close\');'
                        )
                    ),*/
                    'html'      => $html
                );
                $this->_Visual->Json_IncluiTipo('Popup',$popup);
            }else{
                // Coloca Endereco
                $funcao = 'Endereco_'.$tema;
                self::$funcao(true);
                $this->Tema_Endereco('Visualizar '.$titulo);
                // Coloca COnteudo em Janela
                $this->_Visual->Blocar($html);
                if($layoult==='Unico'){
                    $this->_Visual->Bloco_Unico_CriaJanela($titulo.' #'.$identificador->id);
                }else if($layoult==='Maior'){
                    $this->_Visual->Bloco_Maior_CriaJanela($titulo.' #'.$identificador->id);
                }else{
                    $this->_Visual->Bloco_Menor_CriaJanela($titulo.' #'.$identificador->id);
                }
            }
            //Carrega Json
            $this->_Visual->Json_Info_Update('Titulo',$titulo.' #'.$identificador->id);
        }
    }
}
?>
