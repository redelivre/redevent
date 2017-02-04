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
    //add_action( 'init', array( $this , 'create_eventcategory_taxonomy'));
    add_filter( 'manage_edit-tf_events_columns', array( $this , 'tf_events_edit_columns' ) );
    add_action( 'manage_posts_custom_column', array( $this , 'tf_events_custom_columns') );
    add_action( 'admin_init', array( $this , 'tf_events_create' ) );
    add_action( 'save_post', array( $this, 'save_tf_events' ) );
    add_filter( 'post_updated_messages', array( $this, 'events_updated_messages' ) );

    add_action( 'admin_print_styles-post.php', array( $this , 'events_styles' ), 1000 );
    add_action( 'admin_print_styles-post-new.php', array( $this , 'events_styles' ), 1000 );

    add_action( 'admin_print_scripts-post.php', array( $this , 'events_scripts' ), 1000 );
    add_action( 'admin_print_scripts-post-new.php', array( $this , 'events_scripts' ), 1000 );

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
        'menu_icon' => 'dashicons-calendar',
        'hierarchical' => false,
        'rewrite' => array( "slug" => "events" ),
        'supports'=> array('title', 'thumbnail', 'excerpt', 'editor') ,
        'show_in_nav_menus' => true,
        'taxonomies' => array( 'tf_eventcategory', 'post_tag')
    );

    register_post_type( 'tf_events', $args);


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

    register_taxonomy('tf_eventcategory','tf_events', array(
        'label' => __('Categoria dos Eventos'),
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'event-category' ),
    ));
  }

  function create_eventcategory_taxonomy() {


  }



  function tf_events_edit_columns($columns) {

    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "tf_col_ev_cat" => "Category",
        "tf_col_ev_date" => "Dates",
        "tf_col_ev_times" => "Times",
        "tf_col_ev_thumb" => "Thumbnail",
        "title" => "Event",
        "tf_col_ev_desc" => "Description",
        );
    return $columns;
  }

  function tf_events_custom_columns($column)
  {
    global $post;
    $custom = get_post_custom();
    switch ($column)
    {
      case "tf_col_ev_desc";
          the_excerpt();
      break;
      case "tf_col_ev_cat":
          // - show taxonomy terms -
          $eventcats = get_the_terms($post->ID, "tf_eventcategory");
          $eventcats_html = array();
          if ($eventcats) {
          foreach ($eventcats as $eventcat)
          array_push($eventcats_html, $eventcat->name);
          echo implode($eventcats_html, ", ");
          } else {
          _e('None', 'themeforce');;
          }
      break;
      case "tf_col_ev_date":
          // - show dates -
          $startd = $custom["tf_events_startdate"][0];
          $endd = $custom["tf_events_enddate"][0];
          $startdate = date("F j, Y", $startd);
          $enddate = date("F j, Y", $endd);
          echo $startdate . '<br /><em>' . $enddate . '</em>';
      break;
      case "tf_col_ev_times":
          // - show times -
          $startt = $custom["tf_events_startdate"][0];
          $endt = $custom["tf_events_enddate"][0];
          $time_format = get_option('time_format');
          $starttime = date($time_format, $startt);
          $endtime = date($time_format, $endt);
          echo $starttime . ' - ' .$endtime;
      break;
      case "tf_col_ev_thumb":
          // - show thumb -
          $post_image_id = get_post_thumbnail_id(get_the_ID());
          if ($post_image_id) {
          $thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
          if ($thumbnail) (string)$thumbnail = $thumbnail[0];
          echo '<img src="';
          echo bloginfo('template_url');
          echo '/timthumb/timthumb.php?src=';
          echo $thumbnail;
          echo '&h=60&w=60&zc=1" alt="" />';
      }
      break;

    }
  }

  function tf_events_create() {
    add_meta_box('tf_events_meta', 'Eventos', array( $this , 'tf_events_meta' ), 'tf_events');
}

