<?php
$this->startSetup();
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'laser', array(
    'group'                => 'General',
    'type'              => 'int',//can be int, varchar, decimal, text, datetime
    'backend'           => '',
    'frontend_input'    => '',
    'frontend'          => '',
    'label'             => 'Laser',
    'input'             => 'select', //text, textarea, select, file, image, multilselect
    'default' => array(0),
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',//this is necessary for select and multilelect, for the rest leave it blank
    'global'             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,//scope can be SCOPE_STORE or SCOPE_GLOBAL or SCOPE_WEBSITE
    'visible'           => true,
    'frontend_class'     => '',
    'required'          => false,//or true
    'user_defined'      => true,
    'default'           => '',
    'position'            => 100,//any number will do
));
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'devlink', array(
    'group'                => 'General',
    'type'              => 'text',//can be int, varchar, decimal, text, datetime
    'backend'           => '',
    'frontend_input'    => '',
    'frontend'          => '',
    'label'             => 'Developers site',
    'input'             => 'text', //text, textarea, select, file, image, multilselect
    'default' => array(0),
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',//this is necessary for select and multilelect, for the rest leave it blank
    'global'             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,//scope can be SCOPE_STORE or SCOPE_GLOBAL or SCOPE_WEBSITE
    'visible'           => true,
    'frontend_class'     => '',
    'required'          => false,//or true
    'user_defined'      => true,
    'default'           => '',
    'position'            => 100,//any number will do
));
 
$this->endSetup();