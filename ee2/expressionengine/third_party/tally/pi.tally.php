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
    
    Copyright 2011 Derek Hogue
*/

$plugin_info = array(
	'pi_name' => 'Tally',
	'pi_version' => '1.0.4',
	'pi_author' => 'Derek Hogue',
	'pi_author_url' => 'http://amphibian.info',
	'pi_description' => 'Tally or average numbers in an entries loop.',
	'pi_usage' => Tally::usage()
);

class Tally
{
	function __construct()
	{
		$this->EE =& get_instance();
	}

	
	function add()
	{
		$collection = $this->EE->TMPL->fetch_param('collection');
		$value = $this->EE->TMPL->fetch_param('value');
		if($collection != '' && $value != '')
		{
			$this->EE->session->cache['tally'][$collection][] = $this->_float($value);			
		}
	}
	
	
	function total()
	{
		// This tag must be called from an embedded template
		$collection = $this->EE->TMPL->fetch_param('collection');
		$decimals = $this->EE->TMPL->fetch_param('decimals', 2);
		$point = $this->EE->TMPL->fetch_param('point', '.');
		$thousands = $this->EE->TMPL->fetch_param('thousands', ',');
		
		if(isset($collection) && isset($this->EE->session->cache['tally'][$collection]))
		{
			$total = number_format(
				array_sum($this->EE->session->cache['tally'][$collection]), 
				$decimals, 
				$point, 
				$thousands
			);
			if(empty($this->EE->TMPL->tagdata))
			{
				return $total;
			}
			else
			{
				return $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, array('tally_total' => $total));
			}
		}
	}

	function average()
	{
		// This tag must be called from an embedded template
		$collection = $this->EE->TMPL->fetch_param('collection');
		$decimals = $this->EE->TMPL->fetch_param('decimals', 2);
		$point = $this->EE->TMPL->fetch_param('point', '.');
		$thousands = $this->EE->TMPL->fetch_param('thousands', ',');
		
		if(isset($collection) && isset($this->EE->session->cache['tally'][$collection]))
		{
			$average = number_format((array_sum($this->EE->session->cache['tally'][$collection])/count($this->EE->session->cache['tally'][$collection])), $decimals, $point, $thousands);
			if(empty($this->EE->TMPL->tagdata))
			{
				return $average;
			}
			else
			{
				return $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, array('tally_average' => $average));
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

	static function usage() {
  		ob_start(); 
	?>

	Tally has three tags: {exp:tally:add}, {exp:tally:total} and {exp:tally:average}.
	
	Run {exp:tally:add} wherever you'd like to add a number to the ongoing total for a particular collection. The tag accepts three required parameters:
	
	"collection" - the name of the collection of values you're adding
	"value" - the number you're adding to the collection (can be positive or negative)
	"count" - in most cases this should be the {count} variable from your entries loop. It's required to make each tag unique, or else {exp:tally:add} tags which are adding identical amounts to the same collection in a loop will be skipped due to EE's internal caching. As an alternative, you can add 'random="random" to the tag to prevent this caching.
	
	--
	
	The {exp:tally:total} and {exp:tally:average} tags must be included on your page in an embedded template. This allows them to be processed last, after all of your entries loops have run, saving all of your values.
	
	These tags accepts four parameters:
	
	"collection" - the name of the collection whose values you want to add or average
	"decimals" - the number of decimal places to display (defaults to 2)
	"point" - character to use as a decimal separator (defaults to ".")
	"thousands" - character to use as a thousands separator (defaults to ",")
	
	--
	
	Here's an example of using Tally to display a total of orders placed on your e-commerce EE site:
		
	{exp:channel:entries channel="orders" year="2010" month="12"}
		{exp:tally:add collection="orders" value="{order_subtotal}" count="{count}"}
		{exp:tally:add collection="shipping" value="{order_shipping}" count="{count}"}
	{/exp:channel:entries}
	
	{embed="reports/_total"}
	
	--
	
	The contents of the reports/_total template would be:
		
	Subtotal for month: ${exp:tally:total collection="orders" decimals="2"}
	Shipping for month: ${exp:tally:total collection="shipping" decimals="2"}
	
	--

<?php
      $buffer = ob_get_contents();

      ob_end_clean(); 

      return $buffer;	
	}

}