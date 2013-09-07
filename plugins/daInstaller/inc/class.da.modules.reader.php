<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of daInstaller, a plugin for DotClear2.
# Copyright (c) 2008-2011 Tomtom, Pep and contributors, for DotAddict.org.
# All rights reserved.
#
# ***** END LICENSE BLOCK *****

class daModulesReader extends netHttp
{
	protected $user_agent 		= 'DotAddict.org RepoBrowser/0.1';
	protected $timeout 			= 5;
	protected $validators 		= null;				///< <b>array</b>	HTTP Cache validators
	protected $cache_dir 		= null;				///< <b>string</b>	Cache temporary directory
	protected $cache_file_prefix 	= 'daorg';			///< <b>string</b>	Cache file prefix
	protected $cache_ttl 		= '-30 minutes';		///< <b>string</b>	Cache TTL
	protected $force			= false;
	
	public function __construct()
	{
		parent::__construct('');
		$this->setUserAgent(daInstaller::getUserAgent());
	}
	
	public function parse($url)
	{
		$this->validators = array();
		if ($this->cache_dir)
		{
			return $this->withCache($url);
		}
		else
		{
			if (!$this->getModulesXML($url)) {
				return false;
			}
			
			if ($this->getStatus() != '200') {
				return false;
			}
			
			return new daModulesParser($this->getContent());
		}
	}
	
	public static function quickParse($url,$cache_dir = null,$force = false)
	{
		$parser = new self();
		if ($cache_dir) {
			$parser->setCacheDir($cache_dir);
		}
		if ($force) {
			$parser->setForce($force);
		}
		
		return $parser->parse($url);
	}
	
	public function setCacheDir($dir)
	{
		$this->cache_dir = null;
		
		if (!empty($dir) && is_dir($dir) && is_writeable($dir))
		{
			$this->cache_dir = $dir;
			return true;
		}
		
		return false;
	}
	
	public function setCacheTTL($str)
	{
		$str = trim($str);
		if (!empty($str))
		{
			if (substr($str,0,1) != '-') {
				$str = '-'.$str;
			}
			$this->cache_ttl = $str;
		}
	}
	
	public function setForce($force)
	{
		$this->force = $force;
	}
	
	protected function getModulesXML($url)
	{
		if (!self::readURL($url,$ssl,$host,$port,$path,$user,$pass)) {
			return false;
		}
		$this->setHost($host,$port);
		$this->useSSL($ssl);
		$this->setAuthorization($user,$pass);
		
		return $this->get($path);
	}
	
	protected function withCache($url)
	{
		$url_md5 = md5($url);
		$cached_file = sprintf('%s/%s/%s/%s/%s.ser',
			$this->cache_dir,
			$this->cache_file_prefix,
			substr($url_md5,0,2),
			substr($url_md5,2,2),
			$url_md5
		);
		
		$may_use_cached = false;
		
		if (@file_exists($cached_file) && !$this->force)
		{
			$may_use_cached = true;
			$ts = @filemtime($cached_file);
			if ($ts > strtotime($this->cache_ttl))
			{
				# Direct cache
				return unserialize(file_get_contents($cached_file));
			}
			$this->setValidator('IfModifiedSince', $ts);
		}
		
		if (!$this->getModulesXML($url))
		{
			if ($may_use_cached)
			{
				# connection failed - fetched from cache
				return unserialize(file_get_contents($cached_file));
			}
			return false;
		}
		
		switch ($this->getStatus())
		{
			case '304':
				@files::touch($cached_file);
				return unserialize(file_get_contents($cached_file));
			case '200':
				if ($modules = new daModulesParser($this->getContent()))
				{
					try {
						files::makeDir(dirname($cached_file),true);
					} catch (Exception $e) {
						return $modules;
					}
					
					if (($fp = @fopen($cached_file, 'wb')))
					{
						fwrite($fp, serialize($modules));
						fclose($fp);
						files::inheritChmod($cached_file);
					}
					return $modules;
				}
		}
		
		return false;
	}
	
	protected function buildRequest()
	{
		$headers = parent::buildRequest();
		
		# Cache validators
		if (!empty($this->validators))
		{
			if (isset($this->validators['IfModifiedSince'])) {
				$headers[] = 'If-Modified-Since: '.$this->validators['IfModifiedSince'];
			}
			if (isset($this->validators['IfNoneMatch'])) {
				if (is_array($this->validators['IfNoneMatch'])) {
					$etags = implode(',',$this->validators['IfNoneMatch']);
				} else {
					$etags = $this->validators['IfNoneMatch'];
				}
				$headers[] = '';
			}
		}
		
		return $headers;
	}
	
	private function setValidator($key,$value)
	{
		if ($key == 'IfModifiedSince') {
			$value = gmdate('D, d M Y H:i:s',$value).' GMT';
		}
		
		$this->validators[$key] = $value;
	}
}

?>