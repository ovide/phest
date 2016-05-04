<?php namespace Mocks\Examples;

use Ovide\Libs\Mvc\Rest\Controller;
use Ovide\Libs\Mvc\Rest\Exception;

class User extends Controller
{
	const ID   = 'username';
	const PATH = '/users';
	const RX   = '[a-z]*';

	/**
	 * @var array
	 *  - username
	 *  - sha1(password)
	 */
	public static $data = [];

	public function get()
	{
		$result = [];
		foreach (static::$data as $userData) {
			$name = $userData[static::ID];
			$result[] = $this->_content($name);
		}
		return $result;
	}

	public function getOne($name)
	{
		if (!isset(static::$data[$name])) {
			throw new Exception\NotFound("User $name not found");
		}

		return $this->_content($name);
	}

	public function post($user)
	{
		if (!isset($user['username']) || !isset($user['password'])) {
			throw new Exception\BadRequest();
		}

		if (mb_strlen($user['password']) < 8) {
			throw new Exception\BadRequest('Password is too short');
		}

		if (isset(static::$data[$user['username']])) {
			throw new Exception\Conflict("User {$user['username']} already exists");
		}

		$new = [
			'username' => $user['username'],
			'password' => sha1($user['password'])
		];

		static::$data[$user['username']] = $new;

		return $this->_content($user['username']);
	}

	public function put($user, $data)
	{
		if (!isset(static::$data[$user])) {
			throw new Exception\NotFound("User $user not found");
		}

		$old = static::$data[$user];
		if (sha1($data['old_password']) !== $old['password']) {
			throw new Exception\Conflict("Old password doesn't match");
		}

		$new = [
			'username' => $old['username'],
			'password' => sha1($data['password'])
		];

		static::$data[$user] = $new;
	}


	protected function _content($username)
	{
		return [
			'username' => $username,
			'uri'      => static::PATH."/$username",
			'articles' => static::PATH."/$username/articles"
		];
	}
}
