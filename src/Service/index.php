<?php


namespace Service;

use Service\TnCode;

class index
{
    public function make()
    {
        $tn = new TnCode();
        $tn->make();
    }

    public function check()
    {
        $tn = new TnCode();
        $tn->check();
    }
}