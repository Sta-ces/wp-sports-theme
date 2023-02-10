<?php
/**
 * The template for displaying the header
 *
 * @package Staces
 */

	$description = get_infoth('description', '');
	if(isset($args['meta-description']) && $args['meta-description'] !== "")
		$description = $args['meta-description'];
	if($description === '') $description = get_infoth('description');
	$description = wp_strip_all_tags($description, true);

	if(isset($args['title'])){ $start_title = $args['title']; }
	else{ $start_title = (wp_title("", false) === "") ? "" : wp_title("", false) . " - "; }

	$descriptionDisplay = get_infoth('description');
	if(isset($args['display-description']) && $args['display-description'] === false)
		$descriptionDisplay = "";

	$title = preg_replace("/^\|/","",trim($start_title." ".$descriptionDisplay." | ".get_infoth('sitename')));
	$class_body = (ISLOG) ? 'is_connected' : 'is_not_connected';
	$class_body .= (get_infoth('is-dark-mode')) ? ' dark-mode' : '';
	$idGoogleTagManager = get_infoth('tagmanager');

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<?php if(get_infoth("is-close") && !ISLOG) get_infoth("page-close"); ?>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<link rel="canonical"					href="<?php echo get_home_url(); ?>">
	<meta name="viewport"					content="width=device-width, initial-scale=1">
	<link rel="profile"						href="https://gmpg.org/xfn/11">
	<meta name="description"				content="<?php echo $description; ?>">
	<meta name="author"						content="<?php infoth('author'); ?>">
	<meta name="keywords"					content="<?php infoth('keywords'); ?>">
	<meta property="fb:app_id"				content="2908287049300335" />
	<meta property="og:url"					content="<?php echo get_site_url(); ?>" />
	<meta property="og:type"				content="website" />
	<meta property="og:locale"				content="fr_FR" />
	<meta property="og:title"				content="<?php infoth('sitename'); ?>" />
	<meta property="og:description"			content="<?php echo $description; ?>" />
	<meta property="og:image"				content="<?php echo get_background_image(); ?>" />
	<meta property="og:image:secure_url"	content="<?php echo get_background_image(); ?>" />
	<meta property="og:image:width"			content="1200" />
	<meta property="og:image:height"		content="800" />
	<meta property="twitter:site"			content="<?php infoth('twitter'); ?>" />
	<meta property="twitter:creator"		content="<?php infoth('twitter'); ?>" />
	<meta property="twitter:type"			content="website" />
	<meta property="twitter:title"			content="<?php infoth('sitename'); ?>" />
	<meta property="twitter:description"	content="<?php echo $description; ?>" />
	<meta property="twitter:image"			content="<?php echo get_background_image(); ?>" />
	<meta property="twitter:card"			content="summary_large_image" />
	<title><?php echo $title; ?></title>
	<?php wp_head(); ?>
	<!-- Google Tag Manager -->
	<?php if($idGoogleTagManager): ?>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer',<?php echo $idGoogleTagManager; ?>);</script>
	<?php endif; ?>
	<!-- End Google Tag Manager -->
</head>

<body <?php body_class($class_body); ?>>
	<!-- Google Tag Manager (noscript) -->
	<?php if($idGoogleTagManager): ?>
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $idGoogleTagManager; ?>"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<?php endif; ?>
	<!-- End Google Tag Manager (noscript) -->