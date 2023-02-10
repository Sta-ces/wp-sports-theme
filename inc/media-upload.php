<?php
class MediaUpload{
    private $image_id = 0;
    private $image_name = '';
    private $is_multiple = false;

    function __construct($args){
        $this->image_id = $args['id'];
        $this->image_name = $args['name'];
        if(isset($args['multiple'])) $is_multiple = $args['multiple'];
        $this->render();
    }
    function getID(){ return $this->image_id; }
    function setID($id){ $this->image_id; }
    function render(){
        $image = wp_get_attachment_url( $this->image_id );
        if( preg_match("/(\.pdf)$/", $image) ) $image = includes_url('/images/media/document.png');
        $content = ($image) ? "<img src='".$image."' width='100' height='auto'>" : 'Upload image';
        $style_remove = ($image) ? 'inherit' : 'none';
        $hidden = ($image) ? $this->image_id : '';
        ?>
            <a href="#" class="staces-upl"><?php echo $content; ?></a>
            <a href="#" class="staces-rmv" style="display:<?php echo $style_remove; ?>">Remove image</a>
            <input type="hidden" name="<?php echo $this->image_name; ?>" id="<?php echo $this->image_name; ?>" value="<?php echo $hidden; ?>">
        <?php
    }
}