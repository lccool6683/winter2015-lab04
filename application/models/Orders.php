<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
        $CI = &get_instance();
        $CI->load->model('orderitems');
        $CI->load->model('menu');
    }

    // add an item to an order
    function add_item($num, $code) {
        if (($item = $this->orderitems->get($num, $code)) != null)
        {
            $item->quantity += 1;
            $this->orderitems->update($item);
        }
        else
        {
            $item = $this->orderitems->create();
            $item->order = $num;
            $item->item = $code;
            $item->quantity = 1;
            
            $this->orderitems->add($item);
        }
        
    }

    // calculate the total for an order
    function total($num) {

        
        // Retrieve the order

        $orderitems = $this->orderitems->some('order', $num);
        
        $total = 0.0;
        
        foreach($orderitems as $item)
        {
            $total += $item->quantity * $this->menu->get($item->item)->price;
        }
        
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        $items = $this->orderitems->group($num);
        
        $gotem = array();
        
        // Loop through all items and set the category that they're in to 1
        if (count($items) > 0)
        {
            foreach ($items as $item)
            {
                $menu = $this->menu->get($item->item);
                $gotem[$menu->category] = 1;
            }
        }
              
        return isset($gotem['m']) && isset($gotem['d']) && isset($gotem['s']);
    }

}
