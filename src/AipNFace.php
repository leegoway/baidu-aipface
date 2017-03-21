<?php

namespace leegoway\aipface;

use leegoway\aipface\lib\AipAKSKBase;

class AipNFace extends AipAKSKBase
{

	private $userAddUrl = '/api/v1/faceverify/user/add';
    private $userDelUrl = '/api/v1/faceverify/user/delete';
    private $userGetUrl = '/api/v1/faceverify/user/get';
    private $userUpdateUrl = '/api/v1/faceverify/user/update';
    private $userIdentifyUrl = '/api/v1/faceverify/user/identify';
    private $userVerifyUrl = '/api/v1/faceverify/user/verify';
    private $groupUrl = '/api/v1/faceverify/app/getgroups';
    private $groupUsersUrl = '/api/v1/faceverify/group/getusers';


	public function AddUser($uid, $images, $userInfo, $group = 'elong')
	{
		$data = array();
        $data['images'] = $this->getEncodeImages($images);
        $data['uid'] = $uid;
        $data['group_id'] = $group;
        $data['user_info'] = json_encode($userInfo);
        return $this->request($this->userAddUrl, $data); 
	}

    public function UpdateUser($uid, $images)
    {
        $data = array();
        $data['images'] = $this->getEncodeImages($images);
        $data['uid'] = $uid;
        return $this->request($this->userUpdateUrl, $data); 
    }

    public function GetUser($uid, $group = 'elong')
    {
        $data = array();
        $data['uid'] = $uid;
        $data['group_id'] = $group;
        return $this->request($this->userGetUrl, $data); 
    }

    public function DeleteUser($uid)
    {
        $data = array();
        $data['uid'] = $uid;
        return $this->request($this->userDelUrl, $data); 
    }

    public function VerifyUser($uid, $images, $group = 'elong')
    {
        $data = array();
        $data['images'] = $this->getEncodeImages($images);
        $data['uid'] = $uid;
        $data['group_id'] = $group;
        return $this->request($this->userVerifyUrl, $data); 
    }

    public function IdentifyUser($image, $group = 'elong')
    {
        $data = array();
        $data['group_id'] = $group;
        $data['images'] = base64_encode($image);
        return $this->request($this->userIdentifyUrl, $data); 
    }

    public function IdentifyUsers($images)
    {
        $data = array();
        $data['images'] = $this->getEncodeImages($images);
        return $this->request($this->userIdentifyUrl, $data); 
    }

    public function GetGroup($start = 0, $num = 1)
    {
        $data = array();
        $data['start'] = $start;
        $data['num'] = $num;
        return $this->request($this->groupUrl, $data); 
    }

    public function GetGroupUsers($groupId)
    {
        $data = array();
        $data['group_id'] = $groupId;
        return $this->request($this->groupUsersUrl, $data); 
    }

	/**
     * 图片 array base64 encode
     * @param  arrya $images
     * @return string
     */
    private function getEncodeImages($images){
        $result = array();
        
        foreach($images as $image){
            $result[] = base64_encode($image);
        }

        return implode(',', $result);
    }

}