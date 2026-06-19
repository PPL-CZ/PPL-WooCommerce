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

<ppl-access-point-widget
        id="pplWidget"
        api-key="<?php echo esc_html(pplcz_map_args()['apikey']); ?>"
        config="<?php echo esc_html(wp_json_encode(pplcz_map_args()['config'])); ?>"
></ppl-access-point-widget>

<?php wp_footer(); ?>
</body>
</html>
