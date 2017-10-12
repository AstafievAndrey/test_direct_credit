<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main,
    Bitrix\Main\Loader;

if(isset($_POST['ajax']) || isset($_GET['ajax'])){
    $request = json_decode(file_get_contents("php://input"));
    
    if (!Loader::includeModule('iblock'))
	return;
    $arrFio=[];
    $arSelect = [
        "ID", "NAME", "DATE_ACTIVE_FROM",
        "PROPERTY_SURNAME","PROPERTY_NAME",
        "PROPERTY_LASTNAME"
    ];
    $arFilter = [
        "IBLOCK_ID"=>IntVal(1), 
        "ACTIVE_DATE"=>"Y", 
        "ACTIVE"=>"Y"
    ];
    $arNavStartParams = [
        "nPageSize"=>(int)$request->nPageSize,
        "iNumPage"=>(int)$request->iNumPage
    ];
    
    $res = CIBlockElement::GetList([], $arFilter, false, $arNavStartParams, $arSelect);
    $arrFio["totalItems"]=intval($res->SelectedRowsCount());
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();  
        $arrFio["fio"][]=array(
            "ID"=>$arFields["ID"],
            "PROPERTY"=>array(
                "SURNAME"=>$arFields["PROPERTY_SURNAME_VALUE"],
                "NAME"=>$arFields["PROPERTY_NAME_VALUE"],
                "LASTNAME"=>$arFields["PROPERTY_LASTNAME_VALUE"],
                "DISABLED"=>true,
                "LOADSAVEFIO"=>false,
            )
        );
    }
    $APPLICATION->RestartBuffer();
    header('Content-Type: application/json');
    echo Main\Web\Json::encode($arrFio);
    die();
}else{
    $this->IncludeComponentTemplate();
}




//$APPLICATION->AddHeadScript($this->getPath()."/js/fio.js");


//$el = new CIBlockElement;
//
//$arLoadProductArray = Array(
//  "IBLOCK_ID"      => 1,
//  "PROPERTY_VALUES"=> array(
//       "SURNAME" => "Сидоров",
//       "NAME" => "Иван",
//       "LASTNAME" => "Иванович",
//    ),
//  "NAME"           => "Элемент",
//);
//
//if($PRODUCT_ID = $el->Add($arLoadProductArray))
//  echo "New ID: ".$PRODUCT_ID;
//else
//  echo "Error: ".$el->LAST_ERROR;