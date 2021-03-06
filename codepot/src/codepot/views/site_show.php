<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="<?php print base_url_make('/js/codepot.js')?>"></script>
<link type="text/css" rel="stylesheet" href="<?php print base_url_make('/css/common.css')?>" />
<link type="text/css" rel="stylesheet" href="<?php print base_url_make('/css/site.css')?>" />
<link type="text/css" rel="stylesheet" href="<?php print base_url_make('/css/font-awesome.min.css')?>" />

<script type="text/javascript" src="<?php print base_url_make('/js/creole.js')?>"></script>

<script type="text/javascript" src="<?php print base_url_make('/js/prettify/prettify.js')?>"></script>
<script type="text/javascript" src="<?php print base_url_make('/js/prettify/lang-css.js')?>"></script>
<script type="text/javascript" src="<?php print base_url_make('/js/prettify/lang-lisp.js')?>"></script>
<script type="text/javascript" src="<?php print base_url_make('/js/prettify/lang-lua.js')?>"></script>
<script type="text/javascript" src="<?php print base_url_make('/js/prettify/lang-sql.js')?>"></script>
<script type="text/javascript" src="<?php print base_url_make('/js/prettify/lang-vb.js')?>"></script>

<script type="text/javascript" src="<?php print base_url_make('/js/jquery.min.js')?>"></script>
<script type="text/javascript" src="<?php print base_url_make('/js/jquery-ui.min.js')?>"></script>
<link type="text/css" rel="stylesheet" href="<?php print base_url_make('/css/jquery-ui.css')?>" />

<script type="text/javascript">
function render_wiki()
{
	creole_render_wiki (
		"site_show_result_wiki_text",
		"site_show_result_wiki",
		"<?php print site_url()?>/site/wiki/",
		"<?php print site_url()?>/site/image/",
		false
	);

	prettyPrint ();
}
</script>

<?php
?>
<title><?php print htmlspecialchars($site->name)?> (<?php print $site->id?>)</title>
</head>

<body onload="render_wiki()">

<div class="content" id="site_show_content">

<!---------------------------------------------------------------------------->

<?php $this->load->view ('taskbar'); ?>

<!---------------------------------------------------------------------------->

<?php

if ($login['sysadmin?'])
{
	$ctxmenuitems = array (
		//array ("site/create", '<i class="fa fa-plus"></i> ' . $this->lang->line('New')),
		array ("site/update/{$site->id}", '<i class="fa fa-edit"></i> ' . $this->lang->line('Edit')),
		array ("site/delete/{$site->id}", '<i class="fa fa-trash"></i> ' . $this->lang->line('Delete'))
	);
}
else $ctxmenuitems = array ();

$this->load->view (
	'projectbar',
	array (
		'banner' => $this->lang->line('Administration'),

		'page' => array (
			'type' => 'site',
			'id' => 'catalog',
			'site' => $site,
		),

		'ctxmenuitems' => $ctxmenuitems
	)
);
?>

<!---------------------------------------------------------------------------->

<div class="mainarea" id="site_show_mainarea">

<div class="title">
<?php print htmlspecialchars($site->name)?> (<?php print htmlspecialchars($site->id)?>)
</div>

<div id="site_show_result" class="codepot-relative-container-view">
	<div id="site_show_result_wiki" class="codepot-styled-text-view">
	<pre id="site_show_result_wiki_text" style="visibility: hidden">
	<?php print htmlspecialchars($site->text); ?>
	</pre>
	</div> <!-- site_show_result_wiki -->
</div>

<!----------------------------------------------------------->

</div> <!-- site_show_mainarea -->

<div class='codepot-footer-pusher'></div> <!-- for sticky footer -->

</div> <!-- site_show_content -->

<?php $this->load->view ('footer'); ?>


</body>
</html>
