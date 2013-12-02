<?php
session_start();
function get_data($data) {
    $dbh = new PDO("sqlite:data/datastore.sqlite");
    $IDq = $dbh->query("SELECT value FROM settings WHERE option = " . $dbh->quote($data));
    $IDq->setFetchMode(PDO::FETCH_ASSOC);
    $IDf = $IDq->fetch();
    $output = $IDf['value'];
    return $output;
}
function get_page_content($data) {
    $dbh = new PDO("sqlite:data/datastore.sqlite");
    $IDq = $dbh->query("SELECT * FROM pages WHERE title = " . $dbh->quote($data));
    $IDq->setFetchMode(PDO::FETCH_ASSOC);
    $IDf = $IDq->fetch();
    $output = $IDf['text'];
    return $output;
}
?>
