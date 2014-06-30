<?php

namespace Delovodnik\Model;

use Zend\Db\TableGateway\TableGateway;

use Delovodnik\Model\Data; // this is the model
// with injection of the Zend Table Data Gateway
// This is My Repository of statments. Here I can use different SQL statements
// Like Repo in Doctrine
class DataTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	// I could follow the same interface as TableGateway I can even implement it
	// I like the approach here because as paramaters for the functiosn I have to 
	// send values like for ordinary functions
	
	public function select()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }	

    public function getData($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $data = $rowset->current();
        if (!$data) {
            throw new \Exception("Nije pronađen dokument sa id brojem: $id");
        }
        return $data;
    }	

	public function insert(Data $data)
	{
		$this->tableGateway->insert($this->prepareData($data));
	}
	
	public function update (Data $data)
	{
		if (!$this->getData($data->id)) {
			throw new \Exception('Form id does not exist');
		}
		$this->tableGateway->update(
			$this->prepareData($data), 
			array('id' => $data->id)
		);		
	}
	
    public function prepareData(Data $data)
    {
		// for Zend\Db\TableGateway\TableGateway we need the data in array not object
        $data = array(
            'id' 					=> $data->id,
            'doc_id'				=> $data->doc_id,
            'doc_Title'  			=> $data->doc_Title,
            'company'	  			=> $data->company,
            'doc_company_id'		=> $data->doc_company_id,
            'date'  				=> $data->date,
            'doc_location'			=> $data->doc_location,
            'remark'  				=> $data->remark,
		);
	
		return $data;
    }

    public function delete(Data $data)
    {
        $this->tableGateway->delete(array('id' => $data->id));
    }

	// Add more functions here when you need them This is table data gateway
	// Also you can get the adapter and create far more complicated quesries
	// using SQL or statements ir whatever you like.
}