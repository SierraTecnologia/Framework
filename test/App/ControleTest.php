<?php

namespace Framework\App;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-08-24 at 16:29:46.
 */
class ControleTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Controle
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        //$this->object = new Controle;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers Framework\App\Controle::Json_Definir_zerar
     * @todo   Implement testJson_Definir_zerar().
     */
    public function testJson_Definir_zerar() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Tema_Endereco
     * @todo   Implement testTema_Endereco().
     */
    public function testTema_Endereco() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Enviar_Email
     * @todo   Implement testEnviar_Email().
     */
    public function testEnviar_Email() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Tema_Travar
     * @todo   Implement testTema_Travar().
     */
    public function testTema_Travar() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Tema_Travar_GET
     * @todo   Implement testTema_Travar_GET().
     */
    public function testTema_Travar_GET() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Export_Download
     * @todo   Implement testExport_Download().
     */
    public function testExport_Download() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Gerador_Visualizar_Unidade
     * @todo   Implement testGerador_Visualizar_Unidade().
     */
    public function testGerador_Visualizar_Unidade() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Gerador_Formulario
     * @todo   Implement testGerador_Formulario().
     */
    public function testGerador_Formulario() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }
    
    /**
     * @covers Framework\App\Controle::Gerador_Formulario_Col_Escondido
     * @todo   Implement testGerador_Formulario_Col_Escondido().
     */
    public function testGerador_Formulario_Col_Escondido(){
        // Teste1
        $coluna = Array(
            'edicao' => Array(
                'form_escondido' => true
            )
        );
        $this->assertEquals('apagado',Controle::Gerador_Formulario_Col_Escondido($coluna),'Falhou Teste 1');
        // Teste2
        $coluna = Array(
            'edicao' => Array(
                'form_escondido' => 'apagado'
            )
        );
        $this->assertEquals('apagado',Controle::Gerador_Formulario_Col_Escondido($coluna),'Falhou Teste 2');
        // Teste3
        $coluna = Array(
            'edicao' => Array(
                'form_escondido' => 'apagar'
            )
        );
        $this->assertEquals('apagar',Controle::Gerador_Formulario_Col_Escondido($coluna),'Falhou Teste 3');
        // Teste4
        $coluna = Array(
            'TabelaLinkada' => Array(
                'formtipo'        => 'BoleanoMultiplo',
                'BoleanoMultiplo' => Array(
                    'form_escondido' => true
                )
            )
            
        );
        $this->assertEquals('apagado',Controle::Gerador_Formulario_Col_Escondido($coluna),'Falhou Teste 4');
        // Teste5
        $coluna = Array(
            'TabelaLinkada' => Array(
                'formtipo'        => 'BoleanoMultiplo',
                'BoleanoMultiplo' => Array(
                    'form_escondido' => 'apagado'
                )
            )
        );
        $this->assertEquals('apagado',Controle::Gerador_Formulario_Col_Escondido($coluna),'Falhou Teste 5');
        // Teste6
        $coluna = Array(
            'TabelaLinkada' => Array(
                'formtipo'        => 'BoleanoMultiplo',
                'BoleanoMultiplo' => Array(
                    'form_escondido' => 'apagar'
                )
            )
        );
        $this->assertEquals('apagar',Controle::Gerador_Formulario_Col_Escondido($coluna),'Falhou Teste 6');
    }

    /**
     * @covers Framework\App\Controle::Gerador_Formulario_Janela
     * @todo   Implement testGerador_Formulario_Janela().
     */
    public function testGerador_Formulario_Janela() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_Campos_TrocaID
     * @todo   Implement testDAO_Campos_TrocaID().
     */
    public function testDAO_Campos_TrocaID() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_Campos_TrocaNOME
     * @todo   Implement testDAO_Campos_TrocaNOME().
     */
    public function testDAO_Campos_TrocaNOME() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_Campos_RetiraAlternados
     * @todo   Implement testDAO_Campos_RetiraAlternados().
     */
    public function testDAO_Campos_RetiraAlternados() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_Campos_TrocaAlternados
     * @todo   Implement testDAO_Campos_TrocaAlternados().
     */
    public function testDAO_Campos_TrocaAlternados() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_Campos_AlternadosDesabilitados
     * @todo   Implement testDAO_Campos_AlternadosDesabilitados().
     */
    public function testDAO_Campos_AlternadosDesabilitados() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::mysql_MudaLeitura
     * @todo   Implement testMysql_MudaLeitura().
     */
    public function testMysql_MudaLeitura() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_RemoveLinkExtra
     * @todo   Implement testDAO_RemoveLinkExtra().
     */
    public function testDAO_RemoveLinkExtra() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_Ext_Alterar
     * @todo   Implement testDAO_Ext_Alterar().
     */
    public function testDAO_Ext_Alterar() {
        // Teste1
        $coluna = Array(Array(
            'mysql_titulo'      => 'bairro',
            'mysql_estrangeira' => 'E.id|E.nome|E.valor={valor}',
            'edicao' => Array(
                'form_escondido' => false
            )
        ));
        Controle::DAO_Ext_Alterar($coluna,'bairro','13');
        $this->assertEquals('E.id|E.nome|E.valor=13',$coluna[0]['mysql_estrangeira'],'Falhou Teste 1');
        // Teste2
        $coluna = Array(Array(
            'mysql_titulo'      => 'bairro',
            'mysql_estrangeira' => 'E.id|E.nome|E.valor={valor}',
            'edicao' => Array(
                'form_escondido' => false
            )
        ));
        Controle::DAO_Ext_Alterar($coluna,'bairro','teste');
        $this->assertEquals('E.id|E.nome|E.valor=teste',$coluna[0]['mysql_estrangeira'],'Falhou Teste 2');
        
        // Teste de Alteração 1
        $coluna = Array(
            0 => Array(
                'mysql_titulo'      => 'bairro',
                'mysql_estrangeira' => 'E.id|E.nome|E.valor={cidade}',
                'edicao' => Array(
                    'form_escondido' => false
                )
            ),
            1 => Array(
                'mysql_titulo'      => 'cidade',
                'mysql_estrangeira' => 'E.id|E.nome',
                'edicao' => Array(
                    'valor_padrao'   => '13',
                    'form_escondido' => false
                )
            )
        );
        Controle::DAO_Ext_Alterar($coluna,'bairro');
        $this->assertEquals('E.id|E.nome|E.valor=13',$coluna[0]['mysql_estrangeira'],'Falhou Teste de Alteração 1 -> Não Era pra Imprimir: '.$coluna[0]['mysql_estrangeira']);
        // Teste de Alteração 2
        $coluna = Array(
            0 => Array(
                'mysql_titulo'      => 'bairro',
                'mysql_estrangeira' => 'E.id|E.nome|E.valor=15',
                'edicao' => Array(
                    'form_escondido' => false
                )
            ),
            1 => Array(
                'mysql_titulo'      => 'cidade',
                'mysql_estrangeira' => 'E.id|E.nome',
                'edicao' => Array(
                    'valor_padrao'   => '13',
                    'form_escondido' => false
                )
            )
        );
        Controle::DAO_Ext_Alterar($coluna,'bairro');
        $this->assertEquals('E.id|E.nome|E.valor=15',$coluna[0]['mysql_estrangeira'],'Falhou Teste de Alteração 2 -> Não Era pra Imprimir: '.$coluna[0]['mysql_estrangeira']);
    }
    /**
     * @covers Framework\App\Controle::DAO_Ext_ADD
     * @todo   Implement testDAO_Ext_ADD().
     */
    public function testDAO_Ext_ADD() {
        // Teste1
        $coluna = Array(
            0 => Array(
                'mysql_titulo'      => 'bairro',
                'mysql_estrangeira' => 'E.id|E.nome',
                'edicao' => Array(
                    'form_escondido' => false
                )
            )
        );
        Controle::DAO_Ext_ADD($coluna,'bairro','E.teste');

        $this->assertEquals('E.id|E.nome|E.teste',$coluna[0]['mysql_estrangeira'],'Falhou Teste 1 -> Não Era pra Imprimir: '.$coluna[0]['mysql_estrangeira']);
    }

    /**
     * @covers Framework\App\Controle::mysql_AtualizaValor
     * @todo   Implement testMysql_AtualizaValor().
     */
    public function testMysql_AtualizaValor() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::mysql_AtualizaValores
     * @todo   Implement testMysql_AtualizaValores().
     */
    public function testMysql_AtualizaValores() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::DAO_Campos_Retira
     * @todo   Implement testDAO_Campos_Retira().
     */
    public function testDAO_Campos_Retira() {
        $colunas = \Usuario_DAO::Gerar_Colunas();
        Controle::DAO_Campos_Retira($colunas, 'login');
        Controle::DAO_Campos_Retira($colunas, __('Permissões do Usuário'));
        foreach($colunas as &$valor){
            if(isset($valor['TabelaLinkada'])){
                $this->assertNotEquals($valor['TabelaLinkada']['Nome'],__('Permissões do Usuário'),'DAO_Campos_Retira não Retira TabelaLinkada das Colunas');
            }else{
                $this->assertNotEquals($valor['mysql_titulo'],'login','DAO_Campos_Retira não Retira Campos das Colunas');
            }
        }
        Controle::DAO_Campos_Retira($colunas, 'senha',1);
        foreach($colunas as &$valor){
            $this->assertEquals($valor['mysql_titulo'],'senha','DAO_Campos_Retira Retirou Campos Errados da Coluna');
        }
        $this->assertEquals(sizeof($colunas),1,'DAO_Campos_Retira Deixou Campos além da Senha');
    }

    /**
     * @covers Framework\App\Controle::__destruct
     * @todo   Implement test__destruct().
     */
    public function test__destruct() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Framework\App\Controle::Widget_Add
     * @todo   Implement testWidget_Add().
     */
    public function testWidget_Add() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
