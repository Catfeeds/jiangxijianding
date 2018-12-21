<?php
namespace app\index\controller;
use app\index\model\Art;
use think\Controller;
use think\Db;
use think\Loader;

class IndexController extends Controller
{
//    private $Art;
//	public function __construct(Request $request)
//	{
//		parent::__construct($request);
//		$this->Art=new Art();
//	}

	public function index(){
//		$data=Request::instance("post.");
//		dump($data);die;
//		$data=Db::name('article')->select();
//		$data=$this->Art->sel();
//		dump(db('article')->select());die;
//		dump(Db::name('article')->select());die;
//		dump($data);die;
		return $this->fetch();
	}

	// 1. 生成原始的二维码(生成图片文件)
	public function scerweima($url = 'wwww')
	{
		Loader::import('phpqrcode.phpqrcode',EXTEND_PATH,'.php');
		$value = $url;                    //二维码内容
		$errorCorrectionLevel = 'H';    //容错级别
		$matrixPointSize = 8;            //生成图片大小
		//生成二维码图片
		$filename = microtime() . '.png';
		//实例化
		$qr = new \QRcode();
		//会清除缓冲区的内容，并将缓冲区关闭，但不会输出内容。
		ob_end_clean();
		//输入二维码
		$qr::png($value, false, $errorCorrectionLevel, $matrixPointSize);
		$QR = $filename;                //已经生成的原始二维码图片文件
		$QR = imagecreatefromstring(file_get_contents($QR));
		//输出图片
		imagepng($QR, 'qrcode.png');
		imagedestroy($QR);
		return '<img src="/qrcode.png" alt="使用微信扫描支付">';
	}


//2. 在生成的二维码中加上logo(生成图片文件)
	public function scerweima1($url = 'www.xqi.com',$logo = 'c:/Users/Public/Pictures/Sample Pictures/1526974586(1).jpg')
	{
		Loader::import('phpqrcode.phpqrcode',EXTEND_PATH,'.php');
		$value = $url;                    //二维码内容
		$errorCorrectionLevel = 'H';    //容错级别
		$matrixPointSize = 8;            //生成图片大小
		//生成二维码图片
		$filename =  microtime() . '.png';
		//实例化
		$qr = new \QRcode();
		//会清除缓冲区的内容，并将缓冲区关闭，但不会输出内容。
		ob_end_clean();
		//输入二维码
		$qr::png($value, $filename, $errorCorrectionLevel, $matrixPointSize,2);
		$logo = $logo;   //准备好的logo图片
		$QR = $filename;            //已经生成的原始二维码图
		if (file_exists($logo)) {
			$QR = imagecreatefromstring(file_get_contents($QR));        //目标图象连接资源。
			$logo = imagecreatefromstring(file_get_contents($logo));    //源图象连接资源。
			$QR_width = imagesx($QR);            //二维码图片宽度
			$QR_height = imagesy($QR);            //二维码图片高度
			$logo_width = imagesx($logo);        //logo图片宽度
			$logo_height = imagesy($logo);        //logo图片高度
			$logo_qr_width = $QR_width / 3;    //组合之后logo的宽度(占二维码的1/5)
			$scale = $logo_width / $logo_qr_width;    //logo的宽度缩放比(本身宽度/组合后的宽度)
			$logo_qr_height = $logo_height / $scale;  //组合之后logo的高度
			$from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点

			/*  重新组合图片并调整大小
			 *	imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
			 */
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}

		//输出图片
		imagepng($QR, 'qrcode.png');
		imagedestroy($QR);
		imagedestroy($logo);
		return '<img src="__ROOT__/qrcode.png" alt="使用微信扫描支付">';
	}

	/**
	 * 生成原始的二维码(不生成图片文件)
	 * @param string $url 二维码内容
	 * @param string $error  容错级别：L、M、Q、H
	 * @param string $matrix  生成图片大小 ：1到10
	 */
	public function scerweima2($url = 'www.xuweiqi.com',$error='Q',$matrix='5')
	{
		Loader::import('phpqrcode.phpqrcode',EXTEND_PATH,'.php');
		$value = $url;                    //二维码内容
		$errorCorrectionLevel =$error;    //容错级别
		$matrixPointSize =$matrix;            //生成图片大小
		//生成二维码图片
		//实例化
		$qr = new \QRcode();
		//会清除缓冲区的内容，并将缓冲区关闭，但不会输出内容。
		ob_end_clean();
		//输入二维码
		$qr::png($value, false, $errorCorrectionLevel, $matrixPointSize);
	}

