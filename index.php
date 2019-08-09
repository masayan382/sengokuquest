<?php

ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う

// 自分のHP
define("MY_HP", 700);
// モンスター達格納用
$busho = array();
// クラス（設計図）の作成。クラスの頭は大文字が習わし。
class Busho{
  // プロパティ
  public $name; // 定義しただけだとnullが入る
  public $hp;
  public $img;
  public $attack = ''; // nullを入れたくない場合、空文字などで初期化する
  // コンストラクタ（関数）
  public function __construct($name, $hp, $img, $attack) {
    $this->name = $name;
    $this->hp = $hp;
    $this->img = $img;
    $this->attack = $attack;
  }
  // メソッド
  public function attack(){
    $_SESSION['myhp'] -= $this->attack;
    $_SESSION['history'] .= $this->attack.'ポイントのダメージを受けた！<br>';
  }
}
// インスタンス生成
$bushoes[] = new Busho( '明智光秀', 100, 'img/akechi.gif', mt_rand(10, 40) );
$bushoes[] = new Busho( '長宗我部元親', 125, 'img/chosokabe.gif', mt_rand(15, 45) );
$bushoes[] = new Busho( '伊達政宗', 150, 'img/date.gif', mt_rand(10, 45) );
$bushoes[] = new Busho( '今川義元', 80, 'img/imagawa.gif', mt_rand(10, 20) );
$bushoes[] = new Busho( '小早川隆景', 160, 'img/kobayakawa.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '黒田官兵衛', 180, 'img/kuroda.gif', mt_rand(15, 65) );
$bushoes[] = new Busho( '前田利家', 200, 'img/maeda.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '毛利元就', 200, 'img/mouri.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '織田信長', 400, 'img/oda.gif', mt_rand(50, 70) );
$bushoes[] = new Busho( '真田昌幸', 150, 'img/sanada.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '柴田勝家', 250, 'img/shibata.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '武田信玄', 300, 'img/takeda.gif', mt_rand(25, 55) );
$bushoes[] = new Busho( '徳川家康', 270, 'img/tokugawa.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '豊臣秀吉', 270, 'img/toyotomi.gif', mt_rand(20, 50) );
$bushoes[] = new Busho( '上杉謙信', 290, 'img/uesugi.gif', mt_rand(20, 55) );

function createBusho(){
  global $bushoes;
  $busho =  $bushoes[mt_rand(0, 14)];
  $_SESSION['history'] .= $busho->name.'が現れた！<br>';
  $_SESSION['busho'] =  $busho;
}
function init(){
  $_SESSION['history'] .= '初期化します！<br>';
  $_SESSION['knockDownCount'] = 0;
  $_SESSION['myhp'] = MY_HP;
  createBusho();
}
function gameOver(){
  $_SESSION = array();
}


//1.post送信されていた場合
if(!empty($_POST)){
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  error_log('POSTされた！');

  if($startFlg){
    $_SESSION['history'] = 'ゲームスタート！<br>';
    init();
  }else{
    // 攻撃するを押した場合
    if($attackFlg){
      $_SESSION['history'] .= '攻撃した！<br>';

      // ランダムで武将に攻撃を与える
      $attackPoint = mt_rand(50,100);
      $_SESSION['busho']->hp -= $attackPoint;
      $_SESSION['history'] .= $attackPoint.'ポイントのダメージを与えた！<br>';
      // 武将から攻撃を受ける
      $_SESSION['busho']->attack();

      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['myhp'] <= 0){
        gameOver();
      }else{
        // hpが0以下になったら、別の武将を出現させる
        if($_SESSION['busho']->hp <= 0){
          $_SESSION['history'] .= $_SESSION['busho']->name.'を倒した！<br>';
          unset($_SESSION['history']);
          createBusho();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        }
      }
    }else{ //逃げるを押した場合
      $_SESSION['history'] .= '逃げた！<br>';
      createBusho();
    }
  }
  $_POST = array();
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>戦国QUEST</title>
    <style>
    	body{
	    	margin: 0 auto;
	    	padding: 10px;
	    	width: 25%;
	    	background: #fbfbfa;
        color: white;
    	}
    	h1{ color: white; font-size: 20px; text-align: center;}
      h2{ color: white; font-size: 16px; text-align: center;}
    	form{
	    	overflow: hidden;
    	}
    	input[type="text"]{
    		color: #545454;
	    	height: 60px;
	    	width: 100%;
	    	padding: 5px 10px;
	    	font-size: 16px;
	    	display: block;
	    	margin-bottom: 10px;
	    	box-sizing: border-box;
    	}
      input[type="password"]{
    		color: #545454;
	    	height: 60px;
	    	width: 100%;
	    	padding: 5px 10px;
	    	font-size: 16px;
	    	display: block;
	    	margin-bottom: 10px;
	    	box-sizing: border-box;
    	}
    	input[type="submit"]{
	    	border: none;
	    	padding: 15px 30px;
	    	margin-bottom: 15px;
	    	background: black;
	    	color: white;
	    	float: right;
    	}
    	input[type="submit"]:hover{
	    	background: #3d3938;
	    	cursor: pointer;
    	}
    	a{
	    	color: #545454;
	    	display: block;
    	}
    	a:hover{
	    	text-decoration: none;
    	}
    </style>
  </head>
  <body>
   <h1 style="text-align:center; color:#333;">戦国QUEST</h1>
    <div style="background:black; padding:15px; position:relative;">
      <?php if(empty($_SESSION)){ ?>
        <h2 style="margin-top:60px;">GAME START ?</h2>
        <form method="post">
          <input type="submit" name="start" value="▶ゲームスタート">
        </form>
      <?php }else{ ?>
        <h2><?php echo $_SESSION['busho']->name.'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['busho']->img; ?>" style="width:220px; height:auto; margin:10px auto 0px auto; display:block;">
        </div>
        <p style="font-size:14px; margin-top:110px; text-align:center;">武将のHP：<?php echo $_SESSION['busho']->hp; ?></p>
        <p>討ち取った首数：<?php echo $_SESSION['knockDownCount']; ?></p>
        <p>拙者の残りHP：<?php echo $_SESSION['myhp']; ?></p>
        <form method="post">
          <input type="submit" name="attack" value="▶攻撃する">
          <input type="submit" name="escape" value="▶逃げる">
          <input type="submit" name="start" value="▶ゲームリスタート">
        </form>
      <?php } ?>
      <div style="position:absolute; right:-300px; top:0; color:black; width: 250px;">
        <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>

  </body>
</html>