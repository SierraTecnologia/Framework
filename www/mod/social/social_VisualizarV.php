<?php
class social_VisualizarVisual extends social_Visual
{

    public function __construct() {
        parent::__construct();
    } 
    public function exibetipos(&$tipo,&$tipos,&$tipon) {
        unset($table);
        $table = array();
        $i = 0;
        foreach ($tipon as $indice=>&$valor) {
            $table['Nome'][$i] = $valor;
            ++$i;
        }
        $i=0;
        foreach ($tipo as $indice=>&$valor) {
            $table['Quantidade'][$i] = $valor;
            ++$i;
        }
        $i = 0;
        foreach ($tipos as $indice=>&$valor) {
            $table['Pontos'][$i] = $valor;
            ++$i;
        }
        $this->novatabela("relatorio", $table);
    }
    public function exibe_persona(&$persona) {
        $this->blocos .= '<a href="javascript:popup()"><a href="http://www.facebook.com/profile.php?id='.$persona['id_face'].'" target="_blank"><img alt="'.__('Foto de Perfil').' src="http://graph.facebook.com/'.$persona['id_face'].'/picture"></a>';
        $this->blocos .= $persona['nome'];
        if ($persona['fis_sexo']==0) $this->blocos .= __('Feminino');
        else $this->blocos .= __('Masculino');
        $this->blocos .= date_time($persona['nasc'], "d/m/Y");
        $this->blocos .= $persona['pontos'];
        $this->blocos .= '<p class="botao"><a onclick="return popup(\''.__('Nova Ação do Usuário').'\',\social/acoes/newacaouser/'.$persona['id'].'\')" href="#">'.__('Adicionar Ação').'</a></p>';
        $this->blocos .= '<p class="botao"><a onclick="return popup(\''.__('Nova Ação do Usuário').'\',\social/acoes/newacaouser/'.$persona['id'].'\')" href="#">'.__('Subir Fotos').'</a></p><br>
        '.__('Chance de Estar Falando a Verdade: ').$persona['por_confiar'].'%<br>
        '.__('Chance de Ficar: ').$persona['por_ficar'].'%<br>
        '.__('Vontade de Passar tempo Junto: ').$persona['por_chata'].'%';
    } 
}
?>