<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="keywords" content="<?php print $project->id?>" />
<meta name="description" content="<?php print htmlspecialchars($project->summary)?>" />

<script type="text/javascript" src="<?php print base_url_make('/js/codepot.js')?>"></script>
<link type="text/css" rel="stylesheet" href="<?php print base_url_make('/css/common.css')?>" />
<link type="text/css" rel="stylesheet" href="<?php print base_url_make('/css/project.css')?>" />
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
		"project_home_result_wiki_text", 
		"project_home_result_wiki", 
		"<?php print site_url()?>/wiki/show/<?php print $project->id?>/",
		"<?php print site_url()?>/wiki/attachment0/<?php print $project->id?>/",
		false
	);

	prettyPrint ();

	$("#project_home_sidebar_info_box").accordion ({
		collapsible: true,
		heightStyle: "content"
	});

<?php if ($total_open_issue_count > 0): ?>
	$("#project_home_sidebar_issue_box").accordion ({
		collapsible: true,
		heightStyle: "content"
	});
<?php endif; ?>

	$("#project_home_sidebar_member_box").accordion ({
		collapsible: true,
		heightStyle: "content"
	});

	$("#project_home_sidebar_repo_box").accordion ({
		collapsible: true,
		heightStyle: "content"
	});

	$("#project_home_sidebar_log_box").accordion ({
		collapsible: true,
		heightStyle: "content"
	});

	$("#project_home_sidebar_log_all_button").button ().click (function () {
		$(location).attr ('href', codepot_merge_path("<?php print site_url(); ?>", "/project/log/" + "<?php print $project->id; ?>" + "/" + "<?php print $this->converter->AsciiToHex(''); ?>" + "/"));
		return false;
	});
}

$(function() {
	render_wiki ();

	
});
</script>

<title><?php print htmlspecialchars($project->name)?></title>
</head>

<body>

<div class="content" id="project_home_content">

<!-- /////////////////////////////////////////////////////////////////////// -->

<?php $this->load->view ('taskbar'); ?>

<!-- /////////////////////////////////////////////////////////////////////// -->

<?php 
$this->load->view (
	'projectbar', 
	array (
		'banner' => NULL,

		'page' => array (
			'type' => 'project',
			'id' => 'project',
			'project' => $project,
		),

		'ctxmenuitems' => array (
			array ("project/create", '<i class="fa fa-plus"></i> ' . $this->lang->line('New')),
			array ("project/update/{$project->id}", '<i class="fa fa-edit"></i> ' . $this->lang->line('Edit')),
			array ("project/delete/{$project->id}", '<i class="fa fa-trash"></i> ' . $this->lang->line('Delete'))
		)
	)
); 
?>

<!-- /////////////////////////////////////////////////////////////////////// -->
<div class="codepot-sidebar" id="project_home_sidebar">

<div id="project_home_sidebar_info_box" class="collapsible-box">
	<div id="project_home_sidebar_info_header" class="collapsible-box-header"><?php print $this->lang->line('Summary')?></div>

	<ul id="project_home_sidebar_info_list" class="collapsible-box-list">
	<li><?php print $this->lang->line('Created on')?> <?php print codepot_dbdatetodispdate($project->createdon);?></li>
	<li><?php print $this->lang->line('Created by')?> <?php print $project->createdby;?></li>
	<li><?php print $this->lang->line('Last updated on')?> <?php print codepot_dbdatetodispdate($project->updatedon);?></li>
	<li><?php print $this->lang->line('Last updated by')?> <?php print $project->updatedby?></li>
	</ul>
</div>

<?php if ($total_open_issue_count > 0): ?>
<div id="project_home_sidebar_issue_box" class="collapsible-box">
	<div id="project_home_sidebar_issue_header" class="collapsible-box-header"><?php print $this->lang->line('Issues')?></div>

	<ul id="project_home_issue_stat_list" class="collapsible-box-list">
	<li><a href='<?php print site_url() . "/issue/home/{$project->id}"; ?>'><?php printf ($this->lang->line('FMT_TOTAL_OPEN_ISSUES_X'), $total_open_issue_count); ?></a></li>
	<li><a href='<?php print site_url() . "/issue/home/{$project->id}"; ?>'><?php printf ($this->lang->line('FMT_YOUR_OPEN_ISSUES_X'), $your_open_issue_count); ?></a></li>
	</ul>
</div>
<?php endif; ?>

