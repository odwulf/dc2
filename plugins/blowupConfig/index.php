<?php
# -- BEGIN LICENSE BLOCK ---------------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2013 Olivier Meunier & Association Dotclear
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK -----------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }

require dirname(__FILE__).'/lib/class.blowup.config.php';

$can_write_images = blowupConfig::canWriteImages();
$can_write_css = blowupConfig::canWriteCss();

if ($core->error->flag()) {
		$notices = $core->error->toHTML();
		$core->error->reset();
}

$blowup_base = array(
	'body_bg_c' => null,
	'body_bg_g' => 'light',

	'body_txt_f' => null,
	'body_txt_s' => null,
	'body_txt_c' => null,
	'body_line_height' => null,

	'top_image' => 'default',
	'top_height' => null,
	'uploaded' => null,

	'blog_title_hide' => null,
	'blog_title_f' => null,
	'blog_title_s' => null,
	'blog_title_c' => null,
	'blog_title_a' => null,
	'blog_title_p' => null,

	'body_link_c' => null,
	'body_link_f_c' => null,
	'body_link_v_c' => null,

	'sidebar_position' => null,
	'sidebar_text_f' => null,
	'sidebar_text_s' => null,
	'sidebar_text_c' => null,
	'sidebar_title_f' => null,
	'sidebar_title_s' => null,
	'sidebar_title_c' => null,
	'sidebar_title2_f' => null,
	'sidebar_title2_s' => null,
	'sidebar_title2_c' => null,
	'sidebar_line_c' => null,
	'sidebar_link_c' => null,
	'sidebar_link_f_c' => null,
	'sidebar_link_v_c' => null,

	'date_title_f' => null,
	'date_title_s' => null,
	'date_title_c' => null,

	'post_title_f' => null,
	'post_title_s' => null,
	'post_title_c' => null,
	'post_comment_bg_c' => null,
	'post_comment_c' => null,
	'post_commentmy_bg_c' => null,
	'post_commentmy_c' => null,

	'prelude_c' => null,
	'footer_f' => null,
	'footer_s' => null,
	'footer_c' => null,
	'footer_l_c' => null,
	'footer_bg_c' => null,

	'extra_css' => null
);

$blowup_user = $core->blog->settings->themes->blowup_style;

$blowup_user = @unserialize($blowup_user);
if (!is_array($blowup_user)) {
	$blowup_user = array();
}

$blowup_user = array_merge($blowup_base,$blowup_user);

$gradient_types = array(
	__('Light linear gradient') => 'light',
	__('Medium linear gradient') => 'medium',
	__('Dark linear gradient') => 'dark',
	__('Solid color') => 'solid'
);

$top_images = array(__('Custom...') => 'custom');
$top_images = array_merge($top_images,array_flip(blowupConfig::$top_images));


