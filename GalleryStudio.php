<?php

namespace TravelStudio\Classes;

/**
 * GalleryStudio
 * use it to write your custom functions.
 */
class GalleryStudio
{
  /**
   * Register Metabox
   */
  public function __construct(){ 
      add_action( 'add_meta_boxes', array( $this, 'metabox' ) );
      add_action( 'save_post', array( $this, 'save_gallery_data' ) );
   }

  /**
   * Initiate Metabox
   */
  public function metabox(){
      add_meta_box( 'GalleryStudio', 'Page Gallery', array( $this, 'metabox_callback' ), 'tour', 'side', 'high' );
  }


  function metabox_callback( $post ) {
    wp_nonce_field( array($this, 'save_gallery_data'), 'gallery_meta_box_nonce' );
    
    $gallery_data = get_post_meta( $post->ID, '_gallery_value_key', true ) ;

    
    echo '<div class="right"><button type="button" class="button btn-gallery-studio" aria-expanded="false" aria-label="Add New Gallery Image">Add New Image(s)</button></div>';
    echo '<input type="hidden" id="gallery_images_field" name="gallery_images_field" value="' . esc_attr( $gallery_data ) . '" size="25" />';
    
    echo '<ul id="gallery-studio-admin" class="gallery-studio-admin">';
    
    $gallery_data = explode(',', $gallery_data);

    foreach ($gallery_data as $key => $value) {
      if (empty($value)){
        return;
      }
      echo '<li data-id="'. $value .'"><a href="#" class="delete"><span class="dashicons dashicons-no"></span><span class="tip">Delete Image</span></a><img class="attachment-thumbnail size-thumbnail" src="'. wp_get_attachment_image_url($value, $size = 'thumbnail', $icon = false) .'" /></li>';
    }
    echo '</ul>'; 

  }


  function save_gallery_data( $post_id ) {

    
    if( ! isset( $_POST['gallery_meta_box_nonce'] ) ){
      return;
    }
    
    if( ! wp_verify_nonce( $_POST['gallery_meta_box_nonce'], array($this, 'save_gallery_data')) ) {
      return;
    }
    
    if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
      return;
    }
    
    if( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
    
    if( ! isset( $_POST['gallery_images_field'] ) ) {
      return;
    }
    
    $my_data =  sanitize_text_field( $_POST['gallery_images_field'] );

    update_post_meta( $post_id, '_gallery_value_key', $my_data );
    update_post_meta( $post_id, '_tour_sort_key', 0 );
    
  }


}