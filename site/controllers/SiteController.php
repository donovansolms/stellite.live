<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionXmr() {
      header("Content-Type: application/json");
      header("Access-Control-Allow-Origin: *");
      //header("Access-Control-Allow-Headers: *");
      echo file_get_contents("http://127.0.0.1:16000/api.json");
      exit;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
      $nodes_scratch = array(
"34.239.226.123",
"52.3.236.155",
"54.236.31.119",
"52.3.231.249",
"34.201.5.250",
      );
      $nodes_resume = array(

"54.208.125.191",
"34.231.255.14",
"34.201.14.147",
"34.201.205.52",
"34.201.6.73",
"34.234.236.135",
"18.232.144.228",
"35.153.31.242",
"54.88.149.135",
"34.200.228.18",


      );

      $data_scratch = array();
      foreach ($nodes_scratch as $node)
      {
        $data_scratch[$node] = array('error' => 'No data yet');
      }
      $data_resume = array();
      foreach ($nodes_resume as $node)
      {
        $data_resume[$node] = array('error' => 'No data yet');
      }

      $this->layout = 'blank';
      return $this->render("sync_check", [
        "scratch" => $data_scratch,
        "resume" => $data_resume,
      ]);
      //return $this->render('index');
    }

    public function actionUpdate($ip)
    {
      echo json_encode($this->getNodeHeight($ip));
      exit;
    }

    public function getNodeHeight($ip)
    {

      $ch = curl_init('http://' . $ip . ':20189/getheight');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, '');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
      ));

      $result = curl_exec($ch);
      if(curl_error($ch))
      {
	return array('error' => curl_error($ch));
//          return curl_error($ch);
      }

      $r = json_decode($result);
      $height = $r->height;

      $ch = curl_init('http://' . $ip . ':20189/json_rpc');
      $payload = json_encode( array(
        "jsonrpc"=> "2.0",
        "id"=> "0",
         "method" => "get_info",
      ) );
      //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

      $result = curl_exec($ch);
      if(curl_error($ch))
      {
return array('error' => curl_error($ch));
//          return curl_error($ch);
      }
      $r = json_decode($result);
      $alt_blocks_count = $r->result->alt_blocks_count;
      $status = $r->result->status;
      return array(
        "height" => $height,
        "alts" =>$alt_blocks_count,
        "status" => $status,
      );
    }
}
