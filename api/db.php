<?php
include_once('sql.php');

class DB extends Sql
{

	private $users_count = 100000;

	function __construct()
	{
		parent::__construct();

		if (!$this->check_db()) {
			$this->create_users();
			$this->create_countries();
		}

		echo json_encode("200");
		$this->conn->close();
	}

	private function check_db()
	{
		$sql = "select * from users limit 1";
		$res = $this->conn->query($sql);
		$cnt = mysqli_num_rows($res);

		return ($cnt > 0) ? true : false;
	}

	private function create_users()
	{

		for ($i = 0; $i < $this->users_count; ++$i) {

			$username = $this->rand_str(10);
			$lastname = $this->rand_str(10);
			$email = $this->rand_str(20) . '@abv.bg';
			$country_code = rand(1, 20);

			$sql = "insert into users (username, lastname, email, country_code) 
			values ('$username', '$lastname', '$email', $country_code)";

			$this->conn->query($sql);
		}
	}

	private function create_countries()
	{
		$country_array = array(
			'Bulgaria',
			'Cambodia',
			'France',
			'Hungary',
			'Italy',
			'India',
			'Kuwait',
			'Kazakhstan',
			'Libya',
			'Moldova',
			'Russia',
			'Slovenia',
			'Turkey',
			'Vietnam',
			'Cyprus',
			'China',
			'Brazil',
			'Albania',
			'Afghanistan',
			'Indonesia',
		);

		$index = 1;
		for ($i = 0; $i < count($country_array); $i++) {
			$c_name = $country_array[$i];
			$sql = "insert into countries (name, code) VALUES ('$c_name', $index)";
			$this->conn->query($sql);
			$index++;
		}
	}

	private function rand_str($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}
}

$db = new DB();