	//生成条形码
	public function barcode_create($content='112345678'){
		// 引用barcode文件6949233177018
		//夹对应的类
		Loader::import('BCode.BCGFontFile',EXTEND_PATH);
//		Loader::import('BCode.BCGColor',EXTEND_PATH);
		Loader::import('BCode.BCGDrawing',EXTEND_PATH);
//		$content= isset($_GET['text']) ? $_GET['text'] : 'HELLO';
		// 条形码的编码格式
		Loader::import('BCode.BCGcode128',EXTEND_PATH,'.barcode.php');
		// $code = '';
		// 加载字体大小
//		$font = new BCGFontFile('./class/font/Arial.ttf', 18);
//		$font = new \BCGFontFile(Loader::import('BCode.Arial',EXTEND_PATH,'ttf'), 18);

		//颜色条形码
		$color_black = new \BCGColor(0, 0, 0);
		$color_white = new \BCGColor(255, 255, 255);

		$drawException = null;
		try
		{
			$code = new \BCGcode128();
			$code->setScale(1);
			$code->setThickness(30); // 条形码的厚度
			$code->setForegroundColor($color_black); // 条形码颜色
			$code->setBackgroundColor($color_white); // 空白间隙颜色
//			 $code->setFont($font); //
			$code->parse($content); // 条形码需要的数据内容
		}
		catch(\Exception $exception)
		{
			$drawException = $exception;
		}

		//根据以上条件绘制条形码
		$drawing = new \BCGDrawing('', $color_white);
		if($drawException) {
			$drawing->drawException($drawException);
		}else{
			$drawing->setBarcode($code);
			$drawing->draw();
		}
		ob_end_clean();
		// 生成PNG格式的图片
		header('Content-Type: image/png');
//		header('Content-Disposition:attachment; filename="barcode.png"'); //自动下载
		$drawing->finish(\BCGDrawing::IMG_FORMAT_PNG);
	}


