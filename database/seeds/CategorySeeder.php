<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private $categories = [
        [
            'name' => '电子产品',
            'children' => [
                [
                    'name' => '笔记本',
                    'children' => [
                        [
                            'name' => '超级笔记本'
                        ]
                    ],
                ], [
                    'name' => '台式电脑',
                    'children' => [
                        [
                            'name' => '高配台式机'
                        ]
                    ]
                ], [
                    'name' => '服务器',
                    'children' => [
                        ['name' => '超级计算机'],
                        ['name' => '普通运算型计算机'],
                    ],
                ], [
                    'name' => '智能手表'
                ]
            ],
        ],
        [
            'name' => '服装鞋帽'
        ], [
            'name' => '日常用品'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->categories as $data) {
            $this->create($data);
        }
    }

    public function create($data, $parent = null)
    {
        $category = new \App\Category(['name' => $data['name']]);

        $category->is_directory = isset($data['children']);

        if (!is_null($parent)) {
            $category->parent()->associate($parent);
        }

        $category->save();

        if (isset($data['children']) && !empty($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->create($child, $category);
            }
        }
    }
}
