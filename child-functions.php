<?php
require_theme_path("/classes/activity.php");
require_theme_path("/classes/member.php");

$unity_measurement = new CustomSettings("unity-measurement", "");
$unity_measurement->add_fields(array(
	[
		"name"=>"measure-unity",
		"title"=>"Unity measurement",
		"type"=>"select",
		"datas"=>[ "meter" => _st("Meter"), "centimeter" => _st("Centimeter"), "feet" => _st("Feet"), "inch" => _st("Inch") ]
	],
	[
		"name"=>"mass-unity",
		"title"=>"Unity mass",
		"type"=>"select",
		"datas"=>[ "kilo" => _st("Kilogramme"), "pounds" => _st("Pounds") ]
	]
));

/**
 * CUSTOMIZER - SETUPS
 */
function stth_customizer($wp_customize){
    // new STHCustomizer($wp_customize, name_param, [ params ]);
}

/**
 * NPTS - SETUPS
 */
function npts_setup_theme(){
	global $scripts;
	$scripts = [];
	// MEMBERS
	$npt_members = new NewPostType('members', _st('Members'));
	$ply_register = [
		'menu_icon'	=> 'dashicons-groups',
		'supports'	=> [ 'title', 'thumbnail', 'editor' ],
		"gutenberg" => true,
		'has_archive' => true
	];
	$ply_taxonomy = [
		"label" => _st("Team"),
		"tags" => [ "label" => _st("Roles") ]
	];
	$countries = (function_exists("getCountries")) ? getCountries() : [];
	array_unshift($countries, "Sélectionnez un pays");
	$measurement = (get_infoth("measure-unity") === "meter" || get_infoth("measure-unity") === "centimeter") ? "cm" : "feet";
	$mass = (get_infoth("mass-unity") === "kilo") ? "kg" : "lbs";
	$ply_side = [
		[ "name" => "lastname_player", "label" => "Lastname" ],
		[ "name" => "firstname_player", "label" => "Firstname" ],
		[ "name" => "nickname_player", "label" => "Nickname" ],
		[ "name" => "birthday_player", "label" => "Birthday", "type" => "date" ],
		[ "name" => "country_player", "label" => "Origin country", "type" => "select", "options" => $countries ],
		[ "name" => "exp_player", "label" => "Experience", "description" => "(by year)", "type" => "number", "min" => 0 ],
		[ "name" => "physic_information", "label" => "Physic", "type" => "none", "class" => "bold" ],
		[ "name" => "tall_player", "label" => _st("Tall")." <em>($measurement)</em>", "type" => "number", "min" => 0 ],
		[ "name" => "weight_player", "label" => _st("Weight")." <em>($mass)</em>", "type" => "number", "min" => 0 ]
	];
	$npt_members->run($ply_register, $ply_taxonomy, $ply_side);
	// END MEMBERS

	// MATCH
	$npt_match = new NewPostType('matchs', 'Matchs');
	$mtch_register = [
		'menu_icon' => 'dashicons-schedule',
		'supports' => [ 'title', 'thumbnail' ],
		'has_archive' => true
	];
	$mtch_normal = [
		[ "name" => "against_team", "label" => "Opponent" ],
		[ "name" => "home_team", "label" => "Our team", "type" => "taxonomies", "post_type" => "members" ],
		[ "name" => "date_start", "label" => "Date of the match", "type" => "datetime-local" ],
		[ "name" => "is_home_match", "label" => "At home", "type" => "checkbox" ],
		[ "name" => "location", "label" => "Place", "placeholder" => "Place of the match", "description" => "Optional if at home" ],
		[ "name" => "prefix", "label" => "Header", "placeholder" => "Match for the title, Derby, etc.", "description" => "Optional" ]
	];
	$npt_match->run($mtch_register, [], [], $mtch_normal);
	// END MATCH
}

function get_npts(){ return ["members", "matchs"]; }
function get_activities($types, $limit = -1){
	$query = new WP_Query([
        "posts_per_page" => $limit,
        "post_type" => $types,
        'orderby' => [
            '_matchs_services_date_start_meta_box' => 'ASC',
            'title' => 'DESC'
        ],
        'meta_query' => [
            'relation' => 'OR',
               [
                  'key' => '_matchs_services_date_start_meta_box',
                  'value' => date("Y-m-d"),
                  'compare' => '>=',
               ]
        ]
    ]);
	return array_map(function($activity){ return new Activity($activity); }, $query->posts);
}
function get_activity($id, $types){ return array_values(array_filter(get_activities($types), function($activity) use($id){ return $activity->getId() === $id; }))[0]; }
function get_members($limit = -1, $hasTeam = true){
	$query = new WP_Query([
        "posts_per_page" => $limit,
        "post_type" => "members",
        'orderby' => [
            'title' => 'ASC'
        ]
    ]);
    $members = array_map(function($member){ return new Member($member); }, $query->posts);
	return ($hasTeam) ? array_values(array_filter($members, function($member){ return $member->hasTeam(); })) : $members;
}
function get_member($id, $hasTeam = false){ return array_values(array_filter(get_members(-1, $hasTeam), function($member) use($id){ return $member->getId() === intval($id); }))[0]; }
function get_members_by_team($team, $slug = false){ return array_values(array_filter(get_members(), function($member) use($team, $slug){ return $member->inTeam($team, $slug); })); }
function get_teams(){ return get_terms([ "taxonomy" => "members_tax" ]); }
function get_team($id){ return array_values(array_filter(get_teams(), function($team) use($id){ return (is_numeric($id)) ? $team->term_id === intval($id) : $team->slug === strval($id); }))[0]; }

