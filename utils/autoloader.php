<?php

return function($className) {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $ds = DIRECTORY_SEPARATOR;
	include_once __DIR__ .$ds.'..'.$ds.$className.'.php';
};