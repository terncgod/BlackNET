<?php

class Settings
{

    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSettings($id)
    {
        $this->db->query("SELECT * FROM settings WHERE id = :id");

        $this->db->bind(":id", $id, PDO::PARAM_INT);

        if ($this->db->execute()) {
            return $this->db->single();
        }
    }

    public function updateSettings($id, $recaptchaprivate, $recaptchapublic, $recaptchastatus, $panel_status)
    {
        $this->db->query("UPDATE settings SET
        recaptchaprivate = :private,
        recaptchapublic = :public,
        recaptchastatus = :status,
        panel_status = :pstatus
        WHERE id = :id");

        $this->db->bind(":private", $recaptchaprivate, PDO::PARAM_STR);
        $this->db->bind(":public", $recaptchapublic, PDO::PARAM_STR);
        $this->db->bind(":status", $recaptchastatus, PDO::PARAM_STR);
        $this->db->bind(":pstatus", $panel_status, PDO::PARAM_STR);
        $this->db->bind(":id", $id, PDO::PARAM_INT);

        if ($this->db->execute()) {

            return 'Settings Updated';

        }
    }
}
