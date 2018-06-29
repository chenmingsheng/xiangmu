<?php
require('connect.php');        //引入数据库配置文件和公共函数文件
require('db_sql.php');        //引入数据库操作文件
$link=db_connect();                //连接MYSQL
$empire=new mysqlquery();        //声明数据库操作类

$c = new e\extend\taobao\TopSdk;
$c->appkey = $appkey;
$c->secretKey = $secret;
$req = new TbkCouponGetRequest;
$sql=$empire->query("select intro,id from {$dbtbpre}ecms_shop");        //查询新闻表最新10条记录
while($r=$empire->fetch($sql))        //循环获取查询记录
{
    if(!empty($r['intro'])){
        $e=getUrlKeyValue($r['intro']);
        var_dump($e);exit();
        $req->setMe($e);
        $resp = $c->execute($req);
        $end_time=$resp->tbk_coupon_get_response->data->coupon_end_time;              //结束时间
        $amount=$resp->tbk_coupon_get_response->data->coupon_amount;                //优惠卷金额
        $id=$r['id'];
        $sql=$empire->query("update {$dbtbpre}ecms_shop set overtime='$end_time',productno='$amount' WHERE id=$id" );
    }


}

/**
 * @brief 正则取 url 参数
 * @param $url
 * @return
 */
function getUrlKeyValue($url)
{
    $result = array();
    $mr = preg_match_all('/(\?|&)(.+?)=([^&?]*)/i', $url, $matchs);
    if ($mr !== false) {
        for ($i = 0; $i < $mr; $i++) {
            $result[$matchs[2][$i]] = $matchs[3][$i];
        }
    }
    return $result;
}
db_close();                        //关闭MYSQL链接
$empire=null;
?>