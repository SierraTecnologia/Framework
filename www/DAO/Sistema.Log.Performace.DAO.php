<?php
final Class Sistema_Log_Performace_DAO extends Framework\App\Dao 
{
    protected $chave;
    protected $nome;
    protected $tempo_total;
    protected $tempo_media;
    protected $tempo_minimo;
    protected $tempo_maximo;
    protected $ocorrencia;
    protected static $aceita_config     = FALSE;
    protected static $objetocarregado     = FALSE;     protected static $mysql_colunas       = FALSE;     protected static $mysql_outside       = Array();     protected static $mysql_inside        = Array(); public function __construct() {  parent::__construct(); } public static function Get_Nome() {
        return MYSQL_SIS_LOG_PERFORMACE;
    }
    /**
     * Fornece Permissão de Copia da tabela
     * @return string
     */
    public static function Permissao_Copia() {
        return FALSE;
    }
    public static function Get_Sigla() {
        return 'SLPE';
    }
    public static function Get_Engine() {
        return 'InnoDB';
    }
    public static function Get_Charset() {
        return 'latin1';
    }
    public static function Get_Autoadd() {
        return 1;
    }
    public static function Get_Class() {
        return get_class() ; //return str_replace(Array('_DAO'), Array(''), get_class());
    }
    public static function Gerar_Colunas() {
        return Array(
            Array(
                'mysql_titulo'      => 'chave',
                'mysql_tipovar'     => 'varchar', //varchar, int, 
                'mysql_tamanho'     => 120,
                'mysql_null'        => FALSE,  // nulo ?
                'mysql_default'     => FALSE, // valor padrao
                'mysql_primary'     => TRUE,  // chave primaria
                'mysql_estrangeira' => FALSE, // chave estrangeira
                'mysql_autoadd'     => FALSE,
                'mysql_comment'     => FALSE,
                'mysql_inside'      => FALSE, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => FALSE, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => FALSE, //permissao funcional necessaria para campo 2 todos 
                'linkextra'         => '' ,//0 ninguem, 1 admin, 2 todos 
            ),
            Array(
                'mysql_titulo'      => 'nome',
                'mysql_tipovar'     => 'varchar', //varchar, int, 
                'mysql_tamanho'     => 120,
                'mysql_null'        => FALSE,  // nulo ?
                'mysql_default'     => FALSE, // valor padrao
                'mysql_primary'     => FALSE,  // chave primaria
                'mysql_estrangeira' => FALSE, // chave estrangeira
                'mysql_autoadd'     => FALSE,
                'mysql_comment'     => FALSE,
                'mysql_inside'      => FALSE, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => FALSE, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => FALSE, //permissao funcional necessaria para campo 2 todos 
                'linkextra'         => '' ,//0 ninguem, 1 admin, 2 todos 
            ),
            Array(
                'mysql_titulo'      => 'tempo_total',
                'mysql_tipovar'     => 'float', //varchar, int, 
                'mysql_tamanho'     => '15,10',
                'mysql_null'        => TRUE,  // nulo ?
                'mysql_default'     => 'Vazio', // valor padrao
                'mysql_primary'     => FALSE,  // chave primaria
                'mysql_estrangeira' => FALSE, // chave estrangeira     ligacao|apresentacao|condicao
                'mysql_autoadd'     => FALSE,
                'mysql_comment'     => FALSE,
                'mysql_inside'      => FALSE, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => FALSE, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => FALSE, //permissao funcional necessaria para campo 2 todos
                'linkextra'         => '' ,//0 ninguem, 1 admin, 2 todos 
            ),
            Array(
                'mysql_titulo'      => 'tempo_media',
                'mysql_tipovar'     => 'float', //varchar, int, 
                'mysql_tamanho'     => '15,10',
                'mysql_null'        => TRUE,  // nulo ?
                'mysql_default'     => 'Vazio', // valor padrao
                'mysql_primary'     => FALSE,  // chave primaria
                'mysql_estrangeira' => FALSE, // chave estrangeira     ligacao|apresentacao|condicao
                'mysql_autoadd'     => FALSE,
                'mysql_comment'     => FALSE,
                'mysql_inside'      => FALSE, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => FALSE, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => FALSE, //permissao funcional necessaria para campo 2 todos
                'linkextra'         => '' ,//0 ninguem, 1 admin, 2 todos 
            ),
            Array(
                'mysql_titulo'      => 'tempo_minimo',
                'mysql_tipovar'     => 'float', //varchar, int, 
                'mysql_tamanho'     => '15,10',
                'mysql_null'        => TRUE,  // nulo ?
                'mysql_default'     => 'Vazio', // valor padrao
                'mysql_primary'     => FALSE,  // chave primaria
                'mysql_estrangeira' => FALSE, // chave estrangeira     ligacao|apresentacao|condicao
                'mysql_autoadd'     => FALSE,
                'mysql_comment'     => FALSE,
                'mysql_inside'      => FALSE, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => FALSE, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => FALSE, //permissao funcional necessaria para campo 2 todos
                'linkextra'         => '' ,//0 ninguem, 1 admin, 2 todos 
            ),
            Array(
                'mysql_titulo'      => 'tempo_maximo',
                'mysql_tipovar'     => 'float', //varchar, int, 
                'mysql_tamanho'     => '15,10',
                'mysql_null'        => TRUE,  // nulo ?
                'mysql_default'     => 'Vazio', // valor padrao
                'mysql_primary'     => FALSE,  // chave primaria
                'mysql_estrangeira' => FALSE, // chave estrangeira     ligacao|apresentacao|condicao
                'mysql_autoadd'     => FALSE,
                'mysql_comment'     => FALSE,
                'mysql_inside'      => FALSE, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => FALSE, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => FALSE, //permissao funcional necessaria para campo 2 todos
                'linkextra'         => '' ,//0 ninguem, 1 admin, 2 todos 
            ),
            Array(
                'mysql_titulo'      => 'ocorrencia',
                'mysql_tipovar'     => 'int', //varchar, int, 
                'mysql_tamanho'     => 11,
                'mysql_null'        => TRUE,  // nulo ?
                'mysql_default'     => 0, // valor padrao
                'mysql_primary'     => FALSE,  // chave primaria
                'mysql_estrangeira' => FALSE, // chave estrangeira     ligacao|apresentacao|condicao
                'mysql_autoadd'     => FALSE,
                'mysql_comment'     => FALSE,
                'mysql_inside'      => FALSE, // Funcao Executada quando o dado for inserido no banco de dados
                'mysql_outside'     => FALSE, // Funcao Executada quando o dado for retirado no banco de dados
                'perm_copia'        => FALSE, //permissao funcional necessaria para campo 2 todos
                'linkextra'         => '' ,//0 ninguem, 1 admin, 2 todos 
            )
        );
    }
}
?>
