<?php
/**
 * NewPostType - Add-ons
 */
class NewPostType
{
	protected $addon_name;
	protected $name;
	protected $all_list;
	protected $version;

	private $register;
	private $taxonomy;
	private $side;
	private $normal;
	
	function __construct($addon_name, $name, $all_list = "")
	{
		if(empty($all_list)) $all_list = ucfirst(_st($name));

		$this->addon_name = $addon_name;
		$this->name = _st($name);
		$this->all_list = _st($all_list);
		$this->version = '2.6';
	}

	private function setup(){
		$setups = new Setups($this->register, $this->taxonomy, $this->side, $this->normal);
		$setups->sets($this);
		// STYLES
		$baseuri = get_path_uri("/inc/new-post-type/npt-styles.css");
		wp_enqueue_style( 'npt-styles', $baseuri, array(), $this->get_version() );
	}

	/**
	 * @param Register $rg
	 * @param Taxonomy $tx
	 * @param Side $side
	 * @param Normal $normal
	 */
	public function run($rg = array(), $tx = array(), $side = array(), $normal = array()){
		$is_gutenberg = isset($rg['gutenberg']) ? $rg['gutenberg'] : false;
		if(isset($tx["tags"])) $tx["tags"]["show_in_rest"] = $is_gutenberg;
		
		$rg_default = array(
			'public'				=> true,
			'slug'					=> $this->get_addon_name(),
			'label'					=> $this->get_name(),
			'labels'				=> array( 'all_items' => $this->get_all_list() ),
			'menu_position'			=> 5,
			'hierarchical'			=> true,
			'rewrite'				=> array( 'slug' => str_replace("_", "-", $this->get_addon_name()) ),
			'menu_icon'				=> 'dashicons-admin-post',
			'supports'				=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
			'photo'					=> false,
			'show_in_rest'			=> $is_gutenberg
		);
		$tx_default = array(
			"label" 		=> _st("Categories"),
			"hierarchical" 	=> true,
			"submenus"		=> array(),
			"tags"			=> array_merge( [ "hierarchical" => false ], (isset($tx["tags"]) ? $tx["tags"] : []) ),
			"show_tags"		=> isset($tx["tags"]),
			"show_admin_column" => true,
			"show_ui"		=> true,
			"show_in_rest" 	=> $is_gutenberg
		);

		$rg = array_merge($rg_default, $rg);
		$tx = array_merge($tx_default, $tx);

		$this->register = $rg;
		$this->taxonomy = $tx;
		$this->side = $side;
		$this->normal = $normal;
		$this->setup();
	}

	public function get_addon_name(){ return $this->addon_name; }
	public function get_name(){ return $this->name; }
	public function get_all_list(){ return $this->all_list; }
	public function get_version(){ return $this->version; }
	public function get_uri(){ return $this->uri; }
}