<div id="project_home_sidebar_member_box" class="collapsible-box">
<div id="project_home_sidebar_member_header" class="collapsible-box-header"><?php print $this->lang->line('Members')?></div>
<ul id="project_home_sidebar_member_list" class="collapsible-box-list">
<?php
	$members = $project->members;
	$member_count = count($members);
	$members = array_unique ($members);
	$priority = 0;

	$icons = $this->projects->getUserIcons($members);
	if ($icons === FALSE) $icons = array(); // can't get the icon array for members.

	for ($i = 0; $i < $member_count; $i++)
	{
		if (!array_key_exists($i, $members)) continue;

		$m = $members[$i];
		if ($m == '') continue;

		/*
		$icon_src = '';
		if (array_key_exists($m, $icons))
		{
			// old browsers don't support image data URI.
			$icon_path = CODEPOT_USERICON_DIR . '/' . $icons[$m];
			$icon_image = @file_get_contents($icon_path);
			if ($icon_image)
			{
				$icon_src = sprintf (
					'<img class="user_icon_img" src="data:%s;base64,%s" alt="" /> ',
					mime_content_type($icon_path),
					base64_encode($icon_image)
				);
			}
		}

		print "<li>{$icon_src}{$m}</li>";
		*/
		$user_icon_url = codepot_merge_path (site_url(), '/user/icon/' . $this->converter->AsciiToHex($m));
		//print "<li><img src='{$user_icon_url}' class='user_icon_img' />{$m}</li>";
		$user_home_url = "/user/home/" . $this->converter->AsciiToHex($m);
		print "<li>";
		print anchor ($user_home_url,  "<img src='{$user_icon_url}' class='user_icon_img' />{$m}");
		print "</li>";

	}
?>
</ul>
</div>

<div id="project_home_sidebar_repo_box" class="collapsible-box">
<div id="project_home_sidebar_repo_header" class="collapsible-box-header"><?php print $this->lang->line('Repository')?></div>
<ul id="project_home_sidebar_repo_list" class="collapsible-box-list">
<?php
$urls = explode (',', CODEPOT_SVN_BASE_URL);
foreach ($urls as $url)
{
	$url = trim($url);
	if ($url == '') continue;
	print '<li>';
	print anchor ($this->converter->expand($url,$_SERVER) . "/{$project->id}/");
	print '</li>';
}
?>
</ul>
</div>

<div id="project_home_sidebar_log_box" class="collapsible-box">
<div id="project_home_sidebar_log_header" class="collapsible-box-header">
<span><?php print $this->lang->line('Change log'); ?></span>
<span id="project_home_sidebar_log_all_span"><a href='#' id="project_home_sidebar_log_all_button"><?php print $this->lang->line('All'); ?></a></span>
</div>

<?php 
	print '<div id="project_home_sidebar_log_table_container" class="collapsible-box-panel">';
	print '<table id="project_home_sidebar_log_table" class="collapsible-box-table codepot-full-width-table">';

	if (count($log_entries) > 0)
	{
		$xdot = $this->converter->AsciiToHex ('.');
		foreach ($log_entries as $log)
		{
			$createdon = codepot_dbdatetodispdate ($log['createdon'], 'Y-m-d H:i');

			if ($log['type'] == 'code')
			{
				$x = $log['message'];
				print '<tr class="odd">';
				print '<td class="date">';
				
				print $createdon;
				print '</td>';
				print '<td class="object">';
				print anchor (	
					"code/revision/{$x['repo']}/{$xdot}/{$x['rev']}", 
					"#R{$x['rev']}");
				print '</td>';

				print '</tr>';

				print '<tr class="even">';

				print '<td colspan="2" class="details">';
				print '<span class="description">';
				if ($log['action'] == 'revpropchange')
				{
					$fmt = $this->lang->line ('MSG_LOG_REVPROP_CHANGE_BY');
					print htmlspecialchars (sprintf($fmt, $x['propname'], $x['author']));
				}
				else
				{
					$fmt = $this->lang->line (
						'MSG_LOG_'.strtoupper($log['action']).'_BY');
					print htmlspecialchars (sprintf($fmt, $x['author']));
				}
				print '</span>';

				if ($log['action'] != 'revpropchange')
				{
					print '<div class="codepot-plain-text-view">';
					print '<pre>';
					$sm = strtok (trim ($x['message']), "\r\n");
					print htmlspecialchars ($sm);
					print '</pre>';
					print '</div>';
				}
				print '</td>';
				print '</tr>';
			}
			else
			{
				print '<tr class="odd">';
				print '<td class="date">';
				print $createdon;
				print '</td>';

				print '<td class="object">';
				$uri = '';
				if ($log['type'] == 'project')
				{
					$uri = "/project/home/{$log['projectid']}";
					$trimmed = preg_replace("/(.{20}).+/u", "$1…", $log['message']);
				}
				else if ($log['type'] == 'wiki')
				{
					$hex = $this->converter->AsciiToHex ($log['message']);
					$uri = "/wiki/show_r/{$log['projectid']}/{$hex}";
					$trimmed = preg_replace("/(.{20}).+/u", "$1…", $log['message']);
				}
				else if ($log['type'] == 'file')
				{
					$hex = $this->converter->AsciiToHex ($log['message']);
					$uri = "/file/show/{$log['projectid']}/{$hex}";
					$trimmed = preg_replace("/(.{20}).+/u", "$1…", $log['message']);
				}
				else if ($log['type'] == 'issue')
				{
					$hex = $this->converter->AsciiToHex ($log['message']);
					$uri = "/issue/show/{$log['projectid']}/{$hex}";
					//$trimmed =  $this->lang->line('Issue') . " {$log['message']}";
					$trimmed = "#I{$log['message']}";
				}

				if ($uri != '')
					print anchor ($uri, htmlspecialchars($trimmed));
				else
					print htmlspecialchars($trimmed);
				print '</td>';

				print '</tr>';

				print '<tr class="even">';
				print '<td colspan="2" class="details">';
				print '<span class="description">';
				$fmt = $this->lang->line (
					'MSG_LOG_'.strtoupper($log['action']).'_BY');
				print htmlspecialchars (sprintf($fmt, $log['userid']));
				print '</span>';
				print '</td>';

				print '</tr>';
			}
		}
	}
	else
	{
		printf ('<tr><td>%s</td></tr>', $this->lang->line('PROJECT_MSG_NO_CHANGE_LOG'));
	}

	print '</table>';
	print '</div>';
