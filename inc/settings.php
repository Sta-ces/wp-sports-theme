<?php
/**
 * CUSTOM SETTINGS
 */
class CustomSettings
{
	private $name_setting;
	private $title;
	private $slug;
	private $menu_title;
	private $description;
	private $settings;
	
	function __construct($_name_setting, $_title, $_slug = "general", $_menu_title = "", $_description = "")
	{
		$this->name_setting = $_name_setting;
		$this->title = $_title;
		$this->slug = $_slug;
		$this->menu_title = empty($_menu_title) ? $_title : $_menu_title;
		$this->description = $_description;
		add_action( 'admin_menu', array($this, 'create_submenu') );
		add_action( 'admin_init', array($this, 'create_settings') );
	}

	function add_fields($_settings){
		$this->settings = $_settings;
		add_action( 'admin_init', array($this, 'create_add_field') );
	}

	function create_add_field(){
		for ($i = 0; $i < count($this->settings); $i++) {
			$field_setting = $this->settings[$i];

		    add_settings_field(
		        $field_setting['name'],
		        $field_setting['title'],
		        array($this, 'settings_add_field_callback'),
		        $this->slug,
		        $this->name_setting.'-section-id',
		        $field_setting
		    );

			register_setting( $this->slug, $field_setting['name'] );
		}
	}

	function create_submenu(){
		if(!$this->is_menu_exist($this->slug, true))
			add_submenu_page( 'options-general.php', _st($this->menu_title), _st($this->menu_title), 'administrator', $this->slug, function(){ $this->settings_callback($this->title, $this->description, $this->slug); } );
	}

	function create_settings(){
	    add_settings_section(
	    	$this->name_setting.'-section-id',
	    	$this->title,
	    	array($this, 'settings_section_description'),
	    	$this->slug
	    );
	}

	function settings_section_description(){ if(!empty($this->description)) echo wpautop( $this->description ); }

	function settings_add_field_callback($field_setting){
		$type = (isset($field_setting['type']) && $field_setting['type'] !== "") ? $field_setting['type']: 'text';
		$placeholder = str_ireplace("'", "&apos;", (isset($field_setting['placeholder']) && $field_setting['placeholder'] != "") ? $field_setting['placeholder'] : $field_setting['title']);

		switch($type){
			case "checkbox": ?>
				<input id="<?php echo $field_setting['name']; ?>" type="<?php echo $type; ?>" name="<?php echo $field_setting['name']; ?>" <?php echo !empty(get_option( $field_setting['name'] ))?"checked":""; ?>>
				<?php
				break;
			case "textarea": ?>
				<textarea name="<?php echo $field_setting['name']; ?>" id="<?php echo $field_setting['name']; ?>" cols="50" rows="10" placeholder="<?php echo $field_setting['placeholder']; ?>"><?php echo get_option( $field_setting['name'] ); ?></textarea>
				<?php
				break;
			case "WYSWYG":
			case "wyswyg":
				wp_editor( get_option( $field_setting['name'], '' ) , $field_setting['name'], array(
					'wpautop'       => true,
					'media_buttons' => false,
					'textarea_name' => $field_setting['name'],
					'textarea_rows' => 1
				) );
				break;
			case "media":
				new MediaUpload([
					'id' => get_option( $field_setting['name'], 0 ),
					'name' => $field_setting['name']
				]);
				break;
			case "select": ?>
				<select name="<?php echo $field_setting['name']; ?>" id="<?php echo $field_setting['name']; ?>">
					<option value="">---</option>
					<?php
						foreach ($field_setting['datas'] as $key => $value) {
							?><option value='<?php echo $key; ?>' <?php if( get_option( $field_setting['name'], '' ) === $key ) echo "selected"; ?>><?php echo $value; ?></option><?php
						}
					?>
				</select>
				<?php
				break;
			default: ?>
				<input id="<?php echo $field_setting['name']; ?>" type="<?php echo $type; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo get_option( $field_setting['name'], '' ); ?>" name="<?php echo $field_setting['name']; ?>" style='min-width: 350px;' class="regular-text">
				<?php
				break;
		}
	}

	function settings_callback($title, $description, $slug){
		?>
		<div class="wrap">
			<?php if(!empty($title)): ?>
				<h2><?php _e($title); ?></h2>
			<?php endif; ?>
			<?php if(!empty($description)): ?>
				<p><?php _e($description); ?></p>
			<?php endif; ?>
			<form method='POST' action='options.php'>
				<?php 
					settings_fields( $slug );
					do_settings_sections( $slug );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}
	
	function is_menu_exist( $handle, $sub = false ){
		if( !is_admin() || (defined('DOING_AJAX') && DOING_AJAX) ) return false;
		global $menu, $submenu;
		$handle = "options-".$handle.".php";
		$check_menu = $sub ? $submenu : $menu;
		if( empty( $check_menu ) ) return false;
		foreach( $check_menu as $k => $item ){
			if( $sub ){
				foreach( $item as $sm ){ if( $handle == $sm[2] ) return true; }
			} else { if( $handle == $item[2] ) return true; }
		}
		return false;
	}
}