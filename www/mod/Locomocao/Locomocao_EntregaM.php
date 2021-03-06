<?php

class Locomocao_EntregaModelo extends Locomocao_Modelo
{
    public function __construct(){
        parent::__construct();
    }
    public function Entregas(){
        // Table's primary key
        $primaryKey = 'id';
        $tabela = 'Locomocao_Entrega';
        
        
        $perm_editar = $this->_Registro->_Acl->Get_Permissao_Url('Locomocao/Entrega/Entregas_Edit');
        $perm_del = $this->_Registro->_Acl->Get_Permissao_Url('Locomocao/Entrega/Entregas_Del');
        


        

        $columns = Array();
        
        $numero = -1;

        ++$numero;
        $columns[] = array( 'db' => 'id', 'dt' => $numero); //'Chave';
        ++$numero;
        $columns[] = array( 'db' => 'descricao', 'dt' => $numero); //'Descrição';
        ++$numero;
        $columns[] = array( 'db' => 'valor', 'dt' => $numero); //'Valor';


        $function = '';
        if($perm_editar){
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Editar\'     ,Array(__(\'Editar Entrega\')        ,\'Locomocao/Entrega/Entregas_Edit/\'.$d.\'/\'    ,\'\'),true);';
        }
        if($perm_del){
            $function .= ' $html .= Framework\App\Registro::getInstacia()->_Visual->Tema_Elementos_Btn(\'Deletar\'    ,Array(__(\'Deletar Entrega\')       ,\'Locomocao/Entrega/Entregas_Del/\'.$d.\'/\'     ,__(\'Deseja realmente deletar essa Entrega ?\')),true);';
        }
        
        ++$numero;
        eval('$function = function( $d, $row ) { $html = \'\'; '.$function.' return $html; };');       
        $columns[] = array( 'db' => 'id',            'dt' => $numero,
            'formatter' => $function
        ); //'Funções';
                
        echo json_encode(
            \Framework\Classes\Datatable::complex( $_GET, Framework\App\Registro::getInstacia()->_Conexao, $tabela, $primaryKey, $columns, null)
        );
    }
}
?>