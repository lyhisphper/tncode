<?php
namespace Service;
use Service\TnCode;

class handler
{
    private $tncode;


    public static function make()
    {
        $tncode = new TnCode();
        $tncode->make();
    }

    public static function check()
    {
        $tncode = new TnCode();
        if ($tncode->check()) {
            $_SESSION['tncode_check'] = 'ok';
            echo "ok";
        } else {
            $_SESSION['tncode_check'] = 'error';
            echo "error";
        }
    }

    public static function test()
    {
        echo "TEST";
    }
}