if (!empty($_POST))
{
	try
	{
		$blowup_user['body_txt_f'] = $_POST['body_txt_f'];
		$blowup_user['body_txt_s'] = blowupConfig::adjustFontSize($_POST['body_txt_s']);
		$blowup_user['body_txt_c'] = blowupConfig::adjustColor($_POST['body_txt_c']);
		$blowup_user['body_line_height'] = blowupConfig::adjustFontSize($_POST['body_line_height']);

		$blowup_user['blog_title_hide'] = (integer) !empty($_POST['blog_title_hide']);
		$update_blog_title = !$blowup_user['blog_title_hide'] && (
			!empty($_POST['blog_title_f']) || !empty($_POST['blog_title_s']) ||
			!empty($_POST['blog_title_c']) || !empty($_POST['blog_title_a']) ||
			!empty($_POST['blog_title_p'])
		);

		if ($update_blog_title)
		{
			$blowup_user['blog_title_f'] = $_POST['blog_title_f'];
			$blowup_user['blog_title_s'] = blowupConfig::adjustFontSize($_POST['blog_title_s']);
			$blowup_user['blog_title_c'] = blowupConfig::adjustColor($_POST['blog_title_c']);
			$blowup_user['blog_title_a'] = preg_match('/^(left|center|right)$/',$_POST['blog_title_a']) ? $_POST['blog_title_a'] : null;
			$blowup_user['blog_title_p'] = blowupConfig::adjustPosition($_POST['blog_title_p']);
		}

		$blowup_user['body_link_c'] = blowupConfig::adjustColor($_POST['body_link_c']);
		$blowup_user['body_link_f_c'] = blowupConfig::adjustColor($_POST['body_link_f_c']);
		$blowup_user['body_link_v_c'] = blowupConfig::adjustColor($_POST['body_link_v_c']);

		$blowup_user['sidebar_text_f'] = $_POST['sidebar_text_f'];
		$blowup_user['sidebar_text_s'] = blowupConfig::adjustFontSize($_POST['sidebar_text_s']);
		$blowup_user['sidebar_text_c'] = blowupConfig::adjustColor($_POST['sidebar_text_c']);
		$blowup_user['sidebar_title_f'] = $_POST['sidebar_title_f'];
		$blowup_user['sidebar_title_s'] = blowupConfig::adjustFontSize($_POST['sidebar_title_s']);
		$blowup_user['sidebar_title_c'] = blowupConfig::adjustColor($_POST['sidebar_title_c']);
		$blowup_user['sidebar_title2_f'] = $_POST['sidebar_title2_f'];
		$blowup_user['sidebar_title2_s'] = blowupConfig::adjustFontSize($_POST['sidebar_title2_s']);
		$blowup_user['sidebar_title2_c'] = blowupConfig::adjustColor($_POST['sidebar_title2_c']);
		$blowup_user['sidebar_line_c'] = blowupConfig::adjustColor($_POST['sidebar_line_c']);
		$blowup_user['sidebar_link_c'] = blowupConfig::adjustColor($_POST['sidebar_link_c']);
		$blowup_user['sidebar_link_f_c'] = blowupConfig::adjustColor($_POST['sidebar_link_f_c']);
		$blowup_user['sidebar_link_v_c'] = blowupConfig::adjustColor($_POST['sidebar_link_v_c']);

		$blowup_user['sidebar_position'] = ($_POST['sidebar_position'] == 'left') ? 'left' : null;

		$blowup_user['date_title_f'] = $_POST['date_title_f'];
		$blowup_user['date_title_s'] = blowupConfig::adjustFontSize($_POST['date_title_s']);
		$blowup_user['date_title_c'] = blowupConfig::adjustColor($_POST['date_title_c']);

		$blowup_user['post_title_f'] = $_POST['post_title_f'];
		$blowup_user['post_title_s'] = blowupConfig::adjustFontSize($_POST['post_title_s']);
		$blowup_user['post_title_c'] = blowupConfig::adjustColor($_POST['post_title_c']);
		$blowup_user['post_comment_c'] = blowupConfig::adjustColor($_POST['post_comment_c']);
		$blowup_user['post_commentmy_c'] = blowupConfig::adjustColor($_POST['post_commentmy_c']);


		$blowup_user['footer_f'] = $_POST['footer_f'];
		$blowup_user['footer_s'] = blowupConfig::adjustFontSize($_POST['footer_s']);
		$blowup_user['footer_c'] = blowupConfig::adjustColor($_POST['footer_c']);
		$blowup_user['footer_l_c'] = blowupConfig::adjustColor($_POST['footer_l_c']);
		$blowup_user['footer_bg_c'] = blowupConfig::adjustColor($_POST['footer_bg_c']);


		$blowup_user['extra_css'] = blowupConfig::cleanCSS($_POST['extra_css']);

		if ($can_write_images)
		{
			$uploaded = null;
			if ($blowup_user['uploaded'] && is_file(blowupConfig::imagesPath().'/'.$blowup_user['uploaded'])) {
				$uploaded = blowupConfig::imagesPath().'/'.$blowup_user['uploaded'];
			}

			if (!empty($_FILES['upfile']) && !empty($_FILES['upfile']['name'])) {
				files::uploadStatus($_FILES['upfile']);
				$uploaded = blowupConfig::uploadImage($_FILES['upfile']);
				$blowup_user['uploaded'] = basename($uploaded);
			}

			$blowup_user['top_image'] = in_array($_POST['top_image'],$top_images) ? $_POST['top_image'] : 'default';

			$blowup_user['body_bg_c'] = blowupConfig::adjustColor($_POST['body_bg_c']);
			$blowup_user['body_bg_g'] = in_array($_POST['body_bg_g'],$gradient_types) ? $_POST['body_bg_g'] : '';
			$blowup_user['post_comment_bg_c'] = blowupConfig::adjustColor($_POST['post_comment_bg_c']);
			$blowup_user['post_commentmy_bg_c'] = blowupConfig::adjustColor($_POST['post_commentmy_bg_c']);
			$blowup_user['prelude_c'] = blowupConfig::adjustColor($_POST['prelude_c']);
			blowupConfig::createImages($blowup_user,$uploaded);
		}

		if ($can_write_css)
		{
			blowupConfig::createCss($blowup_user);
		}

		$core->blog->settings->addNamespace('themes');
		$core->blog->settings->themes->put('blowup_style',serialize($blowup_user));
		$core->blog->triggerBlog();

		dcPage::addSuccessNotice(__('Theme configuration has been successfully updated.'));
		http::redirect($p_url);
	}
	catch (Exception $e)
	{
		$core->error->add($e->getMessage());
	}
}
?>
<html>
<head>
  <title><?php echo __('Blowup configuration'); ?></title>
  <?php echo dcPage::jsLoad('index.php?pf=blowupConfig/config.js'); ?>
  <?php echo dcPage::jsColorPicker(); ?>
  <script type="text/javascript">
  //<![CDATA[
  <?php
  echo dcPage::jsVar('dotclear.blowup_public_url',blowupConfig::imagesURL());
  echo dcPage::jsVar('dotclear.msg.predefined_styles',__('Predefined styles'));
  echo dcPage::jsVar('dotclear.msg.apply_code',__('Apply code'));
  echo dcPage::jsVar('dotclear.msg.predefined_style_title',__('Choose a predefined style'));
  ?>
  //]]>
  </script>
