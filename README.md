# tncode

- 进一步封装取出session，使用redis保存，适应前后端分离
- 具体使用方式请参考<a href="https://github.com/binwind8/tncode">binwind8/tncode</a>
- 警告：千万不要直接通过html访问，会出现各种莫名奇妙的情况，请通过web服务器访问html文件（个人血泪）
- 静态文件单独存放在/static/ 目录内
- 根据开发者的习惯，修改验证返回的结果
tn_code.js
```
_send_result_success: function (responseText, responseXML) {
        tncode._doing = false;
        if (responseText == 'ok') { // 修改为开发者的返回习惯，修改
            tncode._tncode.innerHTML = '√验证成功';
            tncode._showmsg('√验证成功', 1);
            tncode._result = true;
            document.getElementByClassName('hgroup').style.display = "block";
            setTimeout(tncode.hide, 3000);
            if (tncode._onsuccess) {
                tncode._onsuccess();
            }
        } else {
            console.log('jalsdfjlawd')
            console.log(responseText, responseXML);
            var obj = document.getElementById('tncode_div');
            addClass(obj, 'dd');
            setTimeout(function () {
                removeClass(obj, 'dd');
            }, 200);
            tncode._result = false;
            tncode._showmsg('验证失败');
            tncode._err_c++;
            if (tncode._err_c > 5) {
                tncode.refresh();
            }
        }
    },
```


**项目转载自<a href="https://github.com/binwind8/tncode">binwind8/tncode</a>,本人作进一步封装，如有侵权，请联系894806814@qq.com,将删除**

# 下载
```
composer require lyh/tncode:dev-master
```

# 使用(仅laravel)
```
<?php

<?php


namespace App\Service;


use Illuminate\Support\Facades\Redis;
use Service\TnCode;

class TncodeService
{
    public function make($type = 0)
    {
        $tn  = new TnCode();
        $r   = $tn->make();
        $key = self::getTypeAttribute($type);
        Redis::set($key, $r); // 有效时长30s
        Redis::expire($key, 60);
    }

    /**
     * 验证
     * @param $type 验证类型
     * @param $offset 偏移量
     * @param bool $isclear 是否清除
     * @return bool
     */
    public function check($offset, $type = 0)
    {
        $key      = self::getTypeAttribute($type);
        $tn       = new TnCode();
        $tncode_r = Redis::get($key);
        if (empty($tncode_r)) return false;
        Redis::del($key); // 只能检查一次
        $r = $tn->check($offset, $tncode_r);
        if (empty($r)) return false;

        $rand = md5(rand(10000000, 99999999));
        Redis::hset('tncode:rand', $rand, time() + 600); // 有效期

        return $rand;
    }

    /**
     * 检查随机值是否有效
     * @param $rand
     * @return bool
     */
    public static function checkRand($rand)
    {
        $key = Redis::hget('tncode:rand', $rand);
        if (empty($key)) return false;
        if (time() > $key) return false;
        Redis::hset($key, $rand);
        return true;
    }

    public static function getTypeAttribute($type = 0)
    {
        $typeArr = ['mixd', 'login', 'register', 'pay', 'withdraw',];
        if (!array_key_exists($type, $typeArr)) {
            $type = 0;
        }
        return "tncode:" . $typeArr[$type];
    }
}
```
