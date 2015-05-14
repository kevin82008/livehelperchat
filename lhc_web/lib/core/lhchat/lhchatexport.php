<?php

class erLhcoreClassChatExport {

	public function chatExportXML(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/xml.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}

	public function chatExportJSON(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/json.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}
	
	public static function chatListExportXLS($chats){
		include 'lib/core/lhform/PHPExcel.php';		
		$objPHPExcel = new PHPExcel();		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);		
		$objPHPExcel->getActiveSheet()->setTitle('Report');
						
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "ID");
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor Name'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','E-mail'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Phone'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Country'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','City'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','IP'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Department'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Date'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Vote status'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Mail send'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Referrer'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Link'));
		
		$attributes = array(
			'id',
			'nick',
			'email',
			'phone',
			'country_name',
			'city',
			'ip',
			'user',
			'department'
		);
		
		$i = 2;
		foreach ($chats as $item) {
			foreach ($attributes as $key => $attr) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item->{$attr});
			}
			$key++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i,  date(erLhcoreClassModule::$dateFormat,$item->time));
			
			$key++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i,  ($item->fbst == 1 ? 'UP' : ($item->fbst == 2 ? 'DOWN' : 'NONE')));
			
			$key++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $item->mail_send == 1 ? 'Yes' : 'No');
			
			$key++;	
			if ($item->session_referrer != ''){
				$referer = parse_url($item->session_referrer);				
				if (isset($referer['host'])) {					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $referer['host']);
				}				
			}			
			$key++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, "URL");
			$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($key, $i)->getHyperlink()->setUrl(erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('user/login').'/(r)/'.rawurlencode(base64_encode('chat/single/'.$item->id)));
			
			$i++;
		}
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
		
		// It will be called file.xls
		header('Content-Disposition: attachment; filename="report.xlsx"');
		
		// Write file to the browser
		$objWriter->save('php://output');
	}
}

?>