<?php
$config_Modulo = function (){
    return Array(
        'Nome'                      =>  'banner',
        'Descrição'                 =>  '',
        'System_Require'            =>  '3.1.0',
        'Version'                   =>  '3.1.1',
        'Dependencias'              =>  false,
    );
};
$config_Menu = function (){
    return Array(
        'Administrar'=>Array(
            'Filhos'                => Array('Banners'=>Array(
                'Nome'                  => __('Banners'),
                'Link'                  => 'banner/Admin/Main/',
                'Gravidade'             => 3,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'picture',
                'Filhos'                => false,
            ),),
        ),
    );
};
$config_Permissoes = function (){
    return Array(
        Array(
            'Nome'                  => __('Banners - Listagem'),
            'Desc'                  => '',
            'Chave'                 => 'banner_Admin',
            'End'                   => 'banner/Admin', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'banner', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Banners - Add'),
            'Desc'                  => '',
            'Chave'                 => 'banner_Admin_Banners_Add', // CHave unica nunca repete, chave primaria
            'End'                   => 'banner/Admin/banners_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'banner', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'banners_Add,banners_Add2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Banners - Editar'),
            'Desc'                  => '',
            'Chave'                 => 'banner_Admin_Banners_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => 'banner/Admin/banners_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'banner', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'banners_Edit,banners_Edit2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Banners - Deletar'),
            'Desc'                  => '',
            'Chave'                 => 'banner_Admin_Banners_Del', // CHave unica nunca repete, chave primaria
            'End'                   => 'banner/Admin/Banners_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'banner', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Banners_Del',  // Metodos referentes separados por virgula
        ),
    );
};
/**
 * Serve Para Personalizar o Modulo de Acordo com o gosto de cada "Servidor"
 * @return type
 * 
 * @author Ricardo Sierra <web@ricardosierra.com.br>
 */
$config_Funcional = function (){
    return Array();
};
/**
 * Configurações que podem ser Alteradas por Admin ou outros usuarios do Sistema (Parametros Opcionais: Mascara e Max
 * @return type
 * 
 * @author Ricardo Sierra <web@ricardosierra.com.br>
 */
$config_Publico = function (){
    return Array(
        /*'{chave}'  => Array(
            'Nome'                  => 'Nome',
            'Desc'                  => __('Descricao'),
            'Chave'                 => '{chave}',
            'Valor'                 => 'valor_padrao'
        )*/
    );
};
?>
