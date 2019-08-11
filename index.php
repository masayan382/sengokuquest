<?php
ini_set("display_errors", 1);  
error_reporting(E_ALL);  
ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う

// 武将達格納用
$busho = array();
// クラス（設計図）の作成。クラスの頭は大文字が習わし。
// 階級クラス
class Division{
  const junior = 1;
  const intermediate = 2;
  const advanced = 3;
}
// 人クラス
class Samurai{
  protected $name;
  protected $division;
  protected $hp;
  protected $attackMin;
  protected $attackMax;
  public function __construct($name, $division, $hp, $attackMin, $attackMax) {
    $this->name = $name;
    $this->division = $division;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
  public function setDivision($num){
    $this->division = $num;
  }
  public function getDivision(){
    return $this->division;
  }
  public function setHp($num){
    $this->hp = $num;
  }
  public function getHp(){
    return $this->hp;
  }
  public function sayCry(){
    switch($this->division){
      case Division::junior :
        History::set('ぐはぁっ！');
        break;
      case Division::intermediate :
        History::set('ぐふ！');
        break;
      case Division::advanced :
        History::set('是非もなし…。');
        break;
    }
  }
  public function attack(){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0,4)){ //5分の1の確率でクリティカル
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒット!!');
    }
    $_SESSION['busho']->setHp($_SESSION['busho']->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージを与えた！');
  }
}
// モンスタークラス
class Busho{
  // プロパティ
  protected $name;
  protected $hp;
  protected $img;
  protected $attack;
  // コンストラクタ
  public function __construct($name, $hp, $img, $attack) {
    $this->name = $name;
    $this->hp = $hp;
    $this->img = $img;
    $this->attack = $attack;
  }
  // メソッド
  public function attack(){
    $attackPoint = $this->attack;
    if(!mt_rand(0,6)){ //7分の1の確率で武将の渾身の一撃
      $attackPoint *= 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'の渾身の一撃!!');
    }
    $_SESSION['samurai']->setHp( $_SESSION['samurai']->getHp() - $attackPoint );
    History::set($attackPoint.'ポイントのダメージを受けた！');
  }
  // セッター
  public function setHp($num){
    $this->hp = filter_var($num, FILTER_VALIDATE_INT);
  }
  public function setAttack($num){
    $this->attack = (int)filter_var($num, FILTER_VALIDATE_FLOAT);
  }
  // ゲッター
  public function getName(){
    return $this->name;
  }
  public function getHp(){
    return $this->hp;
  }
  public function getImg(){
    return $this->img;
  }
  public function getAttack(){
    return $this->attack;
  }
}
// 火縄銃を使える武将クラス
class HinawaBusho extends Busho{
  private $hinawaAttack;
  function __construct($name, $hp, $img, $attack, $hinawaAttack) {
    // 親クラスのコンストラクタで処理する内容を継承したい場合には親コンストラクタを呼び出す。
    parent::__construct($name, $hp, $img, $attack);
    $this->hinawaAttack = $hinawaAttack;
  }
  public function getHinawaAttack(){
    return $this->hinawaAttack;
  }
  public function attack(){
    $attackPoint = $this->attack;
    if(!mt_rand(0,2)){ //3分の1の確率で魔法攻撃
      History::set($this->name.'が火縄銃を発泡!!');
      $_SESSION['samurai']->setHp( $_SESSION['samurai']->getHp() - $this->hinawaAttack );
      History::set($this->hinawaAttack.'ポイントのダメージを受けた！');
    }else{
      // 通常の攻撃の場合は、親クラスの攻撃メソッドを使うことで、親クラスの攻撃メソッドが修正されてもMagicMonsterでも反映される
      parent::attack();
    }
  }
}
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History{
  public static function set($str){
    // セッションhistoryが作られてなければ作る
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}

// インスタンス生成
$samurai = new Samurai('侍', Division::intermediate, 700, 50, 150);
$bushoes[] = new Busho( '明智光秀', 100, 'img/akechi.gif', mt_rand(10, 40) );
$bushoes[] = new Busho( '長宗我部元親', 125, 'img/chosokabe.gif', mt_rand(15, 45) );
$bushoes[] = new Busho( '伊達政宗', 150, 'img/date.gif', mt_rand(10, 45) );
$bushoes[] = new Busho( '今川義元', 80, 'img/imagawa.gif', mt_rand(10, 20) );
$bushoes[] = new Busho( '小早川隆景', 160, 'img/kobayakawa.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '黒田官兵衛', 180, 'img/kuroda.gif', mt_rand(15, 65) );
$bushoes[] = new Busho( '前田利家', 200, 'img/maeda.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '毛利元就', 200, 'img/mouri.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '真田昌幸', 150, 'img/sanada.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '柴田勝家', 250, 'img/shibata.gif', mt_rand(20, 60) );
$bushoes[] = new Busho( '武田信玄', 300, 'img/takeda.gif', mt_rand(25, 55) );
$bushoes[] = new Busho( '上杉謙信', 290, 'img/uesugi.gif', mt_rand(20, 55) );
$bushoes[] = new HinawaBusho( '徳川家康', 270, 'img/tokugawa.gif', mt_rand(20, 60), mt_rand(50, 100) );
$bushoes[] = new HinawaBusho( '豊臣秀吉', 270, 'img/toyotomi.gif', mt_rand(20, 50), mt_rand(50, 100) );
$bushoes[] = new HinawaBusho( '織田信長', 400, 'img/oda.gif', mt_rand(50, 70), mt_rand(60, 100) );

function createBusho(){
  global $bushoes;
  $busho =  $bushoes[mt_rand(0, 14)];
  History::set($busho->getName().'が現れた！');
  $_SESSION['busho'] =  $busho;
}
function createSamurai(){
  global $samurai;
  $_SESSION['samurai'] =  $samurai;
}
function init(){
  History::clear();
  History::set('初期化します！');
  $_SESSION['knockDownCount'] = 0;
  createSamurai();
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
    History::set('ゲームスタート！');
    init();
  }else{
    // 攻撃するを押した場合
    if($attackFlg){

      // 武将に攻撃を与える
      History::set('拙者が攻撃した！');
      $_SESSION['samurai']->attack();

      // 武将が攻撃をする
      $_SESSION['busho']->attack();
      
      // 自分が叫ぶ
      $_SESSION['samurai']->sayCry();

      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['samurai']->getHp() <= 0){
        gameOver();
      }else{
        // hpが0以下になったら、別の武将を出現させる
        if($_SESSION['busho']->getHp() <= 0){
          History::set($_SESSION['busho']->getName().'を倒した！<br>');
          // unset($_SESSION['history']);
          createBusho();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        }
      }
    }else{ //逃げるを押した場合
      History::set('逃げた！');
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
        <h2><?php echo $_SESSION['busho']->getName().'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['busho']->getImg(); ?>" style="width:220px; height:auto; margin:10px auto 0px auto; display:block;">
        </div>
        <p style="font-size:14px; margin-top:110px; text-align:center;">武将のHP：<?php echo $_SESSION['busho']->getHp(); ?></p>
        <p>討ち取った首数：<?php echo $_SESSION['knockDownCount']; ?></p>
        <p>拙者の残りHP：<?php echo $_SESSION['samurai']->getHp(); ?></p>
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