<?php namespace Ovide\Libs\Mvc\Rest;

class User
{
	const ID   = 'username';
	const PATH = 'users';
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
			$name = $userData['username'];
			$result[] = [
				'username' => $name,
				'articles' => static::PATH."/$name/articles"
			];
		}
		return $result;
	}

	public function getOne($name)
	{
		if (!isset(static::$data[$name])) {
			throw new Exception\NotFound("User $name not found");
		}

		$result = [
			'username' => $name,
			'articles' => static::PATH."/$name/articles"
		];
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

		static::$data[$user['username']] = [
			'username' => $user['username'],
			'password' => sha1($user['password'])
		];
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
}
