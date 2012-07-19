<?php
/**
 *  MongoRandomElements.class.php
 *  File with the class used to generate random elements and save then in MongoDB (users, URL's ...)
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page {@link http://www.ciges.net}
 *  @license http://www.gnu.org/copyleft/gpl.html GNU GPLv3
 *  @version 20120718
 *
 *  @package InternetAccessLog
 *  @filesource
 */
require_once("RandomElements.class.php");
 /**
 *  This class is used to generate random elements (users, IP's and URL's) and save them into MongoDB
 *  With this elements created we can simulate non FTP and FTP log entries (in our demo the acces by FTP are stored in a separate collection)
 */
 class MongoRandomElements extends RandomElements	{
 
    /**#@+
	 * Default names for random data collections
     */
	const RNDUSERSC_NAME = "Random_UsersList";
	const RNDIPSC_NAME = "Random_IPsList";
	const RNDDOMAINSC_NAME = "Random_DomainsList";
    /**#@-*/
    
    /**#@+
     * Constants for default connection values
     */
    const DEFAULT_USER = "mongodb";
    const DEFAULT_PASSWORD = "mongodb";
    const DEFAULT_HOST = "localhost";
    const DEFAULT_DB = "InternetAccessLog";
	/**#@-*/
    
    /**
     * Connection to the database
     * @access private
     * @var Mongo
     */
    private $db_conn;
    /**
     * Database name
     * @access private
     * @var Mongo
     */
    private $db_databasename;
    
    /**#@+
     * Number of element of each created collection in MongoDB (for cache purposes)
     * @access private
     * @var string
     */
	private $rnd_users_number;
	private $rnd_ips_number;
	private $rnd_domains_number;
    /**#@-*/
    
    /**
     *  Constructor. For creating an instance we need to pass all the parameters for the MongoDB database where the data will be stored (user, password, host & database name).
     *  <ul>
     *  <li>The default user and password will be mongodb
     *  <li>The default host will be localhost
     *  <li>The default database name will be InternetAccessLog
     *  </ul>
     * @param string $user
     * @param string $password
     * @param string $host
     * @param string $database
	*/
	function __construct($user = self::DEFAULT_USER, $password = self::DEFAULT_PASSWORD, $host = self::DEFAULT_HOST, $database = self::DEFAULT_DB)	{		
		// Open a connection to MongoDB
		try {
			$this->db_conn = new Mongo("mongodb://".$user.":".$password."@".$host."/".$database);
            $this->db_databasename = $database;
		}
		catch (MongoConnectionException $e) {
			die("Connection to random database in MongoDB not possible: (".$e->getCode().") ".$e->getMessage()."\n");
		}
		
		// Stores the number of elements of each stored random elements collection
        $db = $this->db_databasename;
        $userscol_name = self::RNDUSERSC_NAME;
        $ipscol_name = self::RNDIPSC_NAME;
        $domainscol_name = self::RNDDOMAINSC_NAME;
		$this->rnd_users_number =  $this->db_conn->$db->$userscol_name->count();
		$this->rnd_ips_number = $this->db_conn->$db->$ipscol_name->count();
		$this->rnd_domains_number = $this->db_conn->$db->$domainscol_name->count();
	}

    /**
     *  Destructor. Close the open connection to MongoDB database
 	 */
	function __destruct()	{
		$this->db_conn->close();
	}
    
    /**
     *  Save random users in MongoDB.  
     *  The parameters are the number of users to create and if we want an unique index to be created for the user name (default is TRUE)
     *  The id will be autonumeric (1, 2, 3 ....)
	 *  @param integer $number
     *  @param boolean $use_index
     */
	function createUsers($number, $use_index = TRUE)	{
		$id = $this->rnd_users_number + 1;   // Autonumeric
        
        $db = $this->db_databasename;      
        $col_name = self::RNDUSERSC_NAME;
        $col = $this->db_conn->$db->$col_name;
        
        if ($use_index) {
            $col->ensureIndex(array('user' => 1), array("unique" => true));	// Unique index for the 'user' field
        }
        $i = 1;
		while ($i <= $number)	{
			$user = $this->getRandomUser();
			if (!$col->findOne(array("user" => $user))) {
				try {
					$col->insert(array("_id" => $id, "user" => $user));
                    $id++;
				}
				catch (MongoConnectionException $e) {
					die("Save of User document in MongoDB not possible: (".$e->getCode().") ".$e->getMessage()."\n");
				}
				$i++;
			}
		}
        $this->rnd_users_number = $col->count();
	}
    
    /**
     *  Returns true if the random users collection has at least one user
     *  @return boolean
     */
	function randomUsers_exists()	{
        $db = $this->db_databasename;
        $col = self::RNDUSERSC_NAME;
        $this->db_conn->$db->$col->count() > 0 ? true : false;
	}

    /**
     *  Save random IPs in MongoDB
     *  The parameters are the number of IPs to create and if we want an unique index to be created for the IP name (default is TRUE)
     *  The id will be autonumeric (1, 2, 3 ....)
     *  @param integer $number
     *  @param boolean $use_index
     */
	function createIPs($number, $use_index = TRUE)	{
		$id = $this->rnd_ips_number + 1;   // Autonumeric
        
        $db = $this->db_databasename;      
        $col_name = self::RNDIPSC_NAME;
        $col = $this->db_conn->$db->$col_name;
        
        if ($use_index) {
            $this->mngrnd_ips->ensureIndex(array('ip' => 1), array("unique" => true));	// Unique index for the 'ip' field
        }
        $i = 1;
		while ($i <= $number)	{
			$ip = $this->getRandomIP();
			if (!$col->findOne(array("ip" => $ip))) {
				try {
					$col->insert(array("_id" => $id, "ip" => $ip));
                    $id++;
				}
				catch (MongoConnectionException $e) {
					die("Save of IP document in MongoDB not possible: (".$e->getCode().") ".$e->getMessage()."\n");
				}
				$i++;
			}
		}	
        $this->rnd_ips_number = $col->count();
	}	
    
    /**
     *  Returns true if the IPs collection has at least one IP
     *  @return boolean
     */
	function randomIPs_exists()	{
        $db = $this->db_databasename;
        $col = self::RNDIPSC_NAME;
        $this->db_conn->$db->$col->count() > 0 ? true : false;
	}

    /**
     *  Save random domains in MongoDB
     *  The parameter are the number of domains to create and if we want an unique index to be created for the domain name (default is TRUE)
     *  The id will be autonumeric (1, 2, 3 ....)
     */
	function createDomains($number)	{
        $id = $this->rnd_domains_number + 1;   // Autonumeric
        
		$db = $this->db_databasename;      
        $col_name = self::RNDDOMAINSC_NAME;
        $col = $this->db_conn->$db->$userscol_name;
        
        if ($use_index) {
            $col->ensureIndex(array('domain' => 1), array("unique" => true));	// Unique index for the 'domain' field
        }
        $i = 1;
		while ($i <= $number)	{
			$domain = $this->getRandomDomain();
			if (!$col->findOne(array("domain" => $domain))) {
				try {
					$col->insert(array("_id" => $id, "domain" => $domain));
                    $id++;
				}
				catch (MongoConnectionException $e) {
					die("Save of domain document in MongoDB not possible: (".$e->getCode().") ".$e->getMessage()."\n");
				}
				$i++;
			}
		}
        $this->rnd_domains_number = $col->count();
	}
    
    /**
     *  Returns true if the random domains collection has at least one domain
     *  @return boolean
     */
	function randomDomains_exists()	{
		$db = $this->db_databasename;
        $col = self::RNDDOMAINSC_NAME;
        $this->db_conn->$db->$col->count() > 0 ? true : false;
	}

    /**
     *  Returns a random IP from the generated collection
     *  @returns string
     */
	function searchIP()	{
    	$position = mt_rand(1, $this->rnd_ips_number);
        $db = $this->db_databasename;
        $col = self::RNDIPSC_NAME;
        $result = $this->db_conn->$db->$col->findOne(array("_id" => $position));
		return $result["ip"];
	}
    
    /**
     *  Returns a random user from the generated collection
     *  @return string
     */
	function searchUser()	{
		$position = mt_rand(1, $this->rnd_users_number);
        $db = $this->db_databasename;
        $col = self::RNDUSERSC_NAME;
        $result = $this->db_conn->$db->$col->findOne(array("_id" => $position));
		return $result["user"];
	}
    
    /**
     *  Returns a random HTTP method from the generated collection
     *  @returns string
     */
	function searchHTTPMethod()	{
		return $this->getRandomHTTPMethod();
	}
    
    /**
     *  Returns a random FTP method from the generated collection
     *  @returns string
     */
	function searchFTPMethod()	{
		return $this->getRandomFTPMethod();
	}
	
    /**
     *  Returns a random domain
     *  @returns string
     */
	function searchDomain() {
        $position = mt_rand(1, $this->rnd_domains_number);
        $db = $this->db_databasename;
        $col = self::RNDDOMAINSC_NAME;
        $result = $this->db_conn->$db->$col->findOne(array("_id" => $position));
		return $result["domain"];
	}
	
    /**
     *  Returns a random URI
     *  @returns string
     */
	function searchURI()	{
        return $this->getRandomString(mt_rand(0,100));
	}

    /**
     *  Returns a random size
     *  @returns integer
     */
	function searchSize()	{
		return $this->getRandomSize();
	}

    /**
     *  Returns a random protocol
     *  @returns string
     */
	function searchProtocol()	{
		return $this->getRandomProtocol();
	}
	
    /**
     *  Returns a random return code
     *  @returns integer
     */
	function searchReturnCode()	{
		return $this->getRandomRetourCode();
	}
 
    /**
     *  Return a random log entry for non FTP access (http and tunnel)
     *  It has two optional arguments, initial and final timestamps, if we want to get a random time in log entry created
     *  @param integer $initial_timestamp
     *  @param integer $final_timestamp
     *  @returns array
     */
	function getRandomNonFTPLogEntry()	{
		if (func_num_args() == 2)	{	
			$initial_timestamp = func_get_arg(0);
			$final_timestamp =  func_get_arg(1);
			$ts = mt_rand($initial_timestamp, $final_timestamp);
		}
		elseif (func_num_args() != 0)	{
			$arguments = func_get_args();
			die("Incorrect arguments number in getRrandomSORLogEntry function: ".implode(" ", $arguments)."\n");
		}
		else {
			$ts = time();
		}
		
		$document = array(
			'clientip' => $this->searchIP(),
			'user' => $this->searchUser(),
			'datetime' => $ts,
			'method' => $this->searchHTTPMethod(),
			'protocol' => $this->searchProtocol(),
			'domain' => $this->searchDomain(),
			'uri' => $this->searchURI(),
			'return_code' => $this->searchReturnCode(),
			'size' => $this->searchSize()	// Size is recorded in the database as string
		);
        
        return $document;
    }

}
 
?>