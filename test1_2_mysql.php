<?php
/**
 *  Test 1.2:  MongoDB insertion WITHOUT indexes and WITHOUT verifying if the IP is already in the table
 *  In this test 70.000 random IPs are generated and saved in MongoDB
 *  Note that most of time the script is generating the random data!
 *
 *  @author Jos� Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120720
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$mre = new MySQLRandomElements();
$mre->createIPs(70000, FALSE, FALSE);

 
?>
