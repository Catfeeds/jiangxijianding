<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/12/10
 * Time: 3:28 PM
 */
return [
    'p1_MerId'        => "10012442782",
    'merchantKey'     => "mP42238826nuW64r7yh26DGK34o2L2m81L25RG32lD7Lo1058A7iJ28at6QS",
    #支付请求、 退款查询接口地址
    'reqURL_onLine'   => "https://www.yeepay.com/app-merchant-proxy/node",
    #订单查询，退款、撤销
    'OrderURL_onLine' => "https://cha.yeepay.com/app-merchant-proxy/command",
    //同步回调
    'callback'        => 'http://testjx.etlpx.com/index/pay/callback',
    //异步回调
    'async'           => 'http://testjx.etlpx.com/index/pay/callback',
    //商品名称
    'name'            => '江西省职业鉴定中心',
    //商品种类
    'pcat'            => '江西省职业鉴定中心',
    //商品描述
    'pdesc'           => '江西省职业鉴定中心',
    //订单有效期
    'validity'        => 7,
    //币种
    'currency'        => 'CNY',
    //业务类型
    'cmd'             => 'Buy',
    //应答机制
    'NeedResponse'    => 1,
    //订单有效期单位
    'unit'            => 'day',
];