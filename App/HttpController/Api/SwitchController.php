<?php
namespace App\HttpController\Api;
use App\HttpController\Base;
use App\Models\EshopGame;


class SwitchController extends Base
{
    public function getlist()
    {
        $params = $this->request()->getRequestParam();
        $page = isset($params['page'])?$params['page']:1;
        $perpage = isset($params['perpage'])?$params['perpage']:10;
        $name = isset($params['name'])?$params['name']:'';
        $model = EshopGame::create()->order('sale_time','DESC')->limit($perpage * ($page - 1), $perpage)->withTotalCount();
        if( $name){
            $model->where('game_name','%'.$name.'%','like');
        }
        // 列表数据
        $list = $model->all(null);
        $result = $model->lastQueryResult();
        // 总条数
        $total = $result->getTotalCount();
        $finalresult = ['current_page'=>$page,'total'=>$total,'data'=>$list];
        return $this->writeJson(200,$finalresult,'获取数据成功');
    }

}