<?php namespace App\Shop\Http\Controllers\Admin;



/**
 * 平台充值卡
 */
class  RechargecardControl extends SystemControl {
    
    /**
     * Must be larger than page size of pagination
     */
    const EXPORT_SIZE = 100;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function indexOp() {
        Tpl::setDirquna('shop');
        Tpl::showpage('rechargecard.index');
    }
    
    protected function getConditionAndSort() {
        $condition = [];
        
        if($_REQUEST['advanced']){
            foreach(['sn', 'batchflag', 'admin_name',] as $sk){
                if(strlen($q = trim((string)$_REQUEST[ $sk ]))){
                    $condition[ $sk ] = ['like', '%' . $q . '%'];
                }
            }
            if(strlen($q = trim((string)$_REQUEST['member_name']))){
                $condition['member_name'] = $q;
            }
            if(strlen($q = trim((string)$_REQUEST['state']))){
                $condition['state'] = (int)$q;
            }
            
            $sdate = $_GET['sdate'] ? strtotime($_GET['sdate'] . ' 00:00:00') : 0;
            $edate = $_GET['edate'] ? strtotime($_GET['edate'] . ' 00:00:00') : 0;
            if($sdate > 0 || $edate > 0){
                $condition['tscreated'] = ['time', [$sdate, $edate]];
            }
            
            $sdate = $_GET['sdate2'] ? strtotime($_GET['sdate2'] . ' 00:00:00') : 0;
            $edate = $_GET['edate2'] ? strtotime($_GET['edate2'] . ' 00:00:00') : 0;
            if($sdate > 0 || $edate > 0){
                $condition['tsused'] = ['time', [$sdate, $edate]];
            }
            
        } else {
            if(strlen($q = trim($_REQUEST['query']))){
                switch($_REQUEST['qtype']) {
                    case 'sn':
                    case 'batchflag':
                    case 'admin_name':
                        $condition[ $_REQUEST['qtype'] ] = ['like', '%' . $q . '%'];
                        break;
                    case 'member_name':
                        $condition[ $_REQUEST['qtype'] ] = $q;
                        break;
                }
            }
        }
        
        switch($_REQUEST['sortname']) {
            case 'denomination':
            case 'tscreated':
            case 'tsused':
                $sort = $_REQUEST['sortname'];
                break;
            default:
                $sort = 'id';
                break;
        }
        if($_REQUEST['sortorder'] != 'asc'){
            $sort .= ' desc';
        }
        
        return [
            $condition,
            $sort,
        ];
    }
    
    public function index_xmlOp() {
        list($condition, $sort) = $this->getConditionAndSort();
        
        $model = Model('rechargecard');
        $list = (array)$model->getRechargeCardList($condition, $_REQUEST['rp'], null, $sort);
        
        $data = [];
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        
        foreach($list as $val){
            $i = [];
            
            $isUsed = $val['state'] == 1 && $val['member_id'] > 0 && $val['tsused'] > 0;
            
            $i['operation'] = $isUsed ? '--' : <<<EOB
<a class="btn green confirm-del-on-click" href="javascript:;" data-href="index.php?act=rechargecard&op=del_card&id={$val['id']}"><i class="fa fa-trash"></i>删除</a>
EOB;
            
            $i['sn'] = $val['sn'];
            $i['batchflag'] = $val['batchflag'];
            $i['denomination'] = $val['denomination'];
            $i['admin_name'] = $val['admin_name'];
            $i['tscreated'] = date('Y-m-d H:i:s', $val['tscreated']);
            
            if($isUsed){
                $i['member_name'] = $val['member_name'];
                $i['tsused'] = date('Y-m-d H:i:s', $val['tsused']);
            } else {
                $i['member_name'] = '-';
                $i['tsused'] = '';
            }
            
            $data['list'][ $val['id'] ] = $i;
        }
        
        echo Tpl::flexigridXML($data);
        exit;
    }
    
