<?php
require_theme_path("/classes/sport.php");

class Activity extends Sport{
    protected string $opponent;
    protected object $team;
    protected string $date_start;
    protected string $date_end;
    protected bool $is_home;
    protected string $location;
    protected string $prefix;
    protected bool $isMatch;
    protected bool $isEvent;

    function __construct($post){
        parent::__construct($post);
        $this->isMatch = $post->post_type === "matchs";
        $this->isEvent = $post->post_type === "events";
        $this->date_start = $this->_getValue(get_meta($post->ID, $post->post_type, 'date_start'));
        $this->date_end = $this->_getValue(get_meta($post->ID, $post->post_type, 'date_end'));
        $this->location = $this->_getValue(get_meta($post->ID, $post->post_type, 'location'));
        $this->prefix = $this->_getValue(get_meta($post->ID, $post->post_type, 'prefix'));
        $this->opponent = $this->_getValue(get_meta($post->ID, $post->post_type, 'against_team'));
        $team = $this->_getValue(get_meta($post->ID, $post->post_type, 'home_team'));
        $this->team = (!empty($team)) ? get_team($team) : new stdClass();
        $this->is_home = $this->isMatch ? boolval($this->_getValue(get_meta($post->ID, $post->post_type, 'is_home_match'))) : false;
    }

    public function getOpponent(){ return $this->opponent; }
    public function getTeam(){ return ($this->isMatch()) ? $this->team : null; }
    public function getTeamName(){ return ($this->isMatch()) ? $this->team->name : ""; }
    public function getDateStart(){ return $this->date_start; }
    public function getDateEnd(){ return $this->date_end; }
    public function getFullDate($address = true){
        $ds = $this->getDateStart();
        $de = $this->getDateEnd();
        $full_date = ""; $full_place = "";
        if(!empty($ds)){
            $ds_format = createDate($ds, get_infoth("full-date-format"), false);
            $full_date = "<strong>$ds_format</strong>";
        }
        if(!empty($de)){
            $de_format = createDate($de, get_infoth("full-date-format"), false);
            $full_date = (!empty($ds)) ? sprintf(_st("from %s to %s"), $full_date, "<strong>$de_format</strong>") : "<strong>$de_format</strong>";
        }
        if($address){
            $place = ($this->isHome()) ? get_infoth("address") : $this->getLocation();
            $full_place = (!empty($place)) ? " - ".$place : "";
        }
        return $full_date.$full_place;
    }
    public function isHome(){ return $this->is_home; }
    public function getLocation(){ return $this->location; }
    public function getPrefix(){ return $this->prefix; }
    public function isMatch(){ return $this->isMatch; }
    public function isEvent(){ return $this->isEvent; }
}