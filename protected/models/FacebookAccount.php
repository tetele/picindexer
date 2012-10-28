<?php 

class FacebookAccount extends Account {
	protected function beforeValidate() {
		$this->type = 'facebook';
		return parent::beforeValidate();
	}
	
	public function getName() {
		Yii::trace('FacebookAccount::getName');
		if($this->isNewRecord) {
			Yii::log('Trying to get name of new record', 'warning');
			return '';
		}
		
		$fb = Yii::app()->facebook;
		$fb->setAccessToken($this->access_token);
		
		$user = $fb->api('/me');
		
		return $user['name'];
	}
}