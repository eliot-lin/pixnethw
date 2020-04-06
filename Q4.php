<?php

require 'Q2.php';
require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class Q4 extends TestCase
{
    /**
     * @dataProvider salesProvider
     */
    public function testSalesConstruct($saler, $region, $kilos, $freight)
    {
        $sales = new Sales($saler, $region, $kilos);
        $freightAdapter = new FreightAdapter($sales);
        $content = $freightAdapter->calcFreight();
        
        $this->assertEquals($freight, $content);
    }
    
    public function salesProvider()
    {
        return [
            ['Falcon', '大陸', 10, 400],
            ['Tiger', '台灣', 15, 360]
        ];
    }
}