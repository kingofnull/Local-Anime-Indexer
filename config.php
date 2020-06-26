<?php
define("DRIVE_SEARCH_SUB_DIR","Anime");
define("BEFORE_INDEX_QUERIES",
"
CREATE TABLE IF NOT EXISTS 'animelist' (
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
	'genres'	TEXT
);
Delete from animelist;
DELETE FROM SQLITE_SEQUENCE WHERE name='animelist';
");