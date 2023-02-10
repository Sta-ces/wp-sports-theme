<?php
class Setups
{
	protected $register = array();
	protected $taxonomy = array();
	protected $side = array();
	protected $normal = array();
	protected $npt;
	protected $custom_meta_boxes = array();
	protected $submenu_args = array();
	
	function __construct($rg, $tx, $side, $normal)
	{
		$this->register = $rg;
		$this->taxonomy = $tx;
		$this->side = $side;
		$this->normal = $normal;
		$this->push_custom_meta_boxes($side);
		$this->push_custom_meta_boxes($normal);
		if(array_key_exists("submenus", $tx)) $this->submenu_args = $tx['submenus'];
		foreach ($normal as $k => $n)
			if(array_key_exists("options", $n)) $this->push_custom_meta_boxes($n["options"]);
		foreach ($side as $k => $s)
			if(array_key_exists("options", $s)) $this->push_custom_meta_boxes($s["options"]);
	}

	public function sets($npt){
		$this->npt = $npt;
		
		add_action('init', array($this, 'npt_register'));
		add_action('init', array($this, 'npt_taxonomy'));
		add_filter('views_edit-'.$this->npt->get_addon_name(), array($this, 'npt_description'));
		add_action('add_meta_boxes_'.$this->npt->get_addon_name(), array($this, 'npt_add_meta_boxes'));
		add_action('save_post_'.$this->npt->get_addon_name(), array($this, 'npt_save_meta_boxes_data'));
		add_filter('manage_'.$this->npt->get_addon_name().'_posts_columns', array($this, 'npt_filter_posts_columns'));
		add_action('manage_'.$this->npt->get_addon_name().'_posts_custom_column', array($this, 'npt_columns_content'), 10, 2);
		add_action('quick_edit_custom_box', array($this, 'npt_quick_edit_custom_box'), 10, 2);
		add_action('admin_menu', array($this, 'npt_submenu_page'));
	}

	public function npt_register(){
		register_post_type($this->npt->get_addon_name(), $this->register);
		flush_rewrite_rules();
	}

	public function npt_taxonomy(){
		$tax_name = $this->npt->get_addon_name().'_tax';
		$tag_name = $this->npt->get_addon_name().'_tag';
		register_taxonomy($tax_name, $this->npt->get_addon_name(), $this->taxonomy);
		if($this->taxonomy["show_tags"]) register_taxonomy($tag_name, $this->npt->get_addon_name(), $this->taxonomy["tags"]);
	}

	public function npt_description($views){
		if(isset($this->register['description']) && $this->register['description'] != "")
			echo "<h4>"._st($this->register['description'])."</h4>";

		return $views;
	}

	public function npt_save_meta_boxes_data($post_id){
		// verify meta box nonce
		if ( !isset( $_POST[$this->npt->get_addon_name().'_meta_box_nonce'] ) || !wp_verify_nonce( $_POST[$this->npt->get_addon_name().'_meta_box_nonce'], basename( __FILE__ ) ) ) return;
		// return if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		$this->npt_saver_data($post_id, $this->custom_meta_boxes);
	}

	public function npt_saver_data($post_id, $args = []){
		if(count($args) <= 0) $args = $this->custom_meta_boxes;
		if(count($args) <= 0) return;
		// SAVE CUSTOM META BOX
		foreach ($args as $key => $value) {
			$add_name = $this->npt->get_addon_name().'_services_'.$value.'_meta_box';
			if ( isset( $_POST[$add_name] ) )
				update_post_meta( $post_id, '_'.$add_name, $_POST[$add_name] );
			else delete_post_meta( $post_id, '_'.$add_name );
		}
	}

	public function npt_add_meta_boxes($post){
		$addon_name = $this->npt->get_addon_name();

		// ADD META BOX
		if(count($this->side) > 0){
			add_meta_box(
				$addon_name."_side_info_custom_meta_box",
				_st("Informations"),
				array( $this, 'npt_info_build_meta_box' ),
				$addon_name,
				'side',
				'high'
			);
		}
		if(isset($this->register['photo']) && $this->register['photo']){
			add_meta_box(
				"postimagediv",
				_st("Photo"),
				'post_thumbnail_meta_box',
				$addon_name,
				'side',
				'high'
			);
		}
		if(count($this->normal) > 0){
			add_meta_box(
				$addon_name."_normal_info_custom_meta_box",
				_st("Additional information"),
				array( $this, 'npt_info_normal_build_meta_box' ),
				$addon_name,
				'normal',
				'high'
			);
		}
	}

