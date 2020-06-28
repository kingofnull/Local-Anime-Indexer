<?php
include __DIR__."/core.php";    

// print_r(get("ifconfig.co/ip"));    
	
// exit();	
	
$fso = new COM('Scripting.FileSystemObject');
$D = $fso->Drives;
// $type = array("Unknown", "Removable", "Fixed", "Network", "CD-ROM", "RAM Disk");
$drives = $fso->Drives;
//$dO->DriveType

# https://api.jikan.moe/v3/search/anime?q=Fate/Zero&page=1
# https://jikan.docs.apiary.io/#reference/0/search
// $pdo->query("");
// $pdo->query("");
foreach( explode(";",BEFORE_INDEX_QUERIES) as $q) {
	if(trim($q)!=""){
		$pdo->query($q.";");
	}
}

foreach($fso->Drives as $drive) {
    $drivePath = "{$drive->DriveLetter}:";
    $animeDir = "$drivePath\\".DRIVE_SEARCH_SUB_DIR;
    if (file_exists($animeDir)) {
		echo "Scanning $animeDir ... \n";
        $dir = new DirectoryIterator($animeDir);
        foreach($dir as $fileinfo) {
            $name=$fileinfo->getFilename();
            if (!$fileinfo->isDot() && !in_array(strtolower($name),["watched","fonts"])){
                
                fetchByNameQuery($name,"$animeDir\\$name");
				sleep(2);
            }
        }
		echo "\n---------------------------\n";
		

    }
}

