<?php
/**
 * Setting English Lexicon Entries for Polylang
 *
 * @package polylang
 * @subpackage lexicon
 */

$_lang['polylang_prop_id'] = 'Resource ID';
$_lang['polylang_prop_options'] = 'List of options for output, separated by commas.';
$_lang['polylang_prop_product'] = 'Product ID. If not specified, the id of the current document is used.';
$_lang['polylang_prop_mode'] = 'Type of output. Available values:dropdown;list.';
$_lang['polylang_prop_scheme'] = 'URL generation scheme.';
$_lang['polylang_prop_show_active'] = 'Show link of current language.';
$_lang['polylang_prop_active_class '] = 'Class for the current language.';
$_lang['polylang_prop_language_group'] = 'Language group';
$_lang['polylang_prop_css'] = 'If you want to use your own styles, specify the itinerary to them here or clear the parameter and upload them manually via the website template.';
$_lang['polylang_prop_js'] = 'If you want to use your own scripts, specify the itinerary to them here or clear the parameter and upload them manually via the website template.';
$_lang['polylang_prop_tpl'] = 'Name of a chunk serving as a resource template. If not provided, properties are dumped to output for each resource.';
$_lang['polylang_prop_cache'] = 'Caching the results of the snippet.';
$_lang['polylang_prop_cacheKey'] = 'The name of the key cache.';
$_lang['polylang_prop_context'] = 'Which Context should be searched in.';
$_lang['polylang_prop_depth'] = 'Integer value indicating depth to search for resources from each parent. First level of resources below parent has a depth of 1.';
$_lang['polylang_prop_forceXML'] = 'Force the output page as xml.';
$_lang['polylang_prop_hideUnsearchable'] = 'Do not display resources that are not searchable.';
$_lang['polylang_prop_includeTVs'] = 'An optional comma-delimited list of TemplateVar names to include.';
$_lang['polylang_prop_outputSeparator'] = 'An optional string to separate each tpl instance.';
$_lang['polylang_prop_parents'] = 'Comma-delimited list of ids serving as parents. Use "0" to ignore parents when specifying resources to include. Prefix an id of parent with a dash to exclude it and its children from the result.';
$_lang['polylang_prop_prepareTVs'] = 'Comma-separated list of TV names that need to be prepared. By default it is set to "1", so all TVs in "&includeTVs=``" will be prepared.';
$_lang['polylang_prop_processTVs'] = 'Comma-separated list of TV names that need to be processed. If you set it to "1" - all TVs in "&includeTVs=``" will be processed. By default it is empty.';
$_lang['polylang_prop_resources'] = 'Comma-delimited list of ids to include in the results. Prefix an id with a dash to exclude the resource from the result.';
$_lang['polylang_prop_showDeleted'] = 'If true, will show Resources regardless if they are deleted.';
$_lang['polylang_prop_showHidden'] = 'If true, will show Resources regardless if they are hidden from the menus.';
$_lang['polylang_prop_showUnpublished'] = 'If true, will also show Resources regardless if they are unpublished.';
$_lang['polylang_prop_sitemapSchema'] = 'Schema of sitemap.';
$_lang['polylang_prop_sortby'] = 'Any Resource Field (including Template Variables if they have been included) to sort by. Some common fields to sort on are publishedon, menuindex, pagetitle etc., but see the Resources documentation for all fields. Specify fields with the name only, not using the tag syntax. Note that when using fields like template, publishedby and the likes for sorting, it will be sorted on the raw values, so the template or user ID, and NOT their names. You can also sort randomly by specifying RAND().';
$_lang['polylang_prop_sortdir'] = 'Order which to sort by: descending or ascending';
$_lang['polylang_prop_templates'] = 'Comma-delimited list of templates to filter the results. Prefix an id of template with a dash to exclude the resource with it from the result.';
$_lang['polylang_prop_tplWrapper'] = 'Name of a chunk serving as a wrapper template for the output.';
$_lang['polylang_prop_useWeblinkUrl'] = 'If WebLinks are used in the output, script will output the link specified in the WebLink instead of the normal MODX link. To use the standard display of WebLinks (like any other Resource) set this to 0.';
$_lang['polylang_prop_where'] = 'A JSON-style expression of criteria to build any additional where clauses from.';
$_lang['polylang_prop_trigger'] = 'The class name of the language switching link.';