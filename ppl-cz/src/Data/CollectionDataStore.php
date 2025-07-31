<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery

namespace PPLCZ\Data;

defined("WPINC") or die();


use \DateTime;

class CollectionDataStore extends \WC_Data_Store_WP implements CollectionDataStoreInterface, \WC_Object_Data_Store_Interface
{
    protected $meta_type = 'collection';

    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-collection" =>  self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }

    public function delete(&$shipment, $args = [])
    {
        global $wpdb;

        $wpdb->delete(
            $wpdb->prefix . 'pplcz_collections',
            array(
                'ppl_collection_id' => $shipment->get_id(),
            ),
            array( '%d' )
        ); // WPCS: cache ok, DB call ok.


        wp_cache_delete( $shipment->get_id(), 'pplcz_collection' );
    }

    public function update(&$shipment)
    {
        /**
         * @var CollectionData $shipment
         */
        global $wpdb;

        if (!$shipment->get_changes())
            return;

        $data = array(
            'send_date' => $shipment->get_send_date() ? $shipment->get_send_date() : null,
            'send_to_api_date' => $shipment->get_send_to_api_date() ? $shipment->get_send_to_api_date() : null,
            'reference_id' => $shipment->get_reference_id(),
            'state' => $shipment->get_state(),
            'shipment_count' => $shipment->get_shipment_count(),
            'contact' => $shipment->get_contact(),
            'telephone' => $shipment->get_telephone(),
            'email' => $shipment->get_email(),
            'note' => $shipment->get_note(),
            'remote_collection_id' => $shipment->get_remote_collection_id(),
            'estimated_shipment_count' => $shipment->get_estimated_shipment_count()
        );

        $wpdb->update(
            $wpdb->prefix . 'pplcz_collections',
            $data,
            array(
                'ppl_collection_id' => $shipment->get_id(),
            )
        );
        wp_cache_delete( $shipment->get_id(), 'pplcz_collection' );
        $shipment->apply_changes();

        do_action( 'pplcz_collections_updated', $shipment->get_id() );
    }

    public function read(&$shipment)
    {
        global $wpdb;

        $data = wp_cache_get( $shipment->get_id(), 'pplcz_collection' );

        if ( false === $data ) {
            $data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pplcz_collections WHERE ppl_collection_id = %d LIMIT 1;", $shipment->get_id() ), ARRAY_A ); // WPCS: cache ok, DB call ok.
            wp_cache_add( $shipment->get_id(), $data, 'pplcz_collection' );
        }

        if ( is_array( $data ) ) {
            $import =  array(
                'id'               => $data['ppl_collection_id'],
                'created_date'     => $data['created_date'],
                'send_to_api_date' => '0000-00-00 00:00:00' === $data['send_to_api_date'] || !$data['send_to_api_date']  ? null : $data["send_to_api_date"],
                'send_date'        => '0000-00-00 00:00:00' === $data['send_date'] || !$data['send_date'] ? null : $data['send_date'],
                'reference_id'     => $data['reference_id'],
                'state'            => $data['state'],
                'shipment_count'   => $data['shipment_count'],
                'contact'         => $data['contact'],
                'telephone'        => $data['telephone'],
                'estimated_shipment_count' => $data['estimated_shipment_count'],
                'remote_collection_id' => $data['remote_collection_id'],
                'email' => $data['email'],
                'note' => $data['note']
            );
            $shipment->set_props(
               $import
            );
            $shipment->set_object_read( true );

            do_action( 'pplcz_collections_loaded', $shipment );
        } else {
            throw new \Exception( esc_html__( 'Špatný svoz.', 'ppl-cz' ) ) ;
        }
    }

    public function create( &$collection ) {
        global $wpdb;

        $changes = $collection->get_changes();

        $data = array(
            'created_date' => date('Y-m-d H:i:s'),
            'send_to_api_date' => $collection->get_send_to_api_date() ? $collection->get_send_to_api_date():  null,
            'send_date' => $collection->get_send_date(),
            'state' => $collection->get_state(),
            'shipment_count' => $collection->get_shipment_count(),
            'contact' => $collection->get_contact(),
            'telephone' => $collection->get_telephone(),
            'email' => $collection->get_email(),
            'note' => $collection->get_note(),
            'estimated_shipment_count' => $collection->get_estimated_shipment_count()
        );

        $data["reference_id"] = pplcz_simple_guid();

        $wpdb->insert( $wpdb->prefix . 'pplcz_collections', $data ); // WPCS: DB call ok.

        $id = $wpdb->insert_id;
        $collection->set_id( $id );
        $collection->set_props($data);
        $collection->apply_changes();

        do_action( 'pplcz_new_ppl_collection', $id, $collection );
    }

    public function read_collections($args = [])
    {
        global $wpdb;

        $filter = ["1 = 1"];

        if (isset($args['state']) && $args["state"]) {
            $filter[] = $wpdb->prepare(" state in ( " . join(", ", array_fill(0, count($args["state"]), "%s" )) .   ") ", ...$args["state"]);
        }
        $d = (new \DateTime())->sub(new \DateInterval('P10D'));

        $filter[] = $wpdb->prepare(" send_date > %s ", $d->format("Y-m-d"));

        $collection = [];
        foreach ($wpdb->get_results("select * from {$wpdb->prefix}pplcz_collections where " . join(" AND ", $filter ). " order by send_date desc", ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_collection_id"], $result, "pplcz_collection");
            $collection[] = new CollectionData($result["ppl_collection_id"]);
        }

        return $collection;
    }

    public function available_collections()
    {
        global $wpdb;

        $collection = [];
        foreach ($wpdb->get_results("select * from {$wpdb->prefix}pplcz_collections where state is null or state = '' order by created_date", ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_collection_id"], $result, "pplcz_collection");
            $collection[] = new CollectionData($result["ppl_collection_id"]);
        }

        return $collection;
    }

    public function last_collection()
    {
        global $wpdb;
        $result = null;

        foreach ($wpdb->get_results("select * from {$wpdb->prefix}pplcz_collections order by created_date desc limit 1", ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_collection_id"], $result, "pplcz_collection");

        }

        if ($result)
            return new CollectionData($result["ppl_collection_id"]);
        return null;
    }

    public static function find_reference_for_date($date)
    {
        global $wpdb;

        $output = [];
        foreach ($wpdb->get_results( $wpdb->prepare("select reference_id from {$wpdb->prefix}pplcz_collections where send_date = '%s'", $date ), ARRAY_A) as $item) {
            $output[] = $item["reference_id"];
        }
        return $output;
    }


}