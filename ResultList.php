<?php


class ResultList {
    public $results;
    public $total_size;
    
    public function getJSON()
    {
        return json_encode($this);
    }//end function
    
}//end class
