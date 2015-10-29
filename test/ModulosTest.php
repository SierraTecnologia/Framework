<?php

namespace Framework\App;

use \Kevintweber\PhpunitMarkupValidators\Assert\AssertHTML5;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-08-24 at 16:29:47.
 * @group Compilation
 */
class ModulosTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Dao
     */
    protected $objects;
    protected $modulos = Array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
            // Carrega Todos os DAO
            $diretorio = dir(MOD_PATH);
            // Percorre Diretório
            while($arquivo = $diretorio -> read()){
                if($arquivo=='.' || $arquivo=='..'){
                    continue;
                }
                $this->assertTrue(is_dir(MOD_PATH.$arquivo),'Modulos Obrigatóriamente tem que ser Pastas: '.$arquivo);
                $this->modulos[] = $arquivo;
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
    public function testArquivos() {
        $siglas = Array();
        foreach($this->modulos as &$valor){
            $diretorio = dir(MOD_PATH.$valor.DS);
            $config = false;
            $principal = false;
            $controle = false;
            $modelo = false;
            $visual = false;
            // Percorre Diretório
            while($arquivo = $diretorio -> read()){
                if($arquivo=='.' || $arquivo=='..'){
                    continue;
                }
                if($arquivo === $valor.'_Controle.php'){
                    $controle = true;
                }else if($arquivo === $valor.'_Modelo.php'){
                    $modelo = true;
                }else if($arquivo === $valor.'_Visual.php'){
                    $visual = true;
                }else if($arquivo === '_Config.php'){
                    $config = true;
                }else if($arquivo === '_Principal.Class.php'){
                    $principal = true;
                }else {
                    $this->assertEquals(strpos($arquivo, $valor.'_'),0,'Modulo '.$valor.' possui um submodulo inválido: '.$arquivo);
                }
            }
            $this->assertTrue($controle,'Modulo '.$valor.' não possui class de Controle');
            $this->assertTrue($modelo,'Modulo '.$valor.' não possui class de Modelo');
            $this->assertTrue($visual,'Modulo '.$valor.' não possui class Visual');
            $this->assertTrue($config,'Modulo '.$valor.' não possui arquivo de Config');
            if($valor!=='_Sistema') $this->assertTrue($principal,'Modulo '.$valor.' não possui class Principal');
            $diretorio -> close();
        }
    }
    public function testPaginas() {
        // Declara Completo
        if(!defined('LAYOULT_IMPRIMIR')){
            define('LAYOULT_IMPRIMIR','AJAX');
        }
        $Registro = &\Framework\App\Registro::getInstacia();
        // Pega Instancia e Inicia Cache
        if($Registro->_Cache===false){
            $Registro->_Cache   = new \Framework\App\Cache(CACHE_PATH);   
        }

        if($Registro->_Conexao===false){
            $Registro->_Conexao = new \Framework\App\Conexao();
        }
        if($Registro->_Acl===false){
            $Registro->_Acl = $this->getMockBuilder('\Framework\App\Acl')
                    ->setConstructorArgs(Array(1))
                    ->getMock();
            $Registro->_Acl->method('Get_Permissao_Url')
                    ->willReturn(true);
        }
        
        // Percorre Todos os Modulos
        foreach($this->modulos as &$valor){
            $diretorio = dir(MOD_PATH.$valor.DS);
            // Percorre Todos os SUbmodulos desse Modulo
            while($arquivo = $diretorio -> read()){
                if($arquivo !== '.' && $arquivo !== '..' && $arquivo !== $valor.'_Controle.php' && 
                        $arquivo !== $valor.'_Modelo.php' && $arquivo !== $valor.'_Visual.php' && 
                        $arquivo !== '_Config.php' && $arquivo !== '_Principal.Class.php' && 
                        "C.php" === substr($arquivo, -5, 5)){
                   $metodos =  \Framework\Classes\SierraTec_Manutencao::PHP_GetClasses_Metodos(file_get_contents(MOD_PATH.$valor.DS.$arquivo));
                   $getsubmodulo = substr($arquivo, strlen($valor)+1, -5);
                   //$executar = new $nome();
                   foreach($metodos as &$valor2){
                        $getargs            = Array();
                        // Verifica Argumentos
                        if(!empty($valor2['Args'])){
                            foreach($valor2['Args'] as &$valor3){
                                if($valor3['Opcional']===true){
                                    $getargs[] = $valor3['Padrao'];
                                }
                            }
                            
                            // Se Nao tiver Argumentos Padroes, pula.
                            if(empty($getargs)){
                                continue;
                            }
                        }
                        
                        ob_start();
                        
                        // Recupera pra evitar multiplas solicitacoes
                        $getmetodo          = $valor2['Nome'];
                        $getmodulo          = $valor;

                        // Configura Modulos
                        $controle_Executar = $getmodulo.'_'.$getsubmodulo.'Controle';
                        $modelo_Executar = $getmodulo.'_'.$getsubmodulo.'Modelo';
                        $visual_Executar = $getmodulo.'_'.$getsubmodulo.'Visual';
                        $modulo_rotaC    = MOD_PATH.$getmodulo.DS.$getmodulo.'_Controle.php';
                        $modulo_rotaM    = MOD_PATH.$getmodulo.DS.$getmodulo.'_Modelo.php';
                        $modulo_rotaV    = MOD_PATH.$getmodulo.DS.$getmodulo.'_Visual.php';
                        $submodulo       = $getmodulo.'_'.$getsubmodulo.'Controle';
                        $submodulo_rotaC = MOD_PATH.$getmodulo.DS.$getmodulo.'_'.$getsubmodulo.'C.php';
                        $submodulo_rotaM = MOD_PATH.$getmodulo.DS.$getmodulo.'_'.$getsubmodulo.'M.php';
                        $submodulo_rotaV = MOD_PATH.$getmodulo.DS.$getmodulo.'_'.$getsubmodulo.'V.php';
                        $metodo          = $getmetodo;

                        // Verifica se Existe e Executa
                        if(is_readable($modulo_rotaC) && is_readable($modulo_rotaM) && is_readable($modulo_rotaV)){
                            if(is_readable($submodulo_rotaC) && is_readable($submodulo_rotaM) && is_readable($submodulo_rotaV)){
                                // Chama Controle, Modelo, e Visual
                                $Registro->_Modelo  = new $modelo_Executar;
                                $Registro->_Visual  = new $visual_Executar;
                                $Registro->_Controle = new $controle_Executar;

                                if(REQUISICAO_TIPO=='MODELO'){
                                    if(is_callable(array($Registro->_Modelo,$metodo))){
                                        $metodo = $getmetodo;
                                    }else{
                                        return _Sistema_erroControle::Erro_Fluxo('Metodo não Encontrado (Modelo): '.$getmodulo.' - '.$getsubmodulo.' - '.$getmetodo,404); 
                                    }
                                    if(count($getargs)>0){
                                        call_user_func_array(array($Registro->_Modelo,$metodo), $getargs);
                                    }else{
                                        call_user_func(array($Registro->_Modelo,$metodo));
                                    }
                                    // Impede Retorno do Json do Controle
                                    \Framework\App\Controle::Tema_Travar();
                                }else{
                                    if(is_callable(array($Registro->_Controle,$metodo))){
                                        $metodo = $getmetodo;
                                    }else{
                                        return _Sistema_erroControle::Erro_Fluxo('Metodo não Encontrado: '.$getmodulo.' - '.$getsubmodulo.' - '.$getmetodo,404); //
                                    }
                                    if(count($getargs)>0){
                                        call_user_func_array(array($Registro->_Controle,$metodo), $getargs);
                                    }else{
                                        call_user_func(array($Registro->_Controle,$metodo));
                                    }

                                }
                            }else{
                                if($getsubmodulo==''){
                                    return _Sistema_erroControle::Erro_Fluxo('SubMódulo Vazio: '.$arquivo.' - '.$valor,404); //
                                }else{
                                    return _Sistema_erroControle::Erro_Fluxo('SubMódulo não Encontrado: '.$getmodulo.' - '.$getsubmodulo,404); //
                                }
                            }
                        }else{
                            return _Sistema_erroControle::Erro_Fluxo('Módulo não Encontrado: '.$getmodulo,404); //
                        }
                        
                        $output = ob_get_contents();
                        ob_end_clean();
                        
                        // Trata a Resposta
                        if(is_string($output) && is_object(json_decode($output)) && (json_last_error() == JSON_ERROR_NONE)){
                            
                            // Trata Json se for Json Válido
                            $output = json_decode($output, true);
                            $have_tipo = Array();
                            $have_title = false;
                            $have_history = false;
                            $have_callback = false;
                            foreach($output as $indice3=>&$valor3){
                                if($indice3==='Info'){
                                    foreach($valor3 as $indice4=>&$valor4){
                                        if($indice4==='Titulo'){
                                            $have_title = true;
                                        }else if($indice4==='Historico'){
                                            $have_history = true;
                                        }else if($indice4==='Tipo'){
                                            $have_tipo = &$valor4;
                                        }else if($indice4==='callback'){
                                            $have_callback = true;
                                        }else{
                                            return _Sistema_erroControle::Erro_Fluxo($indice4.' é inválido dentro de Info: '.$getmodulo.' - '.$getsubmodulo.' - '.$getmetodo,404);
                                        }
                                    }
                                }else if($indice3==='Conteudo'){
                                    foreach($valor3 as &$valor4){
                                        if($valor4['html']==='') continue;
                                        if(substr($valor4['html'], 0, 3)==='<li'){
                                            AssertHTML5::isValidMarkup('<ul>'.$valor4['html'].'</ul>','Url:'.$getmodulo.'/'.$submodulo.'/'.$getmetodo.' -> HTML:'.$valor4['html']);
                                        }else{
                                            AssertHTML5::isValidMarkup($valor4['html'],'Url:'.$getmodulo.'/'.$submodulo.'/'.$getmetodo.' -> HTML:'.$valor4['html']);
                                        }
                                    }
                                }else{
                                    return _Sistema_erroControle::Erro_Fluxo($indice3.' é inválido: '.$getmodulo.' - '.$getsubmodulo.' - '.$getmetodo,404); //
                                }

                            }
                        }else if(is_string($output) && $output!=''){
                            var_dump(json_last_error(),JSON_ERROR_NONE);
                            AssertHTML5::isValidMarkup($output,'Url:'.$getmodulo.'/'.$submodulo.'/'.$getmetodo.' -> HTML:'.$output);
                        }
                   }
                }
            }
        }
        
    }
}
?>