<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main,
    Bitrix\Main\Loader;



if(isset($_POST['ajax']) || isset($_GET['ajax'])){
    $request = json_decode(file_get_contents("php://input"));
    
    if (!Loader::includeModule('iblock'))
	return;
    
    $action = isset($_GET['action']) ? $_GET['action'] : null;
    
    switch ($action){
        case null:
            header("HTTP/1.0 404 Not Found");
            break;
        case "list":
            $res = listFio($request);
            break;
        case "addFio":
            $res = addFio($request);
            break;
    }

    $APPLICATION->RestartBuffer();
    header('Content-Type: application/json');
    echo Main\Web\Json::encode($res);
    die();
}else{
    $this->IncludeComponentTemplate();
}

function addFio($request){
    $el  = new CIBlockElement;
    $arLoadProductArray = Array(
      "IBLOCK_ID"       => 1,
      "PROPERTY_VALUES" => array(
           "SURNAME"    => trim(iconv("utf-8", "windows-1251", $request->surname)),
           "FIRSTNAME"       => trim(iconv("utf-8", "windows-1251", $request->name)),
           "LASTNAME"   => trim(iconv("utf-8", "windows-1251", $request->lastname)),
        ),
      "NAME"            => "Ёлемент",
    );
    $PRODUCT_ID = $el->Add($arLoadProductArray);
    if($PRODUCT_ID){
        $res = ["result"=>true];
    }else{
        $res = ["result"=>false,"result"=>$el->LAST_ERROR];
    }
    return  $res;
}

function listFio($request){
    $arrFio=[];
    $arSelect = ["ID", "NAME","PROPERTY_SURNAME","PROPERTY_FIRSTNAME","PROPERTY_LASTNAME"];
    $arFilter = ["IBLOCK_ID"=>IntVal(1), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"];
    $arNavStartParams = ["nPageSize"=>(int)$request->nPageSize, "iNumPage"=>(int)$request->iNumPage];
    $res = CIBlockElement::GetList([], $arFilter, false, $arNavStartParams, $arSelect);
    $arrFio["totalItems"]=intval($res->SelectedRowsCount());
    while($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();  
        $arrFio["fio"][]=array(
            "ID"=>$arFields["ID"],
            "PROPERTY"=>array(
                "SURNAME"=>$arFields["PROPERTY_SURNAME_VALUE"],
                "FIRSTNAME"=>$arFields["PROPERTY_FIRSTNAME_VALUE"],
                "LASTNAME"=>$arFields["PROPERTY_LASTNAME_VALUE"],
                "DISABLED"=>true,"LOADSAVEFIO"=>false,
            )
        );
    }
    return $arrFio;
}