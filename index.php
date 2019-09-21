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

// 抽象クラス（生き物クラス）
abstract class Creature{
  protected $name;
  protected $hp;
  protected $attackMin;
  protected $attackMax;
  abstract public function sayCry();
  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
  public function setHp($num){
    $this->hp = $num;
  }
  public function getHp(){
    return $this->hp;
  }
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0,4)){ //5分の1の確率でクリティカル
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒット!!');
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージを与えた！');
  }
}

// 人クラス
class Samurai extends Creature{
  protected $division;
  public function __construct($name, $division, $hp, $attackMin, $attackMax) {
    $this->name = $name;
    $this->division = $division;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  public function setDivision($num){
    $this->division= $num;
  }
  public function getDivision(){
    return $this->division;
  }
  public function sayCry(){
    // History::set($this->name.'が叫ぶ！');
    switch($this->division){
      case Division::junior :
        History::set('ぐはぁっ！');
        break;
      case Division::intermediate :
        History::set($this->name.'「ぐふ！」<br>');
        break;
      case Division::advanced :
        History::set('是非もなし…。');
        break;
    }
  }
  public function kaihuku(){
    $tiyu = 500;
    if(!mt_rand(0,3)){ //4分の1の確率で回復
      History::set('侍の傷が回復した!!<br>');
      $this->hp = $tiyu;
  }
 } 
}
// 武将ークラス
class Busho extends Creature{
  // プロパティ
  protected $img;
  // コンストラクタ
  public function __construct($name, $hp, $img, $attackMin, $attackMax) {
    $this->name = $name;
    $this->hp = $hp;
    $this->img = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  // ゲッター
  public function getImg(){
    return $this->img;
  }
  public function sayCry(){
    // History::set($this->name.'が叫ぶ！');
    History::set($this->name.'「はうっ！」<br>');
  }
}
// 火縄銃を使える武将クラス
class HinawaBusho extends Busho{
  private $hinawaAttack;
  function __construct($name, $hp, $img, $attackMin, $attackMax, $hinawaAttack) {
    // 親クラスのコンストラクタで処理する内容を継承したい場合には親コンストラクタを呼び出す。
    parent::__construct($name, $hp, $img, $attackMin, $attackMax);
    $this->hinawaAttack = $hinawaAttack;
  }
  public function getHinawaAttack(){
    return $this->hinawaAttack;
  }
  public function attack($targetObj){
    if(!mt_rand(0,2)){ //3分の1の確率で魔法攻撃
      History::set($this->name.'が火縄銃を発砲!!');
      $targetObj->setHp( $targetObj->getHp() - $this->hinawaAttack );
      History::set($this->hinawaAttack.'ポイントのダメージを受けた！');
    }else{
      // 通常の攻撃の場合は、親クラスの攻撃メソッドを使うことで、親クラスの攻撃メソッドが修正されてもMagicMonsterでも反映される
      parent::attack($targetObj);
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
$samurai = new Samurai('侍', Division::intermediate, 500, 50, 150);
$bushoes[] = new Busho( '明智光秀', 100, 'img/akechi.gif',10, 40 );
$bushoes[] = new Busho( '長宗我部元親', 125, 'img/chosokabe.gif', 15, 45);
$bushoes[] = new Busho( '伊達政宗', 150, 'img/date.gif', 10, 45);
$bushoes[] = new Busho( '今川義元', 80, 'img/imagawa.gif', 10, 20 );
$bushoes[] = new Busho( '小早川隆景', 160, 'img/kobayakawa.gif', 20, 60 );
$bushoes[] = new Busho( '黒田官兵衛', 180, 'img/kuroda.gif', 15, 65 );
$bushoes[] = new Busho( '前田利家', 200, 'img/maeda.gif', 20, 60);
$bushoes[] = new Busho( '毛利元就', 200, 'img/mouri.gif', 20, 60 );
$bushoes[] = new Busho( '真田昌幸', 150, 'img/sanada.gif', 20, 60 );
$bushoes[] = new Busho( '柴田勝家', 250, 'img/shibata.gif', 20, 60 );
$bushoes[] = new Busho( '武田信玄', 300, 'img/takeda.gif', 25, 55 );
$bushoes[] = new Busho( '上杉謙信', 290, 'img/uesugi.gif', 20, 55 );
$bushoes[] = new HinawaBusho( '徳川家康', 270, 'img/tokugawa.gif', 20, 60, mt_rand(50, 100) );
$bushoes[] = new HinawaBusho( '豊臣秀吉', 270, 'img/toyotomi.gif', 20, 50, mt_rand(50, 100) );
$bushoes[] = new HinawaBusho( '織田信長', 400, 'img/oda.gif', 50, 70, mt_rand(60, 100) );

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
  History::set('ゲームリスタート！');
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
      unset($_SESSION['history']);
      History::set($_SESSION['samurai']->getName().'の攻撃！');
      $_SESSION['samurai']->attack($_SESSION['busho']);
      $_SESSION['busho']->sayCry();

      // 武将が攻撃をする
      History::set($_SESSION['busho']->getName().'の反撃！');
      $_SESSION['busho']->attack($_SESSION['samurai']);
      $_SESSION['samurai']->sayCry();
      $_SESSION['samurai']->kaihuku();
      

      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['samurai']->getHp() <= 0){
        gameOver();
      }else{
        // hpが0以下になったら、別の武将を出現させる
        if($_SESSION['busho']->getHp() <= 0){
          // unset($_SESSION['history']);
          History::set($_SESSION['busho']->getName().'を倒した！<br>');
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
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
   <h1>戦国QUEST</h1>
    <div class= "container">
      <?php if(empty($_SESSION)){ ?>
        <h2 >GAME START ?</h2>
        <form method="post">
          <input type="submit" name="start" value="▶ゲームスタート">
        </form>
      <?php }else{ ?>
        <h2><?php echo $_SESSION['busho']->getName().'が現れた!!'; ?></h2>
        
      <section>
        <div class="main_container">
          <div class="sidearea">
            <p>討ち取った首数：<?php echo $_SESSION['knockDownCount']; ?></p>
            <p>拙者の残りHP：<?php echo $_SESSION['samurai']->getHp(); ?></p>
          </div>
        <div class="mainarea">
          <img class="img" src="<?php echo $_SESSION['busho']->getImg(); ?>" >
          <p class="mainp"><?php echo $_SESSION['busho']->getName().'のHP'; ?>：<?php echo $_SESSION['busho']->getHp(); ?></p>
        </div>
      </div>
      </section>

      <section>
        <div class="action">
          <form method="post">
            <div class="flexcontainer">
              <div class="flexitem"><input type="submit" name="attack" value="▶攻撃する"></div>
              <div class="flexitem"><input type="submit" name="escape" value="▶逃げる"></div>
              <div class="flexitem"><input type="submit" name="start" value="▶ゲームリスタート"></div>
          </div>
          </form>
        </div>
      </section>

        <section>
          <div class="history">
            <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
          </div>
        </section>
    </>
    <?php }
       ?>
      

  </body>
</html>