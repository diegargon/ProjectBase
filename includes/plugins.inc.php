<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

$actions = []; 
$actions_uniq = [];



function register_action($event, $func, $priority)
{
    global $actions;
    $actions[$event][] = array ("function_name" => $func, "priority" => $priority);
}

function register_uniq_action($event, $func, $priority = 0) {
     global $actions;
    
    foreach($actions as $key => $value)  {
        
        if ($key == $event) {
            
            $actions[$key][0] = array("function_name" => $func, "protiry" => $priority);
            return;
        }
    }
    $actions[$event][] = array ("function_name" => $func, "priority" => $priority);
    
} 

function do_action($event, $params=null)
{
    global $actions;
    

    
    //$return = ""; 
    if(isset($actions[$event]))
    {
        
        usort($actions[$event], function($a, $b) {
             return $a['priority'] - $b['priority'];
        });

        
        foreach($actions[$event] as $func)
        {
            if(function_exists($func['function_name'])) {
                if (isset($return)) {
                   $return .= call_user_func($func['function_name'], $params);
                } else {
                   $return = call_user_func($func['function_name'], $params); 
                }
            }
        }
    } 
    if (isset($return)) {
        return $return;
    }
}

function action_isset($this_event) {
    global $actions;
        
    foreach ($actions as $event=>$func) {
        if (($event == $this_event) && function_exists($func[0])) {
            return true;
        }
        
    }    
    return false;
}