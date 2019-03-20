<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/6/17
 * Time: 1:05 PM
 */
class hpptest extends CI_Controller {

    public function index() {

        $this->load->library('protectpayapi');
        //$this->protectpayapi-> ...
        $data=[];
        $data["core_settings"] = Setting::first();
        $this->load->view($data["core_settings"]->template. '/hpptest/index', $data);
    }
}