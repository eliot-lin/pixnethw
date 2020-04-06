<?php

class Sales
{
    private $kilos;
    private $baseFreight;
    private $addition;
    
    /* 為了方便使用，都塞在同一隻檔案，否則可以寫在資料庫，或是另外弄一個放常數層的資料夾內 */
    private $mappingList = [
        'Dog' => [
            '美國' => [0, 60]
        ],
        'Falcon' => [
            '大陸' => [200, 20],
            '台灣' => [150, 30]
        ],
        'Cat' => [
            '台灣' => [100, 10]
        ],
        'Tiger' => [
            '台灣' => [60, 20]
        ]
     ];
    
    public function __construct($saler, $region, $kilos)
    {
        $this->kilos  = $kilos;
        
        $this->baseFreight = $this->mappingList[$saler][$region][0] ?? null;
        $this->addition    = $this->mappingList[$saler][$region][1] ?? null;
    }
    
    public function getFreight()
    {
        return $this->baseFreight;
    }
    
    public function getAddition()
    {
        return $this->addition;
    }
    
    public function getKilos()
    {
        return $this->kilos;
    }
}

class FreightAdapter
{
    protected $sales;
    
    public function __construct(Sales $sales)
    {
        $this->sales = $sales;
    }
    
    public function calcFreight()
    {
        return $this->sales->getFreight() + $this->sales->getKilos() * $this->sales->getAddition();
    }
}

$sales = new Sales('Dog', '美國', 5);  // 300
// $sales = new Sales('Falcon', '大陸', 10); // 400
// $sales = new Sales('Falcon', '台灣', 10); // 450
// $sales = new Sales('Cat', '台灣', 3); // 130
// $sales = new Sales('Tiger', '台灣', 15); // 360

$freightAdapter = new FreightAdapter($sales);

echo $freightAdapter->calcFreight();
