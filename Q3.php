<?php

const MAX_DISTANCE = null;

$Q3 = new Q3;
$Q3->solution();

class Q3 
{
    public $catToRat;
    
    public $ratToRat = [
        'X' => [
            'Y' => MAX_DISTANCE,
            'Z' => MAX_DISTANCE
        ],
        'Y' => [
            'X' => MAX_DISTANCE,
            'Z' => MAX_DISTANCE
        ],
        'Z' => [
            'X' => MAX_DISTANCE,
            'Y' => MAX_DISTANCE
        ]
    ];
    
    public function solution()
    {
        $map = 
        "00000100
        0C000100
        0110Z000
        01110000
        0X100000
        001Y0000
        00000000"
        
        // "0000Y100
        // 0C011100
        // 0111X000"
        
        // "0000Y100
        // 0C011100"
        
        // "000C100X
        // 10110010
        // Y0000111"
        ;
        
        $M = explode('
        ', $map);
        
        if ($this->validate($M)) {
            /* 紀錄地圖有幾隻老鼠，並記住他們座標 */
            foreach ($M as $key => $val) {
                if (strpos($val, 'C') !== false) {
                    $cat = [$key, strpos($val, 'C')];
                }
                if (strpos($val, 'X') !== false) {
                    $rat['X'] = [$key, strpos($val, 'X')];
                    $this->catToRat['X'] = MAX_DISTANCE; 
                }
                if (strpos($val, 'Y') !== false) {
                    $rat['Y'] = [$key, strpos($val, 'Y')];
                    $this->catToRat['Y'] = MAX_DISTANCE; 
                }
                if (strpos($val, 'Z') !== false) {
                    $rat['Z'] = [$key, strpos($val, 'Z')];
                    $this->catToRat['Z'] = MAX_DISTANCE; 
                } 
            }
            
            /**
             * $visit 紀錄走過的位置
             */
            foreach ($rat as $ratPosition) {
                // initialize
                $visit = array_fill(
                    0, 
                    sizeof($M), 
                    array_fill(0, strlen($M[0]), 0)
                );
                
                $this->shortestPath($M, $ratPosition, $cat, $visit, 0, $this->catToRat);
            }

        } else {
            throw new Exception('地圖不符合限制條件');
        }
        
        foreach ($this->catToRat as $distance) {
            if (is_null($distance)) {
                echo '無解';
                exit;
            }
        }
        
        /**
         * 計算老鼠之間的距離
         */
        foreach ($rat as $ratCode1 => $position1) {
            foreach ($rat as $ratCode2 => $position2) {
                if ($ratCode1 == $ratCode2) {
                    continue;
                }
                
                $this->shortestPath($M, $position2, $position1, $visit, 0, $this->ratToRat[$ratCode1]);
            }
        }
        // 貓到老鼠 | 老鼠到老鼠的距離，反註解可查看是否正常
        // print_r($this->ratToRat['X']); echo '<br><br>';
        // print_r($this->ratToRat['Y']); echo '<br><br>';
        // print_r($this->ratToRat['Z']); echo '<br><br>';
        // print_r($this->catToRat); echo '<br><br>';
        
        echo $this->calcShortestPath();
        exit;
    }

    /**
     * 驗證地圖條件
     * 
     * @return boolean
     */
    public function validate($map)
    {
        if (sizeof($map) <= 0 || sizeof($map) > 20 || strlen($map[0]) <= 0 || strlen($map[0]) > 30) {
            return false;
        }
        
        return true;
    }

    /**
     * 計算貓到老鼠 和 老鼠到老鼠之間的最短距離
     * 
     * @return integer
     */
    public function shortestPath($map, $rat, $cat, $visit, $dis, &$oneToOne)
    {         
        if ($this->getRat($rat, $cat)) {
            $dis--;
            
            if ($oneToOne[$map[$rat[0]][$rat[1]]] > $dis || is_null($oneToOne[$map[$rat[0]][$rat[1]]])) {
                $oneToOne[$map[$rat[0]][$rat[1]]] = $dis;
            }
            
            return $dis;
        }
        
        if ($dis <> 0 && (!$this->isPass($map, $cat) || $this->isVisited($cat, $visit) || (!is_null($oneToOne[$map[$rat[0]][$rat[1]]]) && $oneToOne[$map[$rat[0]][$rat[1]]] < $dis))) {
            return MAX_DISTANCE;
        }
            
        $visit[$cat[0]][$cat[1]] = 1;
        $dis++;
        
        return min(array_filter([
            $this->shortestPath($map, $rat, [$cat[0] + 1, $cat[1]], $visit, $dis, $oneToOne),
            $this->shortestPath($map, $rat, [$cat[0] - 1, $cat[1]], $visit, $dis, $oneToOne),
            $this->shortestPath($map, $rat, [$cat[0], $cat[1] + 1], $visit, $dis, $oneToOne),
            $this->shortestPath($map, $rat, [$cat[0], $cat[1] - 1], $visit, $dis, $oneToOne)
        ]), 0);
    }

    /**
     * 貓不在地圖內，或是貓在1的位置 (是否可走的位置。是: true/ 否:false)
     * 
     * @return boolean
     */
    public function isPass($map, $cat) 
    {
        if (!isset($map[$cat[0]][$cat[1]]) || $map[$cat[0]][$cat[1]] !== '0' || $cat[0] < 0 || $cat[1] < 0) {
            return false;
        }
        
        return true;
    }

    /**
     * 貓是否走(抓)到老鼠的位置 (是:true/ 否:false)
     * 
     * @return boolean
     */
    public function getRat($rat, $cat)
    {
        if ($rat[0] == $cat[0] && $rat[1] == $cat[1]) {
            return true;
        }
        
        return false;
    }

    /**
     * 是否已走過 (是:true /否:false)
     * 
     * @return boolean
     */
    public function isVisited($cat, $visit)
    {
        if ($visit[$cat[0]][$cat[1]] == true) {
            return true;
        }
        
        return false;
    }
    
    public function calcShortestPath()
    {
        $sortedArray = $this->rotate($this->catToRat);
        
        $shortestWay = '';
        $min = 0;
        
        foreach ($sortedArray as $ways => $arr) {
            $path = '';
            foreach ($arr as $key => $ratCode) {
                if ($key == 0) {
                    $cost = $this->catToRat[$ratCode];
                    $path = $path . $this->catToRat[$ratCode] . $ratCode;
                } else {
                    $cost += $this->ratToRat[$arr[$key - 1]][$ratCode];
                    $path = $path . $this->ratToRat[$arr[$key - 1]][$ratCode] . $ratCode;
                }
            }
            if ($min == 0 || $cost < $min) {
                $min         = $cost;
                $shortestWay = $path;
            } 
        }
        
        return $shortestWay;
    }
    
    public function rotate($arr)
    {
        foreach ($arr as $key => $val) {
            $tempArr[] = $key;            
        }
        
        for ($i = 0; $i < $this->factory(sizeof($tempArr)) / sizeof($tempArr); $i++) {
            for ($j = 0; $j < sizeof($tempArr); $j++) {
                if ($j <> 0) {
                    $temp = array_shift($tempArr);
                    array_push($tempArr, $temp);
                }
                $sortedArray[] = $tempArr;
            }
            $first = array_shift($tempArr);
            $last = array_pop($tempArr);
            array_push($tempArr, $first);
            array_unshift($tempArr, $last);
        }
        
        return $sortedArray;
    }
    
    public function factory(int $n)
    {
        if ($n == 1)
            return 1;
        else {
            return $this->factory($n - 1) * $n;
        }
    }
}