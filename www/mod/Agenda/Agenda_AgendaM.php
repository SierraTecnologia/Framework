<?php

class Agenda_AgendaModelo extends Agenda_Modelo
{
    /**
     * __construct
     * 
     * @name __construct
     * @access public
     * 
     * @return void
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.4.2
     * 
     */
    public function __construct(){
      parent::__construct();
    }
    public function Agendas(){
        // Table's primary key
        $primaryKey = 'id';
        $tabela = 'Agenda';
        
        
        $perm_editar = $this->_Registro->_Acl->Get_Permissao_Url('Agenda/Agenda/Agendas_Edit');
        $perm_deletar = $this->_Registro->_Acl->Get_Permissao_Url('Agenda/Agenda/Agendas_Del');
        


        

        $columns = Array();
        
        $numero = -1;

        ++$numero;
        $columns[] = array( 'db' => 'id', 'dt' => $numero); //'Id';
        ++$numero;
        $columns[] = array( 'db' => 'motivo', 'dt' => $numero); //'Motivo';
        ++$numero;
        $columns[] = array( 'db' => 'motivoid', 'dt' => $numero); //'MotidoID';
        ++$numero;
        $columns[] = array( 'db' => 'datainicio', 'dt' => $numero); //'Data Inicio';
        ++$numero;
        $columns[] = array( 'db' => 'datafim', 'dt' => $numero); //'Data Fim';


        $function = '';
        if($perm_editar){
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Editar\'     ,Array(__(\'Editar Agenda\')        ,\'Agenda/Agenda/Agendas_Edit/\'.$d.\'/\'    ,\'\'),true);';
        }
        if($perm_deletar){
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Deletar\'     ,Array(__(\'Deletar Agenda\')        ,\'Agenda/Agenda/Agendas_Del/\'.$d.\'/\'    ,\'\'),true);';
        }
        
        ++$numero;
        eval('$function = function( $d, $row ) { $html = \'\'; '.$function.' return $html; };');       
        $columns[] = array( 'db' => 'chave',            'dt' => $numero,
            'formatter' => $function
        ); //'Funções';
                
        echo json_encode(
            \Framework\Classes\Datatable::complex( $_GET, Framework\App\Registro::getInstacia()->_Conexao, $tabela, $primaryKey, $columns, null)
        );
    }
}
?>