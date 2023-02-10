<?php
class Sport{
    protected int $id;
    protected string $type;
    protected string $url;
    protected string $img_url;
    protected string $title;
    
    function __construct($post){
        $this->id = $post->ID;
        $this->type = $post->post_type;
        $this->url = get_permalink($post);
        $this->img_url = get_the_post_thumbnail_url($this->id);
        $this->title = $post->post_title;
    }
    
    public function getID(){ return $this->id; }
    public function getType(){ return $this->type; }
    public function getUrl(){ return $this->url; }
    public function getImgUrl(){ return $this->img_url; }
    public function getTitle(){ return $this->title; }

    protected function _getValue($value){ return ($value !== null) ? $value : ""; }
}