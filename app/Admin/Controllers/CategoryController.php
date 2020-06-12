<?php

namespace App\Admin\Controllers;

use App\Category;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '分类管理';

    public function edit($id, Content $content)
    {
        return $content->header('编辑')->body($this->form(true)->edit($id));
    }

    public function create(Content $content)
    {
        return $content->header('创建')->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('parent_id', __('Parent id'));
        $grid->column('level', __('Level'));
        $grid->column('is_directory', __('Is directory'))->display(function ($value) {
            return $value === 0 ? '否' : '是';
        });
        $grid->column('path', __('Path'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Category::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('parent_id', __('Parent id'));
        $show->field('level', __('Level'));
        $show->field('is_directory', __('Is directory'));
        $show->field('path', __('Path'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @param bool $isEdit
     * @return Form
     */
    protected function form($isEdit = false)
    {
        $form = new Form(new Category());

        $form->text('name', __('分类名'))->required();
        if ($isEdit) {
            $form->radio('is_directory', '是否为目录')->options([
                1 => '是',
                0 => '否'
            ])->disable();

            $form->display('parent.name', '上级分类');

        } else {
            $form->select('parent_id', '上级分类')->ajax('/admin/api/categories');

            $form->radio('is_directory', '是否为目录')->options([
                1 => '是',
                0 => '否'
            ]);
        }

        return $form;
    }

    public function apiCategories(Request $request)
    {

        $q = $request->input('q');
        $name = '%' . $q . '%';
        $category = Category::query()->where('name', 'like', $name)
            ->where('is_directory', 1)
            ->paginate();
        $category->setCollection($category->getCollection()->map(function (Category $category) {
            return ['id' => $category->id, 'text' => $category->full_name];
        }));


        return $category;
    }
}
