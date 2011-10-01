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
     * Blocks for layout
     *
     * @var string
     * @access public
     */
    public $blocks_for_layout = array();
    
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
        
        if (!isset($this->controller->params['admin']) && !isset($this->controller->params['requested']) && $this->enabled)
        {
          $this->controller->helpers[] = 'Blockanywhere.Block';
          $this->blocksAnywhere();
          
          $this->blocks();
          $this->Croogo->menus();
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
     * Blocks Anywhere
     *
     * Blocks will be available in this variable in views: $blocks_for_layout
     *
     * @return void
     */
    public function blocksAnywhere() {
      $findOptions = array(
        'conditions' => array(
            'Block.status' => 1,
        ),
        'cache' => array(
            'prefix' => 'blocks_',
            'config' => 'croogo_blocks',
        ),
        'recursive' => '-1',
      );
      $blocks = $this->controller->Block->find('all', $findOptions);
      $this->Croogo->processBlocksData($blocks);
      
      $this->blocks_anywhere_for_layout = $blocks;
    }
    

    /**
     * Blocks, improved from Croogo core
     *
     * Blocks will be available in this variable in views: $blocks_for_layout
     *
     * @return void
     */
    public function blocks()
    {
      $regions = $this->controller->Block->Region->find('list', array(
          'conditions' => array(
              'Region.block_count >' => '0',
          ),
          'fields' => array(
              'Region.id',
              'Region.alias',
          ),
          'cache' => array(
              'name' => 'croogo_regions',
              'config' => 'croogo_blocks',
          ),
      ));
      
      //Visibility paths
      $visibility = array();
      $visibility[] = array('Block.visibility_paths'=>'');
      $visibility[] = array('Block.visibility_paths LIKE'=>'%"'.$this->controller->params['url']['url'].'"%');
      $visibility[] = array('Block.visibility_paths LIKE'=>'%"' . 'controller:' . $this->controller->params['controller'] . '/' . 'action:' . $this->controller->params['action'] . '"%');
      
      if(isset($this->controller->params['type']))
      {
          $visibility[] = array('Block.visibility_paths LIKE'=>'%"' . 'controller:' . $this->controller->params['controller'] . '/' . 'action:' . $this->controller->params['action'] . '/' . 'type:' . $this->controller->params['type'] . '"%');
      }
      
      if(isset($this->controller->params['slug']))
      {
          $visibility[] = array('Block.visibility_paths LIKE'=>'%"' . 'controller:' . $this->controller->params['controller'] . '/' . 'action:' . $this->controller->params['action'] . '/' . 'slug:' . $this->controller->params['slug'] . '"%');
      }
      
      foreach ($regions AS $regionId => $regionAlias) {
          $this->blocks_for_layout[$regionAlias] = array();
          $findOptions = array(
              'conditions' => array(
                  'Block.status' => 1,
                  'Block.region_id' => $regionId,
                  'AND' => array(
                      array(
                          'OR' => array(
                              'Block.visibility_roles' => '',
                              'Block.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
                          ),
                      ),
                      array(
                          'OR' => $visibility,
                      )
                  ),
              ),
              'order' => array(
                  'Block.weight' => 'ASC'
              ),
              'cache' => array(
                  'prefix' => 'croogo_blocks_'.$regionAlias.'_'.$this->Croogo->roleId.'_blockanywhere_',
                  'config' => 'croogo_blocks',
              ),
              'recursive' => '-1',
          );
          $blocks = $this->controller->Block->find('all', $findOptions);
          $this->Croogo->processBlocksData($blocks);
          $this->blocks_for_layout[$regionAlias] = $blocks;
      }
      
      $this->Croogo->blocks_for_layout = $this->blocks_for_layout;
    }
      
  }

?>
