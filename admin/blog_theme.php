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

require dirname(__FILE__).'/../inc/admin/prepend.php';

dcPage::check('admin');

# -- "First time" settings setup --
if ($core->blog->settings->system->store_theme_url === null) {
	$core->blog->settings->system->put(
		'store_theme_url', 'http://update.dotaddict.org/dc2/themes.xml', 'string', 'Themes XML feed location', true, true
	);
}

# -- Loading themes --
$core->themes = new dcThemes($core);
$core->themes->loadModules($core->blog->themes_path, null);

# -- Page helper --
$list = new adminThemesList(
	$core->themes, 
	$core->blog->themes_path,
	$core->blog->settings->system->store_theme_url
);
$list::$distributed_modules = array(
	'blueSilence',
	'blowupConfig',
	'customCSS',
	'default',
	'ductile'
);

# -- Theme screenshot --
if (!empty($_GET['shot']) && $list->modules->moduleExists($_GET['shot'])) {

	$f= path::real(empty($_GET['src']) ?
		$core->blog->themes_path.'/'.$_GET['shot'].'/screenshot.jpg' :
		$core->blog->themes_path.'/'.$_GET['shot'].'/'.path::clean($_GET['src'])
	);

	if (!file_exists($f)) {
		$f = dirname(__FILE__).'/images/noscreenshot.png';
	}

	http::cache(array_merge(array($f), get_included_files()));

	header('Content-Type: '.files::getMimeType($f));
	header('Content-Length: '.filesize($f));
	readfile($f);

	exit;
}

# -- Display module configuration page --
if ($list->setConfiguration($core->blog->settings->system->theme)) {

	# Get content before page headers
	include $list->includeConfiguration();

	# Gather content
	$list->getConfiguration();

	# Display page
	dcPage::open(__('Blog appearance'),
		dcPage::jsPageTabs().
		dcPage::jsColorPicker().

		# --BEHAVIOR-- themesToolsHeaders
		$core->callBehavior('themesToolsHeaders', $core, true),

		dcPage::breadcrumb(
			array(
				html::escapeHTML($core->blog->name) => '',
				__('Blog appearance') => $list->getURL('',false),
				'<span class="page-title">'.__('Theme configuration').'</span>' => ''
			))
	);

	# Display previously gathered content
	$list->displayConfiguration();

	dcPage::close();

	# Stop reading code here
	return;
}

# -- Execute actions --
try {
	$list->doActions('themes');
}
catch (Exception $e) {
	$core->error->add($e->getMessage());
}

# -- Page header --
dcPage::open(__('Themes management'),
	dcPage::jsLoad('js/_blog_theme.js').
	dcPage::jsPageTabs().
	dcPage::jsColorPicker().

	# --BEHAVIOR-- themesToolsHeaders
	$core->callBehavior('themesToolsHeaders', $core, false),

	dcPage::breadcrumb(
		array(
			html::escapeHTML($core->blog->name) => '',
			'<span class="page-title">'.__('Blog appearance').'</span>' => ''
		))
);

# -- Display modules lists --
if ($core->auth->isSuperAdmin() && $list->isWritablePath()) {

	# Updated modules from repo
	$modules = $list->store->get(true);
	if (!empty($modules)) {
		echo 
		'<div class="multi-part" id="update" title="'.html::escapeHTML(__('Update themes')).'">'.
		'<h3>'.html::escapeHTML(__('Update themes')).'</h3>'.
		'<p>'.sprintf(
			__('There is one theme to update available from repository.', 'There are %s themes to update available from repository.', count($modules)),
			count($modules)
		).'</p>';

		$list
			->setList('theme-update')
			->setTab('themes')
			->setModules($modules)
			->displayModules(
				/*cols */		array('sshot', 'name', 'desc', 'author', 'version', 'current_version', 'parent'),
				/* actions */	array('update', 'delete')
			);

		echo
		'<p class="info vertical-separator">'.sprintf(
			__("Visit %s repository, the resources center for Dotclear."),
			'<a href="http://dotaddict.org/dc2/themes">Dotaddict</a>'
			).
		'</p>'.

		'</div>';
	}
}

# Activated modules
$modules = $list->modules->getModules();
if (!empty($modules)) {

	echo
	'<div class="multi-part" id="themes" title="'.__('Installed themes').'">'.
	'<h3>'.__('Installed themes').'</h3>'.
	'<p>'.__('You can configure and manage installed themes from this list.').'</p>';

	$list
		->setList('theme-activate')
		->setTab('themes')
		->setModules($modules)
		->displayModules(
			/* cols */		array('sshot', 'distrib', 'name', 'config', 'desc', 'author', 'version', 'parent'),
			/* actions */	array('select', 'behavior', 'deactivate', 'delete')
		);

	echo 
	'</div>';
}

# Deactivated modules
$modules = $list->modules->getDisabledModules();
if (!empty($modules)) {

	echo
	'<div class="multi-part" id="deactivate" title="'.__('Deactivated themes').'">'.
	'<h3>'.__('Deactivated themes').'</h3>'.
	'<p>'.__('Deactivated themes are installed but not usable. You can activate them from here.').'</p>';

	$list
		->setList('theme-deactivate')
		->setTab('themes')
		->setModules($modules)
		->displayModules(
			/* cols */		array('name', 'distrib'),
			/* actions */	array('activate', 'delete')
		);

	echo 
	'</div>';
}

if ($core->auth->isSuperAdmin() && $list->isWritablePath()) {

	# New modules from repo
	$search = $list->getSearch();
	$modules = $search ? $list->store->search($search) : $list->store->get();

	if (!empty($search) || !empty($modules)) {
		echo
		'<div class="multi-part" id="new" title="'.__('Add themes').'">'.
		'<h3>'.__('Add themes from repository').'</h3>'.
		'<p>'.__('Search and install themes directly from repository.').'</p>';

		$list
			->setList('theme-new')
			->setTab('new')
			->setModules($modules)
			->displaySearch()
			->displayIndex()
			->displayModules(
				/* cols */		array('expander', 'sshot', 'name', 'score', 'config', 'desc', 'author', 'version', 'parent', 'details', 'support'),
				/* actions */	array('install'),
				/* nav limit */	true
			);

		echo
		'<p class="info vertical-separator">'.sprintf(
			__("Visit %s repository, the resources center for Dotclear."),
			'<a href="http://dotaddict.org/dc2/themes">Dotaddict</a>'
			).
		'</p>'.

		'</div>';
	}

	# Add a new plugin
	echo
	'<div class="multi-part" id="addtheme" title="'.__('Install or upgrade manually').'">'.
	'<h3>'.__('Add themes from a package').'</h3>'.
	'<p>'.__('You can install themes by uploading or downloading zip files.').'</p>';

	$list->displayManualForm();

	echo
	'</div>';
}

dcPage::close();