    public function add_cardOp() {
        if(!chksubmit()){
            Tpl::setDirquna('shop');
            Tpl::showpage('rechargecard.add_card');
            return;
        }
        
        $denomination = (float)$_POST['denomination'];
        if($denomination < 0.01){
            showMessage('面额不能小于0.01', '', 'html', 'error');
            return;
        }
        if($denomination > 1000){
            showMessage('面额不能大于1000', '', 'html', 'error');
            return;
        }
        
        $snKeys = [];
        
        switch($_POST['type']) {
            case '0':
                $total = (int)$_POST['total'];
                if($total < 1 || $total > 9999){
                    showMessage('总数只能是1~9999之间的整数', '', 'html', 'error');
                    exit;
                }
                $prefix = (string)$_POST['prefix'];
                if(!preg_match('/^[0-9a-zA-Z]{0,16}$/', $prefix)){
                    showMessage('前缀只能是16字之内字母数字的组合', '', 'html', 'error');
                    exit;
                }
                while(count($snKeys) < $total) {
                    $snKeys[ $prefix . md5(uniqid(mt_rand(), true)) ] = null;
                }
                break;
            
            case '1':
                $f = $_FILES['_textfile'];
                if(!$f || $f['error'] != 0){
                    showMessage('文件上传失败', '', 'html', 'error');
                    exit;
                }
                if(!is_uploaded_file($f['tmp_name'])){
                    showMessage('未找到已上传的文件', '', 'html', 'error');
                    exit;
                }
                foreach(file($f['tmp_name']) as $sn){
                    $sn = trim($sn);
                    if(preg_match('/^[0-9a-zA-Z]{1,50}$/', $sn)){
                        $snKeys[ $sn ] = null;
                    }
                }
                break;
            
            case '2':
                foreach(explode("\n", (string)$_POST['manual']) as $sn){
                    $sn = trim($sn);
                    if(preg_match('/^[0-9a-zA-Z]{1,50}$/', $sn)){
                        $snKeys[ $sn ] = null;
                    }
                }
                break;
            
            default:
                showMessage('参数错误', '', 'html', 'error');
                exit;
        }
        
        $totalKeys = count($snKeys);
        if($totalKeys < 1 || $totalKeys > 9999){
            showMessage('只能在一次操作中增加1~9999个充值卡号', '', 'html', 'error');
            exit;
        }
        
        if(empty($snKeys)){
            showMessage('请输入至少一个合法的卡号', '', 'html', 'error');
            exit;
        }
        
        $snOccupied = 0;
        $model = Model('rechargecard');
        
        // chunk size = 50
        foreach(array_chunk(array_keys($snKeys), 50) as $snValues){
            foreach($model->getOccupiedRechargeCardSNsBySNs($snValues) as $sn){
                $snOccupied++;
                unset($snKeys[ $sn ]);
            }
        }
        
        if(empty($snKeys)){
            showMessage('操作失败，所有新增的卡号都与已有的卡号冲突', '', 'html', 'error');
            exit;
        }
        
        $batchflag = $_POST['batchflag'];
        $adminName = $this->admin_info['name'];
        $ts = time();
        
        $snToInsert = [];
        foreach(array_keys($snKeys) as $sn){
            $snToInsert[] = [
                'sn' => $sn,
                'denomination' => $denomination,
                'batchflag' => $batchflag,
                'admin_name' => $adminName,
                'tscreated' => $ts,
            ];
        }
        
        if(!$model->insertAll($snToInsert)){
            showMessage('操作失败', '', 'html', 'error');
            exit;
        }
        
        $countInsert = count($snToInsert);
        $this->log("新增{$countInsert}张充值卡（面额￥{$denomination}，批次标识“{$batchflag}”）");
        
        $msg = '操作成功';
        if($snOccupied > 0){
            $msg .= "有 {$snOccupied} 个卡号与已有的未使用卡号冲突";
        }
        
        showMessage($msg, urlAdminShop('rechargecard', 'index'));
    }
    
    public function del_cardOp() {
        if(empty($_GET['id'])){
            showMessage('参数错误', '', 'html', 'error');
        }
        
        $id = trim($_GET['id']);
        if(is_string($id) && strpos($id, ',') !== false){
            $id = explode(',', $id);
        }
        
        $count = count($id);
        Model('rechargecard')->delRechargeCardById($id);
        
        $this->log("删除{$count}张充值卡（#ID: {$_GET['id']}）");
        
        $this->jsonOutput();
    }
    
