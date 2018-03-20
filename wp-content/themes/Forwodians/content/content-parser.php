<?php

class Theme_Content_Parser {

	/**
	 * xml file with theme content
	 * 
	 * @var object of class SimpleXMLElement
	 */
	private $_xml;

	/**
	 * initialization of <var>$_xml</var>
	 *
	 * @param string $path path to file with content
	 */
	function __construct($path) {
		$this->_xml = simplexml_load_file($path, 'SimpleXMLElement', LIBXML_NOENT);
		if (!$this->_xml) {
			// error
		}
	}

	/**
	 * returns the array of posts
	 * every post is a key=>value array 
	 *
	 * if posts were not found returns "false"
	 *
	 * if successful returns the information about posts in the following way
	 * 'name' - unique post name
	 * 'title' - post title
	 * 'status' - published or draft
	 * 'content' - post content
	 * 'styles' - aditional post styles
	 * 
	 * @return false|array
	 */
	public function get_posts() {
		if (!isset($this->_xml->posts)
				|| !isset($this->_xml->posts->post)) {
			return false;
		}

		$posts_info = array();
		foreach ($this->_xml->posts->post as $post_node) {
			$post_attributes = $post_node->attributes();

			$current_post_info = array(
				'name' => (string) $post_attributes->name,
				'title' => (string) $post_attributes->title,
				'path' => '/Blog%20Posts/' . (string) $post_attributes->name,
				'status' => (string) $post_attributes->status,
				'content' => (string) $post_node->content,
				'styles' => (string) $post_node->styles,
				'pageHead' => (string) $post_node->pageHead
			);

			array_unshift($posts_info, $current_post_info);
		}
		return $posts_info;
	}

	/**
	 * returns the array of pages
	 * every page is a key=>value array 
	 *
	 * if pages were not found returns "false"
	 *
	 * if successful returns the information about pages in the following way
	 * 'name' - unique page name
	 * 'title' - page title 
	 * 'path' - the path from the root page
	 * 'posts_page' - set as "true" if the current page is used for outputting the posts 
	 * 'content' - the content of the page
	 * 'styles' - aditional page styles
	 * 
	 * @return false|array
	 */
	public function get_pages() {
		if (!isset($this->_xml->pages)
				|| !isset($this->_xml->pages->page)) {
			return false;
		}

		$pages_info = array();
		foreach ($this->_xml->pages->page as $page_node) {
			$this->parse_page($page_node, $pages_info);
		}
		return $pages_info;
	}

	/**
	 * returns the array of sidebars
	 * every sidebar is a key=>value array 
	 *
	 * if sidebars were not found returns "false"
	 *
	 * if successful returns the information about sidebars in the following way
	 * 'caption' - sidebar title
	 * 'name' - sidebar1|sidebar2|content-before|content-after|inactive
	 * 'blocks' - the array of text blocks and widgets contained in sidebar
	 *
	 * each block is defined by the following parameters
	 * 'type' - widget|menuWidget|block
	 * 'name' - unique widget name
	 * 'title' - widget title
	 * 'content' - an optional field, widget content
	 *
	 * @return false|array
	 */
	public function get_sidebars() {
		if (!isset($this->_xml->sidebars)
				|| !isset($this->_xml->sidebars->sidebar)) {
			return false;
		}

		$sidebars_info = array();
		foreach ($this->_xml->sidebars->sidebar as $sidebar_node) {
			$blocks = array();
			$this->parse_blocks($sidebar_node, $blocks);

			$sidebars_info[] = array(
				'caption' => (string) $sidebar_node->attributes()->caption,
				'name' => (string) $sidebar_node->attributes()->name,
				'blocks' => $blocks
			);
		}
		return $sidebars_info;
	}
	
	/**
	 * returns the key=>value array of collages
	 * key of array is the name of collage
	 * value of array is the content of collage
	 *
	 * @return false|array
	 */
	public function get_collages() {
		if (!isset($this->_xml->sidebars)
				|| !isset($this->_xml->sidebars->sidebar)) {
			return false;
		}
		
		$collages_info = array();
		foreach ($this->_xml->collages->collage as $collage_node) {
			$collages_info[(string)$collage_node['name']] = (string)$collage_node->content;
		}
		return $collages_info;
	}

	/**
	 * parses the current $page_node, returns the result to в $pages_info
	 * is called recursively for all subpages
	 *
	 * @param SimpleXMLElement $page_node
	 * @param array &$pages_info the information about pages and sub pages of the current node
	 */
	private function parse_page($page_node, &$pages_info) {
		$page_attributes = $page_node->attributes();

		$info = array(
			'name' => (string) $page_attributes->name,
			'title' => (string) $page_attributes->title,
			'path' => (string) $page_attributes->path,
			'content' => (string) $page_node->content,
			'styles' => (string) $page_node->styles,
			'pageHead' => (string) $page_node->pageHead,
			'show_in_menu' => ($page_attributes->showInHmenu == 'True') ? true : false,
			'show_in_vmenu' => ($page_attributes->showInVmenu == 'True') ? true : false,
			'title_in_menu' => (string) $page_attributes->caption,
			'show_page_title' => ($page_attributes->showTitle == 'True') ? true : false,
			'pageTitle' => (string) $page_attributes->titleInBrowser,
            'pageKeywords' => (string) $page_attributes->keywords,
            'pageDescription' => (string) $page_attributes->description,
            'pageMetaTags' => (string) $page_attributes->metaTags,
			'pageCustomHeadHtml' => (string) $page_attributes->customHeadHtml,
			'pageHasCustomHeadHtml' => isset($page_attributes->customHeadHtml) ? 'true' : 'false'
		);

		if (isset($page_attributes->posts_page)) {
			$info['posts_page'] = true;
		}

		$pages_info[] = $info;

		$has_sub_pages = isset($page_node->pages)
				&& isset($page_node->pages->page);
		if (!$has_sub_pages) {
			return;
		}

		$sub_pages = $page_node->pages->page;
		foreach ($sub_pages as $sub_page_node) {
			$this->parse_page($sub_page_node, $pages_info);
		}
	}

	/**
	 * parses blocks contained in $blocks_node, returns the result to $blocks_info
	 * 
	 * @param SimpleXMLElement $blocks_node
	 * @param array &$blocks_info information about blocks
	 */
	private function parse_blocks($blocks_node, &$blocks_info) {
		if (!isset($blocks_node)) {
			return;
		}

		$widget_nodes = $blocks_node->xpath('./*[self::menuWidget or self::widget or self::block]');
		$result = array();
		foreach ($widget_nodes as $node) {
			$info = array();
			$info['type'] = (string) $node->getName();
			$info['name'] = (string) $node->attributes()->name;
			$info['title'] = (string) $node->attributes()->title;
			$info['pageHead'] = (string) $node->pageHead;
			if (isset($node->content)) {
				$info['content'] = (string) $node->content;
			}

			$result[] = $info;
		}
		$blocks_info = array_merge($blocks_info, $result);
	}

}
