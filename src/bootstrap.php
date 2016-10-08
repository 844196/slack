<?php

spl_autoload_register(function ($className) {
	require preg_replace('/\\\/', '/', $className) . '.php';
});