    /**
     * 导出
     */
    public function export_step1Op() {
        $model = Model('rechargecard');
        
        if($_REQUEST['ids']){
            $condition = [];
            $condition['id'] = ['in', $_REQUEST['ids']];
            $sort = null;
        } else {
            list($condition, $sort) = $this->getConditionAndSort();
        }
        
        if(!is_numeric($_GET['curpage'])){
            $count = $model->getRechargeCardCount($condition);
            $array = [];
            //显示下载链接
            if($count > self::EXPORT_SIZE){
                $page = ceil($count / self::EXPORT_SIZE);
                for($i = 1; $i <= $page; $i++){
                    $limit1 = ($i - 1) * self::EXPORT_SIZE + 1;
                    $limit2 = $i * self::EXPORT_SIZE > $count ? $count : $i * self::EXPORT_SIZE;
                    $array[ $i ] = $limit1 . ' ~ ' . $limit2;
                }
                Tpl::output('list', $array);
                Tpl::output('murl', 'index.php?act=rechargecard&op=index');
                Tpl::setDirquna('shop');
                Tpl::showpage('export.excel');
                return;
            }
            
            //如果数量小，直接下载
            $data = $model->getRechargeCardList($condition, self::EXPORT_SIZE, null, $sort);
            $this->createExcel($data);
            return;
        }
        
        //下载
        $limit1 = ($_GET['curpage'] - 1) * self::EXPORT_SIZE;
        $limit2 = self::EXPORT_SIZE;
        
        $data = $model->getRechargeCardList($condition, 20, "{$limit1},{$limit2}", $sort);
        
        $this->createExcel($data);
    }
    
    /**
     * 生成excel
     * @param array $data
     */
    private function createExcel($data = []) {
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = [];
        //设置样式
        $excel_obj->setStyle(['id' => 's_title', 'Font' => ['FontName' => '宋体', 'Size' => '12', 'Bold' => '1']]);
        //header
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '充值卡卡号'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '批次标识'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '面额(元)'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '发布管理员'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '发布时间'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '领取人'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '领取时间'];
        
