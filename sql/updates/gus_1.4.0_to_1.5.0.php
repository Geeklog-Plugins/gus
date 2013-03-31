<?php

// add a table for ignoring referrers
$_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_referrer']} (
  referrer VARCHAR(128) NOT NULL default '',
  PRIMARY KEY  (referrer)
)";
