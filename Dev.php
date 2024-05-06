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
        'OddValue' => 678,
        'TypeId' => 4
    ]
];

$lowId = 2;

$query = (new \UT_Php\Collections\Linq($collection))
    -> where(
        function ($x) use ($lowId) {
            return $x['Id'] > $lowId;
        }
    )
    -> where(
        function ($x) {
            return $x['Id'] < 10;
        }
    )
    -> select(
        function ($x) {
            return [
            'Id' => $x['Id'],
            'Value' => $x['OddValue'],
            'TypeId' => $x['TypeId']
            ];
        }
    )
    -> orderBy(null, UT_Php\Enums\SortDirections::Desc)
    -> groupBy(
        function ($x) {
            return $x['TypeId'];
        }
    )
    -> avg(
        function ($x) {
            return $x['Value'];
        }
    )
    -> where(
        function ($x) {
            return $x > 250;
        }
    );
$result1 = $query -> toArray();
$result2 = $query -> firstOrDefault();
$result3 = $query -> count();

echo '<xmp>';
print_r($query);
print_r($result1);
var_dump($result2);
var_dump($result3);
echo '</xmp>';