        //data
        foreach((array)$data as $k => $v){
            $tmp = [];
            $tmp[] = ['data' => "\t" . $v['sn']];
            $tmp[] = ['data' => "\t" . $v['batchflag']];
            $tmp[] = ['data' => "\t" . $v['denomination']];
            $tmp[] = ['data' => "\t" . $v['admin_name']];
            $tmp[] = ['data' => "\t" . date('Y-m-d H:i:s', $v['tscreated'])];
            if($v['state'] == 1 && $v['member_id'] > 0 && $v['tsused'] > 0){
                $tmp[] = ['data' => "\t" . $v['member_name']];
                $tmp[] = ['data' => "\t" . date('Y-m-d H:i:s', $v['tsused'])];
            } else {
                $tmp[] = ['data' => "\t-"];
                $tmp[] = ['data' => "\t"];
            }
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('充值卡', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('充值卡', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }
    
    /**
     * 充值卡使用明细
     */
    public function log_listOp() {
        Tpl::setDirquna('shop');
        Tpl::showpage('rechargecard.log_list');
    }
    
    protected function getLogConditionAndSort() {
        $condition = [];
        
        if($_REQUEST['advanced']){
            if(strlen($q = trim((string)$_REQUEST['member_name']))){
                $condition['member_name'] = $q;
            }
            $sdate = $_GET['sdate'] ? strtotime($_GET['sdate'] . ' 00:00:00') : 0;
            $edate = $_GET['edate'] ? strtotime($_GET['edate'] . ' 00:00:00') : 0;
            if($sdate > 0 || $edate > 0){
                $condition['add_time'] = ['time', [$sdate, $edate]];
            }
        } else {
            if(strlen($q = trim($_REQUEST['query']))){
                switch($_REQUEST['qtype']) {
                    case 'member_name':
                        $condition[ $_REQUEST['qtype'] ] = $q;
                        break;
                }
            }
        }
        
        switch($_REQUEST['sortname']) {
            case 'add_time':
                $sort = $_REQUEST['sortname'];
                break;
            default:
                $sort = 'id';
                break;
        }
        if($_REQUEST['sortorder'] != 'asc'){
            $sort .= ' desc';
        }
        
        return [
            $condition,
            $sort,
        ];
    }
    
    /**
     * 充值卡使用明细XML
     */
    public function log_list_xmlOp() {
        list($condition, $sort) = $this->getLogConditionAndSort();
        
        $model = Model('rcb_log');
        $list = (array)$model->getRechargeCardBalanceLogList($condition, $_REQUEST['rp'], null, $sort);
        
        $data = [];
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        
        foreach($list as $val){
            $i = [];
            $i['operation'] = '<span>--</span>';
            
            $i['member_name'] = $val['member_name'];
            $i['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
            
            $i['available_amount'] = $this->floatToString($val['available_amount']);
            $i['freeze_amount'] = $this->floatToString($val['freeze_amount']);
            
            $i['description'] = $val['description'];
            
            $data['list'][ $val['id'] ] = $i;
        }
        
        echo Tpl::flexigridXML($data);
        exit;
    }
    
    /**
     * 导出使用日志
     */
    public function log_export_step1Op() {
        $model = Model('rcb_log');
        
        if($_REQUEST['ids']){
            $condition = [];
            $condition['id'] = ['in', $_REQUEST['ids']];
            $sort = null;
        } else {
            list($condition, $sort) = $this->getLogConditionAndSort();
        }
        
        if(!is_numeric($_GET['curpage'])){
            $count = $model->getRechargeCardBalanceLogCount($condition);
            $array = [];
            if($count > self::EXPORT_SIZE){
                //显示下载链接
                $page = ceil($count / self::EXPORT_SIZE);
                for($i = 1; $i <= $page; $i++){
                    $limit1 = ($i - 1) * self::EXPORT_SIZE + 1;
                    $limit2 = $i * self::EXPORT_SIZE > $count ? $count : $i * self::EXPORT_SIZE;
                    $array[ $i ] = $limit1 . ' ~ ' . $limit2;
                }
                Tpl::output('list', $array);
                Tpl::output('murl', 'index.php?act=rechargecard&op=log_list');
                Tpl::setDirquna('shop');
                Tpl::showpage('export.excel');
                return;
                
            } else {
                //如果数量小，直接下载
                $data = $model->getRechargeCardBalanceLogList($condition, self::EXPORT_SIZE, null, $sort);
                
                $this->createLogExcel($data);
            }
        } else {
            //下载
            $limit1 = ($_GET['curpage'] - 1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            
            $data = $model->getRechargeCardBalanceLogList($condition, 20, "{$limit1},{$limit2}", $sort);
            
            $this->createLogExcel($data);
        }
    }
    
    /**
     * 生成使用日志excel
     * @param array $data
     */
    private function createLogExcel($data = []) {
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = [];
        //设置样式
        $excel_obj->setStyle(['id' => 's_title', 'Font' => ['FontName' => '宋体', 'Size' => '12', 'Bold' => '1']]);
        //header
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '会员名称'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '变更时间'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '可用金额(元)'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '冻结金额(元)'];
        $excel_data[0][] = ['styleid' => 's_title', 'data' => '描述'];
        
        //data
        foreach((array)$data as $k => $v){
            $tmp = [];
            $tmp[] = ['data' => "\t" . $v['member_name']];
            $tmp[] = ['data' => "\t" . date('Y-m-d H:i:s', $v['add_time'])];
            $tmp[] = ['data' => "\t" . $this->floatToString($v['available_amount'])];
            $tmp[] = ['data' => "\t" . $this->floatToString($v['freeze_amount'])];
            $tmp[] = ['data' => "\t" . $v['description']];
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('充值卡使用明细', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('充值卡使用明细', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }
    
    private function floatToString($val) {
        if($val > 0){
            return '+' . $val;
        }
        if($val < 0){
            return $val;
        }
        
        return '';
    }
}
