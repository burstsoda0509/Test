<?php require_once('../Connections/ht.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$extdate = date("Y-m-d");

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE hwsn_info SET extdate=%s, warranty=%s WHERE hwsn=%s",
                       GetSQLValueString($extdate, "text"),
                       GetSQLValueString($_POST['wdatetime'], "text"),
                       GetSQLValueString($_POST['hwsn'], "text"));

  mysqli_select_db($ht, $database_ht);
  $Result1 = mysqli_query($ht, $updateSQL) or die(mysqli_error());

  $updateGoTo = "warranty_extended_process.php?hwsn=".$_POST['hwsn']."&wdate=".$_POST['wdatetime'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>延長保固期限 </title>
<link href="css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="loading.css"/>  
<!--ajax函式庫-->
<script type="text/javascript" src="../swinfo/a.js/jquery-1.11.3.min.js"></script>
<!--calender CSS函式庫-->
<link rel="stylesheet" href="jquery-ui.css"/>
<!--日曆函式庫-->
<script src="../swinfo/a.js/jquery-ui.js"></script>
<script src="../swinfo/a.js/jquery-ui.min.js"></script>
<script src="../swinfo/a.js/datepicker-zh-TW.js"></script>
<script src="../swinfo/a.js/jquery-ui-calender.js"></script>
<script type="text/javascript">
$(document).ready(function () {
  //預設為行數值為0
  $('#msg_textNum').html("0");
checkHwsn = function(){
    data = $('#hwsn').val().length;
    //將總字數除以每行佔11個字數來計算出目前行數
    rowNum = Math.round(data/11);
    $('#msg_textNum').html(rowNum);

  }
//按送出鍵動作
  $('#btn').click(function (){
    //送出資料時畫面清空
    $('#result').html("");
    $('#result').fadeIn("");
	$.ajax({
         url: 'warranty_extended_process.php',
         cache: false,
         dataType: 'html',
         type: 'post',//可改 get 或 post
         data: {
          hwsn: $('#hwsn').val(),         //傳送的值
          wdatetime: $('#wdatetime').val()
		 },
         error: function(xhr) {
           alert('request 發生錯誤');
         },
         success: function(response) {  //顯示成功的回傳結果
           $('#result').html(response);
           $('#result').fadeIn();
         }
     });
  });
  //按重置鍵動作
  $('#clean').click(function(){
  $('#msg_textNum').html("0");   //行數設為0
  $('#result').html("");         //結果區塊設為空
	$('#hwsn').val('');            //序號欄位設為空
	$('#wdatetime').val('');       //保固延長日期欄位設為空
 });
 //秀出 loading 圖片
$(document).ajaxStart(function(){
   $("#loadingImg").show();
});
//隱藏 loading 圖片
$(document).ajaxStop(function(){
   $("#loadingImg").hide();
});

})
</script>
</head>

<body>
<p id="page_title">延長保固期限</p>
<table width="100%" height="450px" border="0">
  <tr>
    <td colspan="2">
    
          <p class="warrant_extended"><label style="float:left">請輸入介面序號：</label>&nbsp;&nbsp;&nbsp;&nbsp;
          <textarea name="hwsn" id="hwsn" cols="20" rows="10" maxlength="109" onkeyup="checkHwsn()" style="font-size:15px"></textarea>最多(<b id="msg_textNum"></b>/10)筆資料
          </p>
          <p class="warrant_extended">請輸入保固到期日： 
            <input name="wdatetime" type="text" id="wdatetime" value="" />
            <br/><hr/><input id="clean" type="button" value="重置">&nbsp;<input id="btn" type="submit" value="送出" />
          </p>
          <input type="hidden" name="MM_update" value="form1">
       <!--  </form> -->
    <p>&nbsp;</p>
    </td>
    <td width="51%" align="left" valign="top">
      <!--回傳結果-->
      <div id="result"></div><br/>
        <p>
            <div id="loadingImg" style="display:none">
                <img src="images/ajax-loading.gif" width="100px" height="100px">處理中...
            </div>
        </p>
    </td>
  </tr>
</table>

</body>
</html>