</head>

<body>
<?php
echo dcPage::breadcrumb(
	array(
		html::escapeHTML($core->blog->name) => '',
		__('Blog appearance') => 'blog_theme.php',
		__('Blowup configuration') => ''
	)).dcPage::notices();

echo
'<p><a class="back" href="blog_theme.php">'.__('Back to Blog appearance').'</a></p>';


if (!$can_write_images) {
	dcPage::message(__('For the following reasons, images cannot be created. You won\'t be able to change some background properties.').
		$notices,false,true);
}

echo '<form id="theme_config" action="'.$p_url.'" method="post" enctype="multipart/form-data">';

echo '<div class="fieldset"><h3>'.__('Customization').'</h3>'.
'<h4>'.__('General').'</h4>';

if ($can_write_images) {
	echo
	'<p class="field"><label for="body_bg_c">'.__('Background color:').'</label> '.
	form::field('body_bg_c',7,7,$blowup_user['body_bg_c'],'colorpicker').'</p>'.

	'<p class="field"><label for="body_bg_g">'.__('Background color fill:').'</label> '.
	form::combo('body_bg_g',$gradient_types,$blowup_user['body_bg_g']).'</p>';
}

echo
'<p class="field"><label for="body_txt_f">'.__('Main text font:').'</label> '.
form::combo('body_txt_f',blowupConfig::fontsList(),$blowup_user['body_txt_f']).'</p>'.

'<p class="field"><label for="body_txt_s">'.__('Main text font size:').'</label> '.
form::field('body_txt_s',7,7,$blowup_user['body_txt_s']).'</p>'.

