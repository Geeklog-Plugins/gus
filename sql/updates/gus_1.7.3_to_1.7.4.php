<?php

// Reduce column size to support UTF8mb4 index size max (which is 191 characters)
$_SQL[] = "ALTER TABLE {$_TABLES['gus_userstats']} CHANGE `referer` `referer` VARCHAR(191) NULL DEFAULT NULL;";
$_SQL[] = "ALTER TABLE {$_TABLES['gus_ignore_page']} CHANGE `page` `page` VARCHAR(191) NULL DEFAULT '';";

?>
