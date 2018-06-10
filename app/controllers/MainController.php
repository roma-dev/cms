<?php

namespace app\controllers;


class MainController extends App{


    public function actionIndex(){
	
		$this->vars['title'] = 'Главная страница';
    }
	
}