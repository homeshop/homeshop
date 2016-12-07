<?php namespace App\Circle\Http\Controllers\Admin\Circle;

use App\Circle\Http\Controllers\Admin\SystemControl;
use App\Circle\Http\Controllers\Admin\Language;
use App\Circle\Http\Controllers\Admin\Tpl;


/**
 * 圈子举报
 */
class  InformControl extends SystemControl {
    
    public function __construct() {
        parent::__construct();
        Language::read('circle_inform');
    }
    
    public function indexOp() {
        $this->inform_listOp();
    }
    
    /**
     * 举报列表
     */
    public function inform_listOp() {
        Tpl::setDirquna('circle');
        Tpl::showpage('circle_inform');
    }
    
    /**
     * 输出XML数据
     */
    public function get_xmlOp() {
        $model = Model();
        $condition = [];
        if($_POST['query'] != ''){
            $condition[ $_POST['qtype'] ] = ['like', '%' . $_POST['query'] . '%'];
        }
        $order = '';
        $param = [
            'inform_id',
            'theme_name',
            'inform_content',
            'inform_time',
            'inform_state',
            'member_name',
            'member_id',
            'circle_name',
            'circle_id',
            'inform_opname',
            'inform_opid',
            'inform_opexp',
            'inform_opresult'
        ];
        if(in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], ['asc', 'desc'])){
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $inform_list = $model->table('circle_inform')->where($condition)->page($page)->order($order)->select();
        $data = [];
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        foreach($inform_list as $value){
            $param = [];
            $param['operation'] = "<a class='btn red' href=\"javascript:void(0);\" onclick=\"fg_del(" . $value['inform_id'] . ")\"><i class='fa fa-trash-o'></i>删除</a>";
            $param['inform_id'] = $value['inform_id'];
            $param['theme_name'] = "<a href=" . $this->spellInformUrl($value) . "  target=\"_blank\">" . $value['theme_name'] . "</a>";
            $param['inform_content'] = $value['inform_content'];
            $param['inform_time'] = date('Y-m-d H:i:s', $value['inform_time']);
            $param['inform_state'] = $this->informStatr(intval($value['inform_state']));
            $param['member_name'] = $value['member_name'];
            $param['member_id'] = $value['member_id'];
            $param['circle_name'] = $value['circle_name'];
            $param['circle_id'] = $value['circle_id'];
            $param['inform_opname'] = $value['inform_opname'] != '' ? $value['inform_opname'] : '--';
            $param['inform_opid'] = $value['inform_opid'] > 0 ? $value['inform_opid'] : '--';
            $param['inform_opexp'] = $value['inform_opexp'] > 0 ? $value['inform_opexp'] : '--';
            $param['inform_opresult'] = $value['inform_opresult'] != '' ? $value['inform_opresult'] : '--';
            $data['list'][ $value['inform_id'] ] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }
    
    /**
     * 删除举报
     */
    public function inform_delOp() {
        $ids = explode(',', $_GET['id']);
        if(count($ids) == 0){
            exit(json_encode(['state' => false, 'msg' => L('wrong_argument')]));
        }
        $rs = Model()->table('circle_inform')->where(['inform_id' => ['in', $ids]])->delete();
        if($rs){
            exit(json_encode(['state' => true, 'msg' => '删除成功']));
        } else {
            exit(json_encode(['state' => false, 'msg' => '删除失败']));
        }
    }
    
    /**
     * 举报URL链接
     */
    private function spellInformUrl($param) {
        if($param['reply_id'] == 0){
            return $url = 'index.php?act=theme&op=theme_detail&c_id=' . $param['circle_id'] . '&t_id=' . $param['theme_id'];
        }
        $where = [];
        $where['circle_id'] = $param['circle_id'];
        $where['theme_id'] = $param['theme_id'];
        $where['reply_id'] = ['elt', $param['reply_id']];
        $count = Model()->table('circle_threply')->where($where)->count();
        $page = ceil($count / 15);
        return $url = 'index.php?act=theme&op=theme_detail&c_id=' . $param['circle_id'] . '&t_id=' . $param['theme_id'] . '&curpage=' . $page . '#f' . $param['reply_id'];
    }
    
    /**
     * 举报状态
     */
    private function informStatr($state) {
        switch($state) {
            case 0:
                return L('circle_inform_untreated');
                break;
            case 1:
                return L('circle_inform_treated');
                break;
        }
    }
}
