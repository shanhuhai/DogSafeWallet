<?php

namespace App\Admin\Actions\Wallet;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Copy extends RowAction
{
    public $row ;
    //public $name = 'xxx';
    public $name = '<span class="private-key-grid-column-copyable text-muted" data-content="{{private_key}}" title="Copied!" data-placement="bottom">  <i class="fa fa-copy">复制</i></span>';



    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('复制成功');
    }
    public function setRow($row)
    {
        parent::setRow($row); // TODO: Change the autogenerated stub

        $this->name = str_replace('{{private_key}}', $row->encrypted_private_key, $this->name);
    }


//    public function html()
//    {
//        return <<<HTML
//        <a class="btn btn-sm btn-default import-post">导入数据</a>
//HTML;
//    }

}
