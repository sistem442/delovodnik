<?php

namespace Delovodnik\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select; 


use Delovodnik\Form\DelovodnikForm;
use Delovodnik\Form\DelovodnikFilter;
use Delovodnik\Form\SearchFilter;
use Delovodnik\Form\SearchForm;
use Delovodnik\Form\SearchDateForm;
use Delovodnik\Form\SearchDateFilter;

use Zend\Form\FormInterface;

use Zend\Http\Headers;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class DelovodnikController extends AbstractActionController
{
	private $dataTable;
	
	//full search
	public function searchAction()
	{
		$form = new SearchForm();
		return new ViewModel(array('form' => $form));
	}

	public function SearchResultAction(){
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form = $this->getServiceLocator()->get('SearchForm');
			$form->setInputFilter(new SearchFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $request->getPost()->toArray();
				$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'); 
				$sql = new Sql($adapter);
				$select = $sql->select();
				$select->from('data');
				
				if($data['date_of_entry_start']!='')
				$select->where->greaterThanOrEqualTo("date_of_entry", $data['date_of_entry_start']);
				if($data['date_of_entry_end']!='')
				$select->where->lessThanOrEqualTo("date_of_entry", $data['date_of_entry_end']);											
				if($data['doc_id']!='')
				$select->where(array("doc_id" => $data['doc_id']));
				if($data['doc_title']!='')
				$select->where->like('doc_title', "%" . $data['doc_title'] . "%");
				if($data['company']!='')
				$select->where->like('company', "%" . $data['company'] . "%");
				if($data['doc_company_id']!='')
				$select->where->like('doc_company_id', "%" . $data['doc_company_id'] . "%");
				if($data['doc_location']!='')
				$select->where->like('doc_location', "%" . $data['doc_location'] . "%");
				if($data['remark']!='')
				$select->where->like('remark', "%" . $data['remark'] . "%");
				if($data['date_start']!='')
				$select->where->greaterThanOrEqualTo("date", $data['date_start']);
				if($data['date_end']!='')
				$select->where->lessThanOrEqualTo("date", $data['date_end']);

				$select->order('date_of_entry DESC');
				//$select->limit(20);
				$selectString = $sql->getSqlStringForSqlObject($select);
				$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
				//*********************end of edit
/*						$sql = "SELECT * 
						FROM data 
						WHERE date_of_entry <= '".$data['search_date']."' 
						ORDER BY date_of_entry DESC
						LIMIT 0,20;"; 
				$statement = $adapter->query($sql); 
				$results= $statement->execute();
*/
				$returnArray = array();
				foreach ($results as $result) {
					$returnArray[] = $result;
				}
				//echo $sql->getSqlstringForSqlObject($select); 
				return new ViewModel(array('results' => $returnArray));
										}
			else {
				echo 'form is not valid';
				//die;
			}
		}
	}
	//date search temporary
	public function searchDateAction()
	{
		$form = new SearchDateForm();
		
		return new ViewModel(array('form' => $form));
	}

	public function SearchDateResultAction(){
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form = $this->getServiceLocator()->get('SearchDateForm');
			$form->setInputFilter(new SearchDateFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $request->getPost()->toArray();
				$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'); 
				//******************EDIT
				$sql = new Sql($adapter);
				$select = $sql->select();
				$select->from('data');
				$select->where->lessThan("date_of_entry", $data['search_date']);
				$select->order('date_of_entry DESC');
				$select->limit(20);
				$selectString = $sql->getSqlStringForSqlObject($select);
				$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
				$returnArray = array();
				foreach ($results as $result) {
					$returnArray[] = $result;
				}
				return new ViewModel(array('results' => $returnArray));
										}
			else {
				echo 'form is not valid';
				die;
			}
		}
	}

	// R -retrieve 	CRUD
	public function indexAction()
	{
		return new ViewModel(array('rowset' => $this->getDataTable()->select()));
	}
	
	// C -Create
	public function createAction()
	{
		$form = new DelovodnikForm();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$form->setInputFilter(new DelovodnikFilter());
			$form->setData($request->getPost());
			 if ($form->isValid()) {
				$data = $form->getData();
				unset($data['submit']);	
				$this->getDataTable()->insert($data);
				
				//get id from data table, you are using it in create-success.phtml
				$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
				$sql = "SELECT max(id) as maxid from data"; 
				$statement = $adapter->query($sql); 
				$results= $statement->execute();
				$row = $results->current();
				$new_id = $row['maxid'];
				$data['id']=$new_id;
				$view = new ViewModel(array('data'=>$data));
    			$view->setTemplate('delovodnik/delovodnik/create-success.phtml');
			    return $view;
			}
		}		
		return new viewModel(array('form' => $form));
	}
	
	// U -Update
	public function updateAction()
	{
		$id = $this->params()->fromRoute('id');
		if (!$id) return $this->redirect()->toRoute('delovodnik/default', array('controller' => 'Delovodnik', 'action' => 'index'));
		$form = new DelovodnikForm();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$form->setInputFilter(new DelovodnikFilter());
			$form->setData($request->getPost());
			 if ($form->isValid()) {
				$data = $form->getData();
				unset($data['submit']);
				$this->getDataTable()->update($data, array('id' => $id));
				return $this->redirect()->toRoute('delovodnik/default', array('controller' => 'Delovodnik', 'action' => 'index'));													
			}			 
		}
		else {
			$form->setData($this->getDataTable()->select(array('id' => $id))->current());			
		}
		return new ViewModel(array('form' => $form, 'id' => $id));		
	}
	
		
	public function getDataTable()
	{
		//If $this->usersTable is no already iniciated, inciate new $this->usersTable
		if (!$this->dataTable) {
			$this->dataTable = new TableGateway(
				'data', 
				$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
			);
		}
		return $this->dataTable;
	}
	// R -retrieve 	CRUD
	public function  ViewAction()
	{
		//Get data from data table
		$uploadTable = $this->getServiceLocator()->get('DataTable');
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('data');
		$select->where(array('id' => $this->params()->fromRoute('id')));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		$returnArray = array();
		foreach ($results as $result) {
			$returnArray[] = $result;
		}

		//display files if any

		$uploadTable = $this->getServiceLocator()->get('UploadTable');
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('uploads');
		$select->where(array('data_table_id' => $this->params()->fromRoute('id')));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		//echo "number of results: ".$results->count(); 
		if($results->count() == 0)
		{
			$there_are_files = false;
			$files = false;
		}
		else
		{
			$there_are_files = true;
			$files = array();
			foreach ($results as $result) 
			{
				$files[] = $result;
			}
		}

		return new ViewModel(array('rowset' => $returnArray,'files' => $files,'there_are_files' =>$there_are_files));
	}

}