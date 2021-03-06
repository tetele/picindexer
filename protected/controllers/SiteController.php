<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Logs the user using FB Connect
	 */
	public function actionLogin()
	{
		$fb = Yii::app()->facebook;
		
		$user = $fb->user;
		
		if(!$user) {
			// Not logged into FB. Either authorization was rejected or no attempt was made
			if(isset($_REQUEST['error_reason']) && $_REQUEST['error_reason'] == 'user_denied') {
				// If user denied access, issue a 401
				throw new CHttpException(401, 'You disapproved the login process. Unable to login with Facebook');
			} elseif(isset($_REQUEST['error'])) {
				$reason = isset($_REQUEST['error_reason']) ? $_REQUEST['error_reason'] : '';
				$details = isset($_REQUEST['error_description']) ? $_REQUEST['error_description'] : '';
				
				throw new ServiceException(
					'facebook',
					$_REQUEST['error'],
					$reason,
					$description
				);
			} else {
				// Try to authenticate user
				$this->redirect($fb->loginUrl);
			}
		} else {
			// User is logged into FB. Log him into our app as well
			$identity = new UserIdentity($user, '');
			$identity->authenticate();
			Yii::app()->user->login($identity);
			$this->redirect(Yii::app()->user->getReturnUrl(array('/app')));
		}
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->facebook->getLogoutUrl(array(
			'next' => $this->createAbsoluteUrl(Yii::app()->homeUrl)
		)));
	}
}