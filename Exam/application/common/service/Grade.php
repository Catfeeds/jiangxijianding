<?php
namespace app\common\service;

use app\common\model\Grade as MGrade;
class Grade extends MGrade
{

    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @$user xuwieqi
     */
    public function selGradeData($map,$field='',$order=''){
        return $this->BaseFind($map,$field,$order);
    }

    /**
     * @param $data
     * @return mixed|string
     * @$user xuwieqi
     */
    public function saveGradeData($data){
        return $this->BaseSave($data);
    }

    /**
     * @param $data
     * @param $map
     * @return false|int|string
     * @$user xuwieqi
     */
    public function updateGradeData($data,$map){
       return $this->BaseUpdate($data, $map);
    }

    /***
     * @return false|\PDOStatement|string|\think\Collection 个人
     * @$user xuwieqi
     */
    public function selectGradeData($map){
        $field= "g.*,exam_plan.title,work.name as workname, work_direction.name as directionname";
        $join=array(
            ["__EXAM_PLAN__","g.exam_plan_id=exam_plan.id",'left'],
            ["__WORK__","g.work_id=work.id",'right'],
            ["__WORK_DIRECTION__","g.work_direction_id=work_direction.id",'left'],
        );

        $data= $this->BaseJoinSelect('g',$join,$map,[$field]);
        return $data;
    }

    public function getGradeByidWithPage($paginate, $param = [], $field = [], $order = '', $group = '')
    {
        $join = array(
            ['__EXAM_ORDER_DETAIL__', "exam_order_detail.enroll_id=grade.exam_enroll_id",'left'],
            ['__EXAM_ORDER__', "exam_order.id=exam_order_detail.order_id AND pay_state=".config('ExamOrderStatus.paysuccess')],
            ['__CERTIFICATE__', "grade.exam_enroll_id=certificate.exam_enroll_id AND certificate.id is null",'left'],
        );
        $examJoinData = $this->BaseJoinSelectPage($paginate,'grade',$join,$param,$field);
        return $examJoinData;
    }
    public function getGradeByid($param = [], $field = [], $order = '', $group = '')
    {
        $join = array(
            ['__EXAM_ORDER_DETAIL__', "exam_order_detail.enroll_id=grade.exam_enroll_id",'left'],
            ['__EXAM_ORDER__', "exam_order.id=exam_order_detail.order_id AND pay_state=".config('ExamOrderStatus.paysuccess')],
            ['__CERTIFICATE__', "grade.exam_enroll_id=certificate.exam_enroll_id AND certificate.id is null",'left'],
            ['__EXAM_ENROLL__', "exam_enroll.id=grade.exam_enroll_id"],
            ['__EXAM_PLAN__', "exam_plan.id=grade.exam_plan_id"],
            ['__ORGANIZE__', "exam_enroll.organize_id=organize.id",'left'],
            ['__EXAM_CENTER__', "exam_enroll.site_id=exam_center.id OR organize.subordinate_admin = exam_center.id",'left'],
            ['__USERINFO__', "grade.user_login_id=userinfo.user_login_id",'left'],
        );
        $examJoinData = $this->BaseJoinSelect('grade',$join,$param,$field);
        return $examJoinData;
    }

    /**
     * [selectGrade 机构考生]
     * @return [type] [description]
     */
    public function selectExamGrade($map,$field,$or=[])
    {
        // $join=array(
        //     ['__USER_LOGIN__ ul','ul.id_card=g.id_card','left'],
        //     ['__EXAM_ENROLL__ ee','ee.exam_plan_id=g.exam_plan_id and ul.id=ee.user_login_id','left']
        // );
        $paginate = array(
           config('paginate.list_rows'),
           false,
           ['query'=>Request()->param()],
        );

        return $this
        ->join('__USER_LOGIN__ ul','ul.id_card=grade.id_card','left')
        ->join('__EXAM_ENROLL__ ee','ee.exam_plan_id=grade.exam_plan_id and ul.id=ee.user_login_id','left')
        ->where($map)
        ->where($or)
        ->field($field)
        ->paginate($paginate);


        // $data= $this->BaseJoinSelectPage($paginate,'g',$join,$map,[$field]);
        // return $data;
    }

    /**
     * [Excel 成绩导出Excel]
     * @param [type] $title [标题]
     * @param [type] $data  [数据]
     * @param [type] $column  [表里的字段]
     * @param [string] $filename [文件名]
     */
    public function Excel($title,$data,$column,$filename)
    {
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        foreach ($title as $key => $val) 
        {
            $zi = $this->ExcelTitle($key);
            $objPHPExcel->getActiveSheet()->setCellValue($zi. 1,$val);
        }
        foreach ($data as $key => $val) 
        {
            for ($i=0; $i < count($column); $i++) 
            { 
                $zi = $this->ExcelTitle($i);
                $objPHPExcel->getActiveSheet()->setCellValue($zi.($key+2),$val->$column[$i]);
            }
        }
        header("Content-Type: application/force-download");  
        header("Content-Type: application/octet-stream");  
        header("Content-Type: application/download");  
        header('Content-Disposition:inline;filename="'.$filename.'"');  
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
    }

    private function ExcelTitle($key)
    {
        $zimu = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        return $zimu[$key];
    }


}
