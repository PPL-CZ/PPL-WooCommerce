<?php
defined("WPINC") or die();

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body class="pplcz-map-body">
<div id="pplcz-parcelshop-info">PPL mapa</div>
<div id="ppl-parcelshop-map" <?php
foreach (pplcz_map_args() as $key => $value) {
    echo " " . esc_attr($key) . "=\"" . esc_attr($value) ."\"";
}
?> >
</div>
<?php wp_footer(); ?>
</body>
</html>
