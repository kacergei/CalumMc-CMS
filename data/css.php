<?php

include_once ('../includes/xtemplate.class.php');

$xtpl = new XTemplate('css.xtpl');

// Assign the data to whatever you call the {randomstuff} things.


// Get block data and assign it.
$dbh = new PDO("sqlite:datastore.sqlite");
$IDq = $dbh->query("SELECT * FROM css");
$rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);
foreach ($rowarray as $css) {
  $xtpl->assign($css[id], $css[value]);
}
$xtpl->assign("n", "");
$xtpl->parse('main');
$xtpl->parse('css');
$xtpl->out('css');   
$xtpl->out('main');   

?>
