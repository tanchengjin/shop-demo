<?php

use Illuminate\Database\Seeder;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $menus = [
            [
                'title' => '商品管理',
                'icon' => 'fa-bars',
                'uri' => '',
                'children' => [
                    [
                        'title' => '普通商品',
                        'icon' => 'fa-bars',
                        'uri' => 'products',
                    ], [
                        'title' => '众筹商品',
                        'icon' => 'fa-bars',
                        'uri' => 'crowdfundingProducts'
                    ],[
                        'title'=>'秒杀商品',
                        'icon'=>'fa-bars',
                        'uri'=>'seckills'
                    ]
                ],
            ], [
                'title' => '订单管理',
                'icon' => 'fa-bars',
                'uri' => 'orders'
            ], [
                'title' => '分类管理',
                'icon' => 'fa-bars',
                'uri' => 'categories'
            ], [
                'title' => '优惠券管理',
                'icon' => 'fa-bars',
                'uri' => 'coupons'
            ],
        ];

        $this->createMenus($menus);
    }

    public function createMenus($menus, $parent_id = 0)
    {

        foreach ($menus as $index => $menu) {
            if (\App\AdminMenu::query()->where('title', $menu['title'])->exists()) {
                #跳过本次，执行下一条
                continue;
            } else {
                \Illuminate\Support\Facades\DB::transaction(function () use ($menu, $parent_id) {
                    $data = \App\AdminMenu::query()->create([
                        'title' => $menu['title'],
                        'icon' => $menu['icon'],
                        'uri' => $menu['uri'],
                        'parent_id' => $parent_id
                    ]);

                    if (isset($menu['children']) && is_array($menu['children']) && !empty($menu['children'])) {
                        $this->createMenus($menu['children'], $data['id']);
                    }
                });
            }
        }
    }
}
