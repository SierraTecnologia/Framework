<?php
$config_Modulo = function (){
    return Array(
        'Nome'                      =>  '_Sistema',
        'Descrição'                 =>  '',
        'System_Require'            =>  '3.1.0',
        'Version'                   =>  '3.1.1',
        'Dependencias'              =>  false,
    );
};
$config_Menu = function (){
    return Array(
        'Página Inicial' => Array(
            'Nome'                  => __('Página Inicial'),
            'Link'                  => '_Sistema/Principal/Home',
            'Gravidade'             => 10000,
            'Img'                   => 'turboadmin/m-dashboard.png',
            'Icon'                  => 'dashboard',
            'Filhos'                => false,
        ),'Cadastros' => Array(
            'Nome'                  => __('Cadastros'),
            'Link'                  => '#',
            'Gravidade'             => 10,
            'Img'                   => 'turboadmin/m-dashboard.png',
            'Icon'                  => 'cog',
        ),'Administrar' => Array(
            'Nome'                  => __('Administrar'),
            'Link'                  => '#',
            'Gravidade'             => 8,
            'Img'                   => '',
            'Icon'                  => 'building',
        ),'Configurações' => Array(
            'Nome'                  => __('Configurações'),
            'Link'                  => '#',
            'Gravidade'             => 6,
            'Img'                   => 'turboadmin/m-dashboard.png',
            'Icon'                  => 'wrench',
            'Filhos'                => Array('Grupos'=>Array(
                'Nome'                  => __('Grupos'),
                'Link'                  => '_Sistema/Admin/Grupos',
                'Gravidade'             => 1,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'group',
                'Filhos'                => false,
            ),'Menu'=>Array(
                'Nome'                  => __('Menu'),
                'Link'                  => '_Sistema/Admin/Menus',
                'Gravidade'             => 1,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'dashboard',
                'Permissao_Func'        => Array(// Permissoes NEcessarias
                    '_Sistema_Avancado' => true
                ),
                'Filhos'                => false,
            ))
        ),'Relatório' => Array(
            'Nome'                  => __('Relatório'),
            'Link'                  => '#',
            'Gravidade'             => 4,
            'Img'                   => 'turboadmin/m-dashboard.png',
            'Icon'                  => 'book',
        ),'Gráfico' => Array(
            'Nome'                  => __('Gráfico'),
            'Link'                  => '#',
            'Gravidade'             => 3,
            'Img'                   => 'turboadmin/m-dashboard.png',
            'Icon'                  => 'book',
        )
    );
};
$config_Permissoes = function (){
    return Array(
        Array(
            'Nome'                  => __('Sistema - Administração Avançada'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin',
            'End'                   => '_Sistema/Admin', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Avancado' => true
            ),
        ),
        
        // Menu
        Array(
            'Nome'                  => __('Sistema (Menu) - Listagem'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Menus',
            'End'                   => '_Sistema/Admin/Menus', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Avancado' => true
            ),
        ),
        Array(
            'Nome'                  => __('Sistema (Menu) - Add'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Menus_Add', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Menus_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Menus_Add,Menus_Add2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Avancado' => true
            ),
        ),
        Array(
            'Nome'                  => __('Sistema (Menu) - Editar'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Menus_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Menus_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'Menus_Edit,Menus_Edit2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Avancado' => true
            ),
        ),
        Array(
            'Nome'                  => __('Sistema (Menu) - Deletar'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Menus_Del', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Menus_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Menus_Del',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Avancado' => true
            ),
        ),
        
        // Newsletter
        Array(
            'Nome'                  => __('Newsletter - Listagem'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Newsletter',
            'End'                   => '_Sistema/Admin/Newsletter', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Newsletter' => true
            ),
        ),
        Array(
            'Nome'                  => __('Newsletter - Add'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Newsletter_Add', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Newsletter_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Newsletter_Add,Newsletter_Add2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Newsletter' => true
            ),
        ),
        Array(
            'Nome'                  => __('Newsletter - Editar'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Newsletter_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Newsletter_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'Newsletter_Edit,Newsletter_Edit2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Newsletter' => true
            ),
        ),
        Array(
            'Nome'                  => __('Newsletter - Deletar'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Newsletter_Del', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Newsletter_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Newsletter_Del',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                '_Sistema_Newsletter' => true
            ),
        ),
        
        
        // Grupos
        
        Array(
            'Nome'                  => __('Sistema (Grupos) - Listagem'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Grupos',
            'End'                   => '_Sistema/Admin/Grupos', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => '*',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Sistema (Grupos) - Add'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Grupos_Add', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Grupos_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Grupos_Add,Grupos_Add2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Sistema (Grupos) - Editar'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Grupos_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Grupos_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'Grupos_Edit,Grupos_Edit2',  // Metodos referentes separados por virgula
        ),
        Array(
            'Nome'                  => __('Sistema (Grupos) - Deletar'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Grupos_Del', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Grupos_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Grupos_Del',  // Metodos referentes separados por virgula
        ),
        // PERMISSOES DE GRUPO
        Array(
            'Nome'                  => __('Permissões (Grupo) - Listagem'),
            'Desc'                  => '',
            'Chave'                 => '_Sistema_Admin_Grupos', // CHave unica nunca repete, chave primaria
            'End'                   => '_Sistema/Admin/Grupos', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => '_Sistema', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Grupos',  // Metodos referentes separados por virgula
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
    return Array(
        '_Sistema_Newsletter'  => Array(
            'Nome'                  => 'Sistema -> Newsletter',
            'Desc'                  => __('Se possue Newsletter'),
            'chave'                 => '_Sistema_Newsletter',
            'Valor'                 => false,  // false, true, ou array com os grupos que pode
        ),
        '_Sistema_Avancado'  => Array(
            'Nome'                  => 'Sistema -> Avancado',
            'Desc'                  => __('Se possue acesso a parte Avancada do Sistema'),
            'chave'                 => '_Sistema_Avancado',
            'Valor'                 => false,  // false, true, ou array com os grupos que pode
        ),
    );
};
?>
