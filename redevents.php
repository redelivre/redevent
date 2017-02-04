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
    add_filter ('manage_edit-tf_events_columns', array( $this , 'tf_events_edit_columns' ) );
    add_action ('manage_posts_custom_column', array( $this , 'tf_events_custom_columns') );
    add_action( 'admin_init', array( $this , 'tf_events_create' ) );
    add_action ('save_post', array( $this, 'save_tf_events' ) );
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
    case "tf_col_ev_desc";
        the_excerpt();
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
    <li><label>Start Date</label><input name="tf_events_startdate" class="tfdate" value="<?php echo $clean_sd; ?>" /></li>
    <li><label>Start Time</label><input name="tf_events_starttime" value="<?php echo $clean_st; ?>" /><em>Use 24h format (7pm = 19:00)</em></li>
    <li><label>End Date</label><input name="tf_events_enddate" class="tfdate" value="<?php echo $clean_ed; ?>" /></li>
    <li><label>End Time</label><input name="tf_events_endtime" value="<?php echo $clean_et; ?>" /><em>Use 24h format (7pm = 19:00)</em></li>
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

}

global $Redevents;

$Redevents = new Redevents();

 ?>
