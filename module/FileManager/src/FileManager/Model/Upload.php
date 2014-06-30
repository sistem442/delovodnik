<?php
namespace FileManager\Model;

class Upload
{
    public $id;
    public $filename;
    public $label;
    public $data_id;

    public function setPassword($clear_password)
    {
        $this->password = md5($clear_password);
    }

	function exchangeArray($data)
	{
		$this->id		= (isset($data['id'])) ? $data['id'] : null;
		$this->server_file_name	= (isset($data['server_file_name'])) ? $data['server_file_name'] : null;
		$this->original_file_name	= (isset($data['original_file_name'])) ? $data['original_file_name'] : null;
		$this->label	= (isset($data['label'])) ? $data['label'] : null;
		$this->data_id	= (isset($data['data_table_id'])) ? $data['data_table_id'] : null;	
	}
	
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}	
}
