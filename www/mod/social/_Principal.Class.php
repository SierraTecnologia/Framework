<?php
class social_Principal implements \Framework\PrincipalInterface
{
    /**
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 0.4.24
    */
    static function Home(&$controle, &$Modelo, &$Visual) {
    }
    static function Busca(&$controle, &$Modelo, &$Visual, $busca) {
        return false;
    }
    static function Config() {
        return false;
    }
    
    static function Relatorio($data_inicio, $data_final, $filtro = false) {
        return false;
    }
    
    static function Estatistica($data_inicio, $data_final, $filtro = false) {
        return false;
    }
}
?>