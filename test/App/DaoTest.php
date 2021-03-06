<?php

namespace Framework\App;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-08-24 at 16:29:47.
 */
class DaoTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Dao
     */
    protected $objects;
    protected $tabelas;
    protected $tabelas_ext;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
            // Carrega Todos os DAO
            $diretorio = dir(DAO_PATH);
            $conexao = new Conexao();
            // Percorre Diretório
            while($arquivo = $diretorio -> read()){
                if(strpos($arquivo, 'DAO.php')!==false){
                    $arquivo                = str_replace(Array('.php','.'), Array('','_') , $arquivo);

                    $this->tabelas[$arquivo] = Array (
                        'class'     => $arquivo::Get_Class(),
                        'nome'      => $arquivo::Get_Nome(),
                        'sigla'     => $arquivo::Get_Sigla(),
                        'colunas'   => $arquivo::Get_Colunas(),
                        'engine'    => $arquivo::Get_Engine(),
                        'charset'   => $arquivo::Get_Charset(),
                        'autoadd'   => $arquivo::Get_Autoadd(),
                        'Link'      => $arquivo::Get_LinkTable(),
                        'static'    => $arquivo::Get_StaticTable(),
                    );
                    $sigla = &$this->tabelas[$arquivo]['sigla'];

                    // Aproveita o while e Pega as extrangeiras
                    $resultado_unico = new $arquivo();
                    $extrangeira    = $resultado_unico->Get_Extrangeiras();
                    if($extrangeira!==false){
                        reset($extrangeira);
                        while (key($extrangeira) !== null) {
                            $current = current($extrangeira);
                            list($ligacao,$mostrar,$extcondicao) = $conexao->Extrangeiras_Quebra($current['conect']);

                            // ARMAZENA NA VARIAVEL DE CONTROLE AS SIGLAS
                            $this->tabelas_ext[$ligacao[0]][$sigla] = $sigla;
                            next($extrangeira);
                        }
                    }
                }
            }
            $diretorio -> close();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * 
     */
    public function testSiglas() {
        $siglas = Array();
        foreach($this->tabelas as &$valor){
            $nome = '';
            if(isset($siglas[$valor['sigla']])){
                $nome = $siglas[$valor['sigla']];
            }
            $this->assertArrayNotHasKey(
                $valor['sigla'],$siglas,'Siglas Repetidas: '.$nome.' e '.$valor['class'] //$siglas[$valor['sigla']].' e '.
            );
            $siglas[$valor['sigla']] = $valor['class'];
        }

    }

    /**
     * 
     */
    public function testColunas() {
        $siglas = Array();
        // Abre Todos os DAO
        foreach($this->tabelas as &$valor){
            $coluna_repetida = Array();
            $primarias = 0;
            $possueAutocomplete = false;
            
            // Verifica se Colunas são um Array
            $this->assertTrue(
                is_array($valor['colunas']),'Classe Dao: '.$valor['class'].' -> Não Contem suas Colunas'
            );
            
            // Pega Coluna            
            foreach($valor['colunas'] as &$valor2){

                // Se For Campo de Tabela Linkada (Não é uma coluna da tabela), tem tratamento especial
                if(isset($valor2['TabelaLinkada'])){
                
                
                }else{
                    
                    // Verifica Mysql_Titulo
                    $this->assertArrayHasKey(
                        'mysql_titulo',$valor2,'Classe Dao: '.$valor['class'].' -> Não Contem "mysql_titulo"'
                    );

                    // Coluna Duplicada
                    $this->assertArrayNotHasKey(
                        $valor2['mysql_titulo'],$coluna_repetida,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Essa Coluna Já Existe nessa Tabela'
                    );
                    
                    // Verifica se Atributo da Classe Existe
                    $this->assertClassHasAttribute($valor2['mysql_titulo'],$valor['class'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> O Atributo de Classe dessa Coluna não Existe');

                    //'mysql_tipovar'
                    $this->assertArrayHasKey(
                        'mysql_tipovar',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_tipovar"'
                    );
                    // Verifica Tipos (longtext,text,varchar,int,float)



                    //'mysql_tamanho'
                    $this->assertArrayHasKey(
                        'mysql_tamanho',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_tamanho"'
                    );
                    //'mysql_null'
                    $this->assertArrayHasKey(
                        'mysql_null',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_null"'
                    );
                    //'mysql_default'
                    $this->assertArrayHasKey(
                        'mysql_default',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_default"'
                    );
                    
                    //'mysql_primary'
                    $this->assertArrayHasKey(
                        'mysql_primary',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_primary"'
                    );
                    if($valor2['mysql_primary']!==false){
                        ++$primarias;
                    }
                    
                    //'mysql_estrangeira'
                    $this->assertArrayHasKey(
                        'mysql_estrangeira',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_estrangeira"'
                    );

                    // Testa Extrangeiras se for diferente de false
                    if($valor2['mysql_estrangeira']!==false){
                        $extrangeiras = explode('|',$valor2['mysql_estrangeira']);
                        $this->assertFalse((sizeof($extrangeiras)===1),'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> "mysql_estrangeira" Inválida');
                        $this->assertFalse((sizeof($extrangeiras)>3),'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> "mysql_estrangeira" Inválida');
                    }


                    //'mysql_autoadd'
                    $this->assertArrayHasKey(
                        'mysql_autoadd',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_autoadd"'
                    );
                    if($valor2['mysql_autoadd']!==false){
                        $this->assertFalse($possueAutocomplete,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Pode ter duas Colunas com AutoInclemente');
                        $this->assertNotFalse($valor2['mysql_primary'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Somente Chaves Primários podem ser AutoInclemente');
                        $possueAutocomplete = true;
                    }
                    
                    //'mysql_comment'
                    $this->assertArrayHasKey(
                        'mysql_titulo',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_titulo"'
                    );
                    //'mysql_inside'
                    $this->assertArrayHasKey(
                        'mysql_inside',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_inside"'
                    );
                    //'mysql_outside'
                    $this->assertArrayHasKey(
                        'mysql_outside',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "mysql_outside"'
                    );
                    //'perm_copia'
                    $this->assertArrayHasKey(
                        'perm_copia',$valor2,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "perm_copia"'
                    );

                    // Se tiver Edicao
                    if(isset($valor2['edicao'])){
                        //'Edicao -> Nome'
                        $this->assertArrayHasKey(
                            'Nome',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "Edição -> Nome"'
                        );
                        //'Edicao -> valor_padrao'
                        $this->assertArrayHasKey(
                            'valor_padrao',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "Edição -> valor_padrao"'
                        );
                        //'Edicao -> readonly'
                        $this->assertArrayHasKey(
                            'readonly',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "Edição -> readonly"'
                        );
                        //'Edicao -> aviso'
                        $this->assertArrayHasKey(
                            'aviso',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "Edição -> aviso"'
                        );
                        if($valor2['mysql_estrangeira']===false){
                            //'Edicao -> formtipo'
                            $this->assertArrayHasKey(
                                'formtipo',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Não Contem "Edição -> formtipo", nem possui extrangeira'
                            );
                        }else if(isset($valor2['edicao']['formtipo'])){
                            $this->assertEquals($valor2['edicao']['formtipo'], 'select','Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Conexão com Extrangeira mas o formtipo não é do tipo select');
                        }
                        
                        // Verifica FormTipo é Formvalido
                        if(isset($valor2['edicao']['formtipo'])){
                            if($valor2['edicao']['formtipo']=='select'){
                                // Não Contem Informacoes Sobre
                                $this->assertArrayHasKey(
                                    'select',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> FormTipo select sem "Edição -> Select"'
                                );
                                //'Edicao -> select -> Nao pode ter Tipo
                                $this->assertArrayNotHasKey(
                                    'tipo',$valor2['edicao']['select'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> select Contem "tipo"'
                                );
                                
                                /* NAO É OBRIGATÒRIO//'Edicao -> select -> class
                                $this->assertArrayHasKey(
                                    'class',$valor2['edicao']['select'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> select não Contem "class"'
                                );*/
                                
                                //'Edicao -> select -> opcoes (Se Nao for do Tipo Extrangeira
                                if($valor2['mysql_estrangeira']===false){
                                    $this->assertArrayHasKey(
                                        'opcoes',$valor2['edicao']['select'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Indice Edicao -> select não Contem "opcoes" nen possue Ligação Extrangeira'
                                    );
                                }
                                
                                // Se Tiver 'opcoes', verifica integridade
                                if(isset($valor2['edicao']['select']['opcoes'])){
                                    foreach($valor2['edicao']['select']['opcoes'] as &$valor3){
                                        // Tem que ter "value"
                                        $this->assertArrayHasKey(
                                            'value',$valor3,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Indice Edicao/select/opcoes não Contem "value"'
                                        );
                                        // Tem que ter "nome"
                                        $this->assertArrayHasKey(
                                            'nome',$valor3,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Indice Edicao/select/opcoes não Contem "nome"'
                                        );
                                    }
                                }
                            }else if($valor2['edicao']['formtipo']=='textarea'){
                                // Não Contem Informacoes Sobre
                                $this->assertArrayHasKey(
                                    'textarea',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> FormTipo textarea sem "Edição -> textarea"'
                                );
                                //'Edicao -> select ->ter Tipo
                                $this->assertArrayHasKey(
                                    'tipo',$valor2['edicao']['textarea'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> textarea não Contem "tipo"'
                                );
                                
                                //'Edicao -> select -> class
                                $this->assertArrayHasKey(
                                    'class',$valor2['edicao']['textarea'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> textarea não Contem "class"'
                                );
                            }else if($valor2['edicao']['formtipo']=='input'){
                                // Não Contem Informacoes Sobre
                                $this->assertArrayHasKey(
                                    'input',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> FormTipo input sem "Edição -> input"'
                                );
                                //'Edicao -> select -> ter Tipo
                                $this->assertArrayHasKey(
                                    'tipo',$valor2['edicao']['input'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> input não Contem "tipo"'
                                );
                                
                                //'Edicao -> select -> class
                                $this->assertArrayHasKey(
                                    'class',$valor2['edicao']['input'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> input não Contem "class"'
                                );
                            }else if($valor2['edicao']['formtipo']=='upload'){
                                // Não Contem Informacoes Sobre
                                $this->assertArrayHasKey(
                                    'upload',$valor2['edicao'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> FormTipo upload sem "Edição -> upload"'
                                );
                                //'Edicao -> upload -> ter Tipo
                                $this->assertArrayHasKey(
                                    'tipo',$valor2['edicao']['upload'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> upload não Contem "tipo"'
                                );
                                
                                //'Edicao -> upload -> class
                                $this->assertArrayHasKey(
                                    'class',$valor2['edicao']['upload'],'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> upload não Contem "class"'
                                );
                            }else{
                                $this->assertTrue(false,'Classe Dao: '.$valor['class'].' (Coluna: '.$valor2['mysql_titulo'].') -> Campo Edicao -> formtipo Inválido');
                            }
                        }
                    }
                }
            }
            
            // Se Nao tiver Primarias Avisa
            $this->assertFalse(($primarias===0), 'Classe Dao: '.$valor['class'].' -> Não Contém Chave Primária');
            $this->assertFalse(($primarias>1 && $possueAutocomplete===true), 'Classe Dao: '.$valor['class'].' -> Tabelas com AutoCompĺete só podem ter uma chave primária');
        }
    }

}
