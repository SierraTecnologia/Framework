<?php
final Class Social_Acao_Presente_DAO extends Framework\App\Dao 
{
    protected $acao;
    protected $presente;
    
    protected static $objetocarregado     = false;     protected static $mysql_colunas       = false;     protected static $mysql_outside       = Array();     protected static $mysql_inside        = Array(); public function __construct() {  parent::__construct(); } public static function Get_Nome(){
        return MYSQL_SOCIAL_ACAO_PRESENTE;
    }
    /**
     * 
     * @return string
     */
    /**
     * Fornece Permissão de Copia da tabela
     * @return string
     */
    public static function Permissao_Copia(){
        return false;
    }
    public static function Get_Sigla(){
        return 'SAPP';
    }
    public static function Get_Engine(){
        return 'InnoDB';
    }
    public static function Get_Charset(){
        return 'latin1';
    }
    public static function Get_Autoadd(){
        return 1;
    }
    public static function Get_Class(){
        return get_class() ; //return str_replace(Array('_DAO'), Array(''), get_class());
    }
    public static function Get_LinkTable(){
        return Array(
            'SA'   =>'acao',
            'S'    =>'persona',
        );
    }
    public static function Gerar_Colunas(){
        return Array(
            Array(
                'mysql_titulo'      => 'acao',
                'mysql_tipovar'     => 'int', //varchar, int, 
                'mysql_tamanho'     => 11,
                'mysql_null'        => true,  // nulo ?
                'mysql_default'     => 0, // valor padrao
                'mysql_primary'     => true,  // chave primaria
                'mysql_indice_unico'=> 'acao',
                'mysql_estrangeira' => 'SA.id|SA.nome', // chave estrangeira     ligacao|apresentacao|condicao
                'mysql_autoadd'     => false,
                'mysql_comment'     => false,
                'mysql_inside'      => false, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => false, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => false, //permissao funcional necessaria para campo 2 todos 
                'linkextra'         => 'social/Acao/Acoes_Add',
                'edicao'            => Array(
                    'Nome'              => __('Ação'),
                    'valor_padrao'      => false,
                    'readonly'          => false,
                    'aviso'             => ''
                )
            ),
            Array(
                'mysql_titulo'      => 'presente',
                'mysql_tipovar'     => 'int', //varchar, int, 
                'mysql_tamanho'     => 11,
                'mysql_null'        => true,  // nulo ?
                'mysql_default'     => 0, // valor padrao
                'mysql_primary'     => true,  // chave primaria
                'mysql_indice_unico'=> 'acao',
                'mysql_estrangeira' => 'U.id|U.nome-U.razao_social', // chave estrangeira     ligacao|apresentacao|condicao
                'mysql_autoadd'     => false,
                'mysql_comment'     => false,
                'mysql_inside'      => false, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => false, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => false, //permissao funcional necessaria para campo 2 todos 
                'linkextra'         => 'usuario/Admin/Usuarios_Add/cliente', //0 ninguem, 1 admin, 2 todos
                'edicao'            => Array(
                    'Nome'              => __('Personagem'),
                    'valor_padrao'      => false,
                    'readonly'          => false,
                    'aviso'             => ''
                )
            )
        );
    }
}
?>
