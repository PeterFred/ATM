<?php
namespace agilman\a2\model;
session_start();

/**
 * Class UserCollectionModel
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class UserCollectionModel extends Model
{
    /**
     * @var array of all userID's
     */
    private $userIds;


    /**
     * @return bool
     * Check to see if user is still logged in
     */
    public function getLoggedStatus()
    {
        return (new LoginModel())->checkLoggedState();
    }


    /**
     * UserCollectionModel constructor.
     * Check user is logged in
     * Retrieve user IDs from database and load into array.
     *
     */
    public function __construct()
    {
        parent::__construct();
        if ((new LoginModel())->checkLoggedState()) {
            if (!$result = $this->db->query("SELECT `id` FROM `user`;")) {
                error_log("Error loading user");
            }
            $this->userIds = array_column($result->fetch_all(), 0);
            $_SESSION['timeout'] = time();
        }
    }

    /**
     * Get account collection
     *
     * @return \Generator|UserModel[] Users
     */
    public function getUsers()
    {
        foreach ($this->userIds as $id) {
            // Use a generator to save on memory/resources
            // load users from DB one at a time only when required
            yield (new UserModel())->load($id);
        }
    }
}
