<?php
namespace app\before\controller;
use think\Controller;
use think\Cache;
import('taobao.TopSdk', EXTEND_PATH, '.php');
class Home extends Controller
{

    public function home(){
        $pindex=input('get.pindex');
        if(empty($pindex)||floor($pindex)!=$pindex){
            $pindex=1;
        }
        if(empty(Cache::get('data'))||$pindex>1) {
            $c = new \TopClient;
            $c->appkey = APPKEY;
            $c->secretKey = SECRET;
            $req = new \TbkDgItemCouponGetRequest;   //好劵
            $req->setAdzoneId("724460745");
            $req->setPageSize("100");
            $req->setPageNo("$pindex");
            $resp = $c->execute($req);
            $ary = $resp->results->tbk_coupon;
            $group = [];
            $i = 0;
            foreach ($ary as $v) {
                array_push($group, $v);
                preg_match_all('/\d+/', $v->coupon_info, $arr);
                if ((float)$v->zk_final_price * 100 >= (float)$arr[0][0] * 100) {
                    $group[$i]->info = sprintf("%.2f", (((float)$v->zk_final_price * 100) - ((float)$arr[0][1] * 100)) / 100);
                } else {
                    $group[$i]->info = 1;
                }
                $i++;
            }
            if($pindex==1){
                Cache::set('data',object_to_array($group));
            }
            $data=object_to_array($group);
        }else{
            $data=Cache::get('data');
        }
        $this->assign('data',$data);
        $this->assign('pindex',$pindex);
        $this->assign('time',date('Y-m-d',time()));
        return $this->fetch();
    }
    public function t(){
        Cache::clear();
    }
    public function mui(){
        return $this->fetch('mobile/mui');
    }
}


