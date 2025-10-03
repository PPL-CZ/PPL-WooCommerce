<?php
defined("WPINC") or die();

?>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="pplcz-parcelshop-info">PPL mapa</div>
<?php wp_body_open(); ?>
<div id="ppl-parcelshop-map" <?php
foreach (pplcz_map_args() as $key => $value) {
    echo " " . esc_html($key) . "=\"" . esc_html($value) ."\"";
}
?> >
</div>
<?php wp_footer(); ?>
</body>
</html>
