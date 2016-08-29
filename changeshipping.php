<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class Changeshipping extends Module
{
  public function __construct()
  {
    $this->name = 'changeshipping'; 
    $this->tab = 'administration';
    $this->version = '1.0';
    $this->author = 'Novevision.com, Britoff A.';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
    $this->bootstrap = true;
  
    parent::__construct();
  
    $this->displayName = $this->l('Change carrier and shipping price');
    $this->description = $this->l('Add option to admin order form');
  
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

  }
  
  public function install()
  {
    if (Shop::isFeatureActive())
      Shop::setContext(Shop::CONTEXT_ALL);
   
    if (!parent::install() ||
      !$this->registerHook('displayAdminOrder')
    )
      return false;
   
    return true;
  }
  
  public function uninstall()
  {
    if (!parent::uninstall() 
    )
      return false;
   
    return true;
  }
  
  public function hookDisplayAdminOrder($params)
  {
    $id_order = $params['id_order'];
    
    $this->context->smarty->assign(
        array(
            'id_order' => $id_order,
        )
    );
    return $this->display(__FILE__, 'changeshipping.tpl');
  }

}