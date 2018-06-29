<?php
namespace app\before\controller;
use think\Cache;
use think\Controller;
import('taobao.TopSdk', EXTEND_PATH, '.php');
class Seach extends Controller
{
    /**
     * 获取商品数据
     */
    public function seach(){
        $seach=input('get.n');
        $group=[];
        $i=0;
        $c = new \TopClient;
        $c->appkey = APPKEY;
        $c->secretKey =SECRET;
        $req = new \TbkDgItemCouponGetRequest;   //好劵
        $req->setAdzoneId("724460745");
        $req->setQ("$seach");
        $req->setPageSize("100");
        $req->setPageNo('1');
        $resp = $c->execute($req);
        $ary=$resp->results->tbk_coupon;
        if(empty($ary)){
            $this->assign('data','');
            return $this->fetch();
        }
        if($seach==1){
            if(Cache::get('jiu')){
                $group=Cache::get('jiu');
            }else{
                foreach ($ary as $v) {
                    preg_match_all('/\d+/', $v->coupon_info, $arr);
                    if ((float)$v->zk_final_price * 100 >= (float)$arr[0][0] * 100) {
                        $n = sprintf("%.2f", (((float)$v->zk_final_price * 100) - ((float)$arr[0][1] * 100)) / 100);
                        if ($n < (float)9.9) {
                            array_push($group,$v);
                            $group[$i]->info =$n;
                            $i++;
                        }
                    } else {
                        $group[$i]->info = 1;
                    }
                }
                Cache::set('jiu',object_to_array($group));
            }
        }else if($seach==2){
            if(Cache::get('sjiu')){
                $group=Cache::get('sjiu');
            }else{
                foreach ($ary as $v) {
                    preg_match_all('/\d+/', $v->coupon_info, $arr);
                    if ((float)$v->zk_final_price * 100 >= (float)$arr[0][0] * 100) {
                        $n = sprintf("%.2f", (((float)$v->zk_final_price * 100) - ((float)$arr[0][1] * 100)) / 100);
                        if ($n>(float)9.9&&$n<(float)19.9) {
                            array_push($group,$v);
                            $group[$i]->info =$n;
                            $i++;
                        }
                    } else {
                        $group[$i]->info = 1;
                    }
                }
                Cache::set('sjiu',object_to_array($group));
            }
        }else{
            foreach($ary as $v){
                array_push($group,$v);
                preg_match_all('/\d+/',$v->coupon_info,$arr);
                if((float)$v->zk_final_price*100>=(float)$arr[0][0]*100){
                    $group[$i]->info=sprintf("%.2f",(((float)$v->zk_final_price*100)-((float)$arr[0][1]*100))/100);
                }else{
                    $group[$i]->info=1;
                }
                $i++;
            }
        }
        $this->assign('data',object_to_array($group));
        $this->assign('time',date('Y-m-d',time()));
        return $this->fetch();
    }
}