function tf_events_meta () {

// - grab data -

global $post;
$custom = get_post_custom($post->ID);

if (array_key_exists('tf_events_startdate', $custom)){
  $meta_sd = $custom["tf_events_startdate"][0];
  $meta_st = $meta_sd;
}
else{
  $meta_sd = null;
}

if (array_key_exists('tf_events_enddate', $custom)){
  $meta_ed = $custom["tf_events_enddate"][0];
  $meta_et = $meta_ed;
}


// - grab wp time format -

$date_format = get_option('date_format'); // Not required in my code
$time_format = get_option('time_format');

// - populate today if empty, 00:00 for time -

if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}

// - convert to pretty formats -

$clean_sd = date("D, M d, Y", $meta_sd);
$clean_ed = date("D, M d, Y", $meta_ed);
$clean_st = date($time_format, $meta_st);
$clean_et = date($time_format, $meta_et);

// - security -

echo '<input type="hidden" name="tf-events-nonce" id="tf-events-nonce" value="' .
wp_create_nonce( 'tf-events-nonce' ) . '" />';

// - output -

?>
<div class="tf-meta">
<ul>
    <li><label>Data de Inicio</label>
      <p><input name="tf_events_startdate" class="tfdate calendar" value="<?php echo $clean_sd; ?>" /></p>
    </li>
    <li><label>Horario de Inicio Time</label>
      <p><input name="tf_events_starttime" value="<?php echo $clean_st; ?>" /></p>
      <em>Use o formato de 24h</em>
    </li>
    <li><label>Data de Termino</label>
      <p><input name="tf_events_enddate" class="tfdate" value="<?php echo $clean_ed; ?>" /></p>
    </li>

    <li><label>Horario de Termino</label>
      <p><input name="tf_events_endtime" value="<?php echo $clean_et; ?>" /></p><em>Use o formato de 24h</em>
    </li>
</ul>
</div>
<?php
}

function save_tf_events(){

global $post;

// - still require nonce

if ( !wp_verify_nonce( $_POST['tf-events-nonce'], 'tf-events-nonce' )) {
    return $post->ID;
}

if ( !current_user_can( 'edit_post', $post->ID ))
    return $post->ID;

// - convert back to unix & update post

if(!isset($_POST["tf_events_startdate"])):
return $post;
endif;
$updatestartd = strtotime ( $_POST["tf_events_startdate"] . $_POST["tf_events_starttime"] );
update_post_meta($post->ID, "tf_events_startdate", $updatestartd );

if(!isset($_POST["tf_events_enddate"])):
return $post;
endif;
$updateendd = strtotime ( $_POST["tf_events_enddate"] . $_POST["tf_events_endtime"]);
update_post_meta($post->ID, "tf_events_enddate", $updateendd );

}

function events_updated_messages( $messages ) {

  global $post, $post_ID;

  $messages['tf_events'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Evento atualizado. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Campo personalizado atualizado.'),
    3 => __('Campo personalizado removido.'),
    4 => __('Evento atualizado.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Evento recuperado para revisão de %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Evento publicado. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Evento salvo.'),
    8 => sprintf( __('Evento submetido. <a target="_blank" href="%s">Previsualizar evento</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Evento agendado para: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Previsualizar evento</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Rascunho de Evento atualizado. <a target="_blank" href="%s">Previsualizar Evento</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

function events_styles() {
    global $post_type;
    if( 'tf_events' != $post_type )
        return;
    wp_enqueue_style('ui-datepicker', plugin_dir_url( __FILE__ ) . 'css/jquery-ui-1.8.9.custom.css');
    wp_enqueue_style('ui-datepicker', plugin_dir_url( __FILE__ ) . 'css/tf-functions.css');

}

function events_scripts() {
    global $post_type;
    if( 'tf_events' != $post_type )
        return;
    wp_enqueue_script('jquery-ui', plugin_dir_url( __FILE__ ) . 'js/jquery-ui-1.8.9.custom.min.js', array('jquery'));
    wp_enqueue_script('ui-datepicker', plugin_dir_url( __FILE__ ) . 'js/jquery.ui.datepicker.js');
    wp_enqueue_script('custom_script', plugin_dir_url( __FILE__ ) .'js/pubforce-admin.js', array('jquery'));
}




}

global $Redevents;

$Redevents = new Redevents();

 ?>
