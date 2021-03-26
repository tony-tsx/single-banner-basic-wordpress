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

  function get_single_banner_id() {
    return get_option( SINGLE_BANNER_OPTION_NAME, null );
  }

  function has_single_banner() {
    if ( !get_single_banner_id() ) return false;
    if ( !wp_get_attachment_image_src( get_single_banner_id() ) ) return false;
    return true;
  }

  function get_single_banner_src( $size = 'original' ) {
    if ( !has_single_banner() ) throw new Exception( '' );
    return wp_get_attachment_image_src( get_single_banner_id(), $size );
  }

  function get_single_banner( $size = 'original' ) {
    if ( !has_single_banner() ) throw new Exception( '' );
    return wp_get_attachment_image( get_single_banner_id(), $size );
  }

  add_action( 'wp_ajax_update_single_banner', function() {
    if ( !isset( $_POST['image-id'] ) ) return json_encode( [ 'error' => true, 'message' => 'unpassed image id' ] );
    $option = get_option( SINGLE_BANNER_OPTION_NAME, null );
    if ( $option == null ) add_option( SINGLE_BANNER_OPTION_NAME, $_POST['image-id'] );
    else update_option( SINGLE_BANNER_OPTION_NAME, $_POST['image-id'] );
    echo json_encode( [ 'error' => false, 'message' => 'ok' ] );
    exit();
  } );

  add_action( 'wp_ajax_remove_single_banner', function() {
    if ( get_option(  SINGLE_BANNER_OPTION_NAME, null) ) delete_option( SINGLE_BANNER_OPTION_NAME );
    echo json_encode( [ 'error' => false, 'message' => 'ok' ] );
    exit();
  } );

  add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( strpos( $hook, 'site-banner' ) !== false ) {
      wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' );
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
?>