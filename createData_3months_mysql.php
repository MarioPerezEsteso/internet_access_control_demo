<?php
/**
 *  Example data insertion in MySQL
 *  In this test 70.000 random users, 70.000 random IPs and 1.300.000 domains are generated and saved in MySQL.
 *  With this elements 90 million of non FTP log entries and 4,5 million of FTP entries (with raports by month and day) are created simulting the estimated volume data for 3 months
 *  Note that most of time the script is generating the random data!
 *
 *  @author Jos� Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20130313;
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$mre = new MySQLRandomElements();
$mre->createUsers(70000);
echo "Random users created\n";
$mre->createIPs(70000);
echo "Random IPs created\n";
$mre->createDomains(1300000);
echo "Random Domains created\n";
$mre->createURIs(2000000);
echo "Random URIs created\n";

echo "Loading data in RAM ... ";
$mre->loadDataInRAM();
echo "OK\n";

// Example data for April
$start = mktime(0,0,0,4,1,2012);
$end = mktime(23,59,0,4,30,2012);
for ($i = 0; $i < 30000000; $i++)	{
    $log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntry($log);
}
for ($i = 0; $i < 1500000; $i++)	{
    $log = $mre->getRandomFTPLogEntry($start, $end);
    $mre->saveRandomFTPLogEntry($log);
}
echo "Example data for April created\n";
// Example data for May
$start = mktime(0,0,0,5,1,2012);
$end = mktime(23,59,0,5,31,2012);
for ($i = 0; $i < 30000000; $i++)	{
    $log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntry($log);
}
for ($i = 0; $i < 1500000; $i++)	{
    $log = $mre->getRandomFTPLogEntry($start, $end);
    $mre->saveRandomFTPLogEntry($log);
}
echo "Example data for May created\n";
// Example data for June
$start = mktime(0,0,0,6,1,2012);
$end = mktime(23,59,0,6,30,2012);
for ($i = 0; $i < 30000000; $i++)	{
    $log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntry($log);
}
for ($i = 0; $i < 1500000; $i++)	{
    $log = $mre->getRandomFTPLogEntry($start, $end);
    $mre->saveRandomFTPLogEntry($log);
}
echo "Example data for June created\n";

// Index creation on data
echo "Creating indexes on log tables ... ";
$db = $mre->getDB();
$query = "alter table ".MySQLRandomElements::FTPLOG_NAME." add index (clientip), add index (user), add index (datetime), add index (domain)";
$db->query($query) || die ("Index creation: error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
$query = "alter table ".MySQLRandomElements::NONFTPLOG_NAME." add index (clientip), add index (user), add index (datetime), add index (domain)";
$db->query($query) || die ("Index creation: Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
echo "OK\n";

?>
