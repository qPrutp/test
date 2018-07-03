<?php
$result2 = [['num' => '1', 'test' => 'Проверка наличия файла robots.txt', 'status' => 'Ok', 'state' => 'Файл robots.txt присутствует', 'recommendation' => 'Доработки не требуются'],
			['num' => '2', 'test' => 'Проверка наличия файла robots.txt', 'status' => 'Ошибка', 'state' => 'Файл robots.txt присутствует', 'recommendation' => 'Доработки не требуются']];
$state = 'Cостояние';
$recommendations = 'Рекомендации';
?>

<form action="../logics/search.php" method="post" id="parser-form">
    <div class="url">
        <label style="font-weight: bolder">введіть адрес сайту <input type="text" id="site" name="site"></label>
        <button id="button">Перевірити</button>
    </div>
</form>
<div class="table"></div>

<div class="div-table">
	<?php if($result != []) { ?>
		work table
		<table class="table-test">
			<tr class="table-header">
				<th class="table-number table-text-center">№</th>
				<th class="table-name-test table-text-left">Название проверки</th>
				<th class="table-status table-text-center">Статус</th>
				<th class="table-empty-header table-text-left"></th>
				<th class="table-current-state table-text-left">Текущее состояние</th>
			</tr>
			<?php foreach ($result as $k => $v) { ?>
			<tr class="table-content-gaps"><th colspan="5">&nbsp;</th></tr>
			<tr class="table-content">
                <td class="table-number table-text-center" rowspan="2">			<?php echo $v['num']; ?>				</td>
                <td class="table-name-test table-text-left" rowspan="2">		<?php echo $v['test']; ?>				</td>
                <td class="table-status table-text-center"
                	<?php if($v['status'] == 'Ok')
                		echo 'style="background: green;"';
                	else
                		echo 'style="background: crimson;"'; ?>
                										rowspan="2">			<?php echo $v['status']; ?>				</td>
                <td class="table-empty-header table-text-left">					<?php echo $state; ?>					</td>
                <td class="table-current-state table-text-left">				<?php echo $v['state']; ?>				</td>
            </tr>
            <tr class="table-content">
            	<td class="table-empty-header table-text-left">					<?php echo $recommendations; ?>			</td>
                <td class="table-current-state table-text-left">				<?php echo $v['recommendation']; } ?>	</td>
            </tr>
		</table>
	<?php } ?>
</div>

<div class="div-table">
	<?php if($result2 != []) { ?>
		test table
		<table class="table-test">
			<tr class="table-header">
				<th class="table-number table-text-center">№</th>
				<th class="table-name-test table-text-left">Название проверки</th>
				<th class="table-status table-text-center">Статус</th>
				<th class="table-empty-header table-text-left"></th>
				<th class="table-current-state table-text-left">Текущее состояние</th>
			</tr>
			<?php foreach ($result2 as $k => $v) { ?>
			<tr class="table-content-gaps"><th colspan="5">&nbsp;</th></tr>
			<tr class="table-content">
                <td class="table-number table-text-center" rowspan="2">			<?php echo $v['num']; ?>				</td>
                <td class="table-name-test table-text-left" rowspan="2">		<?php echo $v['test']; ?>				</td>
                <td class="table-status table-text-center"
                	<?php if($v['status'] == 'Ok')
                		echo 'style="background: green;"';
                	else
                		echo 'style="background: crimson;"'; ?>
                										rowspan="2">			<?php echo $v['status']; ?>				</td>
                <td class="table-empty-header table-text-left">					<?php echo $state; ?>					</td>
                <td class="table-current-state table-text-left">				<?php echo $v['state']; ?>				</td>
            </tr>
            <tr class="table-content">
            	<td class="table-empty-header table-text-left">					<?php echo $recommendations; ?>			</td>
                <td class="table-current-state table-text-left">				<?php echo $v['recommendation']; } ?>	</td>
            </tr>
		</table>
	<?php } ?>
</div>
