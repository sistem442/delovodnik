<?php
namespace FileManager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Http\Headers;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

use FileManager\Model\Upload;
use FileManager\Model\UploadTable;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

use ZendPdf;
use ZendPdf\PdfDocument;

class FileManagerController extends AbstractActionController
{
	public function getFileUploadLocation()
	{
		// Fetch Configuration from Module Config
	    	$config  = $this->getServiceLocator()->get('config');
	    	return $config['module_config']['upload_location'];
	}

	public function processUploadAction()
	{		 
		$request = $this->getRequest();
		
		//get number files for loop
		$number_of_files = $this->getRequest()->getPost('number_of_files');
		//echo $number_of_files;
		$i = 0;
		
		//for rsponse to view
		$message = '';

		while($i < $number_of_files){
			if ($request->isPost()) {
				$upload = new Upload();
				$uploadFile = $this->params()->fromFiles('file'.$i);

				// Fetch Configuration from Module Config
				$uploadFolder = $this->getFileUploadLocation();
				
				$i = 0;
				$fileName = time();
				$fileName = $fileName + rand(10000,99999); 
				while($i<$number_of_files){
					$label = $this->getRequest()->getPost($i.'label');
					//echo $label;
					$fileType =  substr(strrchr($_FILES[$i]['name'],'.'), 1);
					//echo $fileType;
					$path = $uploadFolder.'/'.$fileName.".".$fileType;
					$server_file_name = $fileName.".".$fileType;
					$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
					$sql = new Sql($adapter);
					$insert = $sql->insert('uploads');
					$newData = array(
					'label'=> $label,
					'data_table_id'=> $this->params()->fromRoute('id'),
					'original_file_name' =>$_FILES[$i]['name'],
					'server_file_name' => $server_file_name,
					);
					//print_r($newData);
					$insert->values($newData);
					$selectString = $sql->getSqlStringForSqlObject($insert);
					$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
					$results = $adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
					//sacuvaj fajl
					$copy = move_uploaded_file($_FILES[$i]['tmp_name'], $path);
					if($copy)
					{
						$message =	$message."Fajl ".$_FILES[$i]['name']." je uspesno uploudovan!</br>";
						$color = 'green';
					}
					else
					{
						$message = $message."Fajl ".$_FILES[$i]['name']." nije uspesno uploudovan!!</br>";
						$color = 'red';
					}
					$i++;
					$fileName++;
				};//end of while
				
			}
		}//end of while
		return new ViewModel(array('data_table_id'=> $this->params()->fromRoute('id'), 'message'=> $message, 'color' => $color));
	}

	public function editAction()
	{
		$uploadTable = $this->getServiceLocator()->get('UploadTable');
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('uploads');
		$select->where(array('data_table_id' => $this->params()->fromRoute('id')));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		//echo "number of results".$results->count(); 
		if($results->count() == 0)
		{
			$view = new ViewModel(array('data_table_id'=>$this->params()->fromRoute('id')));
    		$view->setTemplate('file-manager/file-manager/add-files.phtml'); 
    		return $view;
		}
		else
		{
			$returnArray = array();
			foreach ($results as $result) {
				$returnArray[] = $result;
			}
			//print_r($returnArray);
			return new ViewModel(array('files' => $returnArray));
		}
	}
    
	public function deleteAction()
	{
		$uploadId = $_POST["upload_id"];
		
		
		$uploadTable = $this->getServiceLocator()->get('UploadTable');
		$upload = $uploadTable->getUpload($uploadId);
		//print_r($upload);
		$uploadPath    = $this->getFileUploadLocation();
		//echo $uploadPath ."/".$upload->server_file_name;
		// Remove File
			unlink($uploadPath. DIRECTORY_SEPARATOR.$upload->server_file_name);    	
		// Delete Records
		$uploadTable->deleteUpload($uploadId);

		echo '{"id": "'.$uploadId.'"}';
		die;
		/**/
	}
	
	public function downloadAction() {
		$uploadId = $this->params()->fromRoute('id');
		$uploadTable = $this->getServiceLocator()->get('UploadTable');
		$upload = $uploadTable->getUpload($uploadId);

		// Fetch Configuration from Module Config
		$uploadPath    = $this->getFileUploadLocation();
		$file = file_get_contents($uploadPath . DIRECTORY_SEPARATOR . $upload->server_file_name);

		// Directly return the Response 
		$response = $this->getEvent()->getResponse();
		$response->getHeaders()->addHeaders(array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment;filename="' .$upload->server_file_name . '"',
		));
		$response->setContent($file);

		return $response;	
	}
	public function init()
	{
		$config = $this->getServiceLocator()->get('Config');
		$fileManagerDir = $config['module_config']['upload_location'];
		$this->_dir = realpath($fileManagerDir) . DIRECTORY_SEPARATOR;
	}
	public function viewAction()
	{
		$this->init();
		
		//get name of file	
		$uploadId = urldecode($this->params()->fromRoute('id'));
		$uploadTable = $this->getServiceLocator()->get('UploadTable');
		$upload = $uploadTable->getUpload($uploadId);
		
		//build path
		$filename = $this->_dir . $upload->server_file_name;
		$contents = null;
		if (file_exists($filename)) {
			$handle = fopen($filename, "r"); // "r" - not r but b for Windows "b" - keeps giving me errors no file
			$contents = fread($handle, filesize($filename));
			fclose($handle);
		}
		$file_type =  pathinfo($filename, PATHINFO_EXTENSION);
		if($file_type == 'pdf')
		{
			$pdf = ZendPdf\PdfDocument::load($filename);
			//print_r($pdf);
			header('Content-type: application/pdf');
			echo $pdf->render();		
		}
		else if($file_type == 'jpg' ||	$file_type == 'jpeg' || $file_type == 'png' || $file_type == 'pjpeg'){
			return new ViewModel(array('contents' => $contents));
		}
		else echo 'Nije moguće pogledati fajl!';
	}
}

