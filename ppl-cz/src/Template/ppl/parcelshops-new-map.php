<?php
defined("WPINC") or die();

$pplcz_map_args = pplcz_map_args();
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

<ppl-access-point-widget
        id="pplWidget"
        api-key="<?php echo esc_attr($pplcz_map_args['apikey']); ?>"
        config="<?php echo esc_attr(wp_json_encode($pplcz_map_args['config'])); ?>"
></ppl-access-point-widget>

<?php wp_footer(); ?>
</body>
</html>
