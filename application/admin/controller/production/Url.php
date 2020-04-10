<?php

namespace app\admin\controller\production;

use app\common\controller\Backend;
use Endroid\QrCode\QrCode;
use think\Response;

/**
 * 商品链接
 *
 * @icon fa fa-link
 */
class Url extends Backend
{
    
    /**
     * Url模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\production\Production_select;

    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where(['team_id'=>$this->adminInfo['team_id']])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where(['team_id'=>$this->adminInfo['team_id']])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 获取商品推广地址
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function url($ids = null)
    {
        $data = $this->model->get(['id' => $ids]);
        $str = 'aid='.$this->adminInfo['id'].'&gid='.$ids.'&tid='.$this->adminInfo['team_id'].'&tp=shoes';
        $checkCode = md5($str);
        //TODO::先使用本机域名，后面加入防封方式进行域名选择切换
        //TODO::目前使用固定这个一，等加了模板之后再做成数据获取
        $url = $this->request->domain().'/index.php/index/index?'.$str.'&check_code='.$checkCode.'&tp=shoes';
//        $url = $this->request->domain().'/index.php/index/index?'.$str.'&check_code='.$checkCode;
        $data['production_url'] = $url;
        $this->assign('data',$data);
        return $this->view->fetch();
    }

    /**
     * 生成二维码
     * @return Response
     * @throws \Endroid\QrCode\Exceptions\DataDoesntExistsException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionFailedException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
     */
    public function build()
    {
        $text = $this->request->get('text', 'hello world');
        $size = $this->request->get('size', 250);
        $padding = $this->request->get('padding', 15);
        $errorcorrection = $this->request->get('errorcorrection', 'medium');
        $foreground = $this->request->get('foreground', "#ffffff");
        $background = $this->request->get('background', "#000000");
        $logo = $this->request->get('logo');
        $logosize = $this->request->get('logosize');
        $label = $this->request->get('label');
        $labelfontsize = $this->request->get('labelfontsize');
        $labelhalign = $this->request->get('labelhalign');
        $labelvalign = $this->request->get('labelvalign');

        // 前景色
        list($r, $g, $b) = sscanf($foreground, "#%02x%02x%02x");
        $foregroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        // 背景色
        list($r, $g, $b) = sscanf($background, "#%02x%02x%02x");
        $backgroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        $qrCode = new QrCode();
        $qrCode
            ->setText($text)
            ->setSize($size)
            ->setPadding($padding)
            ->setErrorCorrection($errorcorrection)
            ->setForegroundColor($foregroundcolor)
            ->setBackgroundColor($backgroundcolor)
            ->setLogoSize($logosize)
            ->setLabel($label)
            ->setLabelFontSize($labelfontsize)
            ->setLabelHalign($labelhalign)
            ->setLabelValign($labelvalign)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        $fontPath = ROOT_PATH . 'public/assets/fonts/SourceHanSansK-Regular.ttf';
        if (file_exists($fontPath)) {
            $qrCode->setLabelFontPath($fontPath);
        }
        if ($logo) {
            $qrCode->setLogo(ROOT_PATH . 'public/assets/img/qrcode.png');
        }
        //也可以直接使用render方法输出结果
        //$qrCode->render();
        return new Response($qrCode->get(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }



    /**
     * 回收站
     * @internal
     */
    public function recyclebin()
    {
        return $this->error('不允许此操作');
    }

    /**
     * 添加
     * @internal
     */
    public function add()
    {
        return $this->error('不允许此操作');
    }

    /**
     * 编辑
     * @param null $ids
     * @internal
     */
    public function edit($ids = null)
    {
        return $this->error('不允许此操作');
    }

    /**
     * 删除
     * @param null $ids
     * @internal
     */
    public function del($ids = null)
    {
        return $this->error('不允许此操作');
    }
}
