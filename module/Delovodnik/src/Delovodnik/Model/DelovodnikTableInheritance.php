<?php

namespace Delovodnik\Model;
// If you are going to extend and inherit use AbstractTableGateway
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class DataTable extends AbstractTableGateway
{
	public function __construct(Adapter $adapter)
	{
		$this->table = 'data';
		$this->adapter = $adapter;
	}
	// Here i can have a lot of methods serving as repo for SQL statements
}