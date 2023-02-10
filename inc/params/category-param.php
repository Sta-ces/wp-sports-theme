<?php
if ( ! class_exists( 'CT_TAX_META' ) ) {

  class CT_TAX_META {

    public function __construct() { }
  
  /*
    * Initialize the class and start calling our hooks and filters
    * @since 1.0.0
  */
    public function init() {
      add_action( 'category_add_form_fields', array ( $this, 'add_category_param' ), 10, 2 );
      add_action( 'created_category', array ( $this, 'save_category_param' ), 10, 2 );
      add_action( 'category_edit_form_fields', array ( $this, 'update_category_param' ), 10, 2 );
      add_action( 'edited_category', array ( $this, 'updated_category_param' ), 10, 2 );
      add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
      add_action( 'admin_footer', array ( $this, 'add_script' ) );
    }

    public function load_media() { wp_enqueue_media(); }
  
  /*
    * Add a form field in the new category page
    * @since 1.0.0
  */
    public function add_category_param ( $taxonomy ) { ?>
    <div class="form-field term-icon-group">
      <label for="category-image-id"><?php _ste('Image'); ?></label>
      <input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
      <div id="category-image-wrapper"></div>
      <p>
        <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _ste( 'Add Image' ); ?>" />
        <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _ste( 'Remove Image' ); ?>" />
      </p>
    </div>
      
    <div class="form-field term-color-wrap">
      <label for="cat_color"><?php _ste('Color'); ?></label>
      <input type="color" size="40" value="#000000" id="cat_color" name="cat_color">
      <p><?php _ste('Please select a color'); ?></p>
    </div>
    <?php
    }
  
  /*
    * Save the form field
    * @since 1.0.0
  */
  public function save_category_param ( $term_id, $tt_id ) {
    if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
      $image = $_POST['category-image-id'];
      add_term_meta( $term_id, 'category-image-id', $image, true );
    }

    if(isset($_POST['cat_color']) && '' !== $_POST['cat_color']){
      add_term_meta( $term_id, 'cat_color', $_POST['cat_color'], true );
    }
  }
  
  /*
    * Edit the form field
    * @since 1.0.0
  */
  public function update_category_param ( $term, $taxonomy ) { ?>
    <tr class="form-field term-group-wrap">
      <th scope="row">
        <label for="category-image-id"><?php _ste( 'Image' ); ?></label>
      </th>
      <td>
        <?php $image_id = get_term_meta ( $term -> term_id, 'category-image-id', true ); ?>
        <input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo $image_id; ?>">
        <div id="category-image-wrapper">
          <?php if ( $image_id ) { ?>
            <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
          <?php } ?>
        </div>
        <p>
          <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _ste( 'Add Image' ); ?>" />
          <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _ste( 'Remove Image' ); ?>" />
        </p>
      </td>
    </tr>
    <tr class="form-field term-color-wrap">
      <?php $cat_color = get_term_meta ( $term -> term_id, 'cat_color', true ); ?>
      <th scope="row"><label for="cat_color"><?php _ste('Color'); ?></label></th>
      <td>
        <input type="color" size="40" value="<?php echo $cat_color; ?>" id="cat_color" name="cat_color">
        <p><?php _ste('Please select a color'); ?></p>
      </td>
    </tr>
  <?php
  }

    /*
    * Update the form field value
    * @since 1.0.0
    */
  public function updated_category_param ( $term_id, $tt_id ) {
    if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
      $image = $_POST['category-image-id'];
      update_term_meta ( $term_id, 'category-image-id', $image );
    } else {
      update_term_meta ( $term_id, 'category-image-id', '' );
    }

    if(isset($_POST['cat_color']) && '' !== $_POST['cat_color']){
      update_term_meta($term_id, 'cat_color', sanitize_text_field($_POST['cat_color']));
    }
    else update_term_meta($term_id, 'cat_color', '#000000');
  }

    /*
    * Add script
    * @since 1.0.0
    */
  public function add_script() { ?>
    <script>
      jQuery(document).ready( function($) {
        function ct_media_upload(button_class) {
          var _custom_media = true,
          _orig_send_attachment = wp.media.editor.send.attachment;
          $('body').on('click', button_class, function(e) {
            var button_id = '#'+$(this).attr('id');
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(button_id);
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment){
              if ( _custom_media ) {
                $('#category-image-id').val(attachment.id);
                $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                $('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
              } else {
                return _orig_send_attachment.apply( button_id, [props, attachment] );
              }
              }
          wp.media.editor.open(button);
          return false;
        });
      }
      ct_media_upload('.ct_tax_media_button.button'); 
      $('body').on('click','.ct_tax_media_remove',function(){
        $('#category-image-id').val('');
        $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
      });
      // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
      $(document).ajaxComplete(function(event, xhr, settings) {
        var queryStringArr = settings.data.split('&');
        if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
          var xml = xhr.responseXML;
          $response = $(xml).find('term_id').text();
          if($response!=""){
            // Clear the thumb image
            $('#category-image-wrapper').html('');
          }
        }
      });
    });
  </script>
  <?php }

  }
 
  $CT_TAX_META = new CT_TAX_META();
  $CT_TAX_META -> init();
 
}