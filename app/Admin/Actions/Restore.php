<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Restore extends RowAction
{
    protected $selector = '.restore';

    public $name='恢复';
    public function handle(Model $model)
    {
        // $request ...
        $model->restore();
        return $this->response()->success('Success message...')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default restore"></a>
HTML;
    }
}
