<?php
/**
 * Plugin Name: Single Banner
 * Description: Single Banner for site
 * Version: 1.0
 * Author: Tony
 * Author URI: https://github.io/tony-tsx
 */

  define( 'SINGLE_BANNER_OPTION_NAME', 'single-site-banner' );
  define( 'SINGLE_BANNER_SCRIPT_HANDLE', 'single-site-banner' );

  $single_banner_areas = [];
  function get_single_banner_areas() {
    global $single_banner_areas;
    return $single_banner_areas;
  }

  function add_single_banner_area( string $label, string $slug, $short = null, $description = null ) {
    global $single_banner_areas;
    array_push( $single_banner_areas, [
      'label' => $label,
      'slug' => $slug,
      'description' => $description,
      'short' => $short,
    ] );
  }

  function get_single_banner_slugs() {
    return array_map(
      function( $area ) { return $area['slug']; },
      get_single_banner_areas()
    );
  }

  function get_single_banner_option( string $slug ) {
    $prefix = SINGLE_BANNER_OPTION_NAME;
    return "{$prefix}[{$slug}]";
  }

  function get_single_banner_area_data( string $slug ) {
    foreach( get_single_banner_areas() as $area )
      if ( $area['slug'] === $slug ) return $area;
  }

  function get_single_banner_area_key( string $slug, $key ) {
    $area = get_single_banner_area_data( $slug );
    if ( $area ) return $area[$key];
  }

  function get_single_banner_area_description( string $slug ) {
    return get_single_banner_area_key( $slug, 'description' );
  }

  function get_single_banner_area_short( string $slug ) {
    return get_single_banner_area_key( $slug, 'short' );
  }

  function get_single_banner_area_label( string $slug ) {
    return get_single_banner_area_key( $slug, 'label' );
  }

  function get_single_banner_id( string $slug ) {
    return get_option( get_single_banner_option( $slug ), null );
  }

  function has_single_banner( string $slug ) {
    if ( !get_single_banner_id( $slug ) ) return false;
    if ( !wp_get_attachment_image_src( get_single_banner_id( $slug ) ) ) return false;
    return true;
  }

  function get_single_banner_src( string $slug, $size = 'original' ) {
    if ( !has_single_banner( $slug ) ) throw new Exception( '' );
    return wp_get_attachment_image_src( get_single_banner_id( $slug ), $size )[0];
  }

  function get_single_banner( string $slug, $size = 'original' ) {
    if ( !has_single_banner( $slug ) ) throw new Exception( '' );
    return wp_get_attachment_image( get_single_banner_id( $slug ), $size );
  }

  add_action( 'wp_ajax_update_single_banner', function() {
    if ( !isset( $_POST['image-id'] ) ) return json_encode( [ 'error' => true, 'message' => 'unpassed image id' ] );
    if ( !isset( $_POST['area'] ) ) return json_encode( [ 'error' => true, 'message' => 'unpassed area' ] );
    $key = get_single_banner_option( $_POST['area'] );
    $option = get_option( $key, null );
    if ( $option == null ) add_option( $key, $_POST['image-id'] );
    else update_option( $key, $_POST['image-id'] );
    echo json_encode( [ 'error' => false, 'message' => 'ok' ] );
    exit();
  } );

  add_action( 'wp_ajax_remove_single_banner', function() {
    if ( !isset( $_POST['area'] ) ) return json_encode( [ 'error' => true, 'message' => 'unpassed area' ] );
    $key = get_single_banner_option( $_POST['area'] );
    if ( get_option( $key ) ) delete_option( $key );
    echo json_encode( [ 'error' => false, 'message' => 'ok' ] );
    exit();
  } );

  add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( strpos( $hook, 'site-banner' ) !== false ) {
      wp_enqueue_style( 'bootstrap.css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' );
      wp_enqueue_script( 'bootstrap.js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js' );
      wp_enqueue_script( SINGLE_BANNER_SCRIPT_HANDLE, plugin_dir_url( __FILE__ ) . 'js/index.js' );
      wp_localize_script( SINGLE_BANNER_SCRIPT_HANDLE, 'single_banner_utils', [
        'wp_action_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'single_banner_nonce' ),
      ] );
      wp_enqueue_media();
    }
  } );

  add_action( 'admin_menu', function() {
    $title = __( 'Site Banner', 'single-banner' );
    add_menu_page( $title, $title, 'manage_options', 'site-banner', function() {
      include __DIR__ . '/pages/single-banner-upload.php';
    } );
  } );

  add_single_banner_area( 'Topo', 'top' );
  add_single_banner_area( 'Depois de promoções', 'after-promotions' );
?>