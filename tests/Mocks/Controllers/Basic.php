<?php namespace Mocks\Controllers;

use Ovide\Phest\Controller;


class Basic extends Controller
{
    const PATH = '/';
    
    /**
     * @acl guest , registered  
     */
    public function get() {}

    /**
     * @acl guest, registered
     * @param type $id
     */
    public function getOne($id) {}

    /**
     * @acl admin
     * @param type $obj
     */
    public function post($obj) {}

    /**
     * @acl registered
     * @param type $id
     * @param type $obj
     */
    public function put($id, $obj) {}

    /**
     * 
     * @param type $id
     */
    public function delete($id) {}
}
