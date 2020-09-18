<?php
define("DRIVE_SEARCH_SUB_DIR","Anime");
define('DEFAULT_PROXY', "http://192.168.1.2:9050");
define('UPDATE_EXISTING',0);
define('FLUSH_DB',false);



define('THUMBNAILS_DIR',__DIR__."/thumbnails");
define("BEFORE_INDEX_QUERIES",
(
FLUSH_DB 
? 
"
DROP TABLE animelist;
Delete from animelist;
DELETE FROM SQLITE_SEQUENCE WHERE name='animelist';
"
:
"").
"CREATE TABLE IF NOT EXISTS 'animelist' (
	'id'	INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
	'title'	TEXT UNIQUE,
	'sec_title'	NUMERIC,
	'year'	INTEGER,
	'score'	REAL,
	'es_score'	REAL,
	'real_id'	INTEGER,
	'url'	TEXT,
	'path'	TEXT,
	'popularity'	INTEGER,
	'members'	INTEGER,
	'rank'	INTEGER,
	'favorites'	INTEGER,
	'synopsis'	TEXT,
	'added_time'	TEXT,
	'genres'	TEXT,
	'episodes'	INTEGER
);"
);

//die(BEFORE_INDEX_QUERIES);



