<?php
// phpcs:ignoreFile WordPress.Security.EscapeOutput.OutputNotEscaped

defined("WPINC") or die();
?>
    <img src='<?php echo esc_url($img) ?>' style='height: 1em;'>
    <?php echo $pplcz_label_safe; ?> <?php if ($free_shipping): ?><strong><?php echo __("Zdarma", "ppl-cz") ?></strong><?php endif; ?>