'<p class="field"><label for="body_txt_c">'.__('Main text color:').'</label> '.
form::field('body_txt_c',7,7,$blowup_user['body_txt_c'],'colorpicker').'</p>'.

'<p class="field"><label for="body_line_height">'.__('Text line height:').'</label> '.
form::field('body_line_height',7,7,$blowup_user['body_line_height']).'</p>'.

'<h4 class="border-top">'.__('Links').'</h4>'.
'<p class="field"><label for="body_link_c">'.__('Links color:').'</label> '.
form::field('body_link_c',7,7,$blowup_user['body_link_c'],'colorpicker').'</p>'.

'<p class="field"><label for="body_link_v_c">'.__('Visited links color:').'</label> '.
form::field('body_link_v_c',7,7,$blowup_user['body_link_v_c'],'colorpicker').'</p>'.

'<p class="field"><label for="body_link_f_c">'.__('Focus links color:').'</label> '.
form::field('body_link_f_c',7,7,$blowup_user['body_link_f_c'],'colorpicker').'</p>'.

'<h4 class="border-top">'.__('Page top').'</h4>';

if ($can_write_images) {
	echo
	'<p class="field"><label for="prelude_c">'.__('Prelude color:').'</label> '.
	form::field('prelude_c',7,7,$blowup_user['prelude_c'],'colorpicker').'</p>';
}

echo
'<p class="field"><label for="blog_title_hide">'.__('Hide main title').'</label> '.
form::checkbox('blog_title_hide',1,$blowup_user['blog_title_hide']).'</p>'.

'<p class="field"><label for="blog_title_f">'.__('Main title font:').'</label> '.
form::combo('blog_title_f',blowupConfig::fontsList(),$blowup_user['blog_title_f']).'</p>'.

'<p class="field"><label for="blog_title_s">'.__('Main title font size:').'</label> '.
form::field('blog_title_s',7,7,$blowup_user['blog_title_s']).'</p>'.

'<p class="field"><label for="blog_title_c">'.__('Main title color:').'</label> '.
form::field('blog_title_c',7,7,$blowup_user['blog_title_c'],'colorpicker').'</p>'.

'<p class="field"><label for="blog_title_a">'.__('Main title alignment:').'</label> '.
form::combo('blog_title_a',array(__('center')=>'center',__('left')=>'left',__('right')=>'right'),$blowup_user['blog_title_a']).'</p>'.

'<p class="field"><label for="blog_title_p">'.__('Main title position (x:y)').'</label> '.
form::field('blog_title_p',7,7,$blowup_user['blog_title_p']).'</p>';

if ($can_write_images) {
	if ($blowup_user['top_image'] == 'custom' && $blowup_user['uploaded']) {
		$preview_image = http::concatURL($core->blog->url,blowupConfig::imagesURL().'/page-t.png');
	} else {
		$preview_image = 'index.php?pf=blowupConfig/alpha-img/page-t/'.$blowup_user['top_image'].'.png';
	}

	echo
	'<h5 class="pretty-title">'.__('Top image').'</h5>'.
	'<p class="field"><label for="top_image">'.__('Top image').'</label> '.
	form::combo('top_image',$top_images,($blowup_user['top_image'] ? $blowup_user['top_image'] : 'default')).'</p>'.
	'<p>'.__('Choose "Custom..." to upload your own image.').'</p>'.

	'<p id="uploader"><label for="upfile">'.__('Add your image:').'</label> '.
	' ('.sprintf(__('JPEG or PNG file, 800 pixels wide, maximum size %s'),files::size(DC_MAX_UPLOAD_SIZE)).')'.
	'<input type="file" name="upfile" id="upfile" size="35" />'.
	'</p>'.

	'<h5>'.__('Preview').'</h5>'.
	'<div class="grid" style="width:800px;border:1px solid #ccc;">'.
	'<img style="display:block;" src="'.$preview_image.'" alt="" id="image-preview" />'.
	'</div>';
}

