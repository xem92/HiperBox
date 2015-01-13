<?php

class Filter
{
	public function filterGet($var)
	{
		return filter_input(INPUT_GET, $var);
	}
}
?>