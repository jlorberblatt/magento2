<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\ResourceModel\Product\Indexer;

/**
 * Provided logic will create temporary table based on memory table and will return new index table name.
 */
class TemporaryTableStrategy implements \Magento\Framework\Indexer\Table\StrategyInterface
{
    /**
     * Suffix for new temporary table
     */
    const TEMP_SUFFIX = '_temp';

    /**
     * @var \Magento\Framework\Indexer\Table\Strategy
     */
    private $strategy;

    /**
     * Application resource
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * TemporaryTableStrategy constructor.
     * @param \Magento\Framework\Indexer\Table\Strategy $strategy
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\Indexer\Table\StrategyInterface $strategy,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->strategy = $strategy;
        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function getUseIdxTable()
    {
        /* return $this->strategy->getUseIdxTable();
   	*/
	return true; 
    }

    /**
     * @inheritdoc
     */
    public function setUseIdxTable($value = false)
    {
        return $this->strategy->setUseIdxTable($value);
    }

    /**
     * @inheritdoc
     */
    public function getTableName($tablePrefix)
    {
        return $this->resource->getTableName($this->prepareTableName($tablePrefix));
    }

    /**
     * Create temporary index table based on memory table
     *
     * {@inheritdoc}
     */
    public function prepareTableName($tablePrefix)
    {
        if ($this->getUseIdxTable()) {
            return $tablePrefix . self::IDX_SUFFIX;
        }

	$temporaryTableName = $this->resource->getTableName($tablePrefix . self::TEMP_SUFFIX . '_jlor_' . uniqid());
        // Create temporary table
        $this->resource->getConnection('indexer')->createTemporaryTableLike(
            $this->resource->getTableName($temporaryTableName),
            $this->resource->getTableName($tablePrefix . self::TMP_SUFFIX),
            true
        );
        return $temporaryTableName;
    }
}
