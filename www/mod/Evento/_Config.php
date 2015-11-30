<?php
$configModule = function () {
    return Array(
        'Nome'                      =>  'Evento',
        'Descrição'                 =>  '',
        'System_Require'            =>  '3.1.0',
        'Version'                   =>  '3.1.1',
        'Dependencias'              =>  false,
    );
};
$configMenu = function () {
    return Array(
        'Administrar' => Array(
            'Nome'                  => __('Administrar'),
            'Link'                  => '#',
            'Gravidade'             => 80,
            'Img'                   => '',
            'Icon'                  => 'building',
            'Filhos'                => Array(__('Eventos')=>Array(
                'Nome'                  => __('Eventos'),
                'Link'                  => 'Evento/Evento/Eventos',
                'Gravidade'             => 90,
                'Img'                   => '',
                'Icon'                  => 'building',
                'Filhos'                => false,
            )),
        ),
    );
};
$config_Permissoes = function () {
    return Array(
        Array(
            'Nome'                  => __('Eventos - Listagem'),
            'Desc'                  => '',
            'Chave'                 => 'Evento_Evento',
            'End'                   => 'Evento/Evento', // Endereco da url de permissão
            'Modulo'                => 'Evento', // Modulo Referente
            'SubModulo'             => 'Evento',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula // Endereco da url de permissão
        ),
        Array(
            'Nome'                  => __('Eventos - Add'),
            'Desc'                  => '',
            'Chave'                 => 'Evento_Evento_Eventos_Add', // CHave unica nunca repete, chave primaria
            'End'                   => 'Evento/Evento/Eventos_Add', // Endereco da url de permissão
            'Modulo'                => 'Evento', // Modulo Referente
            'SubModulo'             => 'Evento',   // Submodulo Referente
            'Metodo'                => 'Eventos_Add,Eventos_Add2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Eventos - Editar'),
            'Desc'                  => '',
            'Chave'                 => 'Evento_Evento_Eventos_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => 'Evento/Evento/Eventos_Edit', // Endereco da url de permissão
            'Modulo'                => 'Evento', // Modulo Referente
            'SubModulo'             => 'Evento',// Submodulo Referente
            'Metodo'                => 'Eventos_Edit,Eventos_Edit2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Eventos - Deletar'),
            'Desc'                  => '',
            'Chave'                 => 'Evento_Evento_Eventos_Del', // CHave unica nunca repete, chave primaria
            'End'                   => 'Evento/Evento/Eventos_Del', // Endereco da url de permissão
            'Modulo'                => 'Evento', // Modulo Referente
            'SubModulo'             => 'Evento',   // Submodulo Referente
            'Metodo'                => 'Eventos_Del',  // Metodos referentes separados por virgula
        ),
    );
};
/**
 * Serve Para Personalizar o Modulo de Acordo com o gosto de cada "Servidor"
 * @return type
 * 
 * @author Ricardo Sierra <web@ricardosierra.com.br>
 */
$configFunctional = function () {
    return Array();
};
/**
 * Configurações que podem ser Alteradas por Admin ou outros usuarios do Sistema (Parametros Opcionais: Mascara e Max
 * @return type
 * 
 * @author Ricardo Sierra <web@ricardosierra.com.br>
 */
$configPublic = function () {
    return Array(
        /*'{chave}'  => Array(
            'Nome'                  => 'Nome',
            'Desc'                  => __('Descricao'),
            'Chave'                 => '{chave}',
            'Valor'                 => 'valor_padrao'
        )*/
    );
};