/**
 * SHORTCODES - SETUPS
 */
function shortcodes_child(){
	// add_shortcode(name, function_name);
	add_shortcode('activities_posts', 'sport_activities_posts');
	add_shortcode('members_posts', 'sport_members_posts');
}

function sport_activities_posts($atts){
	$tp_atts = shortcode_atts(array(
		'type' => 'matchs',
		'limit' => 3,
		'bg' => null,
		'style' => 'grid',
		'excludes' => '',
		'team_id' => ''
	), $atts);
	ob_start();
	echo "<section class='activities-posts sport-posts pad-20 ".$tp_atts['style'].'-posts'."'>";
	get_template_part('template-parts/activities', 'posts', $tp_atts);
	echo "</section>";
	return ob_get_clean();
}

function sport_members_posts($atts){
	$tp_atts = shortcode_atts(array(
		'type' => 'members',
		'limit' => -1,
		'style' => 'slider',
		'member_id' => null,
		'team_id' => null,
		'is_bio' => true,
		'excludes' => ''
	), array_map(function($a){ return ($a === "null") ? null : $a; }, $atts));
	ob_start();
	echo "<section class='members-posts sport-posts ".$tp_atts['style'].'-posts'."'>";
	get_template_part('template-parts/members', 'posts', $tp_atts);
	echo "</section>";
	return ob_get_clean();
}

function sports_free_submenus(){
	add_submenu_page( 'themes.php', _st('Sports Premium'), '<span style="font-weight: bold; color: #F7941E;">'._st('Sports Premium').'</span>', 'manage_options', 'sport-premium', 'sports_premium', 1 );
}
add_action('admin_menu', 'sports_free_submenus');

function sports_premium(){
	?>
		<style>
			#prices-container h2{
				text-align: center;
				font-weight: bold;
				font-size: 50pt;
			}
			#prices-container .wp-list-table{
				margin-top: 50px;
				border: none;
			}
			#prices-container .wp-list-table th{
				font-weight: bold;
				border: none;
				text-transform: uppercase;
			}
			#prices-container .wp-list-table th,
			#prices-container .wp-list-table td{
				padding: 25px;
				text-align: center;
				font-size: 12pt;
			}
			#prices-container .wp-list-table td:first-child{ text-align: left; }
			#prices-container .wp-list-table td.good{ font-weight: bold; color: #F7941E; }
			#prices-container .wp-list-table td.bad{ font-weight: bold; opacity: .5; }
			#prices-container .wp-list-table td em{ font-size: .75em; }
			#prices-container .buy-button{
				display: block;
				text-transform: uppercase;
				font-weight: bold;
				transition-property: color, background-color;
				transition-duration: .25s;
			}
			#prices-container .buy-button:hover{ color: white; background-color: #d02c21; }
		</style>
		<div id="prices-container" class="wrap">
			<h2>Go to Pro!</h2>
			<table class="wp-list-table widefat fixed striped table-view-list pages">
				<thead>
					<tr>
						<th>Features</th>
						<th>Free</th>
						<th>Premium</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Essential features</td>
						<td class="good">&check;</td>
						<td class="good">&check;</td>
					</tr>
					<tr>
						<td>Members management</td>
						<td class="good">&check;</td>
						<td class="good">&check;</td>
					</tr>
					<tr>
						<td>Matchs management</td>
						<td class="good">&check;</td>
						<td class="good">&check;</td>
					</tr>
					<tr>
						<td>Events management</td>
						<td class="bad">&#10006;</td>
						<td class="good">&check;</td>
					</tr>
					<tr>
						<td>Partners management</td>
						<td class="bad">&#10006;</td>
						<td class="good">&check;</td>
					</tr>
					<tr>
						<td>Gutenberg editor compatibility</td>
						<td class="bad">&#10006;</td>
						<td class="good">&check;</td>
					</tr>
					<tr>
						<td>Multiple languages translation<br/><em>(French, Dutch, German, Spanish, Italian, Russian)</em></td>
						<td class="bad">&#10006;</td>
						<td class="good">&check;</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td><a href="https://shop.staces.be/shop/stth-sports-sports-theme-4" target="_blank" title="Buy the premium version" class="button buy-button">Buy now</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php
}