    //压缩文件
	public function zip(){
		$img1 = $_SERVER['SERVER_NAME'].'/static/qrcode/logo.jpg';
		$img2 = $_SERVER['SERVER_NAME'].'/static/qrcode/logo2.jpg';
		$files = array($img1,$img2);
		//$files = array('upload/qrcode/1/1.jpg');
		$zipName = $_SERVER['SERVER_NAME'].'/static/qrcode/download.zip';
		$zip = new \ZipArchive;//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
		/*
         * 通过ZipArchive的对象处理zip文件
         * $zip->open这个方法如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
         * $zip->open('表示处理的zip文件名','')。
         * 这里重点说下第二个参数，它表示处理模式
         * ZipArchive::OVERWRITE 总是以一个新的压缩包开始，此模式下如果已经存在则会被覆盖。
         * ZIPARCHIVE::CREATE 如果不存在则创建一个zip压缩包，若存在系统就会往原来的zip文件里添加内容。
         */
		if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE)!==TRUE) {
			exit('无法打开文件，或者文件创建失败');
		}
		foreach($files as $val){
			if(file_exists($val)){
				//addFile函数首个参数如果带有路径，则压缩的文件里包含的是带有路径的文件压缩
				//若不希望带有路径，则需要该函数的第二个参数
				$zip->addFile($val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
			}
		}
		$zip->close();//关闭

		if(!file_exists($zipName)){
			exit("无法找到文件"); //即使创建，仍有可能失败
		}
		//如果不要下载，下面这段删掉即可，如需返回压缩包下载链接，只需 return $zipName;
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header('Content-disposition: attachment; filename='.basename($zipName)); //文件名
		header("Content-Type: application/zip"); //zip格式的
		header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
		header('Content-Length: '. filesize($zipName)); //告诉浏览器，文件大小
		@readfile($zipName);
	}

	/**压缩一个文件
	 * @param $pathUrl 要压缩的文件 本地具体路径
	 * @param $filenameName  要压缩成的文件名
	 */
	public function zipOne($pathUrl,$filenameName){
		$path = $pathUrl;
		$filename =$filenameName;
		$zip = new \ZipArchive();
		$zip->open($filename,\ZipArchive::CREATE);   //打开压缩包
		$zip->addFile($path,basename($path));   //向压缩包中添加文件
		$zip->close();  //关闭压缩包
		return 'ok';
	}
	public function callZipOne(){
		$img ="d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcode/logo.jpg";
		$fileName = "d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcode/test.zip";
		return self::zipOne($img,$fileName);  //self调用当前控制器zipOne方法

	}

	/**
	 * 压缩多个文件
	 * @param $fileList 多个文件
	 * @param $filename 要压缩的文件zip名
	 */
	public function zipMore($fileList,$filename){
		$zip = new \ZipArchive();
		$zip->open($filename,\ZipArchive::CREATE);   //打开压缩包
		foreach($fileList as $file){
			$zip->addFile($file,basename($file));   //向压缩包中添加文件
		}
		$zip->close();  //关闭压缩包
		return 'ok';
	}
	public function callZipMore(){
		$imgArray =array("d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcode/logo.jpg","d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcode/logo1.jpg");
		$fileName = "d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcode/test.zip";
		return self::zipMore($imgArray,$fileName);
    }


	/** 压缩一个目录  目录递归 文件加入zip对象
	 * @param $path
	 * @param $zip
	 */
	function addFileToZipOne($path,$zip){
		$handler=opendir($path); //打开当前文件夹由$path指定。
		while(($filename=readdir($handler))!==false){
			if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
				if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
					$this->addFileToZipOne($path."/".$filename, $zip);
				}else{ //将文件加入zip对象
					$zip->addFile($path."/".$filename);
				}
			}
		}
		@closedir($path);
	}

	/** 压缩一个目录 调用addFileToZipOne
	 * @param $fileFile 要压缩的目录
	 * @param $filename 压缩包名
	 */
	public function addFileList($fileFile,$filename){
		$zip=new \ZipArchive();
		if($zip->open($filename, \ZipArchive::OVERWRITE)=== TRUE){ //open 打开压缩包
			$this->addFileToZipOne($fileFile, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
			$zip->close(); //关闭处理的zip文件
			return 'ok';
		 }
    }
    //调用  压缩文件名必须先建好
    public function callAddFileList(){
		$fileFile = "d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcode/"; //;目录
		$filename = "d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcodexwq.zip"; //压缩文件名
		return self::addFileList($fileFile,$filename);
	}


     //压缩并下载zip包
	public function addFileToZipDownMore($fileFile,$filename){
		$zip=new \ZipArchive();
		if($zip->open($filename, \ZipArchive::OVERWRITE)=== TRUE){
			$path = $fileFile;
			if(is_dir($path)){  //给出文件夹，打包文件夹
				$this->addFileToZipOne($path, $zip);
			}else if(is_array($path)){  //以数组形式给出文件路径
				foreach($path as $file){
					$zip->addFile($file);
				}
			}else{      //只给出一个文件
				$zip->addFile($path);
			}
			$zip->close(); //关闭处理的zip文件
     	}
    }
     //调用  压缩文件名必须先建好
    public function callAddFileToZipDown(){
		$fileFile = "d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcode/"; //;目录
		$filename = "d:/wampstack-5.6/apache2/htdocs/tp5/public/static/qrcodexwq.zip"; //压缩文件名
		return self::addFileList($fileFile,$filename);
	}

	/**解压文件
	 *@file  需要解压的文件的路径
	 *@destination  解压之后存放的路径   富文本编辑器：文字+图片+格式+排版
	 */
	function unzip_file($file, $destination){
        // 实例化对象
		$zip = new \ZipArchive() ;
        //打开zip文档，如果打开失败返回提示信息
		if ($zip->open($file) !== TRUE) {
			die ("不能打开文件!");
		}
        //将压缩文件解压到指定的目录下
		$zip->extractTo($destination);
        //关闭zip文档
		$zip->close();
		// echo '提取成功';
	}
	//测试执行
	public function callUnzipFile(){
		$file="c:/Users/Public/Music/Sample Music/Sample Music.zip";
		$dest="c:/Users/Public/Music/Sample Music/yinyue/";
		return self::unzip_file($file,$dest);
	}


}
?>

