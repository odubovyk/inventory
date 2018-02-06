<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryConfiguration\Test\Integration\Model;

use Magento\InventoryIndexer\Indexer\IndexStructure;
use Magento\InventoryIndexer\Model\GetStockItemData;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GetStockItemDataMinQtyTest extends TestCase
{
    /**
     * @var GetStockItemData
     */
    private $getStockItemData;

    /**
     * @var array
     */
    private $skus = ['SKU-1', 'SKU-2', 'SKU-3'];

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getStockItemData = Bootstrap::getObjectManager()->get(GetStockItemData::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/products.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/sources.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stocks.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source_items.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stock_source_links.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryIndexer/Test/_files/reindex_inventory.php
     * @magentoConfigFixture default_store cataloginventory/item_options/min_qty 5
     *
     * @param int $stockId
     * @param array $expectedQty
     * @param array $expectedIsSalable
     * @return void
     *
     * @dataProvider executeWithMinQtyDataProvider
     */
    public function testExecuteWithMinQty(int $stockId, array $expectedQty, array $expectedIsSalable)
    {
        foreach ($this->skus as $key => $sku) {
            $stockItemData = $this->getStockItemData->execute($sku, $stockId);
            if (null !== $stockItemData) {
                self::assertEquals($expectedQty[$key], $stockItemData[IndexStructure::QUANTITY]);
                self::assertEquals($expectedIsSalable[$key], $stockItemData[IndexStructure::IS_SALABLE]);
            } else {
                $isSame = $expectedQty[$key] === $expectedIsSalable[$key] && $expectedQty[$key] === $stockItemData
                    ? true
                    : false;

                self::assertTrue($isSame);
            }
        }
    }

    /**
     * @return array
     */
    public function executeWithMinQtyDataProvider(): array
    {
        return [
            ['10', [8.5, null, 0], [1, null, 0]],
            ['20', [null, 5, null], [null, 0, null]],
            ['30', [8.5, 5, 0], [1, 0, 0]],
        ];
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/products.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/sources.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stocks.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source_items.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stock_source_links.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryIndexer/Test/_files/reindex_inventory.php
     * @magentoConfigFixture default_store cataloginventory/item_options/min_qty 5
     * @magentoConfigFixture default_store cataloginventory/item_options/manage_stock 0
     *
     * @param int $stockId
     * @param array $expectedQty
     * @param array $expectedIsSalable
     * @return void
     *
     * @dataProvider executeWithManageStockFalseAndMinQty
     */
    public function testExecuteWithManageStockFalseAndMinQty(int $stockId, array $expectedQty, array $expectedIsSalable)
    {
        foreach ($this->skus as $key => $sku) {
            $stockItemData = $this->getStockItemData->execute($sku, $stockId);
            if (null !== $stockItemData) {
                self::assertEquals($expectedQty[$key], $stockItemData[IndexStructure::QUANTITY]);
                self::assertEquals($expectedIsSalable[$key], $stockItemData[IndexStructure::IS_SALABLE]);
            } else {
                $isSame = $expectedQty[$key] === $expectedIsSalable[$key] && $expectedQty[$key] === $stockItemData
                    ? true
                    : false;

                self::assertTrue($isSame);
            }
        }
    }

    /**
     * @return array
     */
    public function executeWithManageStockFalseAndMinQty(): array
    {
        return [
            ['10', [8.5, null, 0], [1, null, 1]],
            ['20', [null, 5, null], [null, 1, null]],
            ['30', [8.5, 5, 0], [1, 1, 1]],
        ];
    }
}
