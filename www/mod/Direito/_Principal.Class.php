<?php

class advogado_Principal implements \Framework\PrincipalInterface
{
    /**
    * Função Home para o modulo advogado aparecer na pagina HOME
    * 
    * @name Home
    * @access public
    * @static
    * 
    * @param Class &$controle Classe Controle Atual passada por Ponteiro
    * @param Class &$Modelo Modelo Passado por Ponteiro
    * @param Class &$Visual Visual Passado por Ponteiro
    *
    * @uses \Framework\App\Controle::$advogado
    * 
    * @return void 
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.2
    */
    static function Home(&$controle, &$Modelo, &$Visual) {
        return 0;
    }
    static function Busca(&$controle, &$Modelo, &$Visual, $busca) {
        return FALSE;
    }
    static function Config() {
        return FALSE;
    }

    public static function Estatistica($data_inicio, $data_final, $filtro = FALSE) {
        return FALSE;
    }

    public static function Relatorio($data_inicio, $data_final, $filtro = FALSE) {
        return FALSE;
    }
}