	public function npt_submenu_page(){
		if(count($this->submenu_args) > 0){
			if(is_array($this->submenu_args[0]))
				foreach ($this->submenu_args as $key => $submenu){ $this->add_submenu_args($submenu); }
			else $this->add_submenu_args($this->submenu_args);
		}
	}

	public function npt_info_build_meta_box($post){ $this->npt_build_meta_box($post, $this->side); }
	public function npt_info_normal_build_meta_box($post){ $this->npt_build_meta_box($post, $this->normal); }

	public function npt_columns_content($column_name, $post_id){}
	public function npt_filter_posts_columns($columns){ return $columns; }
	public function npt_quick_edit_custom_box($column_name, $post_type){ wp_nonce_field('post_metadata', 'post_metadata_field'); }

	public function npt_build_meta_box($post, $args = []){
		if(count($args) <= 0) return;
		$addon_name = $this->npt->get_addon_name();

		// make sure the form request comes from WordPress
		wp_nonce_field( basename( __FILE__ ), $addon_name.'_meta_box_nonce' );

		// CREATE THE META BOX
		$html_meta_box = "";
		foreach ($args as $key => $addon) {
			if(!isset($addon['name'])) continue;
			$type = isset($addon['type'])?$addon['type']:"text";
			$label = isset($addon['label'])?$addon['label']:"";
			$placeholder = str_ireplace("'", "&apos;", (isset($addon['placeholder'])?$addon['placeholder']:$label));
			$options = isset($addon['options'])?$addon['options']:[];
			if($type === "taxonomies"){
				$opt = [];
				$tax_args = array_merge([
					'order_by' => 'name',
					'hide_empty' => false
				], (isset($addon['taxonomy_args'])?$addon['taxonomy_args']:[]));
				$tax_id = get_object_taxonomies($addon['post_type'])[0];
				$tax = get_terms($tax_id, $tax_args);
				foreach ($tax as $key => $value) { $opt[$value->slug] = $value->name; }
				$options = $opt;
			}
			$name = $addon_name."_services_".$addon['name']."_meta_box";
			$meta_value = get_post_meta($post->ID, '_'.$name, true);
			$classes = (isset($addon['class'])) ? $addon['class'] : "";
			$html_meta_box .= "<p class='custom-meta-box ".$classes." type-".$type."'>";
			if($label !== "")
				$html_meta_box .= "<label class='minWidth' for='".$name."'>"._st($label)."</label>";
			switch($type){
				case "textarea":
					$html_meta_box .= "<textarea id='{$name}' name='{$name}' placeholder='"._st($placeholder)."'>{$meta_value}</textarea>";
					break;
				case "checkbox":
					$html_meta_box .= "<input type='checkbox' id='{$name}' name='{$name}' ".checked(boolval($meta_value), true, false).">";
					break;
				case "taxonomies":
				case "select":
					if(count($options) > 0){
						$html_meta_box .= "<select id='{$name}' name='{$name}'>";
						foreach ($options as $opt_key => $opt_value){
							$html_meta_box .= "<option value='{$opt_key}' ".selected($meta_value, $opt_key, false).">{$opt_value}</option>";
						}
						$html_meta_box .= "</select>";
					}
					break;
				case "WYSWYG":
					wp_editor( $meta_value , $name, array(
						'wpautop'       => true,
						'media_buttons' => false,
						'textarea_name' => $name,
						'editor_class'  => $name."_class",
						'textarea_rows' => 10
					) );
					break;
				case "none": break;
				default:
					$html_meta_box .= "<input type='{$type}' id='{$name}' name='{$name}' value='{$meta_value}' placeholder='"._st($placeholder)."'>";
					break;
			}
			if(isset($addon['description'])) $html_meta_box .= "<span style='display:block;'>"._st($addon['description'])."</span>";
			$html_meta_box .= "</p>";
		}
		echo $html_meta_box;
	}

	function push_custom_meta_boxes($args = []){
		if(count($args) <= 0) return;
		// CUSTOM META BOXES
		$this->custom_meta_boxes = array_merge($this->custom_meta_boxes, array_map(function($a){
			if(is_array($a) && array_key_exists("name", $a)) return $a['name'];
			else if(gettype($a) === "string") return $a;
			else return false;
		}, $args));
	}

	function add_submenu_args($args){
		$sbm = array_merge([
			"title" => "Options",
			"menu_title" => "Options item",
			"capability" => "manage_options",
			"callback" => ""
		], $args);
		add_submenu_page(
			"edit.php?post_type={$this->register['slug']}",
			$sbm['title'],
			$sbm['menu_title'],
			$sbm['capability'],
			str_ireplace(" ", "_", strtolower($sbm['menu_title'])),
			$sbm['callback']
		);
	}
}