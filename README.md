# 百度人脸识别API

## 项目来源
最近在使用百度的人脸识别功能，看了一下百度提供的基础API和SDK，后续需要一些高级接口等所以就在原来SDK的基础上做了一些封装，实现了AKSK认证和高级接口API。

## 安装方法
本项目推荐使用composer安装，请先安装composer。
执行以下命令安装

```
$ php composer.phar require leegoway/yii2-uic "dev-master"
```

或者在composer.json中添加依赖并执行composer install。

```
"leegoway/yii2-uic": "dev-master"
```


## 使用方法

```php
    use use leegoway\aipface\AipNFace;
    ...

    $accessKey = '';
    $secretKey = '';
    $aipface = new AipNFace($accessKey, $secretKey);
    $baiduUser = $aipFace->GetUser($user->employeenumber);
    if(isset($baiduUser['error_code'])){
        echo $baduUser['error_msg'];
    }else{
        echo 'success';var_dump($baiduUser);
    }
];
```
