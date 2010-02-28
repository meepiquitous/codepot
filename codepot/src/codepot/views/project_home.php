<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/common.css" />
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/project.css" />
<script type="text/javascript" src="<?=base_url()?>/js/creole.js"></script>

<script type="text/javascript">
function render_wiki() 
{
	creole_render_wiki (
		"project_home_textpre", 
		"project_home_textarea", 
		"<?=dirname(dirname(dirname(current_url())))?>/wiki/show/<?=$project->id?>/"
	);
}
</script>

<title><?=htmlspecialchars($project->name)?></title>
</head>

<body onLoad="render_wiki()">

<div class="content" id="project_home_content">


<!---------------------------------------------------------------------------->

<?php $this->load->view ('taskbar'); ?>

<!---------------------------------------------------------------------------->

<?php 
$this->load->view (
	'projectbar', 
	array (
		'site' => NULL,
		'pageid' => 'project',
		'ctxmenuitems' => array (
			array ("project/update/{$project->id}", $this->lang->line('Edit')),
			array ("project/delete/{$project->id}", $this->lang->line('Delete'))
		)
	)
); 
?>

<!---------------------------------------------------------------------------->

<div class="sidebar" id="project_home_sidebar">

<div class="box">
<ul>
<li><?=$this->lang->line('Created on')?> <?=$project->createdon?></li>
<li><?=$this->lang->line('Last updated on')?> <?=$project->updatedon?></li>
<li><?=$this->lang->line('Last updated by')?> <?=$project->updatedby?></li>
</ul>
</div>

<div class="box">
<div class="boxtitle"><?=$this->lang->line('Members')?></div>
<ul>
<?php
	$members = preg_split ('/[[:space:],]+/', $project->members);
	$member_count = count ($members);
	$members = array_unique ($members);
	$priority = 0;
	for ($i = 0; $i < $member_count; $i++)
	{
		if (!array_key_exists($i, $members)) continue;

		$m = $members[$i];
		if ($m == '') continue;
		print "<li>{$m}</li>";
	}
?>
</ul>
</div>

<div class="box">
<div class="boxtitle"><?=$this->lang->line('Repository')?></div>
<?= anchor ($this->converter->expand(CODEPOT_SVN_BASE_URL, $_SERVER) . '/' . $project->id) ?>
</div>

<div class="box">
<div class="boxtitle">
<?= anchor ("source/history/{$project->id}", $this->lang->line('Code changes')) ?>
</div>
<table id="project_home_mainarea_sidebar_svn_commits_table">
<?php 
	$xdot = $this->converter->AsciiToHex ('.');
	foreach ($svn_commits as $commit)
	{
		print '<tr class="odd">';
		print '<td>';
		print substr($commit['svn_time'], 0, 10);
		print '</td>';

		print '<td>';
		print anchor (	
			"/source/revision/{$commit['svn_repo']}/{$xdot}/{$commit['svn_rev']}", 
			$commit['svn_rev']);
		print '</td>';

		print '<td>';
		print htmlspecialchars ($commit['svn_author']);
		print '</td>';
		print '</tr>';

		print '<tr class="even">';
		print '<td colspan=3>';
		$sm = strtok (trim ($commit['svn_message']), "\r\n");
		print htmlspecialchars ($sm);
		print '</td>';
		print '</tr>';
	}
?>
</table>
</div>

</div> <!-- project_home_sidebar -->

<!---------------------------------------------------------------------------->

<div class="mainarea" id="project_home_mainarea">

<div class="title">
<?=htmlspecialchars($project->name)?>
</div>

<div id="project_home_textarea">
<pre id="project_home_textpre" style="visibility: hidden">
<?php print htmlspecialchars($project->description); ?>
</pre>
</div> <!-- project_home_textarea -->

</div> <!-- project_home_mainarea -->


<!---------------------------------------------------------------------------->

<?php $this->load->view ('footer'); ?>

<!---------------------------------------------------------------------------->
 
</div> <!--  project_home_content -->

</body>

</html>

