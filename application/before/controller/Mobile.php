<?php
namespace app\before\controller;
use think\Controller;
use think\Cache;
import('taobao.TopSdk', EXTEND_PATH, '.php');
class Mobile extends Home
{
    public function mui(){
            return $this->fetch('mobile/mui');
    }
    public function many(){
        return $this->fetch('mobile/many');
    }
    public function info(){
        $pindex=input('post.page');
        if(empty($pindex)||floor($pindex)!=$pindex){
            $pindex=1;
        }

        if(empty(Cache::get('data'))||$pindex>1) {
            $ary=$this->top($pindex);
            $group = [];
            $i = 0;
            foreach ($ary as $v) {
                array_push($group, $v);
                preg_match_all('/\d+/', $v->coupon_info, $arr);
                if ((float)$v->zk_final_price * 100 >= (float)$arr[0][0] * 100) {
                    $group[$i]->info = sprintf("%.2f", (((float)$v->zk_final_price * 100) - ((float)$arr[0][1] * 100)) / 100);
                    $group[$i]->voucher=$arr[0][1];
                } else {
                    $group[$i]->info = 1;
                    $group[$i]->voucher=$arr[0][1];
                }
                $i++;
            }
            if($pindex==1){
                Cache::set('data',object_to_array($group));
            }
            $info=my_sort(object_to_array($group),'info');
        }else{
            $data=Cache::get('data');
            $info=my_sort($data,'info');
        }
        return json(['code'=>0,'info'=>$info]);
    }
    public function info2(){
        $pindex=input('post.page');
        $type=input('post.type');
        if(empty($pindex)||floor($pindex)!=$pindex){
            $pindex=1;
        }
        if(empty(Cache::get('data2'))||$pindex>1||!empty($type)) {
            $ary=$this->top($pindex,$type);
            $group = [];
            $i = 0;
            foreach ($ary as $v) {
                array_push($group, $v);
                preg_match_all('/\d+/', $v->coupon_info, $arr);
                if ((float)$v->zk_final_price * 100 >= (float)$arr[0][0] * 100) {
                    $group[$i]->info = sprintf("%.2f", (((float)$v->zk_final_price * 100) - ((float)$arr[0][1] * 100)) / 100);
                    $group[$i]->voucher=$arr[0][1];
                } else {
                    $group[$i]->info = 1;
                    $group[$i]->voucher=$arr[0][1];
                }
                $i++;
            }
            if($pindex==1){
                Cache::set('data2',object_to_array($group));
            }
            $info=my_sort(object_to_array($group),'info');
        }else{
            $data=Cache::get('data2');
            $info=my_sort($data,'info');
        }
        return json(['code'=>0,'info'=>$info]);
    }
   public function top($pindex,$type=''){
        $c = new \TopClient;
        $c->appkey = APPKEY;
        $c->secretKey = SECRET;
        $req = new \TbkDgItemCouponGetRequest;   //好劵
        $req->setAdzoneId("724460745");
        $req->setPageSize("15");
        if(!empty($type)){
            $req->setQ("$type");
        }
        $req->setPageNo("$pindex");
        $resp = $c->execute($req);
        $ary = $resp->results->tbk_coupon;
        return $ary;
    }
    public function a(){
        return $this->fetch('mobile/search');
    }
}