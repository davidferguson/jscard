<?php

function encrypt($unencrypted)
{
	$encrypted = md5(md5(base64_encode(md5("random text" . $unencrypted))));
	return $encrypted;
}

?>