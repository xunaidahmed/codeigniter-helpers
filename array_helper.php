<?php defined('BASEPATH') OR exit('No direct script access allowed');

//============================================================+
// File name   : array_helper.php
// Begin       : 2018-04
// Last Update : 2018-04
//
// Description : array includes a variety of global "helper" PHP functions.
// Author      : Junaid Ahmed
// -------------------------------------------------------------------

/**
 * Array Change Key
 * *************************
 * Desc:
 *
 * Suppose: [['id' => 1, 'title' => 'Jan', 'active' => 1]]
 * call: array_change_key( a, array('id', title', 'active'), array('id', 'name', 'status'))
 * Output: [['id' => 1, 'name' => 'Jan', 'status' => 1]]
 *
 */
function array_change_key( $arr, $old_key, $new_key )
{
    $old_key = is_array($old_key) ? $old_key : [$old_key];
    $new_key = is_array($new_key) ? $new_key : [$new_key];

    if( count($old_key) != count($new_key) ) return [];

    foreach ($old_key as $key => &$value)
    {
        if( !in_array($value, $new_key) )
        {
            $_tmp_val   = $arr[$value];
            $key_name   = $new_key[$key];

            unset($arr[$value]);

            $arr[$key_name] = $_tmp_val;
        }
    }

    return $arr;
}

/**
 * Array Recursive Count
 * *************************
 * Desc:
 *
 * Suppose: [['id' => 1, 'title' => 'Jan'], 'id' => 2, 'title' => 'Feb']]
 * call: array_lang( exists_arr, 'title') OR array_lang( exists_arr, ['title']);
 * Output: [['id' => 1, 'title' => 'January'], 'id' => 2, 'title' => 'Febuary']]
 *
 */
function array_recursive_count( $arr, $is_bool = false )
{
    $_tmp = [];

    if( count($arr) )
    {
        foreach( $arr  as $key => $value )
        {
            if( $is_bool && count($value) > 1 )
            {
                return false;
            }

            $_tmp[$key][] = count($value);
        }

        if( $is_bool ) return true;
    }

    return ( $is_bool ? false : $_tmp );
}

/**
 * Array Language
 * *************************
 * Desc:
 *
 * Suppose: [['id' => 1, 'title' => 'Jan'], 'id' => 2, 'title' => 'Feb']]
 * call: array_lang( exists_arr, 'title') OR array_lang( exists_arr, ['title']);
 * Output: [['id' => 1, 'title' => 'January'], 'id' => 2, 'title' => 'Febuary']]
 *
 */
function array_lang($array, $field)
{
    if( (is_array($array) && count($array)) )
    {
        $ci     =& get_instance();
        $fields = (is_array($field) ? $field : [$field]);

        foreach ($array as $key => &$value)
        {
            $value = (object) $value;

            foreach($fields as $field)
            {
                if( isset($value->$field) && $value->$field )
                {
                    $value->$field = $ci->lang->line($value->$field);
                }
            }
        }
    }

    return $array;
}

/**
 * Array First
 * *************************
 * Desc: The array_first function returns the first element of an array passing a given truth test:
 *
 * Suppose: $array = [100, 200, 300];
 * call: array_first( array );
 * Output: 100
 *
 */
function array_first( $array )
{
    return current($array);
}

/**
 * Array Except
 * *************************
 * Desc: The array_except function removes the given key / value pairs from an array:
 *
 * Suppose: [['id' => 1, 'title' => 'Jan'], 'id' => 2, 'title' => 'Feb']]
 * call: array_except( exists_arr, 'title') OR array_except( exists_arr, ['title']);
 * Output: [['id' => 1], 'id' => 2]]
 *
 */
function array_except()
{

}

/**
 * Array Select
 * *************************
 * Desc: The array_select function select the given key / value from an array:
 *
 * Suppose: ['1' => 'A', '2' => 'B', '3' => 'C']
 * call: array_select( exists_arr, 2);
 * Output: B
 *
 */
function array_select( $array, $select, $return = '' )
{
    if( is_array($array) && count($array) )
    {
        if( isset($array[$select]) && $array[$select] )
        {
            return $array[$select];
        }
    }

    return $return;
}

/**
 * Array Add
 * *************************
 * The array_add function adds a given key / value pair to an array if the given key doesn't already exist in the array:
 *
 * Suppose: [['id' => 1, 'title' => 'Jan'], 'id' => 2, 'title' => 'Feb']]
 * call: array_add('price', 100, '1233,12233'); OR array_add('price', 100, '1233.12233');
 * Output: ['name' => 'Desk', 'price' => 100]
 *
 */
