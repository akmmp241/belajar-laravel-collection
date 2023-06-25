<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Support\LazyCollection;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;
use function PHPUnit\Framework\assertTrue;

class collectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3]);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all(), 'waw');
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach ($collection as $key => $value) {
            self::assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });
        $this->assertEqualsCanonicalizing([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["eko"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person("eko")], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ['akmal', 'muhammad'],
            ['eko', 'kurniawan']
        ]);

        $result = $collection->mapSpread(function ($firstname, $lastname) {
            return new Person($firstname . " " . $lastname);
        });

        $this->assertEquals([
            new Person("akmal muhammad"),
            new Person("eko kurniawan")
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "eko",
                "department" => "IT"
            ],
            [
                "name" => "khannedy",
                "department" => "IT"
            ],
            [
                "name" => "kurniawan",
                "department" => "HR"
            ]
        ]);

        $result = $collection->mapToGroups(function ($item) {
            return [
                $item['department'] => $item['name']
            ];
        });

        $this->assertEquals([
            "IT" => collect(['eko', 'khannedy']),
            "HR" => collect(['kurniawan'])
        ], $result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);

        $result = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6])
        ], $result->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);

        $result = $collection1->concat($collection2);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $result->all());
    }

    public function testCombine()
    {
        $collection1 = collect(['name', 'country']);
        $collection2 = collect(['akmal', 'indonesia']);

        $result = $collection1->combine($collection2);

        $this->assertEquals([
            'name' => "akmal",
            'country' => 'indonesia'
        ], $result->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);
        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "akmal",
                "hobbies" => ["coding", "Gaming"]
            ],
            [
                "name" => "joko",
                "hobbies" => ["swimming", "learning"]
            ]
        ]);

        $result = $collection->flatMap(function ($item) {
            return $item["hobbies"];
        });

        $this->assertEquals(['coding', "Gaming", 'swimming', 'learning'], $result->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(['akmal', 'muhammad', 'pridianto']);

        $this->assertEquals("akmal-muhammad-pridianto", $collection->join('-'));
        $this->assertEquals("akmal-muhammad_pridianto", $collection->join('-', '_'));
        $this->assertEquals("akmal, muhammad and pridianto", $collection->join(', ', ' and '));
    }

    public function testFiltering()
    {
        $collection = collect([
            "akmal" => 100,
            "budi" => 90,
            'joko' => 80
        ]);

        $result = $collection->filter(function ($item, $key) {
            return $item >= 90;
        });

        $this->assertEquals([
            'akmal' => 100,
            'budi' => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->filter(function ($item) {
            return $item % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6, 8, 10], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "akmal" => 100,
            "budi" => 90,
            'joko' => 80
        ]);

        [$result1, $result2] = $collection->partition(function ($item) {
            return $item >= 90;
        });

        $this->assertEquals([
            'akmal' => 100,
            'budi' => 90
        ], $result1->all());
        $this->assertEquals([
            'joko' => 80
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(['Akmal', 'Muhammad', 'Pridianto']);
        $this->assertTrue($collection->contains('Akmal'));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == "Akmal";
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            [
                "name" => "Akmal",
                "department" => "IT"
            ],
            [
                "name" => "Muhammad",
                "department" => "IT"
            ],
            [
                "name" => "Pridianto",
                "department" => "HR"
            ]
        ]);

        $result = $collection->groupBy("department");

        $this->assertEquals([
            "IT" => collect([
                [
                    "name" => "Akmal",
                    "department" => "IT"
                ],
                [
                    "name" => "Muhammad",
                    "department" => "IT"
                ]
            ]),
            "HR" => collect([
                [
                    "name" => "Pridianto",
                    "department" => "HR"
                ]
            ])
        ], $result->all());

        $result = $collection->groupBy(function ($value, $key) {
            return strtolower($value['department']);
        });

        $this->assertEquals([
            "it" => collect([
                [
                    "name" => "Akmal",
                    "department" => "IT"
                ],
                [
                    "name" => "Muhammad",
                    "department" => "IT"
                ]
            ]),
            "hr" => collect([
                [
                    "name" => "Pridianto",
                    "department" => "HR"
                ]
            ])
        ], $result->all());
    }

    public function testSlicing()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $result = $collection->slice(3);
        $this->assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9, 10], $result->all());

        $result = $collection->slice(3, 2);
        $this->assertEqualsCanonicalizing([4, 5], $result->all());
    }

    public function testTake()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->take(3);
        assertEquals([1, 2, 3], $result->all());

        $result = $collection->takeUntil(function ($value, $key) {
            return $value == 3;
        });
        assertEquals([1, 2], $result->all());

        $result = $collection->takeWhile(function ($value, $key) {
            return $value < 3;
        });
        assertEquals([1, 2], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->skip(3);
        assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->skipUntil(function ($value, $key) {
            return $value == 3;
        });
        assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->skipWhile(function ($value, $key) {
            return $value < 3;
        });
        assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testChunked()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->chunk(3);
        assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        assertEqualsCanonicalizing([4, 5, 6], $result->all()[1]->all());
        assertEqualsCanonicalizing([7, 8, 9], $result->all()[2]->all());
    }

    public function testFirst()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->first();
        assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 5;
        });
        assertEquals(6, $result);
    }

    public function testLast()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->last();
        assertEquals(9, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 5;
        });
        assertEquals(4, $result);
    }

    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->random();
        self::assertTrue(in_array($result, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
    }

    public function testCheckingExistence()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        self::assertTrue($collection->isNotEmpty());
        self::assertFalse($collection->isEmpty());
        self::assertTrue($collection->contains(1));
        self::assertFalse($collection->contains(10));
        self::assertTrue($collection->contains(function ($value, $key) {
            return $value == 8;
        }));
    }

    public function testOrdering()
    {
        $collection = collect([2, 5, 4, 3, 7, 6, 8, 9, 1]);

        $result = $collection->reverse();
        assertEqualsCanonicalizing([1, 9, 8, 6, 7, 3, 4, 5, 2], $result->all());

        $result = $collection->sort();
        assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->sortDesc();
        assertEqualsCanonicalizing([9, 8, 7, 6, 5, 4, 3, 2, 1], $result->all());
    }

    public function testAggregate()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        assertEquals(45, $collection->sum());
        assertEquals(5, $collection->avg());
        assertEquals(1, $collection->min());
        assertEquals(9, $collection->max());
    }

    public function testReduce()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        assertEquals(45, $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        }));
    }

    public function testLazyCollection()
    {
        $collection = LazyCollection::make(function () {
            $value = 0;

            while (true) {
                yield $value;
                $value++;
            }
        });

        $result = $collection->take(10);
        assertEqualsCanonicalizing([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }


}















