<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
    This file is part of Tally add-on for ExpressionEngine.

    Tally is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Tally is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    Read the terms of the GNU General Public License
    at <http://www.gnu.org/licenses/>.
    
    Copyright 2015 Derek Hogue
*/

class Tally
{
	function __construct(){}
	
	function add()
	{
		$collection = ee()->TMPL->fetch_param('collection');
		$value = ee()->TMPL->fetch_param('value');
		if($collection != '' && $value != '')
		{
			ee()->session->cache['tally'][$collection][] = $this->_float($value);			
		}
	}
	
	function total()
	{
		// This tag must be called from an embedded template
		$collection = ee()->TMPL->fetch_param('collection');
		$decimals = ee()->TMPL->fetch_param('decimals', 2);
		$point = ee()->TMPL->fetch_param('point', '.');
		$thousands = ee()->TMPL->fetch_param('thousands', ',');
		
		if(isset($collection) && isset(ee()->session->cache['tally'][$collection]))
		{
			$total = number_format(
				array_sum(ee()->session->cache['tally'][$collection]), 
				$decimals, 
				$point, 
				$thousands
			);
			if(empty(ee()->TMPL->tagdata))
			{
				return $total;
			}
			else
			{
				return ee()->TMPL->parse_variables_row(ee()->TMPL->tagdata, array('tally_total' => $total));
			}
		}
	}

	function average()
	{
		// This tag must be called from an embedded template
		$collection = ee()->TMPL->fetch_param('collection');
		$decimals = ee()->TMPL->fetch_param('decimals', 2);
		$point = ee()->TMPL->fetch_param('point', '.');
		$thousands = ee()->TMPL->fetch_param('thousands', ',');
		
		if(isset($collection) && isset(ee()->session->cache['tally'][$collection]))
		{
			$average = number_format((array_sum(ee()->session->cache['tally'][$collection])/count(ee()->session->cache['tally'][$collection])), $decimals, $point, $thousands);
			if(empty(ee()->TMPL->tagdata))
			{
				return $average;
			}
			else
			{
				return ee()->TMPL->parse_variables_row(ee()->TMPL->tagdata, array('tally_average' => $average));
			}
		}
	}
	
	function _float($str)
	{
		// http://www.php.net/manual/en/function.floatval.php#43262
		if(strstr($str, ","))
		{ 
	    	$str = str_replace(".", "", $str);
	    	$str = str_replace(",", ".", $str);
	  	} 
	  
	  	if(preg_match("#((-)?[0-9\.]+)#", $str, $match))
	  	{  
	    	return floatval($match[0]); 
	  	}
	  	else
	  	{ 
	    	return floatval($str); 
	  	}
	}
	
}
