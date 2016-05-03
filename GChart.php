<?php

/**
 * GChart
 * simple load google multiple chart
 * google chart https://developers.google.com/chart/
 *
 * @author alk03073135@gmail.com
 * @license MIT
 */
class GChart extends CWidget
{

    // standard chart library
    public $chartLibrary = 'https://www.gstatic.com/charts/loader.js';

    // chart loaded library
    public $chartPackages = array('corechart');  

    // chart library version
    public $chartLibraryVersion = "current";

    // chart data
    public $chartData = array();
  
    // is library loaded by ajax
    public $isPjax = false;

    // load js id
    protected $loadJsId = 'gchart_loadjs';

    // callback js id
    protected $callbackJsId = 'gchart_callbackjs';

    public function init() {
        parent::init();

        if (count($this->chartData) < 1) {
            throw new CException('Chart data must set!');
        }

        $packages = CJavaScript::jsonEncode(array(
            'packages'=>$this->chartPackages,
            ));
        $loadJs = <<<SCRIPTCALLBACK
        google.charts.load('{$this->chartLibraryVersion}', {$packages});
        google.charts.setOnLoadCallback(drawCharts);
        charts = [];
        datas = [];
SCRIPTCALLBACK;
        if ($this->isPjax) {
            $loadScript = <<<LOADSCRIPT
                (function(d,s,l,x){
                    x = d.createElement(s);
                    x.src = l;
                    d.head.insertBefore(x, d.getElementsByTagName(s)[0]);
                })(document,'script','{$this->chartLibrary}');
LOADSCRIPT;
            echo CHtml::tag('script', array(), $loadScript, true);
            echo CHtml::tag('script', array(), $loadJs, true);
        } else {
            Yii::app()->getClientScript()->registerScriptFile($this->chartLibrary, CClientScript::POS_HEAD);
            Yii::app()->getClientScript()->registerScript($this->loadJsId, $loadJs, CClientScript::POS_HEAD);
        }
    }

    /**
     * Widget's run method
     */
    public function run() {
        parent::run();
        $callbackJs = '';
        $totalChart = count($this->chartData);

        for ($i=0; $i<$totalChart; $i++) {
            $data = CJavaScript::jsonEncode($this->chartData[$i]['data']);
            $options = CJavaScript::jsonEncode($this->chartData[$i]['options']);
            $containerId = (isset($this->chartData[$i]['containerId'])) ? $this->chartData[$i]['containerId'] : 'gchart'.$i;
            echo CHtml::tag('div', array('id'=>$containerId), '', true);

            $callbackJs .= <<<CALLBACKJS
            datas[$i] = google.visualization.arrayToDataTable({$data});
            charts[$i] = new google.visualization.{$this->chartData[$i]['chartType']}(document.getElementById('{$containerId}'));
            charts[$i].draw(datas[$i], {$options});
CALLBACKJS;
        }

        if ($this->isPjax) {
            echo CHtml::tag('script', array(), "function drawCharts() {".$callbackJs."}", true);
        } else {
            Yii::app()->getClientScript()->registerScript($this->callbackJsId, "function drawCharts () {".$callbackJs."}", CClientScript::POS_HEAD);
        }
    }
}