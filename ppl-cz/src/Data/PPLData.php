<?php

namespace PPLCZ\Data;
defined("WPINC") or die();


abstract class PPLData extends \WC_Data
{
    protected $store_name;

    public function __construct($data = 0)
    {
        parent::__construct($data);
        if ($data instanceof static)
        {
            $this->set_id(absint($data->get_id()));
        }
        else if (is_numeric($data))
        {
            $this->set_id($data);
        }

        $this->data_store = \WC_Data_Store::load($this->store_name);

        if ( $this->get_id() ) {
            try {
                $this->data_store->read( $this );
            } catch ( \Exception $e ) {
                $this->set_id( 0 );
                $this->set_object_read( true );
            }
        } else {
            $this->set_draft(gmdate("Y-m-d H:i:s"));
            $this->set_object_read( true );
        }
    }

    public function get_props_for_store($context = 'update')
    {
        if (!in_array($context, ["create", "update"]))
            throw new \Exception(esc_html(sprintf(
                /* translators: %s is context */
                __("Neznámý context %s", "ppl-cz"),
                $context)));

        if ($context === "update")
            $this->validateLock();

        if ($context === "create"
            || $context === "update") {
            foreach (array_keys($this->data) as $key) {
                $method = "get_{$key}";
                if (is_callable([$this, $method]))
                    $values[$key] = call_user_func([$this, $method], $context);
            }
            return $values;
        }
        throw new \Exception(esc_html__("Zamknutý záznam", "ppl-cz"));
    }


    public abstract function set_props_from_store(array $sqldata);

    public function validateLock() {
        if ($this->hard_lock)
            return;
        $changes = $this->get_changes();
        $locked = isset($changes["lock"]) ? true : false;
        if (!$this->get_lock() && $locked)
            throw new \Exception(esc_html__("Zamknutý záznam", "ppl-cz"));
        if ( !$this->ignore_lock && $this->get_lock())
        {
            if (!$locked)
                throw new \Exception(esc_html__("Zamknutý záznam", "ppl-cz"));
        }
    }

    protected $ignore_lock = false;

    public function ignore_lock()
    {
        $this->ignore_lock = true;
    }


    protected $hard_lock = false;

    public function hard_lock()
    {
        $this->hard_lock = true;
    }

    public function get_ignore_lock()
    {
        return $this->ignore_lock;
    }

    public function get_draft($context = 'view')
    {
        return $this->get_prop("draft", $context);
    }

    public function set_draft($value)
    {
        $this->set_prop("draft", $value);
    }

    public function get_lock($context = 'view')
    {
        return $this->get_prop("lock", $context);
    }

    public function set_lock($value)
    {
        $this->set_prop("lock", !!$value);
    }
}