<?php
$userName = $_GET["username"];
$folderName = $_GET["foldername"];

if( $userName != "" && $folderName != "" )
{
	$dir = "users/" . $userName . "/" . $folderName . "/";
	if( is_dir($dir) )
	{
		if ($dh = opendir($dir))
		{
			while( ($file = readdir($dh)) !== false )
			{
				if( $file != "" && $file != "." && $file != ".." && $file != "..." )
				{
					echo '<option value="' . $dir . $file . '">' . $file . '</option>';
				}
			}
			closedir( $dh );
		}
		else
		{
			echo "jsCard Internal Error";
		}
	}
	else
	{
		echo "jsCard Directory Error";
	}
}
else
{
	echo "jsCard Internal Server Error";
}
?>