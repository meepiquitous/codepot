<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/common.css" />
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/user.css" />

<!--
<script type="text/javascript" src="<?=base_url()?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>/js/jquery-ui.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/jquery-ui.css" />
-->

<script type="text/javascript">
$(function () {
	$('#user_settings_mainarea_result').accordion();
});
</script>

<title><?=htmlspecialchars($login['id'])?></title>
</head>

<body>

<div class="content" id="user_settings_content">

<!---------------------------------------------------------------------------->

<?php $this->load->view ('taskbar'); ?>

<!---------------------------------------------------------------------------->

<?php
$user->id = $login['id'];

$this->load->view (
        'projectbar',
        array (
		'banner' => NULL,

		'page' => array (
			'type' => 'user',
			'id' => 'issues',
			'user' => $user,
		),

                'ctxmenuitems' => array ()
        )
);
?>

<!---------------------------------------------------------------------------->

<div class="mainarea" id="user_settings_mainarea">

<?php
	if ($message != '') 
		print "<div id='user_settings_mainarea_message' class='form_message'>$message</div>";
?>

<div id="user_settings_mainarea_result">

<?=form_open('user/settings/')?>

	<?=form_fieldset($this->lang->line('Code'))?>

		<?=form_checkbox('code_hide_line_num', 
			'Y', $settings->code_hide_line_num == 'Y')
		?>
		<?= $this->lang->line('USER_MSG_HIDE_LINE_NUMBER')?>

		<?=form_checkbox('code_hide_details',
			'Y', $settings->code_hide_details == 'Y')
		?>
		<?= $this->lang->line('USER_MSG_HIDE_DETAILS')?>
	
	<?=form_fieldset_close()?>

	<!--
	<?=form_fieldset($this->lang->line('Issue'))?>
	<?=form_fieldset_close()?>
	-->

	<div class="buttons">
		<?=form_submit('settings', $this->lang->line('OK'))?>
	</div>

<?=form_close();?>


</div> <!-- user_settings_mainarea_result -->

</div> <!-- user_settings_mainarea -->

<!---------------------------------------------------------------------------->

<?php $this->load->view ('footer'); ?>

<!---------------------------------------------------------------------------->


</div> <!-- user_settings_content -->

</body>
</html>