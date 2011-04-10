<?php

  /**
   * Blockanywhere Component
   *
   * An example hook component for demonstrating hook system.
   *
   * @category Component
   * @package  Croogo
   * @version  1.0
   * @author   Darren Moore <darren.m@firecreek.co.uk>
   * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
   * @link     http://www.firecreek.co.uk
   */
  class BlockanywhereComponent extends Object {
  
    /**
     * Enabled
     *
     * @var boolean
     * @access public
     */
    public $enabled = true;
    
    /**
     * Components
     *
     * @var array
     * @access public
     */
    public $components = array('Croogo');
    
    /**
     * Blocks anywhere for layout
     *
     * @var string
     * @access public
     */
    public $blocks_anywhere_for_layout = array();
    
  
    /**
     * Startup
     *
     * @param object $controller instance of controller
     * @return void
     */
    public function startup(&$controller) {
        $this->controller =& $controller;
        
        if (!isset($this->controller->params['admin']) && !isset($this->controller->params['requested']) && $this->enabled) {
          $this->controller->helpers[] = 'Blockanywhere.Block';
          $this->blocks();
        }
    }
    

    /**
     * beforeRender
     *
     * @param object $controller instance of controller
     * @return void
     */
    public function beforeRender(&$controller) {
      if($this->enabled)
      {
        $this->controller =& $controller;
        $this->controller->set('blocks_anywhere_for_layout', $this->blocks_anywhere_for_layout);
      }
    }
      
    /**
     * Blocks
     *
     * Blocks will be available in this variable in views: $blocks_for_layout
     *
     * @return void
     */
    public function blocks() {
      $findOptions = array(
        'conditions' => array(
            'Block.status' => 1,
        ),
        'cache' => array(
            'prefix' => 'blockanywhere_blocks_',
            'config' => 'croogo_blocks',
        ),
        'recursive' => '-1',
      );
      $blocks = $this->controller->Block->find('all', $findOptions);
      $this->Croogo->processBlocksData($blocks);
      
      $this->blocks_anywhere_for_layout = $blocks;
    }
      
  }

?>
