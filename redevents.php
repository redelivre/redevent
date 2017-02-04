<?php

/*
   Plugin Name: Redelivre Events
   Version: 1.0
   Plugin URI: https://github.com/redelivre/redevents
   Description: Plugin for manage some events
   Group: Rede Livre
   Author: Maurilio Atila
   Author URI: http://cabelotaina.github.io
   Developer: https://github.com/cabelotaina

   Text Domain: redevents
   Domain Path: /languages/
 */

class Redevents {
  public function __construct(){
    add_action( 'init', array( $this , 'create_event_postype' ) );
    add_action( 'init', array( $this , 'create_eventcategory_taxonomy'));
    add_filter ('manage_edit-rl_events_columns', array( $this , 'rl_events_edit_columns' ) );
    add_action ('manage_posts_custom_column', array( $this , 'rl_events_custom_columns') );
  }

  function create_event_postype(){
    $labels = array(
        'name' => _x('Eventos', 'redevents'),
        'singular_name' => _x('Evento', 'redevents'),
        'add_new' => _x('Adicionar Novo', 'redevents'),
        'add_new_item' => __('Adicionar Novo Evento', 'redevents'),
        'edit_item' => __('Editar Evento', 'redevents'),
        'new_item' => __('Novo Evento', 'redevents'),
        'view_item' => __('Vizualizar Evento', 'redevents'),
        'search_items' => __('Buscar Eventos', 'redevents'),
        'not_found' =>  __('Nenhum evento encontrado', 'redevents'),
        'not_found_in_trash' => __('Nenhum evento encontrado na Lixeira', 'redevents'),
        'parent_item_colon' => '',
    );

    $args = array(
        'label' => __('Eventos'),
        'labels' => $labels,
        'public' => true,
        'can_export' => true,
        'show_ui' => true,
        '_builtin' => false,
        'capability_type' => 'post',
        //'menu_icon' => get_bloginfo('template_url').'/functions/images/event_16.png',
        'hierarchical' => false,
        'rewrite' => array( "slug" => "events" ),
        'supports'=> array('title', 'thumbnail', 'excerpt', 'editor') ,
        'show_in_nav_menus' => true,
        'taxonomies' => array( 'rl_eventcategory', 'post_tag')
    );

    register_post_type( 'rl_events', $args);
  }

  function create_eventcategory_taxonomy() {

    $labels = array(
        'name' => _x( 'Categorias', 'taxonomy general name' ),
        'singular_name' => _x( 'Categoria', 'taxonomy singular name' ),
        'search_items' =>  __( 'Buscar Categorias' ),
        'popular_items' => __( 'Popular Categorias' ),
        'all_items' => __( 'Todas as Categorias' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Editar Categoria' ),
        'update_item' => __( 'Atualizar Categoria' ),
        'add_new_item' => __( 'Adicionar Nova Categoria' ),
        'new_item_name' => __( 'Novo Nome de Categoria' ),
        'separate_items_with_commas' => __( 'Separar categorias com virgulas' ),
        'add_or_remove_items' => __( 'Adicionar ou remover categorias' ),
        'choose_from_most_used' => __( 'Escolher das categorias mais usadas' ),
    );

    register_taxonomy('rl_eventcategory','rl_events', array(
        'label' => __('Categoria dos Eventos'),
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'event-category' ),
    ));
  }

}

global $Redevents;

$Redevents = new Redevents();

 ?>
