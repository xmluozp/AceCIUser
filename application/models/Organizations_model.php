<?php
class Organizations_model extends CI_Model {

	private static $db;
	private static $mainTableName;

	public function __construct()
	{
		parent::__construct();
		self::$db = &get_instance()->db;
		self::$mainTableName = "organizations";
	}

	//=============================== basic CRUD below
	public function create($data)
	{
		return self::$db->insert(self::$mainTableName, $data);
	}

	public function read($id)
	{
		$query = self::$db->get_where(self::$mainTableName , array('user_id' => $id));
		self::$db->where('is_deleted', false);
		return $query->result_array();
	}

	public function update($id, $data)
	{
		self::$db->where('organization_id', $id);
		return self::$db->update(self::$mainTableName, $data);
	}

	public function delete($id)
	{
		self::$db->set('is_deleted', TRUE);
		self::$db->where('organization_id', $id);
		return self::$db->update(self::$mainTableName);
	}

	//=============================== basic CRUD above
	public function read_list_dropdown()
	{
		self::$db->where('is_deleted', false);
		self::$db->select('organization_id AS id');
		self::$db->select('organization_name AS value');

		return self::$db->get(self::$mainTableName)->result();
	}

	public function read_list_as_level_dropdown($organization_id, $user_group_level)
	{

		self::$db->where('is_deleted', false);
		if($user_group_level < ADMINISTRATOR)
		{
			self::$db->where('organization_id', $organization_id);
		}
		self::$db->select('organization_id AS id');
		self::$db->select('organization_name AS value');


		return self::$db->get(self::$mainTableName)->result();
	}

}
