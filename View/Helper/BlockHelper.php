<?php
App::uses('AppHelper', 'View/Helper');

/**
 * BlockAnywhere: Block Helper
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Darren Moore <darren.m@firecreek.co.uk>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.firecreek.co.uk
 */
class BlockHelper extends AppHelper {

  /**
   * Other helpers used by this helper
   *
   * @var array
   * @access public
   */
  public $helpers = array(
    'Layout',
  );
  
  /**
   * Node fields to check for blocks and replace
   *
   * @var array
   * @access public
   */
  public $nodeFields = array(
    'body', 'excerpt'
  );
  
  
  /**
   * Before setting the node check for blocks
   *
   * @return void
   */
  public function afterSetNode() {
    foreach($this->nodeFields as $field)
    {
      $nodeValue = $this->parseString($this->Layout->node($field));
      $this->Layout->setNodeField($field, $nodeValue);
    }
  }
  

  /**
   * Parse string
   *
   * @param string $text String to parse
   * @return string
   */
  public function parseString($text) {
    preg_match_all('/\[(block|b):([A-Za-z0-9_\-]*)(.*?)\]/i', $text, $tagMatches);
    
    foreach($tagMatches[0] as $key => $match)
    {
      $text = str_replace($match,$this->display($tagMatches[2][$key]),$text);
    }
    
    return $text;
  }
  

  /**
   * Display block
   *
   * @param string $alias Alias of block
   * @return string
   */
  public function display($alias) {
    $blocks = $this->_View->viewVars['blocks_anywhere_for_layout'];
    
    foreach($blocks as $blockRecord)
    {
      if($blockRecord['Block']['alias'] == $alias)
      {
        $block = $blockRecord;
        break;
      }
    }
    
    if(!isset($block)) { return 'Block alias not found: '.$alias; }
    
    $plugin = false;
    if ($block['Block']['element'] != null) {
      if (strstr($block['Block']['element'], '.')) {
        $plugin_element = explode('.', $block['Block']['element']);
        $plugin  = $plugin_element[0];
        $element = $plugin_element[1];
      } else {
        $element = $block['Block']['element'];
      }
    } else {
      $element = 'block';
    }
    if ($plugin) {
      $output = $this->_View->element($element, array('block' => $block, 'plugin' => $plugin));
    } else {
      $output = $this->_View->element($element, array('block' => $block));
    }
    
    //Check for blocks within this block
    $output = $this->parseString($output);

    return $output;
  }
  

  /**
   * Show Blocks for a particular Region
   *
   * @param string $regionAlias Region alias
   * @param array $options
   * @return string
   */
  public function blocks($regionAlias, $options = array()) {
    $output = $this->Layout->blocks($regionAlias,$options);
    
    $output = $this->parseString($output);
    
    return $output;
  }
  
}