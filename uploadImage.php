<?php
$target_dir = "users/" . $_GET['uploadDir'];
$target_file = $target_dir . basename($_FILES["photos"]["name"][0]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

//Check to see if the file already exists
if (file_exists($target_file))
{
	echo "file-exists";
	$uploadOk = 0;
}

//Check file size
if ($_FILES["photos"]["size"] > 500000)
{
	echo "too-large";
	$uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if($uploadOk)
{
	if (move_uploaded_file($_FILES["photos"]["tmp_name"][0], $target_file))
	{
		echo "upload-success";
	}
	else
	{
		echo "server-error";
	}
}
?>