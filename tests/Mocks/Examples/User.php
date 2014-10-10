<?php namespace Mocks\Examples;

use Ovide\Libs\Mvc\Rest;


class User extends Rest\Controller
{
    const ID = 'username';
    
    public static $data = [];
    
    public function post($login)
    {
        if (!isset($login['username']) || (!isset($login['password']))) {
            throw new Rest\Exception\BadRequest();
        }
        
        $username = $login['username'];
        $password = $login['password'];
        
        if (!preg_match('/^[a-z]+$/', $username)) {
            throw new Rest\Exception\BadRequest('Invalid username');
        }
        
        if (mb_strlen($password) < 8) {
            throw new Rest\Exception\BadRequest('Password too short');
        }
        
        if (isset(static::$data[$username])) {
            $msg = "Username $username already exists";
            throw new Rest\Exception\Conflict($msg);
        }
        
        $data = [
            'username' => $username,
            'password' => sha1($password)
        ];
        
        static::$data[$username] = $data;
        return $data;
    }
    
    public function get()
    {
        $result = [];
        foreach (static::data as $user) {
            $username = $user['username'];
            $result[] = [
                'username' => $username,
                'login'    => "/users/$username/token",
                'articles' => "/users/$user/articles"
            ];
        }
        
        return $result;
    }
    
    public function getOne($user)
    {
        if (!isset(static::$data[$user])) {
            throw new Rest\Exception\NotFound("$user not found");
        }
        
        $data = static::$data[$user];
        return [
            'username' => $data['username'],
            'login'    => "/users/$user/token",
            'articles' => "/users/$user/articles"
        ];
    }
}