echo
'<h4 class="border-top">'.__('Sidebar').'</h4>'.
'<p class="field"><label for="sidebar_position">'.__('Sidebar position:').'</label> '.
form::combo('sidebar_position',array(__('right')=>'right',__('left')=>'left'),$blowup_user['sidebar_position']).'</p>'.

'<p class="field"><label for="sidebar_text_f">'.__('Sidebar text font:').'</label> '.
form::combo('sidebar_text_f',blowupConfig::fontsList(),$blowup_user['sidebar_text_f']).'</p>'.

'<p class="field"><label for="sidebar_text_s">'.__('Sidebar text font size:').'</label> '.
form::field('sidebar_text_s',7,7,$blowup_user['sidebar_text_s']).'</p>'.

'<p class="field"><label for="sidebar_text_c">'.__('Sidebar text color:').'</label> '.
form::field('sidebar_text_c',7,7,$blowup_user['sidebar_text_c'],'colorpicker').'</p>'.

'<p class="field"><label for="sidebar_title_f">'.__('Sidebar titles font:').'</label> '.
form::combo('sidebar_title_f',blowupConfig::fontsList(),$blowup_user['sidebar_title_f']).'</p>'.

'<p class="field"><label for="sidebar_title_s">'.__('Sidebar titles font size:').'</label> '.
form::field('sidebar_title_s',7,7,$blowup_user['sidebar_title_s']).'</p>'.

'<p class="field"><label for="sidebar_title_c">'.__('Sidebar titles color:').'</label> '.
form::field('sidebar_title_c',7,7,$blowup_user['sidebar_title_c'],'colorpicker').'</p>'.

'<p class="field"><label for="sidebar_title2_f">'.__('Sidebar 2nd level titles font:').'</label> '.
form::combo('sidebar_title2_f',blowupConfig::fontsList(),$blowup_user['sidebar_title2_f']).'</p>'.

'<p class="field"><label for="sidebar_title2_s">'.__('Sidebar 2nd level titles font size:').'</label> '.
form::field('sidebar_title2_s',7,7,$blowup_user['sidebar_title2_s']).'</p>'.

'<p class="field"><label for="sidebar_title2_c">'.__('Sidebar 2nd level titles color:').'</label> '.
form::field('sidebar_title2_c',7,7,$blowup_user['sidebar_title2_c'],'colorpicker').'</p>'.

'<p class="field"><label for="sidebar_line_c">'.__('Sidebar lines color:').'</label> '.
form::field('sidebar_line_c',7,7,$blowup_user['sidebar_line_c'],'colorpicker').'</p>'.

'<p class="field"><label for="sidebar_link_c">'.__('Sidebar links color:').'</label> '.
form::field('sidebar_link_c',7,7,$blowup_user['sidebar_link_c'],'colorpicker').'</p>'.

'<p class="field"><label for="sidebar_link_v_c">'.__('Sidebar visited links color:').'</label> '.
form::field('sidebar_link_v_c',7,7,$blowup_user['sidebar_link_v_c'],'colorpicker').'</p>'.

'<p class="field"><label for="sidebar_link_f_c">'.__('Sidebar focus links color:').'</label> '.
form::field('sidebar_link_f_c',7,7,$blowup_user['sidebar_link_f_c'],'colorpicker').'</p>'.

'<h4 class="border-top">'.__('Entries').'</h4>'.
'<p class="field"><label for="date_title_f">'.__('Date title font:').'</label> '.
form::combo('date_title_f',blowupConfig::fontsList(),$blowup_user['date_title_f']).'</p>'.

'<p class="field"><label for="date_title_s">'.__('Date title font size:').'</label> '.
form::field('date_title_s',7,7,$blowup_user['date_title_s']).'</p>'.

'<p class="field"><label for="date_title_c">'.__('Date title color:').'</label> '.
form::field('date_title_c',7,7,$blowup_user['date_title_c'],'colorpicker').'</p>'.

