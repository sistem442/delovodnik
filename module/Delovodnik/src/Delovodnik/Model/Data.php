<?php

namespace Delovodnik\Model;
// I don't have the filters here now I can implement the Interface
// Use with Zend\Db\ResultSet\ResultSet. You send it as argument to the Adapter ot TableDataGateway
/*
The form’s bind() method attaches the model to the form. This is used in two ways:

When displaying the form, the initial values for each element are extracted from the model.
After successful validation in isValid(), the data from the form is put back into the model.
These operations are done using a hydrator object. There are a number of hydrators, but the default 
one is Zend\Stdlib\Hydrator\ArraySerializable which expects to find two methods in the model: 
getArrayCopy() and exchangeArray(). We have already written exchangeArray() in our Album entity, 
so just need to write getArrayCopy():
*/
class Data // implements ArrayObject - but I should define a lot of methods
{
    public $doc_id;
    public $doc_title;		
	public $company;  			
	public $doc_company_id;  			
	public $date;  			
	public $doc_location;  			
	public $remark;  			
	// ArrayObject, or at least implement exchangeArray. For Zend\Db\ResultSet\ResultSet to work
	// 1) hydration!!!!! <- This is enough for resultSet to work but not for the form
    public function exchangeArray($data) 
    {
        $this->doc_id     = (!empty($data['doc_id'])) ? $data['doc_id'] : null;
		$this->doc_title     = (!empty($data['doc_title'])) ? $data['doc_title'] : null;
        $this->company     = (!empty($data['company'])) ? $data['company'] : null;
    	$this->doc_company_id    = (!empty($data['doc_company_id'])) ? $data['doc_company_id'] : null;
    	$this->date     = (!empty($data['date'])) ? $data['date'] : null;
    	$this->doc_location     = (!empty($data['doc_location'])) ? $data['doc_location'] : null;
    	$this->remark     = (!empty($data['remark'])) ? $data['remark'] : null;
    }
	
	// 2) Extraction. For extraction the standard Hydrator the Form expects getArrayCopy to be able to bind
	// -> Extracts the data
    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	
}