function array_add( $array, $ext = '.', $is_unique = TRUE )
{
    $_temp = [];

    if( isset($array) && count($array) )
    {
        foreach ($array as $key => $value)
        {
            if( empty($value) )
            {
                continue;
            }

            $explodes = explode($ext, $value);

            foreach ($explodes as $explode)
            {
                $_temp[] = $explode;
            }
        }

        if( $is_unique )
            return array_unique($_temp);
    }

    return $_temp;
}

/**
 * Array Sum Total
 * *************************
 * The array_sum_total function adds a given key / value pair to an array if the given key doesn't already exist in the array:
 *
 * Suppose: [['id' => 1, 'title' => 'Jan', 'employee' => 10 ], 'id' => 2, 'title' => 'Feb', 'employee' => 50 ]]
 * call: array_sum_total( $array , 'employee');
 * Output: 60
 *
 */
function array_sum_total( $array, $field )
{
    if( is_array($array) AND count($array))
    {
        $sum_of = array_get( $array, $field );

        return array_sum( $sum_of );
    }

    return 0;
}

/**
 * Array Get
 * *************************
 * Desc: The array_get function retrieves a value from a deeply nested array
 *
 * Suppose: [['id' => 1, 'title' => 'Jan'], 'id' => 2, 'title' => 'Feb']]
 * call: array_get( exists_arr, 'id');
 * Output: [1, 2]
 *
 */
function array_get( $array, $field )
{
    $_temp = [];

    if( isset($array) && count($array) )
    {
        foreach ($array as $key => $value)
        {
            $value = (array) $value;

            if( isset($value[$field]) && $value[$field] )
            {
                $_temp[] = $value[$field];
            }
        }
    }

    return $_temp;
}

/**
 * Array Only
 * *************************
 * Desc: The array_pluck function retrieves all of the values for a given key from an array:
 *
 * Suppose: ['name' => 'Desk', 'price' => 100]
 * call: array_only( exists_arr, 'name');
 * Output: ['name' => 'Desk']
 *
 */
function array_only()
{

}

/**
 * Array Pluck
 * *************************
 * Desc: The array_only function returns only the specified key / value pairs from the given array:
 *
 * Suppose: [['id' => 1, 'title' => 'A', 'active' => 1], 'id' => 2, 'title' => 'B', 'active' => 1s]
 * call: array_pluck( exists_arr, ['id', title']);
 * Output: [1=>A, 2=>B]
 *
 */
function array_pluck( $array, $fields )
{
    if( (is_array($array) && count($array)) )
    {
        $_tmp = [];

        foreach ($array as $key => $value)
        {
            $value  = (array) $value;
            if( count($fields) == 2 )
            {
                $_tmp[$value[$fields[0]]] = $value[$fields[1]];
            }
        }

        return $_tmp;
    }

    return [];
}

/*
 * Array Take
 * *************************
 * Desc: The take method returns a new array with the specified number of items:
 *
 * Suppose: ['a' => [1,2], 'a' => [3,4]]
 * call: array_take( exists_arr, 'a');
 * Output: [1,2,3,4]
 */
function array_take($arr, $take )
{
    if( count($arr) )
    {
        foreach ($arr as $key => &$value)
        {
            $value = $value[$take];
        }

        return $arr;
    }

    return [];
}
/**
 * To Array
 * *************************
 * Desc: Any array convert to "array" format
 *
 * Suppose: ['id' => 1, 'title' => 'A'], 'id' => 2, 'title' => 'B']
 * call: to_array( exists_arr );
 * Output: stdClass Object
 * (
 *    [0] =>
 *    (
 *            [id] => 1
 *            [title] => A
 *    )
 *    [1] =>
 *    (
 *            [id] => 2
 *            [title] => B
 *    )
 * )
 *
 */
function to_array( $array )
{
    if( count($array) )
    {
        foreach ($array as $key => &$value)
        {
            $value = (array) $value;
        }

        return (array) $array;
    }

    return [];
}

