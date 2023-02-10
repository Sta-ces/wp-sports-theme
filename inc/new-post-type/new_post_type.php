<?php
require_theme_path('/inc/new-post-type/includes/class-new_post_type.php');
require_theme_path('/inc/new-post-type/includes/class-setups.php');
require_theme_path('/inc/new-post-type/npts-db.php');
add_action('after_setup_theme', 'npts_setup_theme');