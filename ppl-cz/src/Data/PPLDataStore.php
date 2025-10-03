<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery

namespace PPLCZ\Data;
defined("WPINC") or die();


class PPLDataStore extends \WC_Data_Store_WP implements \WC_Object_Data_Store_Interface
{

    protected $table_name;

    protected $id_name;

    protected function getSqlData($id)
    {
        global $wpdb;
        $cache = wp_cache_get($id, $this->table_name);
        if (false === $cache) {
            $cache = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}pplcz_{$this->table_name} WHERE {$this->id_name} = %d LIMIT 1;", $id), ARRAY_A); // WPCS: cache ok, DB call ok.
            wp_cache_add($id, $cache,  $this->table_name);
        }
        return $cache;
    }

    public function create(&$data)
    {
        /**
         * @var PPLData $data
         */

        global $wpdb;
        $insertData = $data->get_props_for_store("create");
        $wpdb->insert($wpdb->prefix . "pplcz_". $this->table_name, $insertData); // WPCS: DB call ok.
        $id = $wpdb->insert_id;
        $data->set_id($id);
        $data->apply_changes();
        do_action("pplcz_{$this->table_name}_new", $id, $data);
    }

    public function read(&$data)
    {
        /**
         * @var PPLData $data
         */
        $sqldata = $this->getSqlData($data->get_id());

        if (is_array($sqldata)) {
            $data->set_props_from_store($sqldata);
            $data->apply_changes();;
            $data->set_object_read(true);

            do_action("pplcz_{$this->table_name}_loaded", $data);
        } else {
            throw new \Exception( esc_html__('Špatná zásilka.', 'ppl-cz'));
        }
    }

    public function update(&$data)
    {
        /**
         * @var PPLData $data
         */

        global $wpdb;
        $values = $data->get_props_for_store("update");

        $wpdb->update(
            $wpdb->prefix . 'pplcz_' .$this->table_name,
            $values,
            array(
                $this->id_name => $data->get_id(),
            )
        );

        wp_cache_delete($data->get_id(), $this->table_name);
        $data->apply_changes();
        do_action("pplcz_{$this->table_name}_update", $data);
    }

    public function delete(&$data, $args = array())
    {
        /**
         * @var PPLData $data
         */

        global $wpdb;

        if ($data->get_lock())
            throw new \Exception(
                esc_html(sprintf(
                    /* translators: %d je id zásilky */
                    __("Záznam %d je zamknut", "ppl-cz"),
                    $data->get_id()
                )
            ));

        $wpdb->delete(
            $wpdb->prefix . 'pplcz_'. $this->table_name,
            array(
                $this->id_name => $data->get_id(),
            ),
            array( '%d' )
        ); // WPCS: cache ok, DB call ok.


        wp_cache_delete( $data->get_id(), $this->table_name );
        do_action( "pplcz_{$this->table_name}_delete", $data->get_id(), $data );
    }
}