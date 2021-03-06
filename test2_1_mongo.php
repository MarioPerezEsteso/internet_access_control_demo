<?php
/**
 *  Test 1.1:  MongoDB insertion with indexes and verifying if the user name is already in the table
 *  In this test 70.000 random users are generated and saved in MongoDB
 *  Note that most of time the script is generating the random data!
 *
 *  @author Jos� Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120719
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");

$mre = new MongoRandomElements();
$mre->createUsers(70000);
 
?>
