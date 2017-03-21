<?php

namespace leegoway\aipface;

use yii\base\Component;

class FaceFacade extends Component
{

	private $aipNFace = null;

    public $access_key = '';
    public $secret_key = '';

	private function getAipHandler() {
		if (null === $this->aipNFace) {
			$this->aipNFace = new AipNFace($this->access_key, $this->secret_key);
		}
		return $this->aipNFace;
	} 

	//添加用户
	public function AddUser($uid, $images, $userInfo, $group = 'elong')
	{
        $this->getAipHandler();
        return $this->aipNFace->AddUser($uid, $images, $userInfo, $group = 'elong'); 
	}

	//更新用户图像
    public function UpdateUser($uid, $images)
    {
        $this->getAipHandler();
        return $this->aipNFace->UpdateUser($uid, $images);
    }

    //获取用户
    public function GetUser($uid, $group = 'elong')
    {
        $this->getAipHandler();
        return $this->aipNFace->GetUser($uid, $group);
    }

    //删除用户
    public function DeleteUser($uid)
    {
        $this->getAipHandler();
        return $this->aipNFace->DeleteUser($uid);
    }

    //校验用户
    public function VerifyUser($uid, $images, $group = 'elong')
    {
        $this->getAipHandler();
        return $this->aipNFace->VerifyUser($uid, $images, $group);
    }

    //识别人头像
    public function IdentifyUser($image, $group = 'elong')
    {
        return $this->aipNFace->IdentifyUser($image, $group);
    }

    //识别一批人头像
    public function IdentifyUsers($images)
    {
        return $this->aipNFace->IdentifyUsers($images);
    }

    //获取分组
    public function GetGroup($start = 0, $num = 1)
    {
        $this->getAipHandler();
        return $this->aipNFace->GetGroup($start, $num);
    }

    //获取分组下的所有用户
    public function GetGroupUsers($groupId)
    {
        return $this->aipNFace->GetGroupUsers($groupId);
    }

}