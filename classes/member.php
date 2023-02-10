<?php
require_theme_path("/classes/sport.php");

class Member extends Sport implements \JsonSerializable{
    protected string $lastname;
    protected string $firstname;
    protected string $nickname;
    protected string $birthday;
    protected string $country;
    protected array $teams;
    protected string $role;
    protected int $experience;
    protected int $height;
    protected int $weight;

    function __construct($post){
        parent::__construct($post);
        $objectTerms = get_object_taxonomies($post->post_type);
        $teams = get_the_terms($post, $objectTerms[0]);
        $roles = get_the_terms($post, $objectTerms[1]);
        $this->lastname = $this->_getValue(get_meta($post->ID, $post->post_type, 'lastname_player'));
        $this->firstname = $this->_getValue(get_meta($post->ID, $post->post_type, 'firstname_player'));
        $this->nickname = $this->_getValue(get_meta($post->ID, $post->post_type, 'nickname_player'));
        $this->birthday = $this->_getValue(get_meta($post->ID, $post->post_type, 'birthday_player'));
        $this->country = $this->_getValue(get_meta($post->ID, $post->post_type, 'country_player'));
        $this->teams = ($teams) ? $teams : [];
        $this->role = (!empty($roles)) ? implode(", ", array_map(function($r){ return $r->name; }, $roles)) : "";
        $this->experience = floatval($this->_getValue(get_meta($post->ID, $post->post_type, 'exp_player')));
        $this->height = floatval($this->_getValue(get_meta($post->ID, $post->post_type, 'tall_player')));
        $this->weight = floatval($this->_getValue(get_meta($post->ID, $post->post_type, 'weight_player')));
    }

    public function getLastname(){ return $this->lastname; }
    public function getFirstname(){ return $this->firstname; }
    public function getNickname(){ return $this->nickname; }
    public function getFullname($nickname = true, $reverse = false){
        $n = ($nickname && !empty($this->nickname)) ? " &quot;{$this->nickname}&quot; " : " ";
        return ($reverse) ? "{$this->lastname}{$n}{$this->firstname}" : "{$this->firstname}{$n}{$this->lastname}";
    }
    public function getBirthday(){ return createDate($this->birthday, get_infoth("date-format"), false); }
    public function getAge(){ return (date_diff(date_create($this->birthday), date_create(date("Y-m-d"))))->format("%y")." "._st("years"); }
    public function getCountry(){ return getCountry($this->country); }
    public function getTeams(){ return $this->teams; }
    public function hasTeam(){ return count($this->teams) > 0; }
    public function inTeam($team, $slug = false){
        return in_array($team, array_map(function($t) use($team, $slug){
            if(is_numeric($team)) return $t->term_id;
            elseif($slug) return $t->slug;
            else return $t->name;
        }, $this->teams));
    }
    public function getRole(){ return $this->role; }
    public function getExperience(){ return $this->experience; }
    public function getHeight(){
        $rslt; $h = number_format($this->height, 2, '.', '');
        switch(get_infoth('measure-unity')){
            case "centimeter": $rslt = ($h)." cm"; break;
            case "inch": $rslt = ($h * 12)." inch"; break;
            case "meter": $rslt = str_replace(".", "m", number_format($h / 100, 2, '.', '')); break;
            case "foot": default: case "feet": $rslt = ($h)." ft"; break;
        }
        return $rslt;
    }
    public function getWeight(){ return number_format($this->weight, 2, '.', '')." ".((get_infoth("mass-unity") === "kilo") ? "kg" : "lbs"); }
    public function getPhysic(){
        $json = new stdClass();
        $json->height = $this->getHeight();
        $json->weight = $this->getWeight();
        return $json;
    }
    
    public function jsonSerialize(){ return get_object_vars($this); }
}