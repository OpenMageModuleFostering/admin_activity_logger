<?php
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */
$installer = $this;
$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS {$installer->getTable('adminlog_activities')};");
$table = $installer->getConnection()->newTable($installer->getTable('adminlog/adminlog'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            'identity'  => true,
            ), 'Id')
        ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            ), 'User Id')
        ->addColumn('logged_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,), 'Logged At')
        ->addColumn('user_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'User Email')
        ->addColumn('full_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Full Path')
        ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Store Name')
        ->addColumn('controller_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Controller Name')
        ->addColumn('action',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Action')
        ->addColumn('additional_info',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Additional Info')
        ->addColumn('remote_ip', Varien_Db_Ddl_Table::TYPE_TEXT,255, array(
            'nullable'  => false,
            'default'   => '0',
            ), 'Remote IP')
        ->setComment('Admin log activities');
$installer->getConnection()->createTable($table);
$installer->endSetup();
