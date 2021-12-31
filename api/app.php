<?php
include_once('sql.php');

define('getCountries', 'getCountries');
define('getReport', 'getReport');

class App extends Sql
{
  private $text_search;
  private $country_code;
  private $limit = 10;
  private $start_from;
  private $method;

  function __construct()
  {
    parent::__construct();

    if (isset($_POST['text_search']))
      $this->text_search = mysqli_real_escape_string($this->conn, $_POST['text_search']);

    if (isset($_POST['country_code']))
      $this->country_code = $_POST['country_code'];

    if (isset($_POST['start_from']))
      $this->start_from = $_POST['start_from'];

    if (isset($_POST['method']))
      $this->method = $_POST['method'];

    switch ($this->method) {

      case 'getCountries':
        $this->getCountries();
        break;

      case 'getReport':
        $this->getReport();
        break;

      default:
        throw new Exception('Invalid REQUEST_METHOD');
        break;
    }
  }

  private function getCountries()
  {

    $result = $this->conn->query("select * from countries");

    $data = array();

    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }

    echo json_encode($data);
  }


  private function getReport()
  {

    $result = null;
    
    if ($this->text_search)
      $result = $this->conn->query("select u.id as user_id, username, lastname, email, country_code,c.name as country_name from users u
      left join countries c on (c.code = u.country_code)
      where u.username = '$this->text_search' or u.email = '$this->text_search'");

    if ($this->country_code)
      $result = $this->conn->query("select u.id as user_id, username, lastname, email, country_code,c.name as country_name, 
      (select COUNT(*) from users where country_code = '$this->country_code') as user_count
      from users u
        left join countries c on (c.code = u.country_code)
        where u.country_code = '$this->country_code' limit $this->start_from, $this->limit");

    $data = array();

    if ($result) {

      while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
      }
    }

    echo json_encode($data);

  }
}

$app = new App();
