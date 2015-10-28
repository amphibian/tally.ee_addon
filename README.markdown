**Tally is a simple plugin for ExpressionEngine 1, 2, and 3 which allows you to calculate totals and averages for numeric values in an entries loop.** A common use for this would be calculating order totals for the month from your e-commerce orders channel, or adding other financial data spreadsheet-style.

##Usage

Tally has three tags: `{exp:tally:add}`, `{exp:tally:total}` and `{exp:tally:average}`.

Run `{exp:tally:add}` wherever you'd like to add a number to the ongoing total for a particular collection. The tag accepts three required parameters:

- `collection` - the name of the collection of values you're adding
- `value` - the number you're adding to the collection (can be positive or negative)
- `count` - in most cases this should be the `{count}` variable from your entries loop. It's required to make each tag unique, or else `{exp:tally:add}` tags which are adding identical amounts to the same collection in a loop will be skipped due to EE's internal caching. As an alternative, you can add `random="random"` to the tag to prevent this caching.

--

The `{exp:tally:total}` and `{exp:tally:average}` tags must be included on your page in an embedded template. This allows it to be processed last, after all of your entries loops have run, saving all of your values.

These tags accept four parameters:

- `collection` - the name of the collection whose values you want to add or average
- `decimals` - the number of decimal places to display (defaults to 2)
- `point` - character to use as a decimal separator (defaults to ".")
- `thousands` - character to use as a thousands separator (defaults to ",")

If used as single tags, these two tags will directly return your total or average. If used as a tag pair, the value will be returned in the `{tally_total}` or `{tally_average}` variables respectively.

--

Here's an example of using Tally to display a total of orders placed on your e-commerce EE site:

	{exp:channel:entries channel="orders" year="2010" month="12"}
		{exp:tally:add collection="orders" value="{order_subtotal}" count="{count}"}
		{exp:tally:add collection="shipping" value="{order_shipping}" count="{count}"}
	{/exp:channel:entries}

	{embed="reports/_total"}

The contents of the **reports/_total** template would be:

	Subtotal for month: ${exp:tally:total collection="orders" decimals="2"}
	Shipping for month: ${exp:tally:total collection="shipping" decimals="2"}
	
##Compatibility
	
Tally has been tested with ExpressionEngine version 1.7.0 and 2.1.3.