?>
</div>

</div> <!-- project_home_sidebar -->

<!-- /////////////////////////////////////////////////////////////////////// -->

<div class="mainarea" id="project_home_mainarea">

<div class="title">
<?php print htmlspecialchars($project->name)?>
</div>

<div id="project_home_result" class="codepot-static-container-view">
	<div id="project_home_result_wiki" class="codepot-styled-text-view">
		<pre id="project_home_result_wiki_text" style="visibility: hidden"><?php print htmlspecialchars($project->description); ?></pre>
	</div> <!-- project_home_result_wiki -->
</div>

<?php if (isset($login['id']) && $login['id'] != ''): ?>
<!--
<div id='project_home_new_form'>
	<div style='line-height: 2em;'>
		<?php/*
		print form_dropdown (
			'project_home_new_type', 
			$project_type_array,
			set_value('project_home_new_type', ''),
			'id="project_home_new_type"'
		);
		print ' ';

		$tmpmemb = array();
		foreach ($project->members as $m) $tmpmemb[$m] = $m;
		print form_dropdown (
			'project_home_new_owner', 
			$tmpmemb,
			set_value('project_home_new_owner', (in_array($login['id'], $project->members)? $login['id']: '')),
			'id="project_home_new_owner"'
		);*/
		?>

		<input type='text' id='project_home_new_summary' name='project_home_new_summary' size='50' placeholder='<?php print $this->lang->line('Summary'); ?>'/>
	</div>

	<div id='project_home_new_description_tabs' style='width:100%;'>
		<ul>
			<li><a href='#project_home_new_description_input'><?php print $this->lang->line('Description'); ?></a></li>
			<li><a href='#project_home_new_description_preview'><?php print $this->lang->line('Preview'); ?></a></li>
		</ul>

		<div id='project_home_new_description_input'>
			<textarea type='textarea' id='project_home_new_description' name='project_home_new_description' rows=24 cols=100 style='width:100%;'></textarea>

			<div style='margin-top: 0.1em;'>
			<?php print $this->lang->line('Attachments'); ?>
			<input type='file' id='project_home_new_files' name='project_home_new_files' multiple='' autocomplete='off' style='color: transparent;' />
			<table id='project_home_new_file_table'></table>
			</div>
		</div>
		<div id='project_home_new_description_preview' class='codepot-styled-text-preview'>
		</div>
	</div>
</div>
-->
<?php endif; ?>

<div id='project_home_alert'></div>

</div> <!-- project_home_mainarea -->


<div class='codepot-footer-pusher'></div> <!-- for sticky footer -->

</div> <!--  project_home_content -->

<!-- /////////////////////////////////////////////////////////////////////// -->

<?php $this->load->view ('footer'); ?>

<!-- /////////////////////////////////////////////////////////////////////// -->
 

</body>

</html>