'<p class="field"><label for="post_title_f">'.__('Entry title font:').'</label> '.
form::combo('post_title_f',blowupConfig::fontsList(),$blowup_user['post_title_f']).'</p>'.

'<p class="field"><label for="post_title_s">'.__('Entry title font size:').'</label> '.
form::field('post_title_s',7,7,$blowup_user['post_title_s']).'</p>'.

'<p class="field"><label for="post_title_c">'.__('Entry title color:').'</label> '.
form::field('post_title_c',7,7,$blowup_user['post_title_c'],'colorpicker').'</p>';

if ($can_write_images) {
	echo
	'<p class="field"><label for="post_comment_bg_c">'.__('Comment background color:').'</label> '.
	form::field('post_comment_bg_c',7,7,$blowup_user['post_comment_bg_c'],'colorpicker').'</p>';
}

echo
'<p class="field"><label for="post_comment_c">'.__('Comment text color:').'</label> '.
form::field('post_comment_c',7,7,$blowup_user['post_comment_c'],'colorpicker').'</p>';

if ($can_write_images) {
	echo
	'<p class="field"><label for="post_commentmy_bg_c">'.__('My comment background color:').'</label> '.
	form::field('post_commentmy_bg_c',7,7,$blowup_user['post_commentmy_bg_c'],'colorpicker').'</p>';
}

echo
'<p class="field"><label for="post_commentmy_c">'.__('My comment text color:').'</label> '.
form::field('post_commentmy_c',7,7,$blowup_user['post_commentmy_c'],'colorpicker').'</p>'.

'<h4 class="border-top">'.__('Footer').'</h4>'.
'<p class="field"><label for="footer_f">'.__('Footer font:').'</label> '.
form::combo('footer_f',blowupConfig::fontsList(),$blowup_user['footer_f']).'</p>'.

'<p class="field"><label for="footer_s">'.__('Footer font size:').'</label> '.
form::field('footer_s',7,7,$blowup_user['footer_s']).'</p>'.

'<p class="field"><label for="footer_c">'.__('Footer color:').'</label> '.
form::field('footer_c',7,7,$blowup_user['footer_c'],'colorpicker').'</p>'.

'<p class="field"><label for="footer_l_c">'.__('Footer links color:').'</label> '.
form::field('footer_l_c',7,7,$blowup_user['footer_l_c'],'colorpicker').'</p>'.

'<p class="field"><label for="footer_bg_c">'.__('Footer background color:').'</label> '.
form::field('footer_bg_c',7,7,$blowup_user['footer_bg_c'],'colorpicker').'</p>';

echo
'<h4 class="border-top">'.__('Additional CSS').'</h4>'.
'<p><label for="extra_css">'.__('Any additional CSS styles (must be written using the CSS syntax):').'</label> '.
form::textarea('extra_css',72,5,html::escapeHTML($blowup_user['extra_css']),'maximal','',false,'title="'.__('Additional CSS').'"').
'</p>'.
'</div>';



// Import / Export configuration
$tmp_array = array();
$tmp_exclude = array('uploaded','top_height');
if ($blowup_user['top_image'] == 'custom') {
	$tmp_exclude[] = 'top_image';
}
foreach ($blowup_user as $k => $v) {
	if (!in_array($k,$tmp_exclude)) {
		$tmp_array[] = $k.':'.'"'.$v.'"';
	}
}
echo
'<div class="fieldset">'.
'<h3 id="bu_export">'.__('Configuration import / export').'</h3>'.
'<div id="bu_export_content">'.
'<p>'.__('You can share your configuration using the following code. To apply a configuration, paste the code, click on "Apply code" and save.').'</p>'.
'<p>'.form::textarea('export_code',72,5,implode('; ',$tmp_array),'maximal','',false,'title="'.__('Copy this code:').'"').'</p>'.
'</div>'.
'</div>';

echo
'<p class="clear"><input type="submit" value="'.__('Save').'" />'.
$core->formNonce().'</p>'.
'</form>';

dcPage::helpBlock('blowupConfig');
?>
</body>
</html>
