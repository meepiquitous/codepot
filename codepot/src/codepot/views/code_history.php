<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link type="text/css" rel="stylesheet" href="<?=base_url_make('/css/common.css')?>" />
<link type="text/css" rel="stylesheet" href="<?=base_url_make('/css/code.css')?>" />

<title><?=htmlspecialchars($project->name)?></title>
</head>

<body>

<div class="content" id="code_history_content">

<!---------------------------------------------------------------------------->

<?php $this->load->view ('taskbar'); ?>

<!---------------------------------------------------------------------------->

<?php
$this->load->view (
	'projectbar',
	array (
		'banner' => NULL,

		'page' => array (
			'type' => 'project',
			'id' => 'code',
			'project' => $project,
		),

		'ctxmenuitems' => array ()
	)
);
?>

<!---------------------------------------------------------------------------->

<div class="mainarea" id="code_history_mainarea">

<div class="title" id="code_history_mainarea_title">
<?php
	if ($revision <= 0)
	{
		$revreq = '';
		$revreqroot = '';
	}
	else
	{
		$revreq = "/{$revision}";
		$revreqroot = '/' . $this->converter->AsciiToHex('.') . $revreq;
	}

	// print the anchor for the root nolder with a project name
	print anchor (
		"code/history/{$project->id}{$revreqroot}",
		htmlspecialchars($project->name));

	// explodes part of the full path name into an array 
	$exps = explode ('/', $fullpath);
	$expsize = count($exps);
	$par = '';
	// print anchors pointing to each part
	for ($i = 1; $i < $expsize; $i++)
	{
		print '/';
		$par .= '/' . $exps[$i];
		$xpar = $this->converter->AsciiToHex ($par);
		print anchor (
			"code/history/{$project->id}/{$xpar}{$revreq}",
			htmlspecialchars($exps[$i]));
	}
?>
</div>

<div class="menu" id="code_history_mainarea_menu">
</div> <!-- code_history_mainarea_menu -->

<div id="code_history_mainarea_result">

<?php
	$xfullpath = $this->converter->AsciiToHex (($fullpath == '')? '.': $fullpath);

	$graph_url = site_url() . "/code/graph/commits-by-users/{$project->id}/{$xfullpath}";
	print "<img src='{$graph_url}' />";

	$graph_url = site_url() . "/code/graph/commit-share-by-users/{$project->id}/{$xfullpath}";
	print "<img src='{$graph_url}' />";
?>

<table id="code_history_mainarea_result_table">
<tr class='heading'>
	<th><?=$this->lang->line('Revision')?></th>
	<th><?=$this->lang->line('Commiter')?></th>
	<th><?=$this->lang->line('Date')?></th>
	<th><?=$this->lang->line('Message')?></th>
	<?php if ($file['type'] == 'file' || $file['type'] == 'dir') print '<th></th>'; ?>
</tr>
<?php 
	$rowclasses = array ('even', 'odd');
	$history = $file['history'];
	$history_count = count($history);	
	$curfullpath = $fullpath;
	for ($i = $history_count; $i > 0; )
	{
		$h = $history[--$i];

		$rowclass = $rowclasses[($history_count - $i) % 2];
		print "<tr class='{$rowclass}'>";

		print '<td>';
		$xfullpath = $this->converter->AsciiToHex (
			($fullpath == '')? '.': $fullpath);

		print anchor ("code/file/{$project->id}/{$xfullpath}/{$h['rev']}", $h['rev']);
		print '</td>';

		print '<td>';
		// Repository migration from googlecode reveales that it did not put 
		// 'author' for initial project creation. So I've added the following check.
		if (array_key_exists('author', $h)) print htmlspecialchars($h['author']);
		print '</td>';

		print '<td><code>';
		//print date('r', strtotime($h['date']));
		print date('Y-m-d', strtotime($h['date']));
		print '</code></td>';

		print '<td>';
		print '<pre>';
		print htmlspecialchars($h['msg']);
		print '</pre>';
		print '</td>';


		if ($file['type'] == 'file')
		{
			print '<td>';
			print anchor ("code/blame/{$project->id}/{$xfullpath}/{$h['rev']}", 
				$this->lang->line('Blame'));
			print ' ';
			print anchor ("code/diff/{$project->id}/{$xfullpath}/{$h['rev']}", 
				$this->lang->line('Difference'));
			print '</td>';
		}
		else if ($file['type'] == 'dir')
		{
			print '<td>';
			print anchor ("code/revision/{$project->id}/{$xfullpath}/{$h['rev']}", 
				$this->lang->line('Details'));
			print '</td>';
		}

		print '</tr>';

		//
		// let's track the copy path.
		//
		$paths = $h['paths'];
		$colspan = ($file['type'] == 'file' || $file['type'] == 'dir')? 5: 4;
		foreach ($paths as $p)
		{
			if (array_key_exists ('copyfrom', $p) && 
			    $p['action'] == 'A')
			{
				$d = $curfullpath;
				$f = '';

				while ($d != '/' && $d != '')
				{
					if ($d == $p['path'])
					{
						$curfullpath = $p['copyfrom'] . $f;
						print "<tr class='title'><td colspan='{$colspan}'>{$curfullpath}</td></tr>";
						break;
					}

					$d = dirname ($d);
					$f = substr ($curfullpath, strlen($d));
				}
			}
		}

	}
?>
</table>
</div> <!-- code_history_mainarea_body -->

</div> <!-- code_history_mainarea -->


<!---------------------------------------------------------------------------->


<?php $this->load->view ('footer'); ?>

<!---------------------------------------------------------------------------->

</div> <!--  code_history_content -->

</body>

</html>

