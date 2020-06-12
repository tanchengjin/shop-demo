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
                'uri' => 'products'
            ], [
                'title' => '订单管理',
                'icon' => 'fa-bars',
                'uri' => 'orders'
            ], [
                'title' => '分类管理',
                'icon' => 'fa-bars',
                'uri' => 'categories'
            ],
        ];
        foreach ($menus as $index => $menu) {
            if (\App\AdminMenu::query()->where('title', $menu['title'])->exists()) {
                continue;
            } else {
                \Illuminate\Support\Facades\DB::transaction(function () use ($menu) {
                    \App\AdminMenu::query()->create([
                        'title' => $menu['title'],
                        'icon' => $menu['icon'],
                        'uri' => $menu['uri']
                    ]);
                });
            }
        }
    }
}
