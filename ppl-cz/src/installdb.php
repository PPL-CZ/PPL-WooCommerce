<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.NoCaching

function pplcz_installdb()
{
    global $wpdb;

    $suppress = $wpdb->suppress_errors(true);
    @$wpdb->query("select * from {$wpdb->prefix}woocommerce_ppl_address limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}woocommerce_ppl_address` RENAME TO `{$wpdb->prefix}pplshipping_address`");

    @$wpdb->query("select * from {$wpdb->prefix}pplshipping_address limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}pplshipping_address` RENAME TO `{$wpdb->prefix}pplcz_address`");

    $wpdb->suppress_errors($suppress);

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $charset = $wpdb->get_charset_collate();
    $charset_collate = $wpdb->get_charset_collate();

    $table = $wpdb->prefix . 'pplcz_log';
    $sql = "CREATE TABLE `$table` (
  `ppl_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime,
  `message` text NOT NULL,
  `errorhash` varchar(128) NOT NULL,
  PRIMARY KEY (`ppl_log_id`),
  UNIQUE KEY `errorhas` (`errorhash`)
) $charset_collate";
    dbDelta($sql);


    $table = $wpdb->prefix . 'pplcz_address';
    $charset = $wpdb->get_charset_collate();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE `$table` (
  `ppl_address_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `address_name` varchar(40) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `mail` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `street` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `type` varchar(10) NOT NULL,
  `note` varchar(300) DEFAULT NULL,
  `draft` datetime DEFAULT NULL,
  `hidden` tinyint(4) NOT NULL,
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`ppl_address_id`)
) $charset_collate";
    dbDelta($sql);
    $wpdb->query("ALTER TABLE `{$wpdb->prefix}pplcz_address` CHANGE `name` `name` varchar(100) COLLATE 'utf8mb4_general_ci' NULL AFTER `address_name`");
    $wpdb->query("
ALTER TABLE `{$wpdb->prefix}pplcz_address`
CHANGE `street` `street` varchar(50) COLLATE 'utf8mb4_general_ci' NULL AFTER `phone`,
CHANGE `city` `city` varchar(50) COLLATE 'utf8mb4_general_ci' NULL AFTER `street`,
CHANGE `zip` `zip` varchar(10) COLLATE 'utf8mb4_general_ci' NULL AFTER `city`,
CHANGE `country` `country` varchar(2) COLLATE 'utf8mb4_general_ci' NULL AFTER `zip`;
        ");




    $suppress = $wpdb->suppress_errors(true);
    @$wpdb->query("select * from {$wpdb->prefix}woocommerce_ppl_cod_bank_account limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}woocommerce_ppl_cod_bank_account` RENAME TO `{$wpdb->prefix}pplshipping_cod_bank_account`");
    @$wpdb->query("select * from {$wpdb->prefix}pplshipping_cod_bank_account limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}pplshipping_cod_bank_account` RENAME TO `{$wpdb->prefix}pplcz_cod_bank_account`");


    $wpdb->suppress_errors($suppress);

    $table = $wpdb->prefix . 'pplcz_cod_bank_account';

    $sql = "CREATE TABLE `$table` (
    `ppl_cod_bank_account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(50) NOT NULL,
  `account` varchar(20) DEFAULT NULL,
  `account_prefix` varchar(20) DEFAULT NULL,
  `bank_code` varchar(7) DEFAULT NULL,
  `iban` varchar(20) DEFAULT NULL,
  `swift` varchar(20) DEFAULT NULL,
  `currency` varchar(3) NOT NULL,
  `draft` timestamp NULL DEFAULT current_timestamp(),
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`ppl_cod_bank_account_id`)
) $charset_collate";

    dbDelta($sql);



    $suppress = $wpdb->suppress_errors(true);
    @$wpdb->query("select * from {$wpdb->prefix}woocommerce_ppl_collections limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}woocommerce_ppl_collections` RENAME TO `{$wpdb->prefix}pplshipping_collections`");
    @$wpdb->query("select * from {$wpdb->prefix}pplshipping_collections limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}pplshipping_collections` RENAME TO `{$wpdb->prefix}pplcz_collections`");
    $wpdb->suppress_errors($suppress);

    $table = $wpdb->prefix . 'pplcz_collections';
    $sql = "CREATE TABLE `$table` (
    `ppl_collection_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `remote_collection_id` varchar(80) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `send_date` datetime NOT NULL,
  `send_to_api_date` datetime DEFAULT NULL,
  `reference_id` varchar(50) NOT NULL,
  `state` varchar(20) DEFAULT NULL,
  `shipment_count` int(11) NOT NULL,
  `estimated_shipment_count` int(11) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `note` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ppl_collection_id`),
  UNIQUE KEY `reference_id` (`reference_id`)
) $charset_collate";
    dbDelta($sql);


    $suppress = $wpdb->suppress_errors(true);
    @$wpdb->query("select * from {$wpdb->prefix}woocommerce_ppl_package limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}woocommerce_ppl_package` RENAME TO `{$wpdb->prefix}pplshipping_package`");

    @$wpdb->query("select * from {$wpdb->prefix}pplshipping_package limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}pplshipping_package` RENAME TO `{$wpdb->prefix}pplcz_package`");

    $wpdb->suppress_errors($suppress);

    $table = $wpdb->prefix . 'pplcz_package';
    $sql = "CREATE TABLE `$table` (
    `ppl_package_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ppl_shipment_id` bigint(20) DEFAULT NULL,
  `reference_id` varchar(40) DEFAULT NULL,
  `wc_order_id` bigint(20) DEFAULT NULL,
  `phase` varchar(20) NOT NULL,
  `status` int DEFAULT NULL,
  `status_label` text DEFAULT NULL,
  `phase_label` text DEFAULT NULL,
  `last_update_phase` datetime DEFAULT NULL,
  `last_test_phase` datetime DEFAULT NULL,
  `ignore_phase` tinyint(4) DEFAULT NULL,
  `shipment_number` varchar(40) DEFAULT NULL,
  `weight` decimal(10,0) DEFAULT NULL,
  `insurance` decimal(10,0) DEFAULT NULL,
  `insurance_currency` varchar(3) DEFAULT NULL,
  `import_error` text DEFAULT NULL,
  `import_error_code` text DEFAULT NULL,
  `label_id` text DEFAULT NULL,
  `draft` timestamp NULL DEFAULT current_timestamp(),
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`ppl_package_id`)
) $charset_collate";
    dbDelta($sql);



    $suppress = $wpdb->suppress_errors(true);
    @$wpdb->query("select * from {$wpdb->prefix}woocommerce_ppl_parcel limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}woocommerce_ppl_parcel` RENAME TO `{$wpdb->prefix}pplshipping_parcel`");
    @$wpdb->query("select * from {$wpdb->prefix}pplshipping_parcel limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}pplshipping_parcel` RENAME TO `{$wpdb->prefix}pplcz_parcel`");
    $wpdb->suppress_errors($suppress);

    $table = $wpdb->prefix . 'pplcz_parcel';
    $sql = "CREATE TABLE `$table` (
    `ppl_parcel_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name2` varchar(100) DEFAULT NULL,
  `street` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `code` varchar(20) NOT NULL,
  `country` varchar(2) NOT NULL,
  `draft` timestamp NULL DEFAULT current_timestamp(),
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `valid` tinyint(4) NOT NULL,
  `hidden` tinyint(4) NOT NULL,
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`ppl_parcel_id`),
  UNIQUE KEY `code_country` (`code`, `country`)
) $charset_collate";

    dbDelta($sql);

    $suppress = $wpdb->suppress_errors(true);
    @$wpdb->query("ALTER TABLE {$wpdb->prefix}pplcz_parcel DROP INDEX `code`");
    $wpdb->suppress_errors($suppress);

    $suppress = $wpdb->suppress_errors(true);
    @$wpdb->query("select * from {$wpdb->prefix}woocommerce_ppl_shipment limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}woocommerce_ppl_shipment` RENAME TO `{$wpdb->prefix}pplshipping_shipment`");
    @$wpdb->query("select * from {$wpdb->prefix}pplshipping_shipment limit 0");
    if (!$wpdb->error)
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}pplshipping_shipment` RENAME TO `{$wpdb->prefix}pplcz_shipment`");
    $wpdb->suppress_errors($suppress);

    $table = $wpdb->prefix . 'pplcz_shipment';


    $sql = "CREATE TABLE `$table` (
  `ppl_shipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `wc_order_id` int(11) DEFAULT NULL,
  `import_errors` text DEFAULT NULL,
  `reference_id` varchar(50) NOT NULL,
  `package_ids` text DEFAULT NULL,
  `import_state` varchar(20) NOT NULL,
  `service_code` varchar(20) DEFAULT NULL,
  `service_name` varchar(40) DEFAULT NULL,
  `recipient_address_id` int(11) DEFAULT NULL,
  `sender_address_id` int(11) DEFAULT NULL,
  `return_address_id` int(11) DEFAULT NULL,
  `cod_value` decimal(10,0) DEFAULT NULL,
  `cod_value_currency` varchar(4) DEFAULT NULL,
  `cod_variable_number` varchar(10) DEFAULT NULL,
  `cod_bank_account_id` int(11) DEFAULT NULL,
  `has_parcel` tinyint(4) NOT NULL,
  `parcel_id` int(11) DEFAULT NULL,
  `batch_id` varchar(50) DEFAULT NULL,
  `batch_local_id` int(11) DEFAULT NULL,
  `batch_label_group` datetime DEFAULT NULL,
  `note` varchar(300) DEFAULT NULL,
  `age` varchar(3) DEFAULT NULL,
  `lock` tinyint(4) NOT NULL,
  `draft` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ppl_shipment_id`)
) $charset_collate";

    dbDelta($sql);

    $suppress = $wpdb->suppress_errors();
    @$wpdb->query("ALTER TABLE `{$wpdb->prefix}pplcz_shipment` DROP INDEX `reference_id`");
    $wpdb->suppress_errors($suppress);


    $table = "{$wpdb->prefix}pplcz_batch";

    $sql = "CREATE TABLE `{$table}` (
        `ppl_batch_id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) COLLATE utf8mb3_bin DEFAULT NULL,
        `remote_batch_id` varchar(50) COLLATE utf8mb3_bin DEFAULT NULL,
        `lock` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
         PRIMARY KEY (`ppl_batch_id`)
    ) $charset_collate";

    dbDelta($sql);

    $wpdb->query("update {$wpdb->prefix}options set option_value='1.0.0' where option_name = 'pplcz_version'");
    $wpdb->query("delete from {$wpdb->prefix}options where option_name = 'pplcz_rules_version'");

    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'woocommerce_ppl_', 'pplshipping_') where option_name like 'woocommerce_ppl_%' ");
    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'pplshipping_', 'pplcz_') where option_name like 'pplshipping_%' ");

    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'woocommerce_woocommerce_ppl_', 'woocommerce_pplshipping_') where option_name like 'woocommerce_woocommerce_ppl_%' ");
    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'woocommerce_pplshipping_', 'woocommerce_pplcz_') where option_name like 'woocommerce_pplshipping_%' ");


    $wpdb->query("update {$wpdb->prefix}postmeta set meta_key = replace(meta_key, '_woocommerce_ppl_', '_pplshipping_') where meta_key like '_woocommerce_ppl_%' ");
    $wpdb->query("update {$wpdb->prefix}postmeta set meta_key = replace(meta_key, '_pplshipping_', '_pplcz_') where meta_key like '_pplshipping_%' ");

    $wpdb->query("update {$wpdb->prefix}woocommerce_order_itemmeta set meta_key = replace(meta_key, 'woocommerce_ppl_', 'pplshipping_') where  meta_key like 'woocommerce_ppl_%' ");
    $wpdb->query("update {$wpdb->prefix}woocommerce_order_itemmeta set meta_key = replace(meta_key, 'pplshipping_', 'pplcz_') where  meta_key like 'pplshipping_%' ");

    $wpdb->query("update {$wpdb->prefix}woocommerce_order_itemmeta set meta_value = replace(meta_value, 'woocommerce_ppl_', 'pplshipping_') where  meta_key = 'method_id' and meta_value like 'woocommerce_ppl_%'");
    $wpdb->query("update {$wpdb->prefix}woocommerce_order_itemmeta set meta_value = replace(meta_value, 'pplshipping_', 'pplcz_') where  meta_key = 'method_id' and meta_value like 'pplshipping_%'");


    $wpdb->query("update {$wpdb->prefix}woocommerce_shipping_zone_methods set method_id = replace(method_id, 'woocommerce_ppl_', 'pplshipping_') where  method_id like 'woocommerce_ppl_%'");
    $wpdb->query("update {$wpdb->prefix}woocommerce_shipping_zone_methods set method_id = replace(method_id, 'pplshipping_', 'pplcz_') where  method_id like 'pplshipping_%'");

    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'pplcz_pplcz_PRIV', 'woocommerce_pplcz_PRIV') where option_name like 'pplcz_pplcz_PRIV%' ");
    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'pplcz_pplcz_SMAR', 'woocommerce_pplcz_SMAR') where option_name like 'pplcz_pplcz_SMAR%' ");
    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'pplcz_pplcz_SMEU', 'woocommerce_pplcz_SMEU') where option_name like 'pplcz_pplcz_SMEU%' ");
    $wpdb->query("update {$wpdb->prefix}options set option_name = replace(option_name, 'pplcz_pplcz_CONN', 'woocommerce_pplcz_CONN') where option_name like 'pplcz_pplcz_CONN%' ");

    $wpdb->query("truncate {$wpdb->prefix}pplcz_log");



    add_option(pplcz_create_name("error_log"), 0, null, 'yes') || update_option(pplcz_create_name("error_log"), 0, 'yes');
    add_option(pplcz_create_name("error_log_hashes"), "", null, 'yes') || update_option(pplcz_create_name("error_log_hashes"), "", 'yes');
}