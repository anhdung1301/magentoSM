<?php

namespace SM\Brand\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use SM\Brand\Helper\Data;

class Brand extends AbstractDb
{


    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \SM\Brand\Helper\Data
     */
    public $helperData;

    /**
     * @var Datetime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        DateTime $dateTime,
        Data $helperData,

        $connectionName = null
    )
    {
        $this->helperData     = $helperData;
        parent::__construct($context, $connectionName);
        $this->_date = $date;
        $this->dateTime = $dateTime;
    }

    public function saveProduct(AbstractModel $object, $product_id = 0)
    {
        if ($object->getId() && $product_id) {
            $table = $this->getTable('sm_brands_product');

            $select = $this->getConnection()->select()->from(
                ['cp' => $table]
            )->where(
                'cp.brand_id = ?',
                (int)$object->getId()
            )->where(
                'cp.product_id = (?)',
                (int)$product_id
            )->limit(1);

            $row_product = $this->getConnection()->fetchAll($select);

            if (!$row_product) { // check if not exists product, then insert it into database
                $data = [];
                $data[] = [
                    'brand_id' => (int)$object->getId(),
                    'product_id' => (int)$product_id,
                    'position' => 0
                ];

                $this->getConnection()->insertMultiple($table, $data);
            }
            return true;
        }
        return false;
    }

    public function deleteBrandsByProduct($product_id = 0)
    {
        if ($product_id) {
            $condition = ['product_id = ?' => (int)$product_id];
            $this->getConnection()->delete($this->getTable('sm_brands_product'), $condition);
            return true;
        }
        return false;
    }

    public function getBrandIdByName($brand_name = '')
    {
        if ($brand_name) {
            $brand_id = null;
            $table = $this->getTable('sm_brands');

            $select = $this->getConnection()->select()->from(
                ['cp' => $table]
            )->where(
                'cp.name = ?',
                $brand_name
            )->limit(1);

            $row_brand = $this->getConnection()->fetchAll($select);
            if ($row_brand) { // check if have brand record

                $brand_id = isset($row_brand[0]['brand_id']) ? (int)$row_brand[0]['brand_id'] : null;
            }
            return $brand_id;
        }
        return null;
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sm_brands', 'brand_id');
    }

    /**
     *  Check whether brand url key is numeric
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isNumericBrandUrlKey(AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Process brand data before deleting
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(AbstractModel $object)
    {

        $condition = ['brand_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('sm_brands_product'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process brand data before saving
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUrlKey(
            $this->helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign brand to store views
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
//        $oldStores = $this->lookupStoreIds($object->getId());
//        $newStores = (array)$object->getStores();
//        if (empty($newStores)) {
//            $newStores = (array)$object->getStoreId();
//        }
//        $table = $this->getTable('magetop_brand_store');
//        $insert = array_diff($newStores, $oldStores);
//        $delete = array_diff($oldStores, $newStores);
//
//        if ($delete) {
//            $where = ['brand_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
//            $this->getConnection()->delete($table, $where);
//        }
//
//        if ($insert) {
//            $data = [];
//            foreach ($insert as $storeId) {
//                $data[] = ['brand_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
//            }
//            $this->getConnection()->insertMultiple($table, $data);
//        }


        // Posts Related
        if (null !== ($object->getData('products'))) {
            $table = $this->getTable('sm_brands_product');
            $where = ['brand_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);

            if ($quetionProducts = $object->getData('products')) {
                $where = ['brand_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                foreach ($quetionProducts as $k => $_post) {
                    $data[] = [
                        'brand_id' => (int)$object->getId(),
                        'product_id' => $k,
                        'position' => $_post['product_position']
                    ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Perform operations after object load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {

        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from($this->getTable('sm_brands_product'))
                ->where(
                    'brand_id = ' . (int)$id
                );
            $products = $connection->fetchAll($select);
            $object->setData('products', $products);
        }

        return parent::_afterLoad($object);
    }


}