/**
 * Object Array
 * *************************
 * Desc: Any array convert to "object array" format
 *
 * Suppose: ['id' => 1, 'title' => 'A'], 'id' => 2, 'title' => 'B']
 * call: to_object_array( exists_arr );
 * Output: stdClass Object
 * (
 *    [0] => stdClass Object
 *    (
 *            [id] => 1
 *            [title] => A
 *    )
 *    [1] => stdClass Object
 *    (
 *            [id] => 2
 *            [title] => B
 *    )
 * )
 *
 */

function to_object_array()
{

}

/**
 * Json Array
 * *************************
 * Desc: Any array convert to "json array" format
 *
 * Suppose: ['id' => 1, 'title' => 'A'], 'id' => 2, 'title' => 'B']
 * call: to_json_array( exists_arr );
 * Output: {"0":{"id":1,"title":"A"},"id":2,"title":"B"}
 *
 */
function to_json_array( $arr, $return = true )
{
    $data = json_encode($arr);

    if( $return )
    {
        return $data;
    }

    echo $data;
}

/**
 * Array Convert to "TD" OR "TH"
 * *****************************
 * Desc:
 *
 */
function array_to_tr($array, $columns = '*', $tag = 'td')
{
    $output = '';

    if( (is_array($array) && count($array)) )
    {
        foreach ($array as $key => $value)
        {
            $value = (object) $value;
            $output .= "<tr>";

            foreach( $value as $fk => $fv )
            {
                if( !is_array($columns) && $columns == '*' )
                {
                    $output .= "<{$tag}>".$value->$fk."</{$tag}>";
                }
                else
                {
                    if( in_array($fk, $columns) )
                    {
                        $output .= "<{$tag}>".$value->$fk."</{$tag}>";
                    }
                }
            }

            $output .= "</tr>";
        }

        return $output;
    }

    return $output;
}

/**
 * Group of Array Index Map
 * *************************
 * Desc:
 *
 * Suppose: [['name' => 'Desk', 'price' => 100], ['name' => 'Table', 'price' => 200], ['name' => 'Desk', 'price' => 300]]
 * call: object_index_map( exists_arr, 'name');
 * Output:
 *     [Desk] =>
 *     [0]
 *         [
 *             'name' => 'Desk',
 *             'price' => 100
 *        ],
 *   [1] =>
 *       [
 *         'name' => 'Desk',
 *         'price' => 300
 *       ]
 *   ]
 *   [Table] =>
 *   [
 *       'name' => 'Table',
 *       'price' => 200
 *  ]
 *
 */
function group_array_index_map($array, $index, $remove_index = false)
{
    $newArray = array();

    foreach ($array as $key => $arr)
    {
        $arr = (array) $arr;

        if ( isset($arr[$index]) )
        {
            $_tmp = $arr[$index];

            if ( $remove_index )
                unset($arr[$index]);

            $newArray[ $_tmp ][] = $arr;
        }
    }

    return $newArray;
}

/**
 * Object Index Map
 * *************************
 * Desc:
 *
 * Suppose: [['name' => 'Desk', 'price' => 100], ['name' => 'Table', 'price' => 200]]
 * call: object_index_map( exists_arr, 'name');
 * Output:
 *     [Desk] =>
 *     [
 *         'name' => 'Desk',
 *         'price' => 100
 *    ],
 *   [Table] =>
 *   [
 *       'name' => 'Table',
 *       'price' => 200
 *  ]
 *
 */

function array_index_map($object, $index, $remove_index = false)
{
    $newArray = array();

    foreach ($object as $key => $obj)
    {
        if ( isset($obj[$index]) )
        {
            $_tmp = $obj[$index];

            if ( $remove_index )
                unset($obj[$index]);

            $newArray[$_tmp] = $obj;
        }
    }

    return $newArray;
}


/**
 * Object Index Map
 * *************************
 * Desc:
 *
 * Suppose: [['name' => 'Desk', 'price' => 100], ['name' => 'Table', 'price' => 200]]
 * call: object_index_map( exists_arr, 'name');
 * Output:
 *     [Desk] =>
 *     [
 *         'name' => 'Desk',
 *         'price' => 100
 *    ],
 *   [Table] =>
 *   [
 *       'name' => 'Table',
 *       'price' => 200
 *  ]
 *
 */

function object_index_map($object, $index, $remove_index = false)
{
    $newArray = new StdClass;

    foreach ($object as $key => $obj)
    {
        if ( isset($obj->$index) )
        {
            $_tmp = $obj->$index;

            if ( $remove_index )
                unset($obj->$index);

            $newArray->$_tmp = $obj;
        }
    }

    return $newArray;
}