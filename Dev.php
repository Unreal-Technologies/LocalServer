<?php
require_once 'Ut.php\.init';

$collection = [
    [
        'Name' => 'T1',
        'Id' => 1,
        'OddValue' => 123,
        'TypeId' => 1
    ],
    [
        'Name' => 'T2',
        'Id' => 2,
        'OddValue' => 234,
        'TypeId' => 1
    ],
    [
        'Name' => 'T3',
        'Id' => 3,
        'OddValue' => 345,
        'TypeId' => 2
    ],
    [
        'Name' => 'T4',
        'Id' => 4,
        'OddValue' => 456,
        'TypeId' => 2
    ],
    [
        'Name' => 'T5',
        'Id' => 5,
        'OddValue' => 567,
        'TypeId' => 3
    ],
    [
        'Name' => 'T6',
        'Id' => 6,
        'OddValue' =>678,
        'TypeId' => 4
    ]
];

$lowId = 2;

$query = (new \UT_Php\Collections\Linq($collection)) 
    -> Where(
        function ($x) use ($lowId) { 
            return $x['Id'] > $lowId; 
        }
    )
    -> Where(
        function ($x) {
            return $x['Id'] < 10; 
        }
    )
    -> Select(
        function ($x) {
            return [
            'Id' => $x['Id'],
            'Value' => $x['OddValue'],
            'TypeId' => $x['TypeId']
            ];
        }
    )
    -> OrderBy(null, UT_Php\Enums\SortDirections::Desc)
    -> GroupBy(
        function ($x) {
            return $x['TypeId']; 
        }
    )
    -> Avg(
        function ($x) {
            return $x['Value']; 
        }
    )
    -> Where(
        function ($x) {
            return $x > 250; 
        }
    );
$result1 = $query -> ToArray();
$result2 = $query -> FirstOrDefault();
$result3 = $query -> Count();

echo '<xmp>';
print_r($query);
print_r($result1);
var_dump($result2);
var_dump($result3);
echo '</xmp>';