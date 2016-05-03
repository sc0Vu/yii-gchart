<h2>yii-gchart usage instructions</h2>
<ol>
<li>check google chart library <a href="https://developers.google.com/chart/" target="_blank" title="google chart link">link</a></li>
<li>import GChart</li>
<li>use in view
<code><?php $this->widget('extensions.gchart.GChart',
		                    array(
							'chartLibrary' => library URL,
							'chartPackages' => packages array,
							'chartLibraryVersion' => 'current',
							'chartData' => array(
								array(
								'chartType' => 'PieChart',
								'data' => data array,
								'options' => chart options
								),
							),
							)); ?></code></li